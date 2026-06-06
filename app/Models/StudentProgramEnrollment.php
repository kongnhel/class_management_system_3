<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProgramEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_user_id',
        'program_id',
        'enrollment_date',
        'graduation_date',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'graduation_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the student (user) that is enrolled in the program.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    /**
     * Get the program that the student is enrolled in.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
