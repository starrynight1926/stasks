<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotMemberLevel
{
    public function handle(Request $request, Closure $next)
    {
        if (session('is_admin')) return $next($request);
        if (session('level') === 'member') {
            if ($request->expectsJson()) abort(403, 'Bạn không có quyền thao tác.');
            return back()->with('error', 'Bạn không có quyền thao tác chức năng này.');
        }
        return $next($request);
    }
}
