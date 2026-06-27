<?php

namespace App\Providers;

use App\Support\Auth as AppAuth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Custom Blade directive to check permission from our simple session-based auth.
        // Usage: @hasPerm('task.create') ... @endhasPerm
        Blade::if('hasPerm', function (string $key) {
            return AppAuth::can($key);
        });
    }
}
