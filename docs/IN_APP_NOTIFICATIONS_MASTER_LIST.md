# Master In-App Notification Message List — Compliance

All 21 in-app notifications (dashboard only) are implemented. Notifications are stored in the `notifications` table and displayed in the dashboard via the Notifications tab.

## Summary

| # | Type | Trigger | Message | Triggered From |
|---|------|---------|---------|-----------------|
| 1 | Registration Completed | User finishes onboarding | "Your registration is complete." | `RegisteredUserController` (after registration) |
| 2 | Welcome to CayMark | Immediately after registration | "Welcome to CayMark — your account is now active." | `RegisteredUserController` (after registration) |
| 3 | Successful Bid Placed | Buyer submits a bid | "Your bid for [VEHICLE_NAME] was placed successfully." | `AuctionBidOrchestrator` (after bid) |
| 4 | Auction Win | Buyer wins auction | "Congratulations! You won [VEHICLE_NAME]. Payment is now required." | `InvoiceService` via `AuctionWonNotification` (when invoice generated) |
| 5 | Payment Reminder — 6 Hours | Payment not completed | "Reminder: Payment for [VEHICLE_NAME] is still outstanding." | `SendPaymentReminders` command (6h before deadline) |
| 6 | Payment Reminder — 24 Hours | Payment still unpaid | "Reminder: Please complete payment for [VEHICLE_NAME]." | `SendPaymentReminders` command (24h before deadline) |
| 7 | Final Payment Warning — 48 Hours | Payment overdue | "FINAL NOTICE: Payment for [VEHICLE_NAME] is overdue. Failure to pay may result in losing the item and account restrictions." | `SendPaymentReminders` command (after deadline) |
| 8 | Payment Successful | Buyer completes payment | "Your payment for [VEHICLE_NAME] was successful." | `BuyerPaymentOps` (after payment) |
| 9 | Pickup Instructions Available | Seller submits pickup details | "Pickup instructions for [VEHICLE_NAME] are now available." | `PostAuctionOps` (when pickup details sent) |
| 10 | Pickup PIN Issued | Pickup appointment confirmed | "Your pickup PIN for [VEHICLE_NAME] is: [PIN]. Present this code during pickup." | `PostAuctionMessageController` (when buyer accepts pickup) |
| 11 | Pickup Reschedule — Approved | Seller approves buyer request | "Your new pickup time has been approved." | `PostAuctionOps` (when seller approves change) |
| 12 | Pickup Reschedule — Rejected | Seller rejects and proposes new time | "Your new pickup time was rejected. A new time has been suggested." | `PostAuctionOps` (when seller rejects change) |
| 13 | Pickup Completed | Seller enters valid PIN | "Pickup of [VEHICLE_NAME] is complete. Thank you for choosing CayMark." | `PickupPinOps` (when seller confirms pickup with PIN) |
| 14 | Listing Submitted | Seller submits listing | "Your listing for [VEHICLE_NAME] has been submitted for review." | `Listing` model (on submit) |
| 15 | Listing Approved | Admin approves listing | "Your listing for [VEHICLE_NAME] has been approved and is now live." | `AdminController` (on approve) |
| 16 | Auction Sold | Auction ends successfully | "Your auction for [VEHICLE_NAME] has ended and the item is SOLD. View your invoice." | `InvoiceService` (when invoice generated for seller) |
| 17 | Send Pickup Info | Buyer payment completed, pickup details not yet submitted | "Payment received for [VEHICLE_NAME]. Please submit pickup details for the buyer." | `BuyerPaymentOps` (after payment) |
| 18 | Transaction Completed — Payout Pending | Seller enters valid pickup PIN | "Transaction for [VEHICLE_NAME] is complete. Your payout is processing and will be issued soon." | `PickupPinOps` (when seller confirms pickup) |
| 19 | Auction Ending Soon | 1 hour remains | "Your auction for [VEHICLE_NAME] ends in 1 hour." | `SendAuctionEndingReminders` command |
| 20 | Invoice Available | Auction win | "Your invoice for [VEHICLE_NAME] is ready. Please complete your payment." | `InvoiceService` (when invoice generated) |
| 21 | Suspicious Login Detected | Unusual account activity (new IP) | "A suspicious login activity was detected on your CayMark account." | `AuthenticatedSessionController` (when login IP differs from last known IP) |

## Implementation Details

- **Service:** `App\Services\NotificationService` — all 21 methods with exact message text from the master list.
- **Channel:** Database only (`GenericNotification` and `AuctionWonNotification` use `via(): ['database']`).
- **Storage:** Laravel `notifications` table (UUID, type, notifiable, data, read_at, timestamps).
- **Display:** Dashboard Notifications tab; notifications include `type`, `message`, and optional `link` in `data`.

## Scheduled Commands

- **Payment reminders (5, 6, 7):** `php artisan caymark:send-payment-reminders` — schedule every 5–15 minutes.
- **Auction ending soon (19):** `php artisan caymark:send-auction-ending-reminders` — schedule every 5–15 minutes.

