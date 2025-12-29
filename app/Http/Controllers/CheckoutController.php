<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Chat; // ✅ Import Chat model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // public function buyNow($listingId)
    // {
    //     try {
    //         $listing = Listing::findOrFail($listingId);

    //         // Update listing state
    //         $listing->listing_state = 'sold';
    //         $listing->save();

    //         // Create chat between buyer and seller
    //         $chat = Chat::firstOrCreate(
    //             [
    //                 'listing_id' => $listing->id,
    //                 'buyer_id'   => auth()->id(),
    //                 'seller_id'  => $listing->seller_id,
    //             ]
    //         );

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Listing bought & chat opened successfully.',
    //             'chat_id' => $chat->id,
    //         ]);

    //     } catch (\Exception $e) {
    //         Log::error("BuyNow failed: " . $e->getMessage());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong while processing purchase.',
    //         ]);
    //     }
    // }
    public function buyNow($listingId)
{
    try {
        $user = auth()->user();
        
        // SELLER RESTRICTION: Sellers cannot buy (per PDF requirements)
        if ($user->role === 'seller') {
            return response()->json([
                'success' => false,
                'message' => 'Sellers are not allowed to purchase items.',
            ], 403);
        }
        
        // BUYER MEMBERSHIP REQUIRED
        if ($user->role !== 'buyer') {
            return response()->json([
                'success' => false,
                'message' => 'Buyer membership required to use Buy Now.',
            ], 403);
        }
        
        $listing = Listing::findOrFail($listingId);

        // Update listing state
        $listing->listing_state = 'sold';
        $listing->save();

        // Create a record in buy_nows table
        $buyNow = \App\Models\BuyNow::create([
            'user_id'    => auth()->id(),
            'listing_id' => $listing->id,
        ]);

        // Create chat between buyer and seller
        $chat = Chat::firstOrCreate(
            [
                'listing_id' => $listing->id,
                'buyer_id'   => auth()->id(),
                'seller_id'  => $listing->seller_id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Listing bought, chat opened, and buyNow recorded successfully.',
            'chat_id' => $chat->id,
        ]);

    } catch (\Exception $e) {
        Log::error("BuyNow failed: " . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong while processing purchase.',
        ]);
    }
}

}
