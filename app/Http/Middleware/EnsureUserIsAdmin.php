<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Restrict admin routes to authenticated users with role 'admin'.
     * Requires 2FA verification for admin (except 2FA challenge/setup routes).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (strtolower(trim($request->user()->role ?? '')) !== 'admin') {
            abort(403, 'Access denied. Administrator only.');
        }

        // Allow 2FA challenge and setup routes without requiring 2FA verification
        if ($request->routeIs('admin.2fa.*')) {
            return $next($request);
        }

        $user = $request->user();

        // Admin must set up 2FA before accessing admin panel
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }

        // Require 2FA verification this session (same user, reasonable recency)
        $verifiedUserId = $request->session()->get('2fa_verified_user_id');
        $verifiedAt = $request->session()->get('2fa_verified_at');
        $lifetime = (int) config('session.lifetime', 120) * 60; // minutes to seconds

        if ($verifiedUserId !== (int) $user->id || !$verifiedAt || (time() - $verifiedAt > $lifetime)) {
            return redirect()->route('admin.2fa.challenge');
        }

        return $next($request);
    }
}
