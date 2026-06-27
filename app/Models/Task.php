<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
            'archived_at' => 'datetime',
            'done_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function lifecycleState(): string
    {
        if ($this->status === 'cancelled') return 'cancelled';
        if ($this->status === 'done') {
            if ($this->due_date && $this->done_at && $this->done_at->gt($this->due_date->endOfDay())) {
                return 'done_late';
            }
            return 'done';
        }
        if ($this->status === 'in_progress') return 'in_progress';
        return 'todo';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'assignee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'dependency_id');
    }

    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'dependency_id', 'task_id');
    }
}
