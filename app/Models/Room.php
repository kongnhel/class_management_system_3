<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'room_number',
        'capacity',
        'wifi_qr_code', // ប្តូរមកប្រើ field នេះវិញ
        'location_of_room',
        'type_of_room',
    ];

    /**
     * Get the course offerings for the room.
     * Room can have many course offerings.
     */
    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class);
    }
}
