<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Restrict admin routes to authenticated users with role 'admin'.
     * 2FA is skipped – admin goes straight to dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        $role = strtolower(trim($user->role ?? ''));
        $adminEmails = config('auth.admin_emails', []);
        $isAdminByEmail = !empty($adminEmails) && in_array(strtolower(trim($user->email ?? '')), array_map('strtolower', $adminEmails), true);
        if ($role !== 'admin' && !$isAdminByEmail) {
            abort(403, 'Access denied. Administrator only.');
        }

        return $next($request);
    }
}
