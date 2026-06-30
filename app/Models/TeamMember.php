<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeamMember extends Model
{
    protected $guarded = [];

    protected $hidden = ['password'];

    protected $casts = [
        'must_change_password' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function systemRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withPivot('role_in_team')
            ->withTimestamps();
    }

    public function initials(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(collect($parts)->map(fn($p) => $p[0] ?? '')->take(2)->join(''));
    }

    public function hasPermission(string $key): bool
    {
        if (!$this->role_id) return false;
        $this->loadMissing('systemRole.permissions');
        return $this->systemRole?->hasPermission($key) ?? false;
    }
}
