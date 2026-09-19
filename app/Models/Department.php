<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'name_km',
        'name_en',
        'head_user_id',
        'degree_level',
        'duration_years',
        'pathway_department_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /**
     * The pathway department that leads into this department.
     */
    public function pathwayDepartment()
    {
        return $this->belongsTo(Department::class, 'pathway_department_id');
    }

    /**
     * Departments that accept students from this department via pathway.
     */
    public function pathwayDepartments()
    {
        return $this->hasMany(Department::class, 'pathway_department_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }

    public function studentDepartmentEnrollments()
    {
        return $this->hasMany(StudentDepartmentEnrollment::class);
    }
}
