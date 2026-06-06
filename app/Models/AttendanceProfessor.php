<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceProfessor extends Model
{
    protected $fillable = [
        'professor_id', 'course_offering_id', 'session_id', 'lat', 'lng', 'verified_at',
    ];

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }
}
