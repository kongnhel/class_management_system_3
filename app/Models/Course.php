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
        'code',
        'title_km',
        'title_en',
        'credits',
        'description',
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
