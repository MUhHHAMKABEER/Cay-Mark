<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellerPayoutMethodStoreRequest;
use App\Models\SellerPayoutMethod;
use App\Services\Seller\PayoutMethodOps;
use Illuminate\Support\Facades\Auth;

class PayoutMethodController extends Controller
{
    /**
     * Show payout method setup form.
     */
    public function create()
    {
        return redirect()
            ->route('seller.account')
            ->with('open_payout_modal', true);
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
