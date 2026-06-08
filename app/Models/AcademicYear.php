<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    /**
     * Get the current academic year
     */
    public static function getCurrent()
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Set this as the current academic year (unset others)
     */
    public function setCurrent()
    {
        static::query()->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }
}
