<?php

namespace App\Services;

use App\Models\ReExamResult;
use App\Models\User;

class GradingService
{
    /**
     * Grading scale thresholds (total score out of 100).
     * Attendance (15) + Assessments (85) = 100 max.
     */
    protected static array $gradeScale = [
        'A' => 85,
        'B+' => 80,
        'B' => 70,
        'C+' => 65,
        'C' => 55,
        'D+' => 50,
        'D' => 45,
        'F' => 0,
    ];

    /**
     * Critical component pass thresholds.
     * Max scores: assignment=20, midterm=15, final=50, attendance=15.
     */
    public const ASSIGNMENT_PASS = 12;
    public const MIDTERM_PASS = 9;
    public const FINAL_PASS = 30;
    public const ATTENDANCE_PASS = 10;

    /**
     * Max scores for each critical component.
     */
    public const ASSIGNMENT_MAX = 20;
    public const MIDTERM_MAX = 15;
    public const FINAL_MAX = 50;
    public const ATTENDANCE_MAX = 15;

    /*
    |--------------------------------------------------------------------------
    | Basic grade helpers (unchanged)
    |--------------------------------------------------------------------------
    */

    /**
     * Get letter grade from total score.
     */
    public static function getLetterGrade(float $totalScore): string
    {
        foreach (static::$gradeScale as $grade => $threshold) {
            if ($totalScore >= $threshold) {
                return $grade;
            }
        }

        return 'F';
    }

    /**
     * Check if a letter grade is passing.
     */
    public static function isPassing(string $grade): bool
    {
        return $grade !== 'F';
    }

    /**
     * Convert a letter grade from this application's single grade scale to GPA points.
     */
    public static function getGradePoints(string $grade): float
    {
        return match ($grade) {
            'A' => 4.0,
            'B+' => 3.5,
            'B' => 3.0,
            'C+' => 2.5,
            'C' => 2.0,
            'D+' => 1.5,
            'D' => 1.0,
            default => 0.0,
        };
    }

    /**
     * Get all grade thresholds (for display/reference).
     */
    public static function getGradeScale(): array
    {
        return static::$gradeScale;
    }

    /*
    |--------------------------------------------------------------------------
    | Critical component logic
    |--------------------------------------------------------------------------
    */

    /**
     * Check if a single component is passing based on its type and score.
     */
    public static function isComponentPassing(string $type, float $score): bool
    {
        return match ($type) {
            'assignment' => $score >= self::ASSIGNMENT_PASS,
            'midterm' => $score >= self::MIDTERM_PASS,
            'final' => $score >= self::FINAL_PASS,
            'attendance' => $score >= self::ATTENDANCE_PASS,
            default => true,
        };
    }

    /**
     * Get the passing threshold for a component type.
     */
    public static function getPassThreshold(string $type): float
    {
        return match ($type) {
            'assignment' => self::ASSIGNMENT_PASS,
            'midterm' => self::MIDTERM_PASS,
            'final' => self::FINAL_PASS,
            'attendance' => self::ATTENDANCE_PASS,
            default => 0,
        };
    }

    /**
     * Get the max score for a component type.
     */
    public static function getMaxScore(string $type): float
    {
        return match ($type) {
            'assignment' => self::ASSIGNMENT_MAX,
            'midterm' => self::MIDTERM_MAX,
            'final' => self::FINAL_MAX,
            'attendance' => self::ATTENDANCE_MAX,
            default => 0,
        };
    }

    /**
     * Determine the display type for an exam result based on the exam's title and max_score.
     * Returns 'midterm', 'final', or 'exam'.
     */
    public static function classifyExamType($exam): string
    {
        $titleEn = strtolower($exam->title_en ?? '');
        $titleKm = strtolower($exam->title_km ?? '');

        if ($titleEn === '' && $titleKm === '' && $exam->max_score <= 15) {
            return 'midterm';
        }

        if (str_contains($titleEn, 'final') || str_contains($titleKm, 'ប្រចាំឆមាស') || str_contains($titleKm, 'ចុងក្រោយ')) {
            return 'final';
        }

        if (str_contains($titleEn, 'midterm') || str_contains($titleKm, 'ពាក់កណ្ដាល់') || str_contains($titleKm, 'ពាក់កណ្តាល់')) {
            return 'midterm';
        }

        return 'exam';
    }

