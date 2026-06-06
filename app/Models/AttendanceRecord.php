<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'student_user_id',
        'user_id',
        'course_offering_id',
        'date',
        'status',
        // 'notes',
        'remarks',

    ];

    protected $casts = [
        'date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the student (user) for the attendance record.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    /**
     * Get the course offering for the attendance record.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }
}
