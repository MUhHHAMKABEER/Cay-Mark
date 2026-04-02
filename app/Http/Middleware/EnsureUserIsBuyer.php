<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsBuyer
{
    /**
     * Restrict buyer routes to authenticated users with role 'buyer'.
     * Sellers and other roles are redirected to their dashboard or home.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $role = strtolower(trim($request->user()->role ?? ''));

        if ($role !== 'buyer') {
            if ($role === 'seller') {
                return redirect()->route('seller.dashboard')->with('error', 'This page is for buyers only.');
            }
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'This page is for buyers only.');
            }
            return redirect()->route('welcome')->with('error', 'You must complete registration as a buyer to access this page.');
        }

        return $next($request);
    }
}
