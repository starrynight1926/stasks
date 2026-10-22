<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickNote extends Model
{
    protected $fillable = ['member_id', 'title', 'iso', 'status', 'project', 'priority', 'note', 'subtasks', 'carried_to_id'];

    protected $casts = [
        'iso' => 'date',
        'subtasks' => 'array',
    ];
}
