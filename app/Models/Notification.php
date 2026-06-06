<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // By default, Laravel's notification table uses UUIDs
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', // UUID
        'type',
        'notifiable_type', // Added by morphs
        'notifiable_id',   // Added by morphs
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the parent notifiable model.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
