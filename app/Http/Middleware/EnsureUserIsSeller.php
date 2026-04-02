<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
    /**
     * Restrict seller routes to authenticated users with role 'seller'.
     * Buyers and other roles are redirected to their dashboard or home.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $role = strtolower(trim($request->user()->role ?? ''));

        if ($role !== 'seller') {
            if ($role === 'buyer') {
                return redirect()->route('buyer.dashboard')->with('error', 'This page is for sellers only.');
            }
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('welcome')->with('error', 'You must complete registration as a seller to access this page.');
        }

        return $next($request);
    }
}
