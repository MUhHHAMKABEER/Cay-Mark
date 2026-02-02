<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;

class WatchlistController extends Controller
{
    // Add or remove a listing from watchlist
    public function toggle(Request $request, $listingId)
    {
        $user = auth()->user();
        $listing = Listing::where('id', $listingId)
            ->orWhere('slug', $listingId)
            ->firstOrFail();

        $wasInWatchlist = $user->watchlist()->where('listing_id', $listing->id)->exists();

        if ($wasInWatchlist) {
            // Remove from watchlist
            $user->watchlist()->detach($listing->id);
            $inWatchlist = false;
            $message = 'Removed from your watchlist.';
        } else {
            $plan = $user->activePlan();
            $limit = $plan === 'standard' ? 50 : 5;
            $watchlistCount = $user->watchlist()->count();

            if ($watchlistCount >= $limit) {
                $message = "You have reached the watchlist limit of $limit items for your plan.";
                if ($request->expectsJson()) {
                    return response()->json([
                        'in_watchlist' => false,
                        'likes_count' => $listing->watchlistedBy()->count(),
                        'message' => $message,
                    ], 422);
                }

                return back()->with('error', $message);
            }

            // Add to watchlist
            $user->watchlist()->attach($listing->id);
            $inWatchlist = true;
            $message = 'Added to your watchlist.';
        }

        $likesCount = $listing->watchlistedBy()->count();

        if ($request->expectsJson()) {
            return response()->json([
                'in_watchlist' => $inWatchlist,
                'likes_count' => $likesCount,
                'message' => $message,
            ]);
        }

        return back()->with('message', $message);
    }

    // Show all watchlist items
    public function index()
    {
        $watchlistItems = auth()->user()->watchlist()->with('images')->get();
        return view('watchlist.index', compact('watchlistItems'));
    }
}

