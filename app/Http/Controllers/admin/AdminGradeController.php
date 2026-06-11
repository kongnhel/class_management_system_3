<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Program;
use App\Models\Quiz;
use App\Services\GradingService;
use Illuminate\Http\Request;

class AdminGradeController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::with(['course', 'lecturer', 'targetPrograms'])
            ->withCount('studentCourseEnrollments');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('course', function ($q2) use ($search) {
                    $q2->where('title_km', 'LIKE', "%{$search}%")
                        ->orWhere('title_en', 'LIKE', "%{$search}%");
                })->orWhereHas('lecturer', function ($q3) use ($search) {
                    $q3->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('program_id')) {
            $query->whereHas('targetPrograms', function ($q) use ($request) {
                $q->where('program_id', $request->input('program_id'));
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->input('academic_year'));
        }

        $courseOfferings = $query->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $programs = Program::orderBy('name_km')->get();

        return view('admin.grades.index', compact('courseOfferings', 'programs'));
    }

    public function show(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'lecturer',
            'targetPrograms',
            'studentCourseEnrollments.student.studentProfile',
        ]);

        // Load all assessments for this course offering
        $assignments = Assignment::where('course_offering_id', $courseOffering->id)->get();
        $exams = Exam::where('course_offering_id', $courseOffering->id)->get();
        $quizzes = Quiz::where('course_offering_id', $courseOffering->id)->get();

        $assessments = collect($assignments)->concat($exams)->concat($quizzes)->sortBy('created_at');

        // Load all exam results for enrolled students
        $allResults = ExamResult::whereIn('student_user_id', $courseOffering->studentCourseEnrollments->pluck('student_user_id'))
            ->get();

        // Build gradebook and compute totals
        $gradebook = [];
        $students = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use ($assessments, $allResults, &$gradebook, $courseOffering) {
            $student = $enrollment->student;

            // Attendance score (15% weight)
            $attendanceScore = (float) ($student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);
            $totalScore = $attendanceScore;

            foreach ($assessments as $assessment) {
                $type = ($assessment instanceof Assignment) ? 'assignment' :
                       (($assessment instanceof Quiz) ? 'quiz' : 'exam');

                $scoreRecord = $allResults->where('assessment_id', $assessment->id)
                    ->where('student_user_id', $student->id)
                    ->where('assessment_type', $type)
                    ->first();

                $score = $scoreRecord ? (float) $scoreRecord->score_obtained : 0;
                $gradebook[$student->id][$type.'_'.$assessment->id] = $score;

                $totalScore += $score;
            }

            $student->temp_total = (float) $totalScore;
            $student->letterGrade = GradingService::getLetterGrade($totalScore);
            $student->isPassing = GradingService::isPassing($student->letterGrade);

            return $student;
        });

        // Sort by total score descending
        $students = $students->sortByDesc('temp_total')->values();

        // Assign ranks
        foreach ($students as $index => $student) {
            $student->rank = $index + 1;
        }

        // Compute stats
        $totalStudents = $students->count();
        $passCount = $students->where('isPassing', true)->count();
        $avgScore = $totalStudents > 0 ? $students->avg('temp_total') : 0;
        $maxScore = $totalStudents > 0 ? $students->max('temp_total') : 0;
        $minScore = $totalStudents > 0 ? $students->min('temp_total') : 0;

        $stats = [
            'total' => $totalStudents,
            'graded' => $students->where('temp_total', '>', 0)->count(),
            'avg_grade' => $avgScore,
            'max_grade' => $maxScore,
            'min_grade' => $minScore,
            'pass_rate' => $totalStudents > 0 ? ($passCount / $totalStudents) * 100 : 0,
        ];

        return view('admin.grades.show', compact('courseOffering', 'students', 'assessments', 'gradebook', 'stats'));
    }

    public function exportGrades(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'lecturer',
            'targetPrograms',
            'studentCourseEnrollments.student.studentProfile',
        ]);

        // Re-use the same grade computation logic
        $assignments = Assignment::where('course_offering_id', $courseOffering->id)->get();
        $exams = Exam::where('course_offering_id', $courseOffering->id)->get();
        $quizzes = Quiz::where('course_offering_id', $courseOffering->id)->get();
        $assessments = collect($assignments)->concat($exams)->concat($quizzes)->sortBy('created_at');

        $allResults = ExamResult::whereIn('student_user_id', $courseOffering->studentCourseEnrollments->pluck('student_user_id'))
            ->get();

        $gradebook = [];
        $students = $courseOffering->studentCourseEnrollments->map(function ($enrollment) use ($assessments, $allResults, &$gradebook, $courseOffering) {
            $student = $enrollment->student;
            $attendanceScore = (float) ($student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);
            $totalScore = $attendanceScore;

            foreach ($assessments as $assessment) {
                $type = ($assessment instanceof Assignment) ? 'assignment' :
                       (($assessment instanceof Quiz) ? 'quiz' : 'exam');
                $scoreRecord = $allResults->where('assessment_id', $assessment->id)
                    ->where('student_user_id', $student->id)
                    ->where('assessment_type', $type)
                    ->first();
                $score = $scoreRecord ? (float) $scoreRecord->score_obtained : 0;
                $gradebook[$student->id][$type.'_'.$assessment->id] = $score;
                $totalScore += $score;
            }
            $student->temp_total = (float) $totalScore;
            $student->letterGrade = GradingService::getLetterGrade($totalScore);
            $student->isPassing = GradingService::isPassing($student->letterGrade);

            return $student;
        });

        $students = $students->sortByDesc('temp_total')->values();

        $fileName = 'grades_'.$courseOffering->course->title_en.'_'.$courseOffering->academic_year.'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () use ($courseOffering, $students, $assessments, $gradebook) {
            // UTF-8 BOM for Excel to recognize Khmer characters
            echo "\xEF\xBB\xBF";
            $file = fopen('php://output', 'w');

            // Header
            $header = ['Rank', 'Student ID', 'Name', 'Email', 'Attendance'];
            foreach ($assessments as $assessment) {
                $typeLabel = $assessment instanceof Assignment ? 'Assign' : ($assessment instanceof Quiz ? 'Quiz' : 'Exam');
                $header[] = $typeLabel.': '.$assessment->title_km;
            }
            $header = array_merge($header, ['Total', 'Letter Grade', 'Status']);
            fputcsv($file, $header);

            // Data
            foreach ($students as $index => $student) {
                $row = [
                    $index + 1,
                    $student->student_id_code ?? '',
                    $student->studentProfile->full_name_km ?? $student->name,
                    $student->email ?? '',
                    $student->getAttendanceScoreByCourse($courseOffering->id) ?? 0,
                ];
                foreach ($assessments as $assessment) {
                    $type = ($assessment instanceof Assignment) ? 'assignment' :
                           (($assessment instanceof Quiz) ? 'quiz' : 'exam');
                    $key = $type.'_'.$assessment->id;
                    $row[] = $gradebook[$student->id][$key] ?? '';
                }
                $row[] = number_format($student->temp_total, 1);
                $row[] = $student->letterGrade;
                $row[] = $student->isPassing ? 'Pass' : 'Fail';
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
