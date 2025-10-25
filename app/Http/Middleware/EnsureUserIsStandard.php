<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsStandard
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'standard') {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
