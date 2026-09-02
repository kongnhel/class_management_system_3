<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ReExamResult;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReExamController extends Controller
{
    public function showForm($offeringId)
    {
        $offering = CourseOffering::with(['course', 'assignments', 'exams'])
            ->findOrFail($offeringId);

        $enrollments = \App\Models\StudentCourseEnrollment::where('course_offering_id', $offeringId)
            ->with('student')
            ->get();

        $existingReExams = ReExamResult::where('course_offering_id', $offeringId)
            ->get()
            ->keyBy(fn ($r) => $r->student_user_id.'_'.$r->assessment_type);

        $studentsData = $enrollments->map(function ($enrollment) use ($offering, $existingReExams) {
            $student = $enrollment->student;
            if (! $student) {
                return null;
            }

            $attendanceScore = $student->getAttendanceScoreByCourse($offering->id);

            $examResults = ExamResult::where('student_user_id', $student->id)
                ->where(function ($q) use ($offering) {
                    $q->where(function ($q2) use ($offering) {
                        $q2->where('assessment_type', 'exam')
                            ->whereIn('assessment_id', fn ($q3) => $q3->select('id')->from('exams')->where('course_offering_id', $offering->id));
                    })->orWhere(function ($q2) use ($offering) {
                        $q2->where('assessment_type', 'quiz')
                            ->whereIn('assessment_id', fn ($q3) => $q3->select('id')->from('quizzes')->where('course_offering_id', $offering->id));
                    })->orWhere(function ($q2) use ($offering) {
                        $q2->where('assessment_type', 'assignment')
                            ->whereIn('assessment_id', fn ($q3) => $q3->select('id')->from('assignments')->where('course_offering_id', $offering->id));
                    });
                })->with(['exam', 'assignment', 'quiz'])
                ->get();

            $componentStatus = GradingService::checkCriticalComponents(
                $attendanceScore,
                $examResults,
                $student,
                $offering->id
            );

            $failedItems = [];
            foreach ($componentStatus['needs_re_exam'] as $type) {
                if ($type === 'assignment') {
                    foreach ($offering->assignments as $assignment) {
                        $result = $examResults->first(fn ($r) => $r->assessment_type === 'assignment' && $r->assessment_id === $assignment->id);
                        $existingReExam = $existingReExams->get($student->id.'_assignment');
                        $failedItems[] = [
                            'assessment_type' => 'assignment',
                            'assessment_id' => $assignment->id,
                            'title' => $assignment->title_km ?? $assignment->title_en ?? 'Assignment',
                            'max_score' => $assignment->max_score,
                            'current_score' => $existingReExam ? (float) $existingReExam->new_score : ($result?->score_obtained ?? 0),
                            'original_score' => $result?->score_obtained ?? 0,
                            'has_re_exam' => (bool) $existingReExam,
                            'threshold' => GradingService::ASSIGNMENT_PASS,
                        ];
                    }
                } elseif (in_array($type, ['midterm', 'final'])) {
                    foreach ($offering->exams as $exam) {
                        $classified = GradingService::classifyExamType($exam);
                        if ($classified !== $type) {
                            continue;
                        }
                        $result = $examResults->first(fn ($r) => $r->assessment_type === 'exam' && $r->assessment_id === $exam->id);
                        $existingReExam = $existingReExams->get($student->id.'_'.$type);
                        $failedItems[] = [
                            'assessment_type' => $type,
                            'assessment_id' => $exam->id,
                            'title' => $exam->title_km ?? $exam->title_en ?? ucfirst($type),
                            'max_score' => $exam->max_score,
                            'current_score' => $existingReExam ? (float) $existingReExam->new_score : ($result?->score_obtained ?? 0),
                            'original_score' => $result?->score_obtained ?? 0,
                            'has_re_exam' => (bool) $existingReExam,
                            'threshold' => $type === 'midterm' ? GradingService::MIDTERM_PASS : GradingService::FINAL_PASS,
                        ];
                    }
                }
            }

            return [
                'student' => $student,
                'attendance_score' => $attendanceScore,
                'attendance_passing' => $componentStatus['attendance']['passing'],
                'failed_items' => $failedItems,
                'total_score' => 0,
            ];
        })->filter()->values();

        $studentsWithFailed = $studentsData->filter(fn ($s) => count($s['failed_items']) > 0);

        return view('admin.re-exam-form', compact('offering', 'studentsWithFailed'));
    }

    public function store(Request $request, $offeringId)
    {
        $offering = CourseOffering::findOrFail($offeringId);

        $request->validate([
            'scores' => 'required|array',
            'scores.*.student_user_id' => 'required|exists:users,id',
            'scores.*.assessment_type' => 'required|in:assignment,midterm,final',
            'scores.*.assessment_id' => 'required|integer',
            'scores.*.new_score' => 'required|numeric|min:0',
        ]);

        $today = now()->toDateString();
        $recordedBy = Auth::id();

        DB::transaction(function () use ($request, $offeringId, $today, $recordedBy) {
            foreach ($request->input('scores') as $entry) {
                $studentId = $entry['student_user_id'];
                $type = $entry['assessment_type'];
                $assessmentId = $entry['assessment_id'];
                $newScore = (float) $entry['new_score'];

                $maxScore = match ($type) {
                    'assignment' => Assignment::find($assessmentId)?->max_score ?? 20,
                    'midterm', 'final' => Exam::find($assessmentId)?->max_score ?? ($type === 'midterm' ? 15 : 50),
                    default => 100,
                };

                $newScore = min($newScore, $maxScore);

                if ($newScore <= 0) {
                    continue;
                }

                ReExamResult::updateOrCreate(
                    [
                        'student_user_id' => $studentId,
                        'course_offering_id' => $offeringId,
                        'assessment_type' => $type,
                    ],
                    [
                        'assessment_id' => $assessmentId,
                        'new_score' => $newScore,
                        're_exam_date' => $today,
                        'recorded_by' => $recordedBy,
                    ]
                );
            }
        });

        return redirect()->route('admin.grades.re-exam-form', $offeringId)
            ->with('success', __('រក្សាទុកពិន្ទុប្រឡងសងបានជោគជ័យ។'));
    }
}
