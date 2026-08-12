<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_offering_id',
        'schedule_id',
        'professor_id',
        'attendance_date',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'started_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function qrTokens(): HasMany
    {
        return $this->hasMany(AttendanceQrToken::class);
    }
}
