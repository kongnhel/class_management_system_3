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
}
