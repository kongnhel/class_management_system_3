<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CourseOffering;
use App\Models\ExamResult;
use App\Models\GradingCategory;
use App\Models\Quiz;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentGradeController extends Controller
{
    public function myGrades(Request $request)
    {
        $user = Auth::user();
        $currentYear = $request->input('academic_year');
        $currentSemester = $request->input('semester');

        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $allExamResults = ExamResult::where('student_user_id', $user->id)
            ->whereIn('assessment_id', function ($q) use ($enrolledOfferingIds) {
                $q->select('id')->from('assignments')->whereIn('course_offering_id', $enrolledOfferingIds)
                    ->union(DB::table('quizzes')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds))
                    ->union(DB::table('exams')->select('id')->whereIn('course_offering_id', $enrolledOfferingIds));
            })
            ->with(['assignment', 'exam', 'quiz'])
            ->get();

        $filteredOfferingIds = $enrolledOfferingIds;
        if ($currentYear || $currentSemester) {
            $filteredOfferingIds = CourseOffering::whereIn('id', $enrolledOfferingIds)
                ->when($currentYear, fn ($q) => $q->where('academic_year', $currentYear))
                ->when($currentSemester, fn ($q) => $q->where('semester', $currentSemester))
                ->pluck('id');
        }

        $filteredResults = $allExamResults->whereIn('course_offering_id', $filteredOfferingIds);

        $courseGrades = $filteredResults->groupBy('course_id')->map(function ($items, $courseId) use ($user) {
            $attendanceScore = $user->getAttendanceScoreByCourse($courseId);
            $absCount = \App\Models\AttendanceRecord::where('student_user_id', $user->id)->where('course_offering_id', $courseId)->where('status', 'absent')->count();
            $perCount = \App\Models\AttendanceRecord::where('student_user_id', $user->id)->where('course_offering_id', $courseId)->where('status', 'permission')->count();

            $nonQuiz = $items->where('assessment_type', '!=', 'quiz')->sum('score_obtained');
            $quizBonus = $items->where('assessment_type', 'quiz')->sum('score_obtained');
            $totalObtained = min($attendanceScore + $nonQuiz + $quizBonus, 100);

            $finalExamScore = $items->where('display_type', 'Final')->sum('score_obtained');
            $midtermScore = $items->where('display_type', 'Midterm')->sum('score_obtained');
            $assignmentScore = $items->where('display_type', 'Assignment')->sum('score_obtained');

            $isFailed = ($finalExamScore < 24 || $midtermScore < 9 || $assignmentScore < 9 || $attendanceScore < 9);
            $letterGrade = $isFailed ? 'F' : $this->calculateGrade($totalObtained, 100);

            // Resolve course info through the first result's assessment relationship
            $firstItem = $items->first();
            $offering = match($firstItem->assessment_type) {
                'assignment' => $firstItem->assignment?->courseOffering,
                'exam' => $firstItem->exam?->courseOffering,
                'quiz' => $firstItem->quiz?->courseOffering,
                default => null,
            };
            $course = $offering?->course;

            // Get course_offering_id for this course for ranking
            $offeringId = $offering?->id ?? $courseId;
            $enrollments = StudentCourseEnrollment::where('course_offering_id', $offeringId)->get();
            $rankings = $enrollments->map(function ($enrol) use ($offeringId) {
                $student = User::find($enrol->student_user_id);
                $att = $student ? $student->getAttendanceScoreByCourse($offeringId) : 0;
                $nonQuiz = ExamResult::where('student_user_id', $enrol->student_user_id)->where('assessment_type', '!=', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('assignments')->where('course_offering_id', $offeringId)
                            ->union(DB::table('exams')->select('id')->where('course_offering_id', $offeringId));
                    })->sum('score_obtained');
                $quiz = ExamResult::where('student_user_id', $enrol->student_user_id)->where('assessment_type', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('quizzes')->where('course_offering_id', $offeringId);
                    })->sum('score_obtained');
                return ['id' => $enrol->student_user_id, 'total' => min((float) $att + (float) $nonQuiz + (float) $quiz, 100)];
            })->sortByDesc('total')->values();

            $rankIndex = $rankings->search(fn ($r) => $r['id'] == $user->id);

            return (object) [
                'course_id' => $courseId,
                'course_code' => $course->code ?? '',
                'course_name_en' => $course->title_en ?? '',
                'course_name_km' => $course->title_km ?? '',
                'credits' => $course->credits ?? 3,
                'academic_year' => $offering?->academic_year ?? '',
                'semester' => $offering?->semester ?? '',
                'course_rank' => ($rankIndex !== false) ? $rankIndex + 1 : '-',
                'total_students' => $rankings->count(),
                'attendance_score' => $attendanceScore,
                'absent_count' => $absCount,
                'permission_count' => $perCount,
                'total_score' => $totalObtained,
                'grade' => $letterGrade,
                'grade_points' => $this->gradeToPoints($letterGrade),
                'is_failed' => $isFailed,
                'assessments' => $items,
            ];
        })->values();

        $totalCredits = $courseGrades->sum('credits');
        $weightedPoints = $courseGrades->sum(fn ($g) => $g->grade_points * $g->credits);
        $gpa = $totalCredits > 0 ? round($weightedPoints / $totalCredits, 2) : 0;
        $averageScore = $courseGrades->count() > 0 ? round($courseGrades->avg('total_score'), 1) : 0;

        $peerIds = StudentCourseEnrollment::whereIn('course_offering_id', $filteredOfferingIds)->pluck('student_user_id')->unique();
        $rankings = $peerIds->map(function ($peerId) use ($filteredOfferingIds) {
            $peer = User::find($peerId);
            if (!$peer) return ['id' => $peerId, 'total' => 0];
            $total = 0;
            foreach ($filteredOfferingIds as $offeringId) {
                $nonQuiz = ExamResult::where('student_user_id', $peerId)->where('assessment_type', '!=', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('assignments')->where('course_offering_id', $offeringId)
                            ->union(DB::table('exams')->select('id')->where('course_offering_id', $offeringId));
                    })->sum('score_obtained');
                $quiz = ExamResult::where('student_user_id', $peerId)->where('assessment_type', 'quiz')
                    ->whereIn('assessment_id', function ($q) use ($offeringId) {
                        $q->select('id')->from('quizzes')->where('course_offering_id', $offeringId);
                    })->sum('score_obtained');
                $att = $peer->getAttendanceScoreByCourse($offeringId);
                $total += min((float) $nonQuiz + (float) $quiz + (float) $att, 100);
            }
            return ['id' => $peerId, 'total' => $total];
        })->sortByDesc('total')->values();

        $rankIndex = $rankings->search(fn ($r) => $r['id'] == $user->id);
        $overallRank = ($rankIndex !== false) ? $rankIndex + 1 : '-';
        $totalClassmates = $rankings->count();
        $overallGrade = $averageScore >= 90 ? 'A' : ($averageScore >= 80 ? 'B' : ($averageScore >= 70 ? 'C' : ($averageScore >= 60 ? 'D' : ($averageScore >= 50 ? 'E' : 'F'))));
        $totalFinalScore = round($averageScore, 1);

        $academicYears = CourseOffering::whereIn('id', $enrolledOfferingIds)->pluck('academic_year')->unique()->filter()->values();
        $semesters = CourseOffering::whereIn('id', $enrolledOfferingIds)->pluck('semester')->unique()->filter()->values();

        $grades = $courseGrades->filter(function ($g) use ($currentYear, $currentSemester) {
            if ($currentYear && $g->academic_year !== $currentYear) return false;
            if ($currentSemester && $g->semester !== $currentSemester) return false;
            return true;
        })->values();

        $page = request()->input('page', 1);
        $perPage = 10;
        $grades = new \Illuminate\Pagination\LengthAwarePaginator(
            $grades->forPage($page, $perPage),
            $grades->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('student.my-grades', compact(
            'user', 'grades', 'averageScore', 'overallRank', 'totalClassmates', 'overallGrade', 'totalFinalScore',
            'gpa', 'totalCredits', 'academicYears', 'semesters', 'currentYear', 'currentSemester', 'courseGrades'
        ));
    }

    public function mySchedule()
    {
        $user = Auth::user();
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $schedules = \App\Models\Schedule::whereIn('course_offering_id', $enrolledOfferingIds)
            ->with(['room', 'courseOffering.course', 'courseOffering.lecturer'])
            ->orderByRaw("FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->orderBy('start_time')
            ->get();
        $studentProgram = $user->program;
        return view('student.my-schedule', compact('schedules', 'studentProgram'));
    }

    public function enrolledCourses($studentId)
    {
        $student = User::findOrFail($studentId);
        $enrollments = StudentCourseEnrollment::where('student_user_id', $student->id)
            ->with(['courseOffering.course', 'courseOffering.lecturer'])
            ->get();
        return view('student.my-enrolled-courses', compact('student', 'enrollments'));
    }

    public function myAssessments()
    {
        $user = Auth::user();
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $courseOfferings = CourseOffering::whereIn('id', $enrolledOfferingIds)
            ->with(['course', 'lecturer', 'assignments', 'exams', 'quizzes'])
            ->get();

        $assessmentsByCourse = $courseOfferings->map(function ($offering) use ($user) {
            $assignments = $offering->assignments->map(function ($a) use ($user) {
                $result = ExamResult::where('student_user_id', $user->id)
                    ->where('assessment_id', $a->id)->where('assessment_type', 'assignment')->first();
                return ['title' => $a->title_km ?? $a->title_en, 'type' => 'assignment', 'type_label' => 'កិច្ចការ', 'max_score' => $a->max_score, 'score' => $result?->score_obtained, 'date' => $a->due_date, 'notes' => $result?->notes];
            });
            $midterms = $offering->exams->where('title_en', 'like', '%Midterm%')->map(function ($e) use ($user) {
                $result = ExamResult::where('student_user_id', $user->id)
                    ->where('assessment_id', $e->id)->where('assessment_type', 'exam')->first();
                return ['title' => $e->title_km ?? $e->title_en, 'type' => 'midterm', 'type_label' => 'ប្រឡងពាក់កណ្ដាល់', 'max_score' => $e->max_score, 'score' => $result?->score_obtained, 'date' => $e->exam_date, 'notes' => $result?->notes];
            });
            $finals = $offering->exams->where('title_en', 'like', '%Final%')->map(function ($e) use ($user) {
                $result = ExamResult::where('student_user_id', $user->id)
                    ->where('assessment_id', $e->id)->where('assessment_type', 'exam')->first();
                return ['title' => $e->title_km ?? $e->title_en, 'type' => 'final', 'type_label' => 'ប្រឡងប្រចាំឆមាស', 'max_score' => $e->max_score, 'score' => $result?->score_obtained, 'date' => $e->exam_date, 'notes' => $result?->notes];
            });
            $quizzes = $offering->quizzes->map(function ($q) use ($user) {
                $result = ExamResult::where('student_user_id', $user->id)
                    ->where('assessment_id', $q->id)->where('assessment_type', 'quiz')->first();
                return ['title' => $q->title_km ?? $q->title_en, 'type' => 'quiz', 'type_label' => 'Quiz (Bonus)', 'max_score' => $q->max_score, 'score' => $result?->score_obtained, 'date' => $q->quiz_date, 'notes' => $result?->notes];
            });
            $all = $assignments->concat($midterms)->concat($finals)->concat($quizzes)->filter(fn ($a) => $a['score'] !== null);
            $att = $user->getAttendanceScoreByCourse($offering->id);
            $nonQuiz = $all->where('type', '!=', 'quiz')->sum('score');
            $quizBonus = $all->where('type', 'quiz')->sum('score');
            return ['offering' => $offering, 'course_name' => $offering->course->title_km ?? $offering->course->title_en, 'assessments' => $all->values(), 'attendance_score' => $att, 'quiz_bonus' => $quizBonus, 'total_score' => min($att + $nonQuiz + $quizBonus, 100)];
        })->filter(fn ($c) => $c['assessments']->isNotEmpty())->values();

        return view('student.my-assessments', compact('assessmentsByCourse'));
    }

    public function availablePrograms()
    {
        $programs = \App\Models\Program::with('department')->get();
        return view('student.available-programs', compact('programs'));
    }

    public function availableCourses()
    {
        $user = Auth::user();
        $enrolledIds = StudentCourseEnrollment::where('student_user_id', $user->id)->pluck('course_offering_id');
        $courses = CourseOffering::with(['course', 'lecturer'])->withCount('studentCourseEnrollments')
            ->whereHas('targetPrograms', fn ($q) => $q->where('program_id', $user->program_id)->where('generation', $user->generation))
            ->where('end_date', '>=', now())->whereNotIn('id', $enrolledIds)->get();
        return view('student.available-courses', compact('courses'));
    }

    public function enrollSelf(Request $request)
    {
        $request->validate(['course_offering_id' => 'required|exists:course_offerings,id']);
        $user = Auth::user();
        $exists = StudentCourseEnrollment::where('student_user_id', $user->id)->where('course_offering_id', $request->course_offering_id)->exists();
        if ($exists) return back()->with('error', 'អ្នកបានចុះឈ្មោះរួចហើយ។');
        StudentCourseEnrollment::create(['student_user_id' => $user->id, 'course_offering_id' => $request->course_offering_id, 'enrollment_date' => now(), 'status' => 'enrolled']);
        return back()->with('success', 'ចុះឈ្មោះជោគជ័យ!');
    }

    public function enrollProgram(Request $request)
    {
        $request->validate(['program_id' => 'required|exists:programs,id']);
        $user = Auth::user();
        $gen = $user->generation;
        $offerings = CourseOffering::whereHas('targetPrograms', fn ($q) => $q->where('program_id', $request->program_id)->where('generation', $gen))->get();
        $enrolled = 0;
        foreach ($offerings as $offering) {
            $exists = StudentCourseEnrollment::where('student_user_id', $user->id)->where('course_offering_id', $offering->id)->exists();
            if (!$exists) {
                StudentCourseEnrollment::create(['student_user_id' => $user->id, 'course_offering_id' => $offering->id, 'enrollment_date' => now(), 'status' => 'enrolled']);
                $enrolled++;
            }
        }
        return back()->with('success', "ចុះឈ្មោះបាន {$enrolled} មុខវិជ្ជា!");
    }

    public function myEnrolledCourses()
    {
        $user = Auth::user();
        $enrollments = StudentCourseEnrollment::where('student_user_id', $user->id)
            ->with(['courseOffering.course', 'courseOffering.lecturer'])->paginate(10);
        $studentProgram = $user->program;
        return view('student.my-enrolled-courses', compact('enrollments', 'studentProgram'));
    }

    protected function calculateGrade($score, $maxScore)
    {
        $percentage = ($score / $maxScore) * 100;
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        if ($percentage >= 50) return 'E';
        return 'F';
    }

    protected function gradeToPoints($grade)
    {
        return match($grade) { 'A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'E' => 0.5, default => 0.0 };
    }
}
