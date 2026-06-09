<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name_km',
        'full_name_en',
        'gender',
        'date_of_birth',
        'phone_number',
        'address',
        'telegram_user',
        'telegram_chat_id',
        'profile_picture_url',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that owns the user profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
