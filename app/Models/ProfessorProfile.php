<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'staff_id', 'full_name_km', 'full_name_en',
        'date_of_birth', 'gender', 'phone_number', 'department_id',
        'position', 'qualifications', 'specializations', 'profile_picture_url',
    ];

    // 💡 NEW: Define the inverse one-to-one relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 💡 NEW: Define the relationship with Department (if you have one)
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
