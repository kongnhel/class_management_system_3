<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_user_id',
        'submission_date',
        'file_path',
        'grade_received',
        'feedback',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the assignment that the submission belongs to.
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student (user) that made the submission.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
