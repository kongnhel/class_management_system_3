<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_km',
        'name_en',
        'dean_user_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user (dean) that leads the faculty.
     */
    public function dean()
    {
        return $this->belongsTo(User::class, 'dean_user_id');
    }

    /**
     * Get the departments for the faculty.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function programs()
    {
        return $this->hasManyThrough(Program::class, Department::class);
    }
    // public function courses()
    // {
    //     return $this->hasManyThrough(Course::class, Program::class);
    // }
    // public function courseOfferings()
    // {
    //     return $this->hasManyThrough(CourseOffering::class, Course::class);
    // }

}
