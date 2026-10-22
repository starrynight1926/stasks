<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('is_admin')) {
            if ($request->expectsJson()) abort(403, 'Chỉ admin được thao tác.');
            return back()->with('error', 'Chỉ admin được thao tác mục này.');
        }
        return $next($request);
    }
}
