<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickNote extends Model
{
    protected $fillable = ['member_id', 'title', 'iso', 'status', 'project', 'priority', 'note'];

    protected $casts = [
        'iso' => 'date',
    ];
}
