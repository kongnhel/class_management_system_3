<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'day_of_week',
        'room_id',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime', // Cast to datetime for easier manipulation
        'end_time' => 'datetime',   // Cast to datetime for easier manipulation
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the course offering that the schedule belongs to.
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
