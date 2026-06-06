<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'title_km',
        'title_en',
        'exam_date',
        'duration_minutes',
        'max_score',
    ];

    protected $casts = [
        'exam_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the course offering that the exam belongs to.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the exam results for this exam.
     */
    // ក្នុង file app/Models/Exam.php

    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'assessment_id');
    }

    // In Exam.php
    public function grade()
    {
        return $this->hasOne(ExamResult::class, 'assessment_id')
            ->where('assessment_type', 'exam');
    }
}
