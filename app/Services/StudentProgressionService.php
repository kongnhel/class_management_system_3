<?php

namespace App\Services;

use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentProgramEnrollment;
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
     * Calculate the current year level for a student in a program.
     * Uses starting_year_level from enrollment to support pathway transitions.
     * Year level = starting_year_level + (current academic year start - generation join year)
     */
    public function getYearLevel(User $student, Program $program): int
    {
        $enrollment = StudentProgramEnrollment::where('student_user_id', $student->id)
            ->where('program_id', $program->id)
            ->where('status', 'active')
            ->first();

        $startingYear = $enrollment?->starting_year_level ?? 1;
        $currentAcademicYear = $this->getCurrentAcademicYearStart();
        $joinYear = $this->generationToJoinYear($student->generation);

        if ($joinYear <= 0 || $currentAcademicYear <= 0) {
            return $startingYear;
        }

        $yearLevel = $startingYear + ($currentAcademicYear - $joinYear);

        return max(1, min($yearLevel, $program->duration_years));
    }

    /**
     * Get the maximum year level for a program.
     */
    public function getMaxYearLevel(Program $program): int
    {
        return $program->duration_years ?? 4;
    }

    /**
     * Check if a student has graduated from a program.
     */
    public function isGraduated(User $student, Program $program): bool
    {
        $enrollment = StudentProgramEnrollment::where('student_user_id', $student->id)
            ->where('program_id', $program->id)
            ->first();

        return $enrollment && $enrollment->status === 'graduated';
    }

    /**
     * Check if a student has any F grade in the current year's courses.
     */
    public function hasFailedCourses(User $student, Program $program): bool
    {
        $yearLevel = $this->getYearLevel($student, $program);
        $courseOfferingIds = $this->getYearCourseOfferingIds($student, $program, $yearLevel);

        if ($courseOfferingIds->isEmpty()) {
            return false;
        }

        // Check exam results for F grades
        $hasF = false;

        foreach ($courseOfferingIds as $offeringId) {
            $attendanceScore = $student->getAttendanceScoreByCourse($offeringId);

            $examResults = \App\Models\ExamResult::where('student_user_id', $student->id)
                ->whereIn('assessment_id', function ($q) use ($offeringId) {
                    $q->select('id')->from('assignments')->where('course_offering_id', $offeringId)
                        ->union(DB::table('quizzes')->select('id')->where('course_offering_id', $offeringId))
                        ->union(DB::table('exams')->select('id')->where('course_offering_id', $offeringId));
                })->get();

            $finalExamScore = 0;
            $midtermScore = 0;
            $assignmentScore = 0;
            $totalScore = 0;

            foreach ($examResults as $result) {
                $assessment = match ($result->assessment_type) {
                    'assignment' => \App\Models\Assignment::find($result->assessment_id),
                    'quiz' => \App\Models\Quiz::find($result->assessment_id),
                    default => \App\Models\Exam::find($result->assessment_id),
                };

                if (! $assessment) {
                    continue;
                }

                $maxScore = (float) $assessment->max_score;
                $displayType = match (true) {
                    $result->assessment_type === 'exam' && $maxScore == 15 => 'midterm',
                    $result->assessment_type === 'exam' => 'final',
                    default => $result->assessment_type,
                };

                $totalScore += $result->score_obtained;

                if ($displayType === 'final') {
                    $finalExamScore += $result->score_obtained;
                } elseif ($displayType === 'midterm') {
                    $midtermScore += $result->score_obtained;
                } elseif ($displayType === 'assignment') {
                    $assignmentScore += $result->score_obtained;
                }
            }

            $courseTotal = $totalScore + $attendanceScore;
            $isFailed = ($finalExamScore < 24 || $midtermScore < 9 || $assignmentScore < 9 || $attendanceScore < 9 || $courseTotal < 50);

            if ($isFailed) {
                $hasF = true;
                break;
            }
        }

        return $hasF;
    }

    /**
     * Get students eligible for advancement (no F grades).
     */
    public function getEligibleStudents(Program $program): Collection
    {
        $yearLevel = $this->getYearLevelFromProgram($program);

        return $this->getStudentsByYearLevel($program, $yearLevel)
            ->filter(fn ($student) => ! $this->hasFailedCourses($student, $program))
            ->values();
    }

    /**
     * Get students held back (have F grades).
     */
    public function getHeldBackStudents(Program $program): Collection
    {
        $yearLevel = $this->getYearLevelFromProgram($program);

        return $this->getStudentsByYearLevel($program, $yearLevel)
            ->filter(fn ($student) => $this->hasFailedCourses($student, $program))
            ->values();
    }

    /**
     * Get all students grouped by year level for a program.
     */
    public function getProgressionSummary(Program $program): array
    {
        $maxYear = $this->getMaxYearLevel($program);
        $summary = [];

        for ($year = 1; $year <= $maxYear; $year++) {
            $students = $this->getStudentsByYearLevel($program, $year);
            $summary[$year] = [
                'count' => $students->count(),
                'students' => $students,
            ];
        }

        // Graduated students
        $graduated = User::where('role', 'student')
            ->whereHas('studentProgramEnrollments', function ($q) use ($program) {
                $q->where('program_id', $program->id)->where('status', 'graduated');
            })->get();

        $summary['graduated'] = [
            'count' => $graduated->count(),
            'students' => $graduated,
        ];

        return $summary;
    }

    /**
     * Advance selected students to the next year level.
     * Returns the number of students advanced.
     */
    public function advanceStudents(Collection $studentIds, Program $program): int
    {
        $advanced = 0;

        DB::transaction(function () use ($studentIds, $program, &$advanced) {
            foreach ($studentIds as $studentId) {
                $student = User::find($studentId);
                if (! $student) {
                    continue;
                }

                $currentYear = $this->getYearLevel($student, $program);
                $nextYear = $currentYear + 1;

                // Mark current year's course enrollments as completed
                $this->completeYearEnrollments($student, $currentYear);

                // If next year is within program duration, enroll in next year's offerings
                if ($nextYear <= $this->getMaxYearLevel($program)) {
                    $this->enrollInNextYear($student, $program, $nextYear);
                    $advanced++;
                } else {
                    // Student has completed all years — graduate them
                    $this->graduateStudent($student, $program);
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
        $offeringIds = $this->getYearCourseOfferingIds($student, $this->getStudentProgram($student), $yearLevel);

        StudentCourseEnrollment::where('student_user_id', $student->id)
            ->whereIn('course_offering_id', $offeringIds)
            ->update(['status' => 'completed']);
    }

    /**
     * Enroll a student in next year's course offerings.
     */
    public function enrollInNextYear(User $student, Program $program, int $nextYear): int
    {
        // Find course offerings for this program targeting this generation and next year
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $student->id)
            ->pluck('course_offering_id');

        // Get offerings that match: program, generation, and not already enrolled
        $offerings = CourseOffering::whereHas('targetPrograms', function ($q) use ($program, $student) {
            $q->where('course_offering_program.program_id', $program->id)
                ->where('course_offering_program.generation', $student->generation);
        })
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
     * Graduate a student from a program.
     */
    public function graduateStudent(User $student, Program $program): void
    {
        StudentProgramEnrollment::where('student_user_id', $student->id)
            ->where('program_id', $program->id)
            ->update([
                'status' => 'graduated',
                'graduation_date' => Carbon::now()->toDateString(),
            ]);
    }

    /**
     * Check if a student is eligible for transition to a bachelor's program.
     * Must have completed all years of their associate's program.
     */
    public function isEligibleForTransition(User $student): bool
    {
        $currentEnrollment = StudentProgramEnrollment::where('student_user_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (! $currentEnrollment) {
            return false;
        }

        $program = $currentEnrollment->program;

        // Must have a pathway program configured
        if (! $program->pathway_program_id) {
            return false;
        }

        // Must be in the final year of the current program
        $currentYear = $this->getYearLevel($student, $program);
        $maxYear = $this->getMaxYearLevel($program);

        return $currentYear >= $maxYear;
    }

    /**
     * Get the available bachelor's programs a student can transition to.
     */
    public function getTransitionPrograms(User $student): \Illuminate\Support\Collection
    {
        $currentEnrollment = StudentProgramEnrollment::where('student_user_id', $student->id)
            ->where('status', 'active')
            ->first();

        if (! $currentEnrollment) {
            return collect();
        }

        $currentProgram = $currentEnrollment->program;

        return Program::where('pathway_program_id', $currentProgram->id)
            ->get();
    }

    /**
     * Transition a student from an associate's program to a bachelor's program.
     * Starts the student at Year 3 in the bachelor's program.
     */
    public function transitionToBachelor(User $student, Program $bachelorProgram): StudentProgramEnrollment
    {
        return DB::transaction(function () use ($student, $bachelorProgram) {
            // 1. Mark current associate's enrollment as graduated
            $student->studentProgramEnrollments()
                ->where('status', 'active')
                ->update([
                    'status' => 'graduated',
                    'graduation_date' => Carbon::now()->toDateString(),
                ]);

            // 2. Create new bachelor's enrollment starting at Year 3
            $enrollment = StudentProgramEnrollment::create([
                'student_user_id' => $student->id,
                'program_id' => $bachelorProgram->id,
                'starting_year_level' => 3,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            // 3. Update student's program on the users table
            $student->update(['program_id' => $bachelorProgram->id]);

            // 4. Auto-enroll in matching course offerings for Year 3
            $this->enrollInMatchingOfferings($student, $bachelorProgram);

            return $enrollment;
        });
    }

    /**
     * Auto-enroll a student in course offerings matching their program and generation.
     */
    private function enrollInMatchingOfferings(User $student, Program $program): void
    {
        $enrolledOfferingIds = StudentCourseEnrollment::where('student_user_id', $student->id)
            ->pluck('course_offering_id');

        $offerings = CourseOffering::whereHas('targetPrograms', function ($q) use ($program, $student) {
            $q->where('course_offering_program.program_id', $program->id)
                ->where('course_offering_program.generation', $student->generation);
        })
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
     * Auto-graduate all eligible students for a program.
     * Students who have completed all years and have no F grades.
     */
    public function autoGraduateStudents(Program $program): int
    {
        $maxYear = $this->getMaxYearLevel($program);
        $graduated = 0;

        // Find students in the final year
        $finalYearStudents = $this->getStudentsByYearLevel($program, $maxYear);

        foreach ($finalYearStudents as $student) {
            if (! $this->hasFailedCourses($student, $program)) {
                $this->completeYearEnrollments($student, $maxYear);
                $this->graduateStudent($student, $program);
                $graduated++;
            }
        }

        return $graduated;
    }

    /**
     * Get course offering IDs for a specific year level.
     * Year is determined by matching the academic year to the generation.
     */
    private function getYearCourseOfferingIds(User $student, Program $program, int $yearLevel): Collection
    {
        $joinYear = $this->generationToJoinYear($student->generation);
        $targetYear = $joinYear + $yearLevel - 1;
        $academicYear = $targetYear.'-'.($targetYear + 1);

        return CourseOffering::whereHas('targetPrograms', function ($q) use ($program, $student) {
            $q->where('course_offering_program.program_id', $program->id)
                ->where('course_offering_program.generation', $student->generation);
        })
            ->where('academic_year', $academicYear)
            ->pluck('course_offerings.id');
    }

    /**
     * Get students by year level for a program.
     * Computes each student's actual year level and filters.
     */
    private function getStudentsByYearLevel(Program $program, int $yearLevel): Collection
    {
        return User::where('role', 'student')
            ->whereHas('studentProgramEnrollments', function ($q) use ($program) {
                $q->where('program_id', $program->id)->where('status', 'active');
            })
            ->with('studentProfile')
            ->get()
            ->filter(fn ($student) => $this->getYearLevel($student, $program) === $yearLevel)
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
     * Get the year level from program context (uses current user).
     */
    private function getYearLevelFromProgram(Program $program): int
    {
        // Get all active students for this program
        $students = User::where('role', 'student')
            ->whereHas('studentProgramEnrollments', function ($q) use ($program) {
                $q->where('program_id', $program->id)->where('status', 'active');
            })
            ->get();

        if ($students->isEmpty()) {
            return 1;
        }

        // Group by calculated year level
        $grouped = $students->groupBy(fn ($s) => $this->getYearLevel($s, $program));

        // Return the year level with the most students
        return $grouped->sortByDesc(fn ($group) => $group->count())->keys()->first();
    }

    /**
     * Get the student's program enrollment.
     */
    private function getStudentProgram(User $student): Program
    {
        $enrollment = StudentProgramEnrollment::where('student_user_id', $student->id)
            ->where('status', 'active')
            ->first();

        return $enrollment ? $enrollment->program : Program::first();
    }
}
