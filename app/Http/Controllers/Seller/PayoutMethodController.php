<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerPayoutMethod;
use App\Models\Listing;
use Illuminate\Http\Request;
use App\Http\Requests\SellerPayoutMethodStoreRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Services\Seller\PayoutMethodOps;

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
    public function store(SellerPayoutMethodStoreRequest $request)
    {
        return PayoutMethodOps::store($request);
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
