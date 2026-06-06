<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'poster_user_id',
        'title_km',
        'title_en',
        'content_km',
        'content_en',
        'is_read',
        'target_role',
        'course_offering_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user (admin/professor) who posted the announcement.
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'poster_user_id');
    }

    /**
     * Get the course offering that the announcement belongs to (if specific).
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function reads()
    {
        return $this->belongsToMany(User::class, 'announcement_reads', 'announcement_id', 'user_id');
    }
}
