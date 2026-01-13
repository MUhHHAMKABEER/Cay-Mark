<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostAuctionThread;
use Illuminate\Support\Facades\Auth;

class BuyerMessageController extends Controller
{
    /**
     * Show messaging center - Post-payment messaging threads
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get post-auction messaging threads (only after payment)
        $messagingThreads = PostAuctionThread::with(['seller', 'listing.images', 'invoice'])
            ->where('buyer_id', $user->id)
            ->latest('updated_at')
            ->get();

        return view('buyer.messaging-center', [
            'user' => $user,
            'messagingThreads' => $messagingThreads,
        ]);
    }

}
