<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceProfessor extends Model
{
    protected $fillable = [
        'professor_id', 'course_offering_id', 'session_id', 'verified_date', 'lat', 'lng', 'verified_at',
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id')->withTrashed();
    }
}
