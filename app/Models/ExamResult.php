<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = ['assessment_id', 'assessment_type', 'student_user_id', 'score_obtained', 'notes', 'recorded_at'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    /**
     * Get the exam that the result belongs to.
     */
    public function exam()
    {
        // return $this->belongsTo(Exam::class);
        return $this->belongsTo(Exam::class, 'assessment_id');
    }

    /**
     * Get the student (user) for the exam result.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    // public function assessment()
    // {
    //     return $this->morphTo(null, 'assessment_type', 'assessment_id');
    // }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assessment_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'assessment_id');
    }

    public function getCourseOfferingIdAttribute()
    {
        return match($this->assessment_type) {
            'assignment' => $this->assignment?->course_offering_id,
            'exam' => $this->exam?->course_offering_id,
            'quiz' => $this->quiz?->course_offering_id,
            default => null,
        };
    }

    public function getDisplayTypeAttribute()
    {
        if ($this->assessment_type === 'assignment') return 'Assignment';
        if ($this->assessment_type === 'quiz') return 'Quiz';
        $title = match($this->assessment_type) {
            'exam' => $this->exam?->title_en ?? '',
            default => '',
        };
        if (str_contains($title, 'Final')) return 'Final';
        if (str_contains($title, 'Midterm')) return 'Midterm';
        return 'Exam';
    }

    public function getCourseIdAttribute()
    {
        return match($this->assessment_type) {
            'assignment' => $this->assignment?->courseOffering?->course_id,
            'exam' => $this->exam?->courseOffering?->course_id,
            'quiz' => $this->quiz?->courseOffering?->course_id,
            default => null,
        };
    }

    public function getCreditsAttribute()
    {
        return match($this->assessment_type) {
            'assignment' => $this->assignment?->courseOffering?->course?->credits ?? 3,
            'exam' => $this->exam?->courseOffering?->course?->credits ?? 3,
            'quiz' => $this->quiz?->courseOffering?->course?->credits ?? 3,
            default => 3,
        };
    }

    public function getMaxScoreAttribute()
    {
        return match($this->assessment_type) {
            'assignment' => $this->assignment?->max_score ?? 0,
            'exam' => $this->exam?->max_score ?? 0,
            'quiz' => $this->quiz?->max_score ?? 0,
            default => 0,
        };
    }
}
