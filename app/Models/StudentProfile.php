<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'student_id_code',
        'full_name_km',
        'full_name_en',
        'date_of_birth',
        'gender',
        'phone_number',
        'address',
        'profile_picture_url',
        'generation', // 💡 NEW: Add 'generation' to fillable fields
        // Add any other fillable fields here
    ];

    // 💡 NEW: Define the inverse one-to-one relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
