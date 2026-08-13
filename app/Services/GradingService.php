<?php

namespace App\Services;

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

    /**
     * Check if a student has failed any non-quiz assessment (score < 50% of max_score).
     * Quizzes are treated as bonus and excluded from this check.
     */
    public static function hasFailedAnyAssessment(array $gradebookRow, iterable $assessments): bool
    {
        foreach ($assessments as $assessment) {
            $type = ($assessment instanceof \App\Models\Assignment) ? 'assignment' :
                   (($assessment instanceof \App\Models\Quiz) ? 'quiz' : 'exam');

            if ($type === 'quiz') {
                continue;
            }

            $key = $type.'_'.$assessment->id;
            $score = $gradebookRow[$key] ?? 0;
            $maxScore = (float) ($assessment->max_score ?? 100);

            if ($maxScore > 0 && ($score / $maxScore) < 0.5) {
                return true;
            }
        }

        return false;
    }
}
