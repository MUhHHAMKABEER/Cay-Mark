<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerPayoutMethod;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PayoutMethodController extends Controller
{
    /**
     * Show payout method setup form.
     */
    public function create()
    {
        $user = Auth::user();
        $payoutMethod = SellerPayoutMethod::where('user_id', $user->id)->first();

        return view('Seller.payout-method-setup', compact('payoutMethod'));
    }

    /**
     * Store or update payout method.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'routing_number' => 'nullable|string|max:50',
            'swift_number' => 'nullable|string|max:50',
            'additional_instructions' => 'nullable|string|max:1000',
        ]);

        // Check if seller has active listings (cannot edit if locked)
        $hasActiveListings = Listing::sellerHasActiveListings($user->id);
        $existingMethod = SellerPayoutMethod::where('user_id', $user->id)->first();

        if ($existingMethod && $existingMethod->is_locked && $hasActiveListings) {
            return back()->with('error', 'Cannot edit payout method while you have active listings. Please wait until all listings are completed.');
        }

        // Create or update payout method
        $payoutMethod = SellerPayoutMethod::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'routing_number' => $request->routing_number,
                'swift_number' => $request->swift_number,
                'additional_instructions' => $request->additional_instructions,
                'is_active' => true,
                'is_verified' => false, // Admin verification required
            ]
        );

        // Lock if seller has active listings
        if ($hasActiveListings) {
            $payoutMethod->lock();
        }

        return redirect()->route('seller.payout-method')
            ->with('success', 'Payout method saved successfully.');
    }

    /**
     * Show seller payout history.
     */
    public function payoutHistory()
    {
        $user = Auth::user();
        $payouts = \App\Models\Payout::where('seller_id', $user->id)
            ->with(['listing', 'invoice.buyer'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('Seller.payout-history', compact('payouts'));
    }

    /**
     * Check if seller has payout method (for listing creation validation).
     */
    public static function sellerHasPayoutMethod($userId): bool
    {
        return SellerPayoutMethod::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }
}
