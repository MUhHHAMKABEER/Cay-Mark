<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Listing;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\Notifications\NotificationMessageBuilder;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected function supportEmailDisplay(): string
    {
        $inbox = config('support.inbox');
        if (is_string($inbox) && filter_var($inbox, FILTER_VALIDATE_EMAIL)) {
            return $inbox;
        }

        return 'support@caymark.co';
    }

    /**
     * @param  array<string, string|int|float|null>  $replacements
     */
    protected function message(string $templateKey, array $replacements = []): string
    {
        $base = array_merge([
            'support_email' => $this->supportEmailDisplay(),
        ], $replacements);

        return NotificationMessageBuilder::render($templateKey, $base);
    }

    protected function sendNotification(User $user, string $type, string $message, array $data = []): void
    {
        try {
            $user->notify(new \App\Notifications\GenericNotification($type, $message, $data));
        } catch (\Exception $e) {
            Log::error('Failed to send notification: '.$e->getMessage(), [
                'user_id' => $user->id,
                'type' => $type,
            ]);
        }
    }

    protected function getVehicleName(Listing $listing): string
    {
        $parts = array_filter([
            $listing->year,
            $listing->make,
            $listing->model,
        ]);

        return ! empty($parts) ? implode(' ', $parts) : '[VEHICLE_NAME]';
    }

    /**
     * @return array{listing_number: string, vehicle_name: string}
     */
    protected function listingCore(Listing $listing): array
    {
        return [
            'listing_number' => (string) ($listing->item_number ?? $listing->id),
            'vehicle_name' => $this->getVehicleName($listing),
        ];
    }

    // ==================== GENERAL USER NOTIFICATIONS ====================

    public function registrationCompleted(User $user): void
    {
        $this->sendNotification($user, 'registration_completed', $this->message('registration_completed'), [
            'link' => route('dashboard'),
        ]);
    }

    public function welcomeToCayMark(User $user): void
    {
        $this->sendNotification($user, 'welcome', $this->message('welcome'), [
            'link' => route('dashboard'),
        ]);
    }

    public function completeRegistrationReminder(User $user): void
    {
        $this->sendNotification($user, 'complete_registration_reminder', $this->message('complete_registration_reminder'), [
            'link' => route('finish.registration'),
        ]);
    }

    // ==================== BUYER NOTIFICATIONS ====================

    public function bidPlaced(User $buyer, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($buyer, 'bid_placed', $this->message('bid_placed', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('auction.show', $listing),
        ]));
    }

    public function outbid(User $buyer, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($buyer, 'outbid', $this->message('outbid', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('auction.show', $listing),
        ]));
    }

    public function auctionWin(User $buyer, Invoice $invoice): void
    {
        $listing = $invoice->listing;
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $listingNumber = $listing ? (string) ($listing->item_number ?? $listing->id) : (string) ($invoice->item_id ?? '');
        $this->sendNotification($buyer, 'auction_won', $this->message('auction_won', [
            'vehicle_name' => $vehicleName,
            'listing_number' => $listingNumber,
        ]), [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'listing_number' => $listingNumber,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    public function paymentReminder6Hours(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_reminder_6h', $this->message('payment_reminder_6h', [
            'vehicle_name' => $vehicleName,
        ]), [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    public function paymentReminder24Hours(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_reminder_24h', $this->message('payment_reminder_24h', [
            'vehicle_name' => $vehicleName,
        ]), [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    public function paymentFinalWarning48Hours(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $this->sendNotification($buyer, 'payment_final_warning_48h', $this->message('payment_final_warning_48h', [
            'vehicle_name' => $vehicleName,
        ]), [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    public function paymentSuccessful(User $buyer, Invoice $invoice): void
    {
        $invoice->loadMissing('listing');
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $pickupCode = $invoice->listing?->pickupCodeDisplay() ?? '—';
        $this->sendNotification($buyer, 'payment_successful', $this->message('payment_successful', [
            'vehicle_name' => $vehicleName,
            'pickup_code' => $pickupCode,
        ]), [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'pickup_code' => $pickupCode,
            'link' => route('messaging.index'),
        ]);
    }

    public function pickupInstructionsAvailable(User $buyer, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $paidInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
        $this->sendNotification($buyer, 'pickup_instructions_available', $this->message('pickup_instructions_available', [
            'vehicle_name' => $core['vehicle_name'],
        ]), [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => $paidInvoice ? route('messaging.thread.show', $paidInvoice->id) : route('messaging.index'),
        ]);
    }

    public function pickupPinIssued(User $buyer, Listing $listing, string $pin): void
    {
        $core = $this->listingCore($listing);
        $paidInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
        $this->sendNotification($buyer, 'pickup_pin_issued', $this->message('pickup_pin_issued', [
            'vehicle_name' => $core['vehicle_name'],
            'pickup_code' => $pin,
        ]), [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'pin' => $pin,
            'link' => $paidInvoice ? route('messaging.thread.show', $paidInvoice->id) : route('messaging.index'),
        ]);
    }

    public function pickupRescheduleApproved(User $buyer, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $paidInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
        $this->sendNotification($buyer, 'pickup_reschedule_approved', $this->message('pickup_reschedule_approved', [
            'vehicle_name' => $core['vehicle_name'],
        ]), [
            'listing_id' => $listing->id,
            'link' => $paidInvoice ? route('messaging.thread.show', $paidInvoice->id) : route('messaging.index'),
        ]);
    }

    public function pickupRescheduleRejected(User $buyer, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $paidInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
        $this->sendNotification($buyer, 'pickup_reschedule_rejected', $this->message('pickup_reschedule_rejected', [
            'vehicle_name' => $core['vehicle_name'],
        ]), [
            'listing_id' => $listing->id,
            'link' => $paidInvoice ? route('messaging.thread.show', $paidInvoice->id) : route('messaging.index'),
        ]);
    }

    public function pickupCompleted(User $buyer, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($buyer, 'pickup_completed', $this->message('pickup_completed', [
            'vehicle_name' => $core['vehicle_name'],
        ]), [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
        ]);
    }

    public function invoiceAvailable(User $buyer, Invoice $invoice): void
    {
        $vehicleName = $invoice->item_name ?? '[VEHICLE_NAME]';
        $listing = $invoice->listing;
        $listingNumber = $listing ? (string) ($listing->item_number ?? $listing->id) : (string) ($invoice->item_id ?? '');
        $this->sendNotification($buyer, 'invoice_available', $this->message('invoice_available', [
            'vehicle_name' => $vehicleName,
            'listing_number' => $listingNumber,
        ]), [
            'invoice_id' => $invoice->id,
            'item_name' => $vehicleName,
            'link' => route('buyer.payment.checkout-single', $invoice->id),
        ]);
    }

    public function auctionEndingSoonBidder(User $buyer, Listing $listing, string $endWindowLabel): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($buyer, 'auction_ending_soon_bidder', $this->message('auction_ending_soon_bidder', array_merge($core, [
            'end_window' => $endWindowLabel,
        ])), array_merge($core, [
            'listing_id' => $listing->id,
            'end_window' => $endWindowLabel,
            'link' => route('auction.show', $listing),
        ]));
    }

    public function auctionEndingSoonWatchlist(User $buyer, Listing $listing, string $endWindowLabel): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($buyer, 'auction_ending_soon_watchlist', $this->message('auction_ending_soon_watchlist', array_merge($core, [
            'end_window' => $endWindowLabel,
        ])), array_merge($core, [
            'listing_id' => $listing->id,
            'end_window' => $endWindowLabel,
            'link' => route('auction.show', $listing),
        ]));
    }

    public function depositReceived(User $buyer, float $amount): void
    {
        $this->sendNotification($buyer, 'deposit_received', $this->message('deposit_received', [
            'amount' => number_format($amount, 2),
        ]), [
            'amount' => $amount,
            'link' => route('buyer.deposit-withdrawal'),
        ]);
    }

    public function depositRefundRequestSubmitted(User $buyer, float $amount): void
    {
        $this->sendNotification($buyer, 'deposit_refund_request_submitted', $this->message('deposit_refund_request_submitted', [
            'amount' => number_format($amount, 2),
        ]), [
            'amount' => $amount,
            'link' => route('buyer.deposit-withdrawal'),
        ]);
    }

    public function depositWithdrawalApproved(User $buyer, float $amount): void
    {
        $this->sendNotification($buyer, 'deposit_withdrawal_approved', $this->message('deposit_withdrawal_approved', [
            'amount' => number_format($amount, 2),
        ]), [
            'amount' => $amount,
            'link' => route('buyer.deposit-withdrawal'),
        ]);
    }

    public function depositWithdrawalRejected(User $buyer, float $amount): void
    {
        $this->sendNotification($buyer, 'deposit_withdrawal_rejected', $this->message('deposit_withdrawal_rejected', [
            'amount' => number_format($amount, 2),
        ]), [
            'amount' => $amount,
            'link' => route('buyer.deposit-withdrawal'),
        ]);
    }

    // ==================== SELLER NOTIFICATIONS ====================

    public function listingSubmitted(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'listing_submitted', $this->message('listing_submitted', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('seller.auctions'),
        ]));
    }

    public function listingApproved(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'listing_approved', $this->message('listing_approved', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('listing.show', ['id' => $listing->id, 'slug' => $listing->getSlugOrGenerate()]),
        ]));
    }

    public function listingRejected(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'listing_rejected', $this->message('listing_rejected', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('seller.auctions'),
        ]));
    }

    public function editingUnavailableListingRejected(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'editing_unavailable_listing_rejected', $this->message('editing_unavailable_listing_rejected', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('seller.auctions'),
        ]));
    }

    public function newBidOnListing(User $seller, Listing $listing, float $currentHighBid): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'new_bid_on_listing', $this->message('new_bid_on_listing', array_merge($core, [
            'amount' => number_format($currentHighBid, 2),
        ])), array_merge($core, [
            'listing_id' => $listing->id,
            'amount' => $currentHighBid,
            'link' => route('seller.listings.show', $listing->id),
        ]));
    }

    public function awaitingBuyerPayment(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'awaiting_buyer_payment', $this->message('awaiting_buyer_payment', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'link' => route('seller.auctions'),
        ]));
    }

    public function reservePriceMet(User $seller, Listing $listing, float $winningBidAmount): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'reserve_price_met', $this->message('reserve_price_met', array_merge($core, [
            'winning_bid_amount' => number_format($winningBidAmount, 2),
        ])), array_merge($core, [
            'listing_id' => $listing->id,
            'winning_bid_amount' => $winningBidAmount,
            'link' => route('seller.listings.show', $listing->id),
        ]));
    }

    public function auctionSold(User $seller, Listing $listing, float $winningBidAmount): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'auction_sold', $this->message('auction_sold', array_merge($core, [
            'winning_bid_amount' => number_format($winningBidAmount, 2),
        ])), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'winning_bid_amount' => $winningBidAmount,
            'link' => route('seller.payouts'),
        ]));
    }

    public function auctionEndedReserveNotMet(User $seller, Listing $listing, float $winningBidAmount, float $reservePrice): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'auction_ended_reserve_not_met', $this->message('auction_ended_reserve_not_met', array_merge($core, [
            'winning_bid_amount' => number_format($winningBidAmount, 2),
            'reserve_amount' => number_format($reservePrice, 2),
        ])), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'winning_bid_amount' => $winningBidAmount,
            'reserve_price' => $reservePrice,
            'link' => route('seller.auctions'),
        ]));
    }

    public function auctionClosedBySeller(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'auction_closed_by_seller', $this->message('auction_closed_by_seller', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'link' => route('seller.auctions'),
        ]));
    }

    public function sendPickupInfo(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $paidInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
        $this->sendNotification($seller, 'send_pickup_info', $this->message('send_pickup_info', [
            'vehicle_name' => $core['vehicle_name'],
        ]), [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => $paidInvoice ? route('messaging.thread.show', $paidInvoice->id) : route('messaging.index'),
        ]);
    }

    public function transactionCompletedPayoutPending(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'transaction_completed_payout_pending', $this->message('transaction_completed_payout_pending', [
            'vehicle_name' => $core['vehicle_name'],
        ]), [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('seller.payouts'),
        ]);
    }

    public function auctionEndingSoon(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'auction_ending_soon', $this->message('auction_ending_soon', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('listing.show', ['id' => $listing->id, 'slug' => $listing->getSlugOrGenerate()]),
        ]));
    }

    public function auctionEndingSoon24h(User $seller, Listing $listing): void
    {
        $core = $this->listingCore($listing);
        $this->sendNotification($seller, 'auction_ending_soon_24h', $this->message('auction_ending_soon_24h', $core), array_merge($core, [
            'listing_id' => $listing->id,
            'item_name' => $core['vehicle_name'],
            'link' => route('listing.show', ['id' => $listing->id, 'slug' => $listing->getSlugOrGenerate()]),
        ]));
    }

    // ==================== SHARED ====================

    public function supportTicketSubmitted(User $user, SupportTicket $ticket): void
    {
        $num = (string) ($ticket->public_ticket_number ?? $ticket->id);
        $this->sendNotification($user, 'support_ticket_submitted', $this->message('support_ticket_submitted', [
            'ticket_number' => $num,
        ]), [
            'ticket_id' => $ticket->id,
            'ticket_number' => $num,
            'link' => route('dashboard'),
        ]);
    }

    public function supportTicketResponded(User $user, SupportTicket $ticket): void
    {
        $num = (string) ($ticket->public_ticket_number ?? $ticket->id);
        $this->sendNotification($user, 'support_ticket_responded', $this->message('support_ticket_responded', [
            'ticket_number' => $num,
        ]), [
            'ticket_id' => $ticket->id,
            'ticket_number' => $num,
            'link' => route('dashboard'),
        ]);
    }

    public function passwordChanged(User $user): void
    {
        $this->sendNotification($user, 'password_changed', $this->message('password_changed'), [
            'link' => route('profile.edit'),
        ]);
    }

    public function emailUpdated(User $user): void
    {
        $this->sendNotification($user, 'email_updated', $this->message('email_updated'), [
            'link' => route('profile.edit'),
        ]);
    }

    public function payoutDetailsUpdated(User $seller): void
    {
        $this->sendNotification($seller, 'payout_details_updated', $this->message('payout_details_updated'), [
            'link' => route('seller.payout-method'),
        ]);
    }

    public function subscriptionActivated(User $user, string $packageName, ?int $subscriptionId = null): void
    {
        $data = [
            'package_name' => $packageName,
            'link' => route('dashboard'),
        ];
        if ($subscriptionId !== null) {
            $data['subscription_id'] = $subscriptionId;
        }
        $this->sendNotification($user, 'subscription_activated', $this->message('subscription_activated', [
            'package_name' => $packageName,
        ]), $data);
    }

    public function subscriptionEndingSoon(User $user, string $packageName, string $endsAtDisplay, ?int $subscriptionId = null): void
    {
        $data = [
            'package_name' => $packageName,
            'date' => $endsAtDisplay,
            'link' => route('dashboard'),
        ];
        if ($subscriptionId !== null) {
            $data['subscription_id'] = $subscriptionId;
        }
        $this->sendNotification($user, 'subscription_ending_soon', $this->message('subscription_ending_soon', [
            'package_name' => $packageName,
            'date' => $endsAtDisplay,
        ]), $data);
    }

    public function subscriptionEnded(User $user, string $packageName, ?int $subscriptionId = null): void
    {
        $data = [
            'package_name' => $packageName,
            'link' => route('dashboard'),
        ];
        if ($subscriptionId !== null) {
            $data['subscription_id'] = $subscriptionId;
        }
        $this->sendNotification($user, 'subscription_ended', $this->message('subscription_ended', [
            'package_name' => $packageName,
        ]), $data);
    }

    /**
     * Successful login from a new IP vs stored last_login_ip.
     */
    public function loginFromNewDevice(User $user): void
    {
        $this->sendNotification($user, 'login_new_device', $this->message('login_new_device'), [
            'link' => route('profile.edit'),
        ]);
    }

    /**
     * Failed password attempt for an existing account (throttled at caller).
     */
    public function loginAttemptUnsuccessful(User $user): void
    {
        $this->sendNotification($user, 'login_attempt_unsuccessful', $this->message('login_attempt_unsuccessful'), [
            'link' => route('profile.edit'),
        ]);
    }

    /**
     * @deprecated Use loginFromNewDevice(); kept for trigger compatibility.
     */
    public function suspiciousLoginDetected(User $user): void
    {
        $this->loginFromNewDevice($user);
    }
}
