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
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the faculty that the department belongs to.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the user (head) that leads the department.
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /**
     * Get the programs for the department.
     */
    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the courses for the department.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
