<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'description',
        'group',
    ];

    /**
     * Get roles that have this permission
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Check if a role has this permission
     */
    public static function hasPermission($role, $permission)
    {
        return static::where('name', $permission)
            ->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            })
            ->exists();
    }
}
