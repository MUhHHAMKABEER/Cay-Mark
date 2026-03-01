<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Restrict admin routes to authenticated users with role 'admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (strtolower(trim($request->user()->role ?? '')) !== 'admin') {
            abort(403, 'Access denied. Administrator only.');
        }

        return $next($request);
    }
}
