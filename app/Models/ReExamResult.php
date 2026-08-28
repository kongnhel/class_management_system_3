<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReExamResult extends Model
{
    use HasFactory;

    protected $table = 're_exam_results';

    protected $fillable = [
        'student_user_id',
        'course_offering_id',
        'assessment_type',
        'assessment_id',
        'new_score',
        're_exam_date',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'new_score' => 'decimal:2',
        're_exam_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assessment_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'assessment_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get the related assessment model based on assessment_type.
     */
    public function getAssessment()
    {
        return match ($this->assessment_type) {
            'assignment' => $this->assignment,
            'midterm', 'final' => $this->exam,
            default => null,
        };
    }

    /**
     * Get display type label (Midterm / Final / Assignment).
     */
    public function getDisplayTypeAttribute(): string
    {
        return match ($this->assessment_type) {
            'assignment' => 'Assignment',
            'midterm' => 'Midterm',
            'final' => 'Final',
            default => ucfirst($this->assessment_type),
        };
    }
}
