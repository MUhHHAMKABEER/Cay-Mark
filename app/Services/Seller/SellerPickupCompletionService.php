<?php

namespace App\Services\Seller;

use App\Models\Invoice;
use App\Models\Listing;
use App\Models\MessagingThreadEvent;
use App\Models\Payout;
use App\Models\PostAuctionThread;
use App\Models\User;
use App\Notifications\GenericNotification;
use App\Services\NotificationService;
use App\Services\PayoutService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Single source of truth for the post-pickup completion flow.
 *
 * The seller submits the buyer's 6-digit PIN; on success we:
 *  - Mark the listing's pickup confirmed and clear the PIN (single-use).
 *  - Sync the matching PostAuctionThread (immediate close, no further messaging).
 *  - Generate the payout (idempotent).
 *  - Record a thread event so the audit trail is consistent.
 *  - Notify seller, buyer, and admins.
 *
 * Replaces ad-hoc completion logic that was previously duplicated across
 * SellerDashboardOps, PickupPinOps, and PostAuction\PostAuctionOps.
 */
class SellerPickupCompletionService
{
    public function __construct(
        protected ?PayoutService $payoutService = null,
        protected ?NotificationService $notifications = null,
    ) {
        $this->payoutService = $payoutService ?? new PayoutService();
        $this->notifications = $notifications ?? new NotificationService();
    }

    /**
     * Run the completion pipeline for a seller-entered PIN.
     *
     * @return array{success: bool, error?: string, payout?: Payout}
     */
    public function completeAfterSellerPin(Listing $listing, User $seller, string $pin): array
    {
        if ($listing->seller_id !== $seller->id) {
            return ['success' => false, 'error' => 'You do not own this listing.'];
        }

        if ($listing->pickup_confirmed) {
            return ['success' => false, 'error' => 'Pickup has already been confirmed for this listing.'];
        }

        $invoice = $listing->invoices()->where('payment_status', 'paid')->first();
        if (! $invoice) {
            return ['success' => false, 'error' => 'No paid invoice found for this listing yet.'];
        }

        if (! $listing->verifyPickupPin($pin)) {
            Log::warning('Invalid pickup PIN attempted', [
                'listing_id' => $listing->id,
                'seller_id' => $seller->id,
            ]);
            return ['success' => false, 'error' => 'Invalid PIN. Please verify the code with the buyer.'];
        }

        $payout = DB::transaction(function () use ($listing, $invoice, $seller) {
            $listing->confirmPickup($listing->pickup_pin, $seller->id);

            $thread = PostAuctionThread::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'listing_id' => $invoice->listing_id,
                    'buyer_id' => $invoice->buyer_id,
                    'seller_id' => $invoice->seller_id,
                    'is_unlocked' => true,
                    'unlocked_at' => now(),
                ]
            );

            $thread->forceFill([
                'pickup_confirmed' => true,
                'pickup_confirmed_at' => $thread->pickup_confirmed_at ?? now(),
                'buyer_completion_confirmed_at' => $thread->buyer_completion_confirmed_at ?? now(),
            ])->save();

            $payout = $this->payoutService->createPayoutAfterPickup($invoice, $listing);

            MessagingThreadEvent::record(
                $thread,
                $seller,
                MessagingThreadEvent::TYPE_PICKUP_CONFIRMED,
                [
                    'invoice_id' => $invoice->id,
                    'listing_id' => $listing->id,
                    'method' => 'pin',
                    'payout_id' => $payout?->id,
                ],
                countsAsExchange: false,
            );

            return $payout;
        });

        $this->fireNotifications($listing, $invoice, $seller, $payout);

        Log::info('Pickup completed via PIN', [
            'listing_id' => $listing->id,
            'invoice_id' => $invoice->id,
            'seller_id' => $seller->id,
            'payout_id' => $payout?->id,
        ]);

        return ['success' => true, 'payout' => $payout];
    }

    /**
     * Send seller / buyer / admin notifications. Failures are logged but do not
     * roll back the completion: the transaction is the source of truth.
     */
    protected function fireNotifications(Listing $listing, Invoice $invoice, User $seller, ?Payout $payout): void
    {
        try {
            $this->notifications->transactionCompletedPayoutPending($seller, $listing);
        } catch (\Throwable $e) {
            Log::error('Seller payout-pending notification failed: '.$e->getMessage(), [
                'seller_id' => $seller->id,
                'listing_id' => $listing->id,
            ]);
        }

        if ($invoice->buyer) {
            try {
                $this->notifications->pickupCompleted($invoice->buyer, $listing);
            } catch (\Throwable $e) {
                Log::error('Buyer pickup-completed notification failed: '.$e->getMessage(), [
                    'buyer_id' => $invoice->buyer_id,
                    'listing_id' => $listing->id,
                ]);
            }
        }

        if ($payout) {
            try {
                Mail::send('emails.caymark.payout-processing-started', [
                    'payout' => $payout,
                    'seller' => $seller,
                    'listing' => $listing,
                ], function ($message) use ($seller) {
                    $message->to($seller->email, $seller->name)
                        ->subject('Payout Processing Started');
                });
            } catch (\Throwable $e) {
                Log::error('Payout-processing email failed: '.$e->getMessage(), [
                    'seller_id' => $seller->id,
                    'payout_id' => $payout->id,
                ]);
            }
        }

        $this->notifyAdmins($listing, $invoice, $payout);
    }

    /**
     * In-app notification to every admin so the back office sees the completed
     * transaction. Mirrors the MessagingNotifier admin fan-out pattern.
     */
    protected function notifyAdmins(Listing $listing, Invoice $invoice, ?Payout $payout): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
        } catch (\Throwable $e) {
            Log::error('Admin lookup failed for pickup completion: '.$e->getMessage());
            return;
        }

        $vehicle = trim(implode(' ', array_filter([
            $listing->year,
            $listing->make,
            $listing->model,
        ]))) ?: 'Listing #'.$listing->id;

        foreach ($admins as $admin) {
            try {
                $admin->notify(new GenericNotification(
                    'transaction_pickup_completed',
                    sprintf('Pickup confirmed for %s. Payout has been initiated.', $vehicle),
                    [
                        'listing_id' => $listing->id,
                        'invoice_id' => $invoice->id,
                        'payout_id' => $payout?->id,
                        'item_name' => $vehicle,
                        'link' => route('admin.payments'),
                    ]
                ));
            } catch (\Throwable $e) {
                Log::error('Admin pickup-completed notification failed: '.$e->getMessage(), [
                    'admin_id' => $admin->id,
                    'listing_id' => $listing->id,
                ]);
            }
        }
    }
}
