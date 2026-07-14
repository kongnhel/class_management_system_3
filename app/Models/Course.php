<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'program_id', // Added program_id to fillable as courses are linked to programs
        'code',
        'title_km',
        'title_en',
        'credits',
        'description',
        'generation', // 💡 NEW: Add 'generation' to fillable fields
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::created(function (Course $course) {
            $defaultComponents = GradingCategory::DEFAULT_COMPONENTS;
            $categoriesToCreate = [];

            foreach ($defaultComponents as $component) {
                $categoriesToCreate[] = array_merge($component, [
                    'course_id' => $course->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $course->gradingCategories()->insert($categoriesToCreate);
        });

        static::deleting(function (Course $course) {
            $course->courseOfferings()->delete();
        });

        static::restoring(function (Course $course) {
            $course->courseOfferings()->restore();
        });
    }

    /**
     * Get the department that the course belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the program that the course belongs to.
     * This method defines the relationship between a Course and a Program.
     * It assumes that your 'courses' table has a 'program_id' foreign key.
     */
    // public function program()
    // {
    //     // return $this->belongsTo(Program::class);
    //     return $this->belongsToMany(Program::class, 'course_program');

    // }

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'course_program', 'course_id', 'program_id');
    }

    /**
     * Get the course offerings for this course.
     */
    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    /**
     * Get the grading categories for this course.
     */
    public function gradingCategories()
    {
        return $this->hasMany(GradingCategory::class);
    }
}
