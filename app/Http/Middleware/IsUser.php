<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow both student and lecturer
        if (!in_array($request->user()?->role, ['student', 'lecturer'])) {
            abort(403, 'Access Denied');
        }

        return $next($request);
    }
}
