<?php

namespace App\Services;

use App\Models\User;
use App\Models\Listing;
use App\Models\Invoice;
use App\Models\Bid;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to user (database channel only for in-app notifications).
     */
    protected function sendNotification(User $user, string $type, string $message, array $data = []): void
    {
        try {
            $user->notify(new \App\Notifications\GenericNotification($type, $message, $data));
        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'type' => $type,
            ]);
        }
    }

    // ==================== GENERAL USER NOTIFICATIONS ====================

    /**
     * 1. Registration Completed
     */
    public function registrationCompleted(User $user): void
    {
        $this->sendNotification($user, 'registration_completed', 'Your registration is complete.');
    }

    /**
     * 2. Welcome to CayMark
     */
    public function welcomeToCayMark(User $user): void
    {
        $this->sendNotification($user, 'welcome', 'Welcome to CayMark — your account is now active.');
    }

    // ==================== BUYER NOTIFICATIONS ====================

    /**
     * 3. Successful Bid Placed
     */
    public function bidPlaced(User $buyer, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($buyer, 'bid_placed', "Your bid for {$vehicleName} was placed successfully.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('auction.show', $listing->id),
        ]);
    }

    /**
     * 4. Auction Win
     */
    public function auctionWin(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'auction_won', "Congratulations! You won {$vehicleName}. Payment is now required.", [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    /**
     * 5. Payment Reminder — 6 Hours
     */
    public function paymentReminder6Hours(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_reminder_6h', "Reminder: Payment for {$vehicleName} is still outstanding.", [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    /**
     * 6. Payment Reminder — 24 Hours
     */
    public function paymentReminder24Hours(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_reminder_24h', "Reminder: Please complete payment for {$vehicleName}.", [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    /**
     * 7. Final Payment Warning — 48 Hours
     */
    public function paymentFinalWarning48Hours(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_final_warning_48h', "FINAL NOTICE: Payment for {$vehicleName} is overdue. Failure to pay may result in losing the item and account restrictions.", [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    /**
     * 8. Payment Successful
     */
    public function paymentSuccessful(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_successful', "Your payment for {$vehicleName} was successful.", [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.auctions-won'),
        ]);
    }

    /**
     * 9. Pickup Instructions Available
     */
    public function pickupInstructionsAvailable(User $buyer, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($buyer, 'pickup_instructions_available', "Pickup instructions for {$vehicleName} are now available.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('post-auction.thread', $listing->invoices()->where('payment_status', 'paid')->first()->id ?? null),
        ]);
    }

    /**
     * 10. Pickup PIN Issued
     */
    public function pickupPinIssued(User $buyer, Listing $listing, string $pin): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($buyer, 'pickup_pin_issued', "Your pickup PIN for {$vehicleName} is: {$pin}. Present this code during pickup.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'pin' => $pin,
            'link' => route('post-auction.thread', $listing->invoices()->where('payment_status', 'paid')->first()->id ?? null),
        ]);
    }

    /**
     * 11. Pickup Reschedule — Approved
     */
    public function pickupRescheduleApproved(User $buyer, Listing $listing): void
    {
        $this->sendNotification($buyer, 'pickup_reschedule_approved', 'Your new pickup time has been approved.', [
            'listing_id' => $listing->id,
            'link' => route('post-auction.thread', $listing->invoices()->where('payment_status', 'paid')->first()->id ?? null),
        ]);
    }

    /**
     * 12. Pickup Reschedule — Rejected
     */
    public function pickupRescheduleRejected(User $buyer, Listing $listing): void
    {
        $this->sendNotification($buyer, 'pickup_reschedule_rejected', 'Your new pickup time was rejected. A new time has been suggested.', [
            'listing_id' => $listing->id,
            'link' => route('post-auction.thread', $listing->invoices()->where('payment_status', 'paid')->first()->id ?? null),
        ]);
    }

    /**
     * 13. Pickup Completed
     */
    public function pickupCompleted(User $buyer, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($buyer, 'pickup_completed', "Pickup of {$vehicleName} is complete. Thank you for choosing CayMark.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
        ]);
    }

    // ==================== SELLER NOTIFICATIONS ====================

    /**
     * 14. Listing Submitted
     */
    public function listingSubmitted(User $seller, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'listing_submitted', "Your listing for {$vehicleName} has been submitted for review.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('seller.listings.index'),
        ]);
    }

    /**
     * 15. Listing Approved
     */
    public function listingApproved(User $seller, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'listing_approved', "Your listing for {$vehicleName} has been approved and is now live.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('listing.show', $listing->id),
        ]);
    }

    /**
     * 16. Auction Sold
     */
    public function auctionSold(User $seller, Listing $listing, float $winningBidAmount): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'auction_sold', "Your auction for {$vehicleName} has ended and the item is SOLD. View your invoice.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'winning_bid_amount' => $winningBidAmount,
            'link' => route('seller.payouts'),
        ]);
    }

    /**
     * 16b. Auction Ended - Reserve Price Not Met
     */
    public function auctionEndedReserveNotMet(User $seller, Listing $listing, float $winningBidAmount, float $reservePrice): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'auction_ended_reserve_not_met', "Your auction for {$vehicleName} has ended. The highest bid was $" . number_format($winningBidAmount, 2) . " but did not meet your reserve price of $" . number_format($reservePrice, 2) . ". Business sellers can relist within 48 hours.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'winning_bid_amount' => $winningBidAmount,
            'reserve_price' => $reservePrice,
            'link' => route('dashboard.seller', ['tab' => 'auctions']),
        ]);
    }

    /**
     * 17. Send Pickup Info
     */
    public function sendPickupInfo(User $seller, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'send_pickup_info', "Payment received for {$vehicleName}. Please submit pickup details for the buyer.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('post-auction.thread', $listing->invoices()->where('payment_status', 'paid')->first()->id ?? null),
        ]);
    }

    /**
     * 18. Transaction Completed — Payout Pending
     */
    public function transactionCompletedPayoutPending(User $seller, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'transaction_completed_payout_pending', "Transaction for {$vehicleName} is complete. Your payout is processing and will be issued soon.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('seller.payouts'),
        ]);
    }

    // ==================== AUCTION SYSTEM ====================

    /**
     * 19. Auction Ending Soon
     */
    public function auctionEndingSoon(User $seller, Listing $listing): void
    {
        $vehicleName = $this->getVehicleName($listing);
        $this->sendNotification($seller, 'auction_ending_soon', "Your auction for {$vehicleName} ends in 1 hour.", [
            'listing_id' => $listing->id,
            'item_name' => $vehicleName,
            'link' => route('listing.show', $listing->id),
        ]);
    }

    // ==================== PAYMENTS ====================

    /**
     * 20. Invoice Available
     */
    public function invoiceAvailable(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'invoice_available', "Your invoice for {$vehicleName} is ready. Please complete your payment.", [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    // ==================== SECURITY ====================

    /**
     * 21. Suspicious Login Detected
     */
    public function suspiciousLoginDetected(User $user): void
    {
        $this->sendNotification($user, 'suspicious_login', 'A suspicious login activity was detected on your CayMark account.', [
            'link' => route('profile.edit'),
        ]);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get vehicle name from listing.
     */
    protected function getVehicleName(Listing $listing): string
    {
        $parts = array_filter([
            $listing->year,
            $listing->make,
            $listing->model,
        ]);
        
        return !empty($parts) ? implode(' ', $parts) : '[VEHICLE_NAME]';
    }
}

