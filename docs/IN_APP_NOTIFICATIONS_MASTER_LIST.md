# In-app and mail-sync notifications (CayMark)

All user-facing notification copy lives in **`config/notifications.php`**. The service **`App\Services\NotificationService`** is the public API; it renders templates via **`App\Services\Notifications\NotificationMessageBuilder`** and sends **`App\Notifications\GenericNotification`**, which uses channels **`database`** and **`mail`** (same body in both).

Brand: **CayMark** (capital M). Support inbox defaults: **`support@caymark.co`** (`config/support.php`, `SUPPORT_*` in `.env`).

## Verify tooling

```bash
php artisan caymark:verify-notifications
```

This asserts every key under `config('notifications.templates')` maps to an existing `NotificationService` method (plus the legacy alias `suspiciousLoginDetected`).

## Catalog by type key (stored on notification `data.type`)

| Type key | Audience | Service method | Primary triggers |
|----------|----------|----------------|------------------|
| `registration_completed` | both | `registrationCompleted` | Registration completion |
| `welcome` | both | `welcomeToCayMark` | Post-registration welcome |
| `complete_registration_reminder` | both | `completeRegistrationReminder` | Login when `registration_complete` is false (once per day, cache) |
| `bid_placed` | buyer | `bidPlaced` | `AuctionBidOrchestrator` |
| `outbid` | buyer | `outbid` | `AuctionBidOrchestrator` (previous high bidder) |
| `auction_won` | buyer | `auctionWin` | Invoice / win flows |
| `invoice_available` | buyer | `invoiceAvailable` | `InvoiceService::generateInvoiceForAuctionWin` |
| `payment_reminder_6h` | buyer | `paymentReminder6Hours` | `SendPaymentReminders` |
| `payment_reminder_24h` | buyer | `paymentReminder24Hours` | `SendPaymentReminders` |
| `payment_final_warning_48h` | buyer | `paymentFinalWarning48Hours` | `SendPaymentReminders` |
| `payment_successful` | buyer | `paymentSuccessful` | `BuyerPaymentOps` |
| `pickup_instructions_available` | buyer | `pickupInstructionsAvailable` | `PostAuctionOps` |
| `pickup_pin_issued` | buyer | `pickupPinIssued` | `PostAuctionOps` |
| `pickup_reschedule_approved` | buyer | `pickupRescheduleApproved` | `PostAuctionOps` |
| `pickup_reschedule_rejected` | buyer | `pickupRescheduleRejected` | `PostAuctionOps` |
| `pickup_completed` | buyer | `pickupCompleted` | Pickup completion services |
| `auction_ending_soon_24h` | seller | `auctionEndingSoon24h` | `SendAuctionEndingReminders` |
| `auction_ending_soon` | seller | `auctionEndingSoon` | `SendAuctionEndingReminders` (≈1h window) |
| `auction_ending_soon_bidder` | buyer | `auctionEndingSoonBidder` | `SendAuctionEndingReminders` |
| `auction_ending_soon_watchlist` | buyer | `auctionEndingSoonWatchlist` | `SendAuctionEndingReminders` (watchlist only if not already a bidder) |
| `deposit_received` | buyer | `depositReceived` | `DepositService::addDeposit` |
| `deposit_refund_request_submitted` | buyer | `depositRefundRequestSubmitted` | `DepositWithdrawalOps` |
| `deposit_withdrawal_approved` | buyer | `depositWithdrawalApproved` | `AdminController::approveWithdrawal` |
| `deposit_withdrawal_rejected` | buyer | `depositWithdrawalRejected` | `AdminController::rejectWithdrawal` |
| `listing_submitted` | seller | `listingSubmitted` | Listing submission |
| `listing_approved` | seller | `listingApproved` | Admin approval |
| `listing_rejected` | seller | `listingRejected` | `AdminActionHub::rejectListing` |
| `editing_unavailable_listing_rejected` | seller | `editingUnavailableListingRejected` | `AdminActionHub::rejectListing` |
| `new_bid_on_listing` | seller | `newBidOnListing` | `AuctionBidOrchestrator` |
| `awaiting_buyer_payment` | seller | `awaitingBuyerPayment` | `InvoiceService` (invoice created) |
| `reserve_price_met` | seller | `reservePriceMet` | `InvoiceService` (invoice issued after reserve met) |
| `auction_sold` | seller | `auctionSold` | `InvoiceService::processEndedAuctions` |
| `auction_ended_reserve_not_met` | seller | `auctionEndedReserveNotMet` | `InvoiceService::processEndedAuctions` |
| `auction_closed_by_seller` | seller | `auctionClosedBySeller` | Seller deletes approved auction listing |
| `send_pickup_info` | seller | `sendPickupInfo` | `BuyerPaymentOps` |
| `transaction_completed_payout_pending` | seller | `transactionCompletedPayoutPending` | Payout flow |
| `support_ticket_submitted` | both | `supportTicketSubmitted` | `SupportOps` |
| `support_ticket_responded` | both | `supportTicketResponded` | `AdminController::replyToTicket` |
| `password_changed` | both | `passwordChanged` | Buyer/seller dashboard password change |
| `email_updated` | both | `emailUpdated` | Buyer/seller email verification success |
| `payout_details_updated` | seller | `payoutDetailsUpdated` | Seller payout method / payout settings |
| `subscription_activated` | both | `subscriptionActivated` | `Subscription` model `created` event |
| `subscription_ending_soon` | both | `subscriptionEndingSoon` | `caymark:send-subscription-notifications` (daily) |
| `subscription_ended` | both | `subscriptionEnded` | `caymark:send-subscription-notifications` (daily) |
| `login_new_device` | both | `loginFromNewDevice` | Successful login when `last_login_ip` differs from current IP |
| `login_attempt_unsuccessful` | both | `loginAttemptUnsuccessful` | Failed login for existing email (throttled 1/hour per user) |

**Legacy:** `suspiciousLoginDetected()` still exists and delegates to `loginFromNewDevice()`.

## Auction reminder deduplication

`SendAuctionEndingReminders` records rows in **`listing_auction_reminder_dispatches`** (`listing_id`, `window`, `purpose`, `user_id`) so seller email, seller in-app, bidder in-app, and watchlist in-app are each sent once per listing per time window (`24_hour` or `1_hour`).

## Scheduled commands

| Command | Schedule |
|---------|----------|
| `caymark:send-auction-ending-reminders` | `routes/console.php` — every five minutes |
| `caymark:send-subscription-notifications` | Daily at 08:00 |
| `caymark:send-payment-reminders` | Every five minutes (6h / 24h / 48h copy from catalog) |

## Payment reminders vs spec

The codebase keeps **6h**, **24h**, and **48h overdue** buyer reminders (`SendPaymentReminders`) with catalog-aligned tone. Adjust or disable intervals in that command if product policy changes.