## Security Notification (#21)

Suspicious login is triggered when the user logs in from an IP address that differs from their last known IP (`users.last_login_ip`). The first login after registration does not trigger it (no previous IP). After each successful login, `last_login_ip` is updated.

**Total: 21 notifications — all implemented and triggered.**

---

## How to check if this document is completed

Use any of the following to confirm all 21 notifications are implemented and triggered.

### 1. Run the verification command (recommended)

From the project root:

```bash
php artisan caymark:verify-notifications
```

This command checks that:
- All 21 notification methods exist in `NotificationService`.
- Each trigger is present in the codebase (controller, service, or command).

Exit code 0 and "All 21 notifications verified" means the document is fully implemented.

### 2. Manual checklist

| # | Notification            | Method in NotificationService     | Trigger location to search                    |
|---|-------------------------|-----------------------------------|-----------------------------------------------|
| 1 | Registration Completed  | `registrationCompleted`           | `RegisteredUserController`                    |
| 2 | Welcome to CayMark       | `welcomeToCayMark`                | `RegisteredUserController`                    |
| 3 | Successful Bid Placed   | `bidPlaced`                       | `AuctionBidOrchestrator`                     |
| 4 | Auction Win             | `auctionWin` / AuctionWonNotification | `InvoiceService` (sendInAppNotification) |
| 5 | Payment Reminder 6h      | `paymentReminder6Hours`           | `SendPaymentReminders` command              |
| 6 | Payment Reminder 24h     | `paymentReminder24Hours`          | `SendPaymentReminders` command              |
| 7 | Final Payment Warning 48h | `paymentFinalWarning48Hours`   | `SendPaymentReminders` command              |
| 8 | Payment Successful      | `paymentSuccessful`               | `BuyerPaymentOps`                            |
| 9 | Pickup Instructions Available | `pickupInstructionsAvailable` | `PostAuctionOps`                         |
| 10 | Pickup PIN Issued      | `pickupPinIssued`                 | `PostAuctionMessageController`               |
| 11 | Pickup Reschedule Approved | `pickupRescheduleApproved`   | `PostAuctionOps`                            |
| 12 | Pickup Reschedule Rejected | `pickupRescheduleRejected`   | `PostAuctionOps`                            |
| 13 | Pickup Completed       | `pickupCompleted`                 | `PickupPinOps`                               |
| 14 | Listing Submitted      | `listingSubmitted`               | `Listing` model                              |
| 15 | Listing Approved       | `listingApproved`                 | `AdminController`                            |
| 16 | Auction Sold            | `auctionSold`                     | `InvoiceService`                             |
| 17 | Send Pickup Info        | `sendPickupInfo`                  | `BuyerPaymentOps`                            |
| 18 | Transaction Completed Payout Pending | `transactionCompletedPayoutPending` | `PickupPinOps`                    |
| 19 | Auction Ending Soon    | `auctionEndingSoon`               | `SendAuctionEndingReminders` command         |
| 20 | Invoice Available      | `invoiceAvailable`                | `InvoiceService`                             |
| 21 | Suspicious Login       | `suspiciousLoginDetected`         | `AuthenticatedSessionController`             |

### 3. Quick grep checks

From project root:

```bash
# All 21 methods exist in NotificationService
rg "public function (registrationCompleted|welcomeToCayMark|bidPlaced|auctionWin|paymentReminder6Hours|paymentReminder24Hours|paymentFinalWarning48Hours|paymentSuccessful|pickupInstructionsAvailable|pickupPinIssued|pickupRescheduleApproved|pickupRescheduleRejected|pickupCompleted|listingSubmitted|listingApproved|auctionSold|sendPickupInfo|transactionCompletedPayoutPending|auctionEndingSoon|invoiceAvailable|suspiciousLoginDetected)" app/Services/NotificationService.php

# Triggers call NotificationService (sample)
rg "NotificationService" app/Http/Controllers/Auth/RegisteredUserController.php app/Services/Buyer/AuctionBidOrchestrator.php app/Services/InvoiceService.php app/Services/Buyer/BuyerPaymentOps.php app/Services/PostAuction/PostAuctionOps.php app/Services/Seller/PickupPinOps.php app/Models/Listing.php app/Http/Controllers/AdminController.php app/Http/Controllers/Auth/AuthenticatedSessionController.php app/Console/Commands/SendPaymentReminders.php app/Console/Commands/SendAuctionEndingReminders.php app/Http/Controllers/PostAuctionMessageController.php
```

If all 21 method names appear in `NotificationService.php` and the trigger files reference `NotificationService`, the document is implemented.

### 4. Database check (after actions occur)

Notifications are stored in the `notifications` table. To see recent notifications for a user:

```sql
SELECT id, type, data, read_at, created_at
FROM notifications
WHERE notifiable_id = :user_id
ORDER BY created_at DESC
LIMIT 50;
```

The `data` column (JSON) contains `type` and `message` matching the master list.
