<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'grading_category_id',
        'title_km',
        'title_en',
        'description',
        'due_date',
        'max_score',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the course offering that the assignment belongs to.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    /**
     * Get the grading category that the assignment belongs to.
     */
    // public function gradingCategory()
    // {
    //     return $this->belongsTo(GradingCategory::class);
    // }

    /**
     * Get the submissions for this assignment.
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'assessment_id')
            ->where('assessment_type', 'assignment');
    }
}
