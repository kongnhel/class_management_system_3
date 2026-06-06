<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceQrToken extends Model
{
    use HasFactory;

    protected $table = 'attendance_qr_tokens';

    // ចំណុចសំខាន់៖ ត្រូវតែមានឈ្មោះ Field ទាំងនេះ
    protected $fillable = [
        'course_offering_id',
        'token_code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
