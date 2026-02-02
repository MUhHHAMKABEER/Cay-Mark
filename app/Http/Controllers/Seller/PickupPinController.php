<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Invoice;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use App\Http\Requests\SellerPickupPinConfirmRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\Seller\PickupPinOps;

class PickupPinController extends Controller
{
    /**
     * Show pickup PIN confirmation form.
     */
    public function show($listingId)
    {
        $user = Auth::user();
        $listing = Listing::where('id', $listingId)
            ->where('seller_id', $user->id)
            ->with(['invoices.buyer'])
            ->firstOrFail();

        // Get the invoice for this listing
        $invoice = $listing->invoices()->where('payment_status', 'paid')->first();

        if (!$invoice) {
            return back()->with('error', 'No paid invoice found for this listing.');
        }

        return view('Seller.pickup-pin-confirm', compact('listing', 'invoice'));
    }

    /**
     * Confirm pickup with PIN (creates payout record).
     */
    public function confirm(SellerPickupPinConfirmRequest $request, $listingId)
    {
        return PickupPinOps::confirm($request, $listingId);
    }
}
