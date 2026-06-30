<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $guarded = [];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
