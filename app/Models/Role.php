<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function permissionKeys(): array
    {
        return $this->permissions->pluck('key')->all();
    }

    public function hasPermission(string $key): bool
    {
        return $this->permissions->contains('key', $key);
    }
}
