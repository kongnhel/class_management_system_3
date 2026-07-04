<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'join_year',
    ];

    protected $casts = [
        'join_year' => 'integer',
    ];

    private const BASE_YEAR = 2006;

    /**
     * Auto-generate join_year from name.
     */
    public static function boot(): void
    {
        parent::boot();

        static::saving(function (Generation $generation) {
            if (empty($generation->join_year) && ! empty($generation->name)) {
                $generation->join_year = (int) $generation->name + self::BASE_YEAR;
            }
        });
    }

    /**
     * Get students in this generation.
     */
    public function students()
    {
        return $this->hasMany(User::class, 'generation', 'name')
            ->where('role', 'student');
    }
}
