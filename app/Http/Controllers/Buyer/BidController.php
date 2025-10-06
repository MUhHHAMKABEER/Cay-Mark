<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
public function bids()
{
    $user = Auth::user();

    $userBids = $user->bids; // you can use this later if needed
    $buyNowItems = $user->buyNows()->with('listing')->get(); // collection of Buy Now items with listing

    return view('Buyer.bids', compact('userBids', 'buyNowItems'));
}



    // Show Watchlist page
    public function watchlist()
    {
        // Fetch user's watchlist from DB (example)
        $watchlistItems = auth()->user()->watchlist ?? [];
        return view('Buyer.watchlist', compact('watchlistItems'));
    }
}
