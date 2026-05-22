<?php

/**
 * Route-name → breadcrumb trail mappings.
 * Last item should omit url/route (current page).
 * Placeholders like {listing_title} resolve from view-shared models.
 */
return [

    'placeholders' => [
        'listing_title' => ['auctionListing', 'listing'],
    ],

    'routes' => [

        // Public
        'welcome' => [],
        'Auction.index' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Auctions'],
        ],
        'auction.show' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Auctions', 'route' => 'Auction.index'],
            ['label' => '{listing_title}'],
        ],
        'auction.dashboard' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Auctions', 'route' => 'Auction.index'],
            ['label' => '{listing_title}'],
        ],
        'listing.show' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Auctions', 'route' => 'Auction.index'],
            ['label' => '{listing_title}'],
        ],
        'marketplace.index' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Marketplace'],
        ],
        'about.index' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'About Us'],
        ],
        'contact' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Contact Us'],
        ],
        'buyer-guide' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Getting Started'],
        ],
        'sellers-guide' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => "Seller's Guide"],
        ],
        'enterprise-seller' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Enterprise Seller'],
        ],
        'fee-calculator' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Fee Calculator'],
        ],
        'help-center' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Help Center'],
        ],
        'video-guide' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Video Guides'],
        ],
        'policy' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Policy'],
        ],
        'terms.of.service' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Terms of Service'],
        ],
        'privacy.policy' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Privacy Policy'],
        ],
        'tow-provider.index' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Tow Providers'],
        ],
        'tow-provider.signup' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Tow Providers', 'route' => 'tow-provider.index'],
            ['label' => 'Sign Up'],
        ],

        // Auth
        'login' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Sign In'],
        ],
        'register' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Register'],
        ],
        'password.request' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Forgot Password'],
        ],
        'password.reset' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Reset Password'],
        ],
        'verification.notice' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Verify Email'],
        ],
        'finish.registration' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Complete Registration'],
        ],

        // Buyer dashboard
        'buyer.dashboard' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Buyer Dashboard'],
        ],
        'buyer.user' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Account Settings'],
        ],
        'buyer.auctions' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Auctions'],
        ],
        'buyer.saved-items' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Saved Items'],
        ],
        'buyer.notifications' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Notifications'],
        ],
        'buyer.messaging-center' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Messaging Center'],
        ],
        'buyer.customer-support' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Customer Support'],
        ],
        'buyer.watchlist' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Watchlist'],
        ],
        'buyer.marketplace' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Marketplace'],
        ],
        'buyer.deposit-withdrawal' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'buyer.dashboard'],
            ['label' => 'Deposit & Withdrawal'],
        ],

        // Seller dashboard
        'seller.dashboard' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Seller Dashboard'],
        ],
        'seller.account' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Account Settings'],
        ],
        'seller.auctions' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Auctions'],
        ],
        'seller.submission' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Submissions'],
        ],
        'seller.notifications' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Notifications'],
        ],
        'seller.support' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Support'],
        ],
        'seller.listings.create' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Submit Listing'],
        ],
        'seller.listings.success' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Listing Submitted'],
        ],
        'seller.Auction.index' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Auctions'],
        ],
        'seller.payouts' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Payouts'],
        ],
        'seller.payout-method' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Payout Method'],
        ],
        'seller.chat' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Dashboard', 'route' => 'seller.dashboard'],
            ['label' => 'Messaging'],
        ],

        // Admin
        'admin.dashboard' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin Dashboard'],
        ],
        'admin.users' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'User Management'],
        ],
        'admin.listing-review' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Listing Review'],
        ],
        'admin.active-listings' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Active Listings'],
        ],
        'admin.payments' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Payments'],
        ],
        'admin.disputes' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Disputes'],
        ],
        'admin.notifications' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Notifications'],
        ],
        'admin.reports-analytics' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Reports & Analytics'],
        ],
        'admin.auctions' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Auction Management'],
        ],
        'admin.support-tickets' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Admin', 'route' => 'admin.dashboard'],
            ['label' => 'Support Tickets'],
        ],
        'messaging.index' => [
            ['label' => 'Home', 'route' => 'welcome'],
            ['label' => 'Messaging Center'],
        ],
    ],
];
