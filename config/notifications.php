<?php

/**
 * In-app + mail-sync notification copy (CayMark branding).
 * Template keys match the `type` stored on GenericNotification / database notifications.
 */
return [

    'templates' => [

        'registration_completed' => [
            'audience' => 'both',
            'body' => 'Your registration on CayMark is complete. You can now use your dashboard and listings.',
        ],
        'welcome' => [
            'audience' => 'both',
            'body' => 'Welcome to CayMark — your account is now active.',
        ],
        'complete_registration_reminder' => [
            'audience' => 'both',
            'body' => 'Please complete your CayMark registration so you can bid, sell, and receive important updates.',
        ],

        'bid_placed' => [
            'audience' => 'buyer',
            'body' => 'Your bid has been placed successfully on CayMark listing #{listing_number} ({vehicle_name}). Stay alert for competing bids and auction updates.',
        ],
        'outbid' => [
            'audience' => 'buyer',
            'body' => 'You have been outbid on listing #{listing_number} ({vehicle_name}). Review the current high bid and place a new bid if you wish to stay in the running.',
        ],
        'auction_won' => [
            'audience' => 'buyer',
            'body' => 'Congratulations — you won the auction for {vehicle_name} (listing #{listing_number}). Proceed with payment within the time shown on your invoice to secure your purchase.',
        ],
        'invoice_available' => [
            'audience' => 'buyer',
            'body' => 'Your invoice for {vehicle_name} is ready. You have 48 hours from invoice issue to complete payment in CayMark.',
        ],
        'payment_reminder_6h' => [
            'audience' => 'buyer',
            'body' => 'Reminder: payment for {vehicle_name} is still outstanding. Complete payment soon to avoid losing the vehicle and possible account restrictions.',
        ],
        'payment_reminder_24h' => [
            'audience' => 'buyer',
            // N10 — inject {listing_number}
            'body' => 'This is a reminder that payment for listing #{listing_number} is still pending. To avoid any issues with your purchase, please complete payment as soon as possible. Thank you.',
        ],
        'payment_final_warning_48h' => [
            'audience' => 'buyer',
            'body' => 'Final notice: payment for {vehicle_name} is overdue. Pay immediately to avoid default, loss of the item, and account restrictions.',
        ],
        'payment_successful' => [
            'audience' => 'buyer',
            'body' => 'Your payment for {vehicle_name} was successful. Use Messaging Center for pickup coordination. Pickup code: {pickup_code}.',
        ],
        'pickup_instructions_available' => [
            'audience' => 'buyer',
            'body' => 'Pickup instructions for {vehicle_name} are now available in your CayMark account.',
        ],
        'pickup_pin_issued' => [
            'audience' => 'buyer',
            'body' => 'Your pickup PIN for {vehicle_name} is {pickup_code}. Present this code during pickup.',
        ],
        'pickup_reschedule_approved' => [
            'audience' => 'buyer',
            'body' => 'Your new pickup time for {vehicle_name} has been approved.',
        ],
        'pickup_reschedule_rejected' => [
            'audience' => 'buyer',
            'body' => 'Your requested pickup time for {vehicle_name} was not approved. A new time has been suggested — check Messaging Center.',
        ],
        'pickup_completed' => [
            'audience' => 'buyer',
            'body' => 'Pickup of {vehicle_name} is complete. Thank you for choosing CayMark.',
        ],

        'auction_ending_soon_24h' => [
            'audience' => 'seller',
            'body' => 'Your auction for {vehicle_name} (listing #{listing_number}) is ending within about 24 hours. Monitor bids and be ready for next steps after the sale.',
        ],
        'auction_ending_soon' => [
            'audience' => 'seller',
            'body' => 'Your auction for {vehicle_name} (listing #{listing_number}) is ending within about one hour. Monitor activity until the hammer falls.',
        ],
        'auction_ending_soon_bidder' => [
            'audience' => 'buyer',
            // N07
            'body' => 'The auction for listing #{listing_number} ({vehicle_name}) is ending soon. Place your bid now if you\'d like to secure your position before it closes.',
        ],
        'auction_ending_soon_watchlist' => [
            'audience' => 'buyer',
            // N06
            'body' => 'Item #{listing_number} ({vehicle_name}) in your watchlist is ending soon. Place a bid now before this auction closes to win.',
        ],

        'deposit_wire_request_received' => [
            'audience' => 'buyer',
            'body' => "We've received your deposit request of \${amount}. Your balance will be updated once your wire transfer clears our team — this usually takes 1–3 business days.",
        ],
        'deposit_wire_confirmed' => [
            'audience' => 'buyer',
            'body' => 'Great news! Your wire transfer of ${amount} has been confirmed. Your buying power has been updated and you can now use it to bid.',
        ],
        'deposit_wire_rejected' => [
            'audience' => 'buyer',
            'body' => 'Your deposit request of ${amount} could not be confirmed. Please contact support@caymark.co if you believe this is an error.',
        ],
        'deposit_received' => [
            'audience' => 'buyer',
            'body' => 'CayMark received your deposit of ${amount}. Funds are available in your deposit wallet according to your account balance.',
        ],
        // N15 — Admin-issued item refund
        'refund_issued_item' => [
            'audience' => 'buyer',
            'body' => 'A refund has been issued for your transaction related to listing #{listing_number}. Please allow a few business days for the funds to reflect in your account.',
        ],

        'deposit_refund_request_submitted' => [
            'audience' => 'buyer',
            // N14
            'body' => "We've received your request to refund your deposit. Your request is currently being reviewed by our team.\n\nPlease note that a deposit is required to bid on all items over \$2,000 on CayMark.\n\nIf you would like to cancel this request, or if you did not make this request, please contact our team immediately at {support_email}.",
        ],
        'deposit_withdrawal_approved' => [
            'audience' => 'buyer',
            // N16
            'body' => 'A refund has been issued for your deposit in the amount of ${amount}. Please allow a few business days for the funds to reflect in your account.',
        ],
        'deposit_withdrawal_rejected' => [
            'audience' => 'buyer',
            'body' => 'Your deposit withdrawal request of ${amount} was not approved. The amount has been returned to your available deposit balance.',
        ],

        'listing_submitted' => [
            'audience' => 'seller',
            'body' => 'Your listing for {vehicle_name} (listing #{listing_number}) has been submitted for CayMark review.',
        ],
        'listing_approved' => [
            'audience' => 'seller',
            'body' => 'Your listing for {vehicle_name} (listing #{listing_number}) has been approved and is now live.',
        ],
        'listing_rejected' => [
            'audience' => 'seller',
            'body' => 'Your listing for {vehicle_name} (listing #{listing_number}) was not approved. Check your email for the reason and next steps from CayMark.',
        ],
        'editing_unavailable_listing_rejected' => [
            'audience' => 'seller',
            'body' => 'Editing is unavailable for listing #{listing_number} ({vehicle_name}) because it was rejected. Address the rejection feedback or contact {support_email} before resubmitting.',
        ],
        'new_bid_on_listing' => [
            'audience' => 'seller',
            'body' => 'New bid on your vehicle {vehicle_name} (listing #{listing_number}). Current high bid is ${amount}.',
        ],
        'awaiting_buyer_payment' => [
            'audience' => 'seller',
            // N21
            'body' => "We're currently awaiting payment for listing #{listing_number}. We'll notify you as soon as payment is completed. Thank you for your patience.",
        ],
        'reserve_price_met' => [
            'audience' => 'seller',
            // N22
            'body' => 'Congratulations! Your reserve price for item (#{listing_number}) has been met. Keep up to date with this listing in the Auctions tab of your dashboard.',
        ],
        'auction_sold' => [
            'audience' => 'seller',
            'body' => 'Your auction for {vehicle_name} (listing #{listing_number}) ended and the item is sold. Winning bid: ${winning_bid_amount}. Check your seller dashboard for payout updates.',
        ],
        'auction_ended_reserve_not_met' => [
            'audience' => 'seller',
            'body' => 'Your auction for {vehicle_name} (listing #{listing_number}) has ended. The highest bid was ${winning_bid_amount} but did not meet your reserve of ${reserve_amount}. Eligible sellers may relist within CayMark guidelines.',
        ],
        'auction_closed_by_seller' => [
            'audience' => 'seller',
            'body' => 'Your live auction listing #{listing_number} ({vehicle_name}) was removed from CayMark before completion. Bidders have been notified where applicable.',
        ],
        'send_pickup_info' => [
            'audience' => 'seller',
            'body' => 'Payment received for {vehicle_name}. Please submit pickup details for the buyer in Messaging Center.',
        ],
        'transaction_completed_payout_pending' => [
            'audience' => 'seller',
            'body' => 'Transaction for {vehicle_name} is complete. Your payout is now processing and will be completed within 2-5 business days.',
        ],

        'support_ticket_submitted' => [
            'audience' => 'both',
            'body' => 'Support ticket #{ticket_number} was submitted. CayMark will respond to the email on your account. For follow-up, contact {support_email}.',
        ],
        'support_ticket_responded' => [
            'audience' => 'both',
            'body' => 'CayMark staff replied to support ticket #{ticket_number}. Sign in and open Support to read the update.',
        ],

        'password_changed' => [
            'audience' => 'both',
            'body' => 'Your CayMark account password was changed. If this was not you, reset your password immediately and contact {support_email}.',
        ],
        'email_updated' => [
            'audience' => 'both',
            'body' => 'The email address on your CayMark account was updated. If this was not you, contact {support_email} right away.',
        ],
        'payout_details_updated' => [
            'audience' => 'seller',
            'body' => 'Your seller payout details were updated in CayMark. Payouts will use the new information on file after verification.',
        ],

        'subscription_activated' => [
            'audience' => 'both',
            'body' => 'Your CayMark subscription ({package_name}) is active. Enjoy full access for your plan period.',
        ],
        'subscription_ending_soon' => [
            'audience' => 'both',
            // N24
            'body' => 'Your subscription is set to expire on {date}. To continue using CayMark without interruption, please renew your subscription before this date. We\'d hate to see you go.',
        ],
        'subscription_ended' => [
            'audience' => 'both',
            // N25
            'body' => 'Your subscription has expired. To regain access to all features, you can reactivate your current plan or choose a new one at any time. We\'d love to have you back on CayMark.',
        ],

        'login_new_device' => [
            'audience' => 'both',
            'body' => 'We noticed a successful sign-in to your CayMark account from a new device or location. If this was you, you can ignore this message. If not, change your password and contact {support_email}.',
        ],
        'login_attempt_unsuccessful' => [
            'audience' => 'both',
            'body' => 'A sign-in attempt to your CayMark account failed. If this was not you, consider changing your password.',
        ],
    ],
];
