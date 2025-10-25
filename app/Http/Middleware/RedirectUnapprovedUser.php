<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectUnapprovedUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // ✅ Check if the user exists, has role 'user' (or 'standard'), and is not approved
        if ($user && in_array($user->role, ['user', 'standard']) && !$user->approved) {

            // ✅ Avoid infinite redirect loops
            if (! $request->routeIs('pending')) {
                return redirect()->route('pending');
            }
        }

        return $next($request);
    }
}