    /**
     * Check all critical components for a student in a course offering.
     *
     * @param float $attendanceScore  Attendance score (0-15)
     * @param array $assessmentScores  Collection/array of ExamResult-like objects with score_obtained, assessment_type, and related assessment
     * @param User|null $student      Student user (for loading re-exam results)
     * @param int|null $courseOfferingId  Course offering ID (for loading re-exam results)
     *
     * @return array{
     *     assignment: array{score: float, passing: bool, has_re_exam: bool, re_exam_score: float|null},
     *     midterm: array{score: float, passing: bool, has_re_exam: bool, re_exam_score: float|null},
     *     final: array{score: float, passing: bool, has_re_exam: bool, re_exam_score: float|null},
     *     attendance: array{score: float, passing: bool},
     *     all_passing: bool,
     *     failed_components: string[],
     *     needs_re_exam: string[],
     *     needs_retake_semester: bool,
     * }
     */
    public static function checkCriticalComponents(
        float $attendanceScore,
        $assessmentScores,
        ?User $student = null,
        ?int $courseOfferingId = null
    ): array {
        // Load re-exam results if student and offering provided
        $reExamMap = [];
        if ($student && $courseOfferingId) {
            $reExamResults = ReExamResult::where('student_user_id', $student->id)
                ->where('course_offering_id', $courseOfferingId)
                ->get()
                ->keyBy('assessment_type');
            foreach ($reExamResults as $type => $reExam) {
                $reExamMap[$type] = $reExam;
            }
        }

        // Aggregate scores by component type
        $totals = ['assignment' => 0.0, 'midterm' => 0.0, 'final' => 0.0];
        foreach ($assessmentScores as $result) {
            $type = $result->assessment_type;
            if ($type === 'quiz') {
                continue; // quizzes are bonus, not critical
            }

            if ($type === 'exam') {
                $exam = $result->exam ?? (method_exists($result, 'getRelation') ? $result->getRelation('exam') : null);
                if ($exam) {
                    $classified = self::classifyExamType($exam);
                    if (isset($totals[$classified])) {
                        $totals[$classified] += $result->score_obtained;
                    } else {
                        $totals['final'] += $result->score_obtained;
                    }
                }
            } elseif (in_array($type, ['midterm', 'final'])) {
                $totals[$type] += $result->score_obtained;
            } elseif ($type === 'assignment') {
                $totals['assignment'] += $result->score_obtained;
            }
        }

        // Check re-exam overrides: use new_score instead of original
        $components = [];
        foreach (['assignment', 'midterm', 'final'] as $type) {
            $originalScore = $totals[$type];
            $hasReExam = isset($reExamMap[$type]);
            $effectiveScore = $hasReExam ? (float) $reExamMap[$type]->new_score : $originalScore;

            $components[$type] = [
                'score' => $effectiveScore,
                'original_score' => $originalScore,
                'passing' => self::isComponentPassing($type, $effectiveScore),
                'has_re_exam' => $hasReExam,
                're_exam_score' => $hasReExam ? (float) $reExamMap[$type]->new_score : null,
            ];
        }

        $components['attendance'] = [
            'score' => $attendanceScore,
            'passing' => self::isComponentPassing('attendance', $attendanceScore),
        ];

        $failedComponents = [];
        $needsReExam = [];
        foreach (['assignment', 'midterm', 'final', 'attendance'] as $type) {
            if (! $components[$type]['passing']) {
                $failedComponents[] = $type;
                if ($type !== 'attendance') {
                    $needsReExam[] = $type;
                }
            }
        }

        return [
            ...$components,
            'all_passing' => empty($failedComponents),
            'failed_components' => $failedComponents,
            'needs_re_exam' => $needsReExam,
            'needs_retake_semester' => in_array('attendance', $failedComponents),
        ];
    }

    /**
     * Calculate the final grade for a student in a course offering,
     * considering critical component rules and re-exam results.
     *
     * @param float $attendanceScore
     * @param $assessmentScores  Collection/array of ExamResult-like objects
     * @param User|null $student
     * @param int|null $courseOfferingId
     *
     * @return array{
     *     letter_grade: string,
     *     is_passing: bool,
     *     total_score: float,
     *     component_status: array,
     *     failed_components: string[],
     *     needs_re_exam: string[],
     *     needs_retake_semester: bool,
     * }
     */
    public static function calculateFinalGrade(
        float $attendanceScore,
        $assessmentScores,
        ?User $student = null,
        ?int $courseOfferingId = null
    ): array {
        $componentStatus = self::checkCriticalComponents(
            $attendanceScore,
            $assessmentScores,
            $student,
            $courseOfferingId
        );

        // Calculate total score using effective (re-exam aware) scores
        $quizBonus = 0.0;
        foreach ($assessmentScores as $result) {
            if ($result->assessment_type === 'quiz') {
                $quizBonus += $result->score_obtained;
            }
        }

        $totalScore = min(
            $componentStatus['attendance']['score']
            + $componentStatus['assignment']['score']
            + $componentStatus['midterm']['score']
            + $componentStatus['final']['score']
            + $quizBonus,
            100
        );

        $letterGrade = self::getLetterGrade($totalScore);

        // Enforce critical component rule: fail ANY critical component = F
        $isPassing = $componentStatus['all_passing'] && self::isPassing($letterGrade);

        return [
            'letter_grade' => $isPassing ? $letterGrade : 'F',
            'is_passing' => $isPassing,
            'total_score' => $totalScore,
            'component_status' => $componentStatus,
            'failed_components' => $componentStatus['failed_components'],
            'needs_re_exam' => $componentStatus['needs_re_exam'],
            'needs_retake_semester' => $componentStatus['needs_retake_semester'],
        ];
    }
}
