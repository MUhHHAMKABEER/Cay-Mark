<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Listing;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyNowController extends Controller
{
    /**
     * Process a Buy Now purchase.
     *
     * Guards (in order):
     *   1. Must be authenticated (route middleware)
     *   2. Sellers may not purchase
     *   3. Must be a fully registered buyer (role = 'buyer')
     *   4. Listing must have a buy_now_price
     *   5. Auction must not have ended
     *   6. Listing must not already be sold / invoiced
     *
     * On success:
     *   - Creates a Bid record at the buy-now price
     *   - Ends the auction immediately (auction_end_time = now)
     *   - Generates invoice via InvoiceService (same path as auction win)
     *   - Marks listing as 'sold'
     *   - Notifies seller
     *   - Returns JSON redirect URL → buyer payment checkout
     */
    public function process(Request $request, Listing $listing)
    {
        $user = Auth::user();

        // ── Guard: seller cannot buy ──────────────────────────────────────
        if ($user->role === 'seller') {
            return response()->json([
                'error' => 'Sellers cannot purchase listings on CayMark.',
            ], 403);
        }

        // ── Guard: must be a fully registered buyer ───────────────────────
        if ($user->role !== 'buyer') {
            return response()->json([
                'error' => 'Please complete your registration to use Buy Now.',
            ], 403);
        }

        // ── Guard: listing must have a buy_now_price ──────────────────────
        $buyNowPrice = (float) ($listing->buy_now_price ?? 0);
        if ($buyNowPrice <= 0) {
            return response()->json([
                'error' => 'This listing does not have a Buy Now price.',
            ], 422);
        }

        // ── Guard: auction must still be active ───────────────────────────
        $endDate = $listing->getAuctionEndDate();
        if ($endDate && $endDate->isPast()) {
            return response()->json([
                'error' => 'This auction has ended.',
            ], 422);
        }

        // ── Guard: not already sold or invoiced ───────────────────────────
        if ($listing->status === 'sold') {
            return response()->json([
                'error' => 'This listing has already been sold.',
            ], 422);
        }

        if ($listing->invoices()->exists()) {
            return response()->json([
                'error' => 'This listing has already been sold.',
            ], 422);
        }

        // ── Process ───────────────────────────────────────────────────────
        DB::beginTransaction();
        try {
            // 1. Create a bid at the buy-now price (invoice service needs a Bid)
            $bid = Bid::create([
                'listing_id' => $listing->id,
                'user_id'    => $user->id,
                'amount'     => $buyNowPrice,
                'status'     => 'active',
            ]);

            // 2. End the auction immediately
            $listing->auction_end_time = now();
            $listing->save();

            // 3. Generate invoice — identical to auction-win flow
            $invoice = (new InvoiceService())->generateInvoiceForAuctionWin($listing, $bid);

            // 4. Mark listing sold
            $listing->status = 'sold';
            $listing->save();

            // 5. Notify seller
            try {
                $listing->loadMissing('seller');
                if ($listing->seller) {
                    (new NotificationService())->auctionSold(
                        $listing->seller,
                        $listing,
                        $buyNowPrice
                    );
                }
            } catch (\Exception $e) {
                Log::warning('[BuyNow] Seller notification failed', [
                    'listing_id' => $listing->id,
                    'error'      => $e->getMessage(),
                ]);
            }

            DB::commit();

            Log::info('[BuyNow] Purchase completed', [
                'listing_id' => $listing->id,
                'buyer_id'   => $user->id,
                'amount'     => $buyNowPrice,
                'invoice_id' => $invoice->id,
            ]);

            return response()->json([
                'success'    => true,
                'redirect'   => route('buyer.payment.checkout-single', $invoice->id),
                'invoice_id' => $invoice->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[BuyNow] Purchase failed', [
                'listing_id' => $listing->id,
                'buyer_id'   => $user->id,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Unable to complete purchase. Please try again.',
            ], 500);
        }
    }
}
