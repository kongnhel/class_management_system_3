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
    public function setCurrent(): void
    {
        static::query()
            ->whereKeyNot($this->getKey())
            ->update(['is_current' => false]);

        $this->forceFill(['is_current' => true])->save();
    }

    /**
     * Get the course offerings for this academic year.
     */
    public function courseOfferings()
    {
        return $this->hasMany(CourseOffering::class, 'academic_year', 'name');
    }
}
