<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAuctionAccess
{
    /**
     * Handle an incoming request.
     * Restricts auction participation for users in restricted mode (14-day restriction after default).
     * Users can still access other platform functions (browse, view account, contact support, etc.)
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_restricted) {
            // Check if restriction has expired
            if ($user->restriction_ends_at && now()->greaterThan($user->restriction_ends_at)) {
                // Auto-remove expired restriction
                $user->update([
                    'is_restricted' => false,
                    'restriction_ends_at' => null,
                    'restriction_reason' => null,
                ]);
            } else {
                // User is still restricted - block auction actions
                if ($request->isMethod('POST') && (
                    $request->routeIs('buyer.bids.store') ||
                    $request->routeIs('buyer.payment.*') ||
                    $request->routeIs('listing.buy') ||
                    str_contains($request->path(), 'bid') ||
                    str_contains($request->path(), 'checkout')
                )) {
                    return redirect()->back()->withErrors([
                        'restricted' => 'Your account is currently restricted from placing bids or making purchases due to a non-payment default. This restriction will be lifted on ' . $user->restriction_ends_at->format('F d, Y') . '. You can still browse listings, view your account, and contact support.'
                    ]);
                }
            }
        }

        return $next($request);
    }
}
