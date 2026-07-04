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
        'degree_level',
        'starting_year_level',
        'is_transition_eligible',
        'enrollment_date',
        'graduation_date',
        'status',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'graduation_date' => 'date',
        'is_transition_eligible' => 'boolean',
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

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
