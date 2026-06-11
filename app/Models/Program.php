<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name_km',
        'name_en',
        'degree_level',
        'duration_years',
        'pathway_program_id',
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

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function studentProgramEnrollments(): HasMany
    {
        return $this->hasMany(StudentProgramEnrollment::class);
    }

    /**
     * The associate's program that leads into this bachelor's program.
     */
    public function pathwayProgram(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'pathway_program_id');
    }

    /**
     * Bachelor's programs that accept students from this associate's program.
     */
    public function pathwayPrograms(): HasMany
    {
        return $this->hasMany(Program::class, 'pathway_program_id');
    }
}
