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
        $listing = Listing::findOrFail($listingId);

        if ($user->watchlist()->where('listing_id', $listingId)->exists()) {
            // Remove from watchlist
            $user->watchlist()->detach($listingId);
            return back()->with('message', 'Removed from your watchlist.');
        }

        // Add to watchlist
        $user->watchlist()->attach($listingId);
        return back()->with('message', 'Added to your watchlist.');
    }

    // Show all watchlist items
    public function index()
    {
        $watchlistItems = auth()->user()->watchlist()->with('images')->get();
        return view('watchlist.index', compact('watchlistItems'));
    }
}

