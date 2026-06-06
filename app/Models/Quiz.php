<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'grading_category_id',
        'title_km',
        'title_en',
        'max_score',
        'quiz_date',
    ];

    // Relationship ទៅកាន់ ExamResult
    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'assessment_id')
            ->where('assessment_type', 'quiz');
    }

    // Relationship ទៅកាន់ CourseOffering
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
