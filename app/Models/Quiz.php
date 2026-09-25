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
        'description_km',
        'description_en',
        'start_time',
        'end_time',
        'is_published',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

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
