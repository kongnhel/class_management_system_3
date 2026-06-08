<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name_km',
        'name_en',
        'degree_level',
        'duration_years',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function courseOfferings(): BelongsToMany
    {
        return $this->belongsToMany(CourseOffering::class, 'course_offering_program', 'program_id', 'course_offering_id')
            ->withPivot('generation');
    }

    /**
     * Get the department that the program belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function users() // Changed from studentProgramEnrollments to users
    {
        return $this->hasMany(User::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the student program enrollments for this program.
     */
    public function studentProgramEnrollments()
    {
        return $this->hasMany(StudentProgramEnrollment::class);
    }
}
