<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuizResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_question_id',
        'student_user_id',
        'selected_option_id',
        'short_answer_text',
        'submitted_at',
        'is_correct',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'is_correct' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the quiz question for the response.
     */
    public function quizQuestion()
    {
        return $this->belongsTo(QuizQuestion::class);
    }

    /**
     * Get the student (user) who submitted the response.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    /**
     * Get the selected option for the response (if multiple choice).
     */
    public function selectedOption()
    {
        return $this->belongsTo(QuizOption::class, 'selected_option_id');
    }
}
