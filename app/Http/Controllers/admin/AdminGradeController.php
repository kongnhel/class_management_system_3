<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AcademicYear;
use App\Models\CourseOffering;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Generation;
use App\Models\Department;
use App\Models\Quiz;
use App\Models\ReExamResult;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminGradeController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseOffering::with(['course', 'lecturer', 'department'])
            ->selectRaw('course_offerings.*, (SELECT COUNT(DISTINCT student_user_id) FROM student_course_enrollments WHERE student_course_enrollments.course_offering_id = course_offerings.id) as student_course_enrollments_count')
            ->whereHas('course')
            ->whereHas('lecturer');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('course', function ($q2) use ($search) {
                    $q2->where('title_km', 'LIKE', "%{$search}%")
                        ->orWhere('title_en', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                })->orWhereHas('lecturer', function ($q3) use ($search) {
                    $q3->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('professor_id')) {
            $query->where('lecturer_user_id', $request->input('professor_id'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->input('academic_year'));
        }

        if ($request->filled('generation')) {
            $query->where('generation', $request->input('generation'));
        }

        $this->applyGradeStatusFilter($query, $request->input('grade_status'));

        $summaryOfferingIds = (clone $query)
            ->reorder()
            ->select('course_offerings.id');
        $gradeSummary = [
            'course_offerings' => (clone $query)->count(),
            'students' => StudentCourseEnrollment::whereIn('course_offering_id', $summaryOfferingIds)
                ->distinct('student_user_id')
                ->count('student_user_id'),
            're_exam_courses' => ReExamResult::whereIn('course_offering_id', $summaryOfferingIds)
                ->distinct('course_offering_id')
                ->count('course_offering_id'),
        ];

        $courseOfferings = $query->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $departments = Department::orderBy('name_km')->get();
        $generations = Generation::where('is_active', true)->orderByDesc('name')->get();
        $academicYears = AcademicYear::orderByDesc('name')->pluck('name');
        $professors = User::where('role', 'professor')->orderBy('name')->get(['id', 'name']);

        return view('admin.grades.index', compact('courseOfferings', 'departments', 'generations', 'academicYears', 'professors', 'gradeSummary'));
    }

    public function exportFiltered(Request $request)
    {
        $query = CourseOffering::with(['course', 'lecturer', 'department'])
            ->selectRaw('course_offerings.*, (SELECT COUNT(DISTINCT student_user_id) FROM student_course_enrollments WHERE student_course_enrollments.course_offering_id = course_offerings.id) as student_course_enrollments_count')
            ->whereHas('course')
            ->whereHas('lecturer');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('course', function ($q2) use ($search) {
                    $q2->where('title_km', 'LIKE', "%{$search}%")
                        ->orWhere('title_en', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                })->orWhereHas('lecturer', function ($q3) use ($search) {
                    $q3->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        $query->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->input('department_id')))
            ->when($request->filled('professor_id'), fn ($q) => $q->where('lecturer_user_id', $request->input('professor_id')))
            ->when($request->filled('semester'), fn ($q) => $q->where('semester', $request->input('semester')))
            ->when($request->filled('academic_year'), fn ($q) => $q->where('academic_year', $request->input('academic_year')))
            ->when($request->filled('generation'), fn ($q) => $q->where('generation', $request->input('generation')));

        $this->applyGradeStatusFilter($query, $request->input('grade_status'));

        $offerings = $query->orderByDesc('academic_year')->orderByDesc('semester')->get();

        return Excel::download(
            new \App\Exports\AdminGradeOfferingsExport($offerings),
            'admin-grade-offerings-'.now()->format('Y-m-d_H-i-s').'.xlsx'
        );
    }

    private function applyGradeStatusFilter($query, ?string $status): void
    {
        $gradedStudentCondition = function (string $enrollmentAlias): string {
            return "EXISTS (
                SELECT 1 FROM exam_results er
                WHERE er.student_user_id = {$enrollmentAlias}.student_user_id
                AND (
                    (er.assessment_type = 'assignment' AND EXISTS (
                        SELECT 1 FROM assignments a
                        WHERE a.id = er.assessment_id
                        AND a.course_offering_id = course_offerings.id
                    ))
                    OR (er.assessment_type = 'exam' AND EXISTS (
                        SELECT 1 FROM exams e
                        WHERE e.id = er.assessment_id
                        AND e.course_offering_id = course_offerings.id
                    ))
                    OR (er.assessment_type = 'quiz' AND EXISTS (
                        SELECT 1 FROM quizzes q
                        WHERE q.id = er.assessment_id
                        AND q.course_offering_id = course_offerings.id
                    ))
                )
            )";
        };

        $hasGradedStudent = $gradedStudentCondition('graded_enrollment');
        $hasUngradedStudent = 'EXISTS (
            SELECT 1 FROM student_course_enrollments ungraded_enrollment
            WHERE ungraded_enrollment.course_offering_id = course_offerings.id
            AND NOT ('.$gradedStudentCondition('ungraded_enrollment').')
        )';

        match ($status) {
            'not_graded' => $query->whereRaw(
                'NOT EXISTS (SELECT 1 FROM student_course_enrollments no_grade_enrollment WHERE no_grade_enrollment.course_offering_id = course_offerings.id AND '.$gradedStudentCondition('no_grade_enrollment').')'
            ),
            'partially_graded' => $query->whereRaw($hasGradedStudent)->whereRaw($hasUngradedStudent),
            'completed' => $query->whereExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('student_course_enrollments completed_enrollment')
                    ->whereColumn('completed_enrollment.course_offering_id', 'course_offerings.id');
            })->whereRaw('NOT '.$hasUngradedStudent),
            'has_re_exam' => $query->whereExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('re_exam_results')
                    ->whereColumn('re_exam_results.course_offering_id', 'course_offerings.id');
            }),
            default => null,
        };
    }

    public function show(CourseOffering $courseOffering)
    {
        $courseOffering->load([
            'course',
            'lecturer',
            'department',
            'studentCourseEnrollments.student.studentProfile',
        ]);

        // Deduplicate enrollments by student_user_id
        $enrollments = $courseOffering->studentCourseEnrollments
            ->unique('student_user_id')
            ->values();

        // Load all assessments for this course offering
        $assignments = Assignment::where('course_offering_id', $courseOffering->id)->get();
        $exams = Exam::where('course_offering_id', $courseOffering->id)->get();
        $quizzes = Quiz::where('course_offering_id', $courseOffering->id)->get();

        $assessments = collect($assignments)->concat($exams)->concat($quizzes)->sortBy('created_at');

        // Load all exam results for enrolled students with relationships
        $allResults = ExamResult::whereIn('student_user_id', $enrollments->pluck('student_user_id'))
            ->with(['exam', 'assignment', 'quiz'])
            ->get();

        // Build gradebook and compute totals using critical component logic
        $gradebook = [];
        $students = $enrollments->map(function ($enrollment) use ($assessments, $allResults, &$gradebook, $courseOffering) {
            $student = $enrollment->student;
            $attendanceScore = (float) ($student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);

            // Get student's exam results for this offering
            $studentResults = $allResults->where('student_user_id', $student->id);

            foreach ($assessments as $assessment) {
                $type = ($assessment instanceof Assignment) ? 'assignment' :
                       (($assessment instanceof Quiz) ? 'quiz' : 'exam');
                $scoreRecord = $studentResults->where('assessment_id', $assessment->id)
                    ->where('assessment_type', $type)
                    ->first();
                $score = $scoreRecord ? (float) $scoreRecord->score_obtained : 0;
                $gradebook[$student->id][$type.'_'.$assessment->id] = $score;
            }

            // Use new grading service with critical component + re-exam logic
            $gradeResult = GradingService::calculateFinalGrade(
                $attendanceScore,
                $studentResults,
                $student,
                $courseOffering->id
            );

            $student->temp_total = (float) $gradeResult['total_score'];
            $student->letterGrade = $gradeResult['letter_grade'];
            $student->isPassing = $gradeResult['is_passing'];
            $student->component_status = $gradeResult['component_status'];
            $student->failed_components = $gradeResult['failed_components'];
            $student->needs_re_exam = $gradeResult['needs_re_exam'];
            $student->needs_retake_semester = $gradeResult['needs_retake_semester'];

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
            'department',
            'studentCourseEnrollments.student.studentProfile',
        ]);

        $enrollments = $courseOffering->studentCourseEnrollments
            ->unique('student_user_id')
            ->values();

        // Re-use the same grade computation logic
        $assignments = Assignment::where('course_offering_id', $courseOffering->id)->get();
        $exams = Exam::where('course_offering_id', $courseOffering->id)->get();
        $quizzes = Quiz::where('course_offering_id', $courseOffering->id)->get();
        $assessments = collect($assignments)->concat($exams)->concat($quizzes)->sortBy('created_at');

        $allResults = ExamResult::whereIn('student_user_id', $enrollments->pluck('student_user_id'))
            ->with(['exam', 'assignment', 'quiz'])
            ->get();

        $gradebook = [];
        $students = $enrollments->map(function ($enrollment) use ($assessments, $allResults, &$gradebook, $courseOffering) {
            $student = $enrollment->student;
            $attendanceScore = (float) ($student->getAttendanceScoreByCourse($courseOffering->id) ?? 0);

            $studentResults = $allResults->where('student_user_id', $student->id);

            foreach ($assessments as $assessment) {
                $type = ($assessment instanceof Assignment) ? 'assignment' :
                       (($assessment instanceof Quiz) ? 'quiz' : 'exam');
                $scoreRecord = $studentResults->where('assessment_id', $assessment->id)
                    ->where('assessment_type', $type)
                    ->first();
                $score = $scoreRecord ? (float) $scoreRecord->score_obtained : 0;
                $gradebook[$student->id][$type.'_'.$assessment->id] = $score;
            }

            $gradeResult = GradingService::calculateFinalGrade(
                $attendanceScore,
                $studentResults,
                $student,
                $courseOffering->id
            );

            $student->temp_total = (float) $gradeResult['total_score'];
            $student->letterGrade = $gradeResult['letter_grade'];
            $student->isPassing = $gradeResult['is_passing'];

            return $student;
        });

        $students = $students->sortByDesc('temp_total')->values();

        $fileName = 'Gradebook_'.str_replace([' ', '/', '\\'], '_', $courseOffering->course->title_km).'.xlsx';

        return Excel::download(
            new \App\Exports\ProfessorGradeExcelExport($courseOffering, $students, $assessments, $gradebook),
            $fileName
        );
    }
}
