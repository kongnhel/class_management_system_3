<?php

namespace App\Services;

use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentDepartmentEnrollment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StudentProgressionService
{
    /**
     * Base year offset for generation mapping.
     * Generation 16 = joined 2022, so base = 2022 - 16 = 2006.
     */
    private const GENERATION_BASE_YEAR = 2006;

    /**
     * Convert a generation number to the join year.
     */
    public function generationToJoinYear(string|int $generation): int
    {
        return self::GENERATION_BASE_YEAR + (int) $generation;
    }

    /**
     * Convert a join year to the generation number.
     */
    public function joinYearToGeneration(int $joinYear): int
    {
        return $joinYear - self::GENERATION_BASE_YEAR;
    }

    /**
     * Calculate the current year level for a student in a department.
     * Uses starting_year_level from enrollment to support pathway transitions.
     */
    public function getYearLevel(User $student, Department $department): int
    {
        $enrollment = null;
        if ($student->relationLoaded('studentDepartmentEnrollments')) {
            $enrollment = $student->studentDepartmentEnrollments
                ->where('department_id', $department->id)
                ->where('status', 'active')
                ->first();
        } else {
            $enrollment = StudentDepartmentEnrollment::where('student_user_id', $student->id)
                ->where('department_id', $department->id)
                ->where('status', 'active')
                ->first();
        }

        $startingYear = $enrollment?->starting_year_level ?? 1;
        $currentAcademicYear = $this->getCurrentAcademicYearStart();
        $joinYear = $this->generationToJoinYear($student->generation);

        if ($joinYear <= 0 || $currentAcademicYear <= 0) {
            return $startingYear;
        }

        $yearLevel = $startingYear + ($currentAcademicYear - $joinYear);

        return max(1, min($yearLevel, $department->duration_years));
    }

    /**
     * Get the maximum year level for a department.
     */
    public function getMaxYearLevel(Department $department): int
    {
        return $department->duration_years ?? 4;
    }

    /**
     * Check if a student has graduated from a department.
     */
    public function isGraduated(User $student, Department $department): bool
    {
        $enrollment = StudentDepartmentEnrollment::where('student_user_id', $student->id)
            ->where('department_id', $department->id)
            ->first();

        return $enrollment && $enrollment->status === 'graduated';
    }

    /**
     * Check if a student has any F grade in the current year's courses.
     */
    public function hasFailedCourses(User $student, Department $department): bool
    {
        $yearLevel = $this->getYearLevel($student, $department);
        $courseOfferingIds = $this->getYearCourseOfferingIds($student, $department, $yearLevel);

        if ($courseOfferingIds->isEmpty()) {
            return true;
        }

        foreach ($courseOfferingIds as $offeringId) {
            $attendanceScore = $student->getAttendanceScoreByCourse($offeringId);

            $examResults = \App\Models\ExamResult::where('student_user_id', $student->id)
                ->where(function ($q) use ($offeringId) {
                    $q->where(function ($q2) use ($offeringId) {
                        $q2->where('assessment_type', 'exam')
                            ->whereIn('assessment_id', fn ($q3) => $q3->select('id')->from('exams')->where('course_offering_id', $offeringId));
                    })->orWhere(function ($q2) use ($offeringId) {
                        $q2->where('assessment_type', 'quiz')
                            ->whereIn('assessment_id', fn ($q3) => $q3->select('id')->from('quizzes')->where('course_offering_id', $offeringId));
                    })->orWhere(function ($q2) use ($offeringId) {
                        $q2->where('assessment_type', 'assignment')
                            ->whereIn('assessment_id', fn ($q3) => $q3->select('id')->from('assignments')->where('course_offering_id', $offeringId));
                    });
                })->with(['exam', 'assignment', 'quiz'])
                ->get();

            if ($examResults->isEmpty()) {
                return true;
            }

            $gradeResult = GradingService::calculateFinalGrade(
                $attendanceScore,
                $examResults,
                $student,
                $offeringId
            );

            if (! $gradeResult['is_passing']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get students eligible for advancement (no F grades).
     */
    public function getEligibleStudents(Department $department): Collection
    {
        $yearLevel = $this->getYearLevelFromDepartment($department);

        return $this->getStudentsByYearLevel($department, $yearLevel)
            ->filter(fn ($student) => ! $this->hasFailedCourses($student, $department))
            ->values();
    }

    /**
     * Get ALL eligible students across ALL year levels.
     */
    public function getAllEligibleStudents(Department $department): Collection
    {
        return $this->getAllActiveStudents($department)
            ->filter(fn ($student) => ! $this->hasFailedCourses($student, $department))
            ->values();
    }

    /**
     * Get ALL held-back students across ALL year levels.
     */
    public function getAllHeldBackStudents(Department $department): Collection
    {
        return $this->getAllActiveStudents($department)
            ->filter(fn ($student) => $this->hasFailedCourses($student, $department))
            ->values();
    }

    /**
     * Get all active students for a department with year level computed.
     */
    private function getAllActiveStudents(Department $department): Collection
    {
        $students = User::where('role', 'student')
            ->whereHas('studentDepartmentEnrollments', function ($q) use ($department) {
                $q->where('department_id', $department->id)->where('status', 'active');
            })
            ->with(['studentProfile', 'studentDepartmentEnrollments'])
            ->get();

        $students->each(function ($student) use ($department) {
            $student->computed_year_level = $this->getYearLevel($student, $department);
        });

        return $students;
    }

    /**
     * Get students held back (have F grades).
     */
    public function getHeldBackStudents(Department $department): Collection
    {
        $yearLevel = $this->getYearLevelFromDepartment($department);

        return $this->getStudentsByYearLevel($department, $yearLevel)
            ->filter(fn ($student) => $this->hasFailedCourses($student, $department))
            ->values();
    }

    /**
     * Get all students grouped by year level for a department.
     */
    public function getProgressionSummary(Department $department, array $filters = []): array
    {
        $maxYear = $this->getMaxYearLevel($department);
        $summary = [];

        $query = User::where('role', 'student')
            ->whereHas('studentDepartmentEnrollments', function ($q) use ($department) {
                $q->where('department_id', $department->id)->where('status', 'active');
            })
            ->with(['studentProfile', 'studentDepartmentEnrollments']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['generation'])) {
            $query->where('generation', $filters['generation']);
        }

        if (! empty($filters['courseId'])) {
            $enrolledStudentIds = StudentCourseEnrollment::where('course_offering_id', $filters['courseId'])
                ->pluck('student_user_id');
            $query->whereIn('users.id', $enrolledStudentIds);
        }

        if (! empty($filters['semester'])) {
            $offeringIds = CourseOffering::where('department_id', $department->id)
                ->where('semester', $filters['semester'])
                ->pluck('id');
            $enrolledStudentIds = StudentCourseEnrollment::whereIn('course_offering_id', $offeringIds)
                ->pluck('student_user_id');
            $query->whereIn('users.id', $enrolledStudentIds);
        }

        if (! empty($filters['scheduleGroup'])) {
            $days = $filters['scheduleGroup'] === 'mon_fri'
                ? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']
                : ['Saturday', 'Sunday'];
            $offeringIdsWithDay = \App\Models\Schedule::whereIn('day_of_week', $days)
                ->pluck('course_offering_id');
            $enrolledStudentIds = StudentCourseEnrollment::whereIn('course_offering_id', $offeringIdsWithDay)
                ->pluck('student_user_id');
            $query->whereIn('users.id', $enrolledStudentIds);
        }

        $allActiveStudents = $query->get();

        $groupedByYear = $allActiveStudents->groupBy(fn ($student) => $this->getYearLevel($student, $department));

        for ($year = 1; $year <= $maxYear; $year++) {
            $students = $groupedByYear->get($year, collect())->values();
            $summary[$year] = [
                'count' => $students->count(),
                'students' => $students,
            ];
        }

        $graduatedQuery = User::where('role', 'student')
            ->whereHas('studentDepartmentEnrollments', function ($q) use ($department) {
                $q->where('department_id', $department->id)->where('status', 'graduated');
            })
            ->with(['studentProfile', 'studentDepartmentEnrollments']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $graduatedQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('student_id_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $graduated = $graduatedQuery->get();

        $summary['graduated'] = [
            'count' => $graduated->count(),
            'students' => $graduated,
        ];

        return $summary;
    }

    /**
     * Advance selected students to the next year level.
     */
    public function advanceStudents(Collection $studentIds, Department $department): int
    {
        $advanced = 0;

        DB::transaction(function () use ($studentIds, $department, &$advanced) {
            foreach ($studentIds as $studentId) {
                $student = User::find($studentId);
                if (! $student) {
                    continue;
                }

                $currentYear = $this->getYearLevel($student, $department);
                $nextYear = $currentYear + 1;

                $this->completeYearEnrollments($student, $currentYear);

                if ($nextYear <= $this->getMaxYearLevel($department)) {
                    StudentDepartmentEnrollment::where('student_user_id', $student->id)
                        ->where('department_id', $department->id)
                        ->where('status', 'active')
                        ->increment('starting_year_level', 1);

                    $this->enrollInNextYear($student, $department, $nextYear);
                    $advanced++;
                } else {
                    $this->graduateStudent($student, $department);
                    $advanced++;
                }
            }
        });

        return $advanced;
    }

    /**
     * Mark a student's current year course enrollments as completed.
     */
    public function completeYearEnrollments(User $student, int $yearLevel): void
    {
        $offeringIds = $this->getYearCourseOfferingIds($student, $this->getStudentDepartment($student), $yearLevel);

        StudentCourseEnrollment::where('student_user_id', $student->id)
            ->whereIn('course_offering_id', $offeringIds)
            ->update(['status' => 'completed']);
    }

    /**
     * Enroll a student in next year's course offerings.
     */
    public function enrollInNextYear(User $student, Department $department, int $nextYear): int
    {
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $student->id)
            ->pluck('course_offering_id');

        $offerings = CourseOffering::where('department_id', $department->id)
            ->where('generation', $student->generation)
            ->whereNotIn('id', $enrolledOfferingIds)
            ->where('end_date', '>=', now())
            ->get();

        $enrolled = 0;
        foreach ($offerings as $offering) {
            StudentCourseEnrollment::firstOrCreate(
                [
                    'student_user_id' => $student->id,
                    'course_offering_id' => $offering->id,
                ],
                [
                    'student_id' => $student->id,
                    'enrollment_date' => now(),
                    'status' => 'enrolled',
                ]
            );
            $enrolled++;
        }

        return $enrolled;
    }

    /**
     * Graduate a student from a department.
     */
    public function graduateStudent(User $student, Department $department): void
    {
        StudentDepartmentEnrollment::where('student_user_id', $student->id)
            ->where('department_id', $department->id)
            ->update([
                'status' => 'graduated',
                'graduation_date' => Carbon::now()->toDateString(),
            ]);
    }

    /**
     * Check if a student is eligible for transition to another department.
     */
    public function isEligibleForTransition(User $student): bool
    {
        $currentEnrollment = StudentDepartmentEnrollment::where('student_user_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (! $currentEnrollment) {
            return false;
        }

        $department = $currentEnrollment->department;

        if (! $department->pathway_department_id) {
            return false;
        }

        $currentYear = $this->getYearLevel($student, $department);
        $maxYear = $this->getMaxYearLevel($department);

        return $currentYear >= $maxYear;
    }

    /**
     * Get the available departments a student can transition to.
     */
    public function getTransitionDepartments(User $student): Collection
    {
        $currentEnrollment = StudentDepartmentEnrollment::where('student_user_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (! $currentEnrollment) {
            return collect();
        }

        $currentDepartment = $currentEnrollment->department;

        return Department::where('pathway_department_id', $currentDepartment->id)
            ->get();
    }

    /**
     * Transition a student from one department to another (pathway).
     */
    public function transitionToBachelor(User $student, Department $bachelorDepartment): StudentDepartmentEnrollment
    {
        return DB::transaction(function () use ($student, $bachelorDepartment) {
            $student->studentDepartmentEnrollments()
                ->where('status', 'active')
                ->update([
                    'status' => 'graduated',
                    'graduation_date' => Carbon::now()->toDateString(),
                ]);

            $enrollment = StudentDepartmentEnrollment::create([
                'student_user_id' => $student->id,
                'department_id' => $bachelorDepartment->id,
                'starting_year_level' => 3,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            $student->update(['department_id' => $bachelorDepartment->id]);

            $this->enrollInMatchingOfferings($student, $bachelorDepartment);

            return $enrollment;
        });
    }

    /**
     * Auto-enroll a student in course offerings matching their department and generation.
     */
    private function enrollInMatchingOfferings(User $student, Department $department): void
    {
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $student->id)
            ->pluck('course_offering_id');

        $offerings = CourseOffering::where('department_id', $department->id)
            ->where('generation', $student->generation)
            ->whereNotIn('id', $enrolledOfferingIds)
            ->where('end_date', '>=', now())
            ->get();

        foreach ($offerings as $offering) {
            StudentCourseEnrollment::firstOrCreate(
                [
                    'student_user_id' => $student->id,
                    'course_offering_id' => $offering->id,
                ],
                [
                    'student_id' => $student->id,
                    'enrollment_date' => now(),
                    'status' => 'enrolled',
                ]
            );
        }
    }

    /**
     * Auto-graduate all eligible students for a department.
     */
    public function autoGraduateStudents(Department $department): int
    {
        $maxYear = $this->getMaxYearLevel($department);
        $graduated = 0;

        $finalYearStudents = $this->getStudentsByYearLevel($department, $maxYear);

        foreach ($finalYearStudents as $student) {
            if (! $this->hasFailedCourses($student, $department)) {
                $this->completeYearEnrollments($student, $maxYear);
                $this->graduateStudent($student, $department);
                $graduated++;
            }
        }

        return $graduated;
    }

    /**
     * Get course offering IDs for a specific year level.
     */
    private function getYearCourseOfferingIds(User $student, Department $department, int $yearLevel): Collection
    {
        $joinYear = $this->generationToJoinYear($student->generation);
        $targetYear = $joinYear + $yearLevel - 1;
        $academicYear = $targetYear.'-'.($targetYear + 1);

        return CourseOffering::where('department_id', $department->id)
            ->where('generation', $student->generation)
            ->where('academic_year', $academicYear)
            ->pluck('course_offerings.id');
    }

    /**
     * Get students by year level for a department.
     */
    private function getStudentsByYearLevel(Department $department, int $yearLevel): Collection
    {
        return User::where('role', 'student')
            ->whereHas('studentDepartmentEnrollments', function ($q) use ($department) {
                $q->where('department_id', $department->id)->where('status', 'active');
            })
            ->with(['studentProfile', 'studentDepartmentEnrollments'])
            ->get()
            ->filter(fn ($student) => $this->getYearLevel($student, $department) === $yearLevel)
            ->values();
    }

    /**
     * Get the start year of the current academic year.
     */
    private function getCurrentAcademicYearStart(): int
    {
        $current = \App\Models\AcademicYear::getCurrent();
        if ($current && preg_match('/(\d{4})/', $current->name, $m)) {
            return (int) $m[1];
        }

        return (int) date('Y');
    }

    /**
     * Get the year level from department context (uses current user).
     */
    private function getYearLevelFromDepartment(Department $department): int
    {
        $students = User::where('role', 'student')
            ->whereHas('studentDepartmentEnrollments', function ($q) use ($department) {
                $q->where('department_id', $department->id)->where('status', 'active');
            })
            ->with('studentDepartmentEnrollments')
            ->get();

        if ($students->isEmpty()) {
            return 1;
        }

        $grouped = $students->groupBy(fn ($s) => $this->getYearLevel($s, $department));

        return $grouped->sortByDesc(fn ($group) => $group->count())->keys()->first();
    }

    /**
     * Get the student's department enrollment.
     */
    private function getStudentDepartment(User $student): Department
    {
        $enrollment = StudentDepartmentEnrollment::where('student_user_id', $student->id)
            ->where('status', 'active')
            ->first();

        return $enrollment ? $enrollment->department : Department::first();
    }
}
