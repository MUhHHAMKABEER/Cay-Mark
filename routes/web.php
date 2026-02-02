<?php


use App\Models\Listing;
use App\Models\Package;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\chatController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BuyerController;


use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Buyer\BidController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\Buyer\EscrowController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Buyer\AuctionController;
use App\Http\Controllers\Buyer\SupportController;
use App\Http\Controllers\Buyer\PurchaseController;
use App\Http\Controllers\Seller\ListingController;
use App\Http\Controllers\Buyer\MarketplaceController;
use App\Http\Controllers\Buyer\BuyerMessageController;
use App\Http\Controllers\Buyer\NotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;




Route::get('/', function () {
    return view('welcome');
})->name("welcome");

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Legacy dashboard routes (for backward compatibility)
Route::get('/dashboard/seller', [App\Http\Controllers\Seller\SellerDashboardController::class, 'index'])->middleware(['auth'])->name('dashboard.seller');
Route::post('/dashboard/seller/update-payout', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updatePayout'])->middleware(['auth'])->name('seller-dashboard.update-payout');
Route::post('/dashboard/seller/change-password', [App\Http\Controllers\Seller\SellerDashboardController::class, 'changePassword'])->middleware(['auth'])->name('seller-dashboard.change-password');
Route::post('/dashboard/seller/confirm-pickup/{listingId}', [App\Http\Controllers\Seller\SellerDashboardController::class, 'confirmPickup'])->middleware(['auth'])->name('seller-dashboard.confirm-pickup');

Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
})->name('dashboard.admin');

Route::get('/dashboard/default', [App\Http\Controllers\BasicDashboardController::class, 'index'])->middleware(['auth'])->name('dashboard.default');
Route::post('/dashboard/default/update-email', [App\Http\Controllers\BasicDashboardController::class, 'updateEmail'])->middleware(['auth'])->name('basic-dashboard.update-email');
Route::post('/dashboard/default/change-password', [App\Http\Controllers\BasicDashboardController::class, 'changePassword'])->middleware(['auth'])->name('basic-dashboard.change-password');

Route::get('/sellers-guide', function () {
    return view('sellers-guide');
})->name('sellers-guide');

Route::get('/enterprise-seller', function () {
    return view('enterprise-seller');
})->name('enterprise-seller');



Route::get('/buyer-guide', function () {
    return view('buyer-guide');
})->name('buyer-guide');




Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/fee-calculator', function () {
    return view('fee-calculator');
})->name('fee-calculator');

Route::get('/help-center', function () {
    return view('help-center');
})->name('help-center');

Route::get('/rules-policies', function () {
    return view('rules-policies');
})->name('rules-policies');



Route::middleware(['auth',
// 'is_buyer'
])->prefix('buyer')->group(function () {
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('buyer.marketplace');
    Route::get('/auctions', [AuctionController::class, 'index'])->name('buyer.auctions');
    Route::get('/bids', [App\Http\Controllers\Buyer\BidController::class, 'index'])->name('buyer.bids');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('buyer.watchlist');
    Route::get('/purchases', [App\Http\Controllers\Buyer\PurchaseController::class, 'index'])->name('buyer.purchases');
    Route::get('/auctions-won', [App\Http\Controllers\Buyer\PurchaseController::class, 'index'])->name('buyer.auctions-won');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\Buyer\PurchaseController::class, 'downloadInvoice'])->name('buyer.invoice.download');
    
    // Post-Auction Messaging & Pickup Coordination
    Route::get('/post-auction/thread/{invoiceId}', [App\Http\Controllers\PostAuctionMessageController::class, 'showThread'])->name('post-auction.thread');
    Route::post('/post-auction/thread/{threadId}/send-pickup-details', [App\Http\Controllers\PostAuctionMessageController::class, 'sendPickupDetails'])->name('post-auction.send-pickup-details');
    Route::post('/post-auction/thread/{threadId}/accept-pickup', [App\Http\Controllers\PostAuctionMessageController::class, 'acceptPickupDetails'])->name('post-auction.accept-pickup');
    Route::post('/post-auction/thread/{threadId}/request-change', [App\Http\Controllers\PostAuctionMessageController::class, 'requestPickupChange'])->name('post-auction.request-change');
    Route::post('/post-auction/change-request/{changeRequestId}/respond', [App\Http\Controllers\PostAuctionMessageController::class, 'respondToChangeRequest'])->name('post-auction.respond-change');
    Route::post('/post-auction/thread/{threadId}/authorize-third-party', [App\Http\Controllers\PostAuctionMessageController::class, 'authorizeThirdPartyPickup'])->name('post-auction.authorize-third-party');
    Route::post('/post-auction/thread/{threadId}/confirm-pickup', [App\Http\Controllers\PostAuctionMessageController::class, 'confirmPickupWithPin'])->name('post-auction.confirm-pickup');
    
    // Payment Checkout (Path A: Single item, Path B: Multiple items)
    Route::get('/payment/checkout/{invoiceId}', [App\Http\Controllers\Buyer\PaymentController::class, 'checkoutSingle'])->name('buyer.payment.checkout-single');
    Route::post('/payment/checkout/multiple', [App\Http\Controllers\Buyer\PaymentController::class, 'checkoutMultiple'])->name('buyer.payment.checkout-multiple');
    Route::post('/payment/process', [App\Http\Controllers\Buyer\PaymentController::class, 'processPayment'])->name('buyer.payment.process');
    
    Route::get('/deposit-withdrawal', [App\Http\Controllers\Buyer\DepositWithdrawalController::class, 'index'])->name('buyer.deposit-withdrawal');
    Route::post('/deposit-withdrawal/request', [App\Http\Controllers\Buyer\DepositWithdrawalController::class, 'requestWithdrawal'])->name('buyer.deposit-withdrawal.request');
    Route::get('/profile', [App\Http\Controllers\Buyer\ProfileController::class, 'index'])->name('buyer.profile');
    Route::get('/notifications', [App\Http\Controllers\Buyer\NotificationController::class, 'index'])->name('buyer.notifications');
    Route::get('/support', [App\Http\Controllers\Buyer\SupportController::class, 'index'])->name('buyer.support');
    Route::post('/support/submit', [App\Http\Controllers\Buyer\SupportController::class, 'store'])->name('buyer.support.submit');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/first-login', [BuyerController::class, 'markFirstLogin'])->name('user.markFirstLogin');
});



// Seller routes are now in routes/seller.php

Route::get('/marketplaces', [App\Http\Controllers\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/About-us', [AboutController::class, 'index'])->name('about.index');

Route::get('/get-models/{make}', [ListingController::class, 'getModels']);

Route::prefix('admin')->name('admin.')->group(function (){

    Route::get('/seller/lisitng',[AdminController::class ,'adminListing'])->name("show.listing");
    Route::put('/listings/{id}/approve', [AdminController::class, 'approve'])->name('listing.approve');
Route::put('/listings/{id}/disapprove', [AdminController::class, 'disapprove'])->name('listing.disapprove');

});


Route::get('/buy-now/{id}', [ListingController::class, 'buyNowGuest'])->name('buy_now.guest');
Route::get('/buyer/dashboard', [ListingController::class, 'showBuyerDashboard'])
    ->name('buyer.dashboard')
    ->middleware('auth'); // Ensure only logged-in users



// Removed duplicate routes - already defined in buyer prefix group above

Route::get('/listing/{id}', [ListingController::class, 'show'])->name('listing.show');
// Removed duplicate get-models route - already defined above and in seller routes


// Removed subscription/plans route - no longer needed after registration flow
// Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])
//     ->name('subscription.plans');

Route::get('/subscription/simulate', [SubscriptionController::class, 'simulate'])->name('subscription.simulate');

    Route::get('/AuctionPage',[AuctionController::class, 'index'])->name("Auction.index");

Route::middleware(['auth'])->group(function () {
    Route::post('/listing/{listing}/watchlist', [WatchlistController::class, 'toggle'])->name('listing.watchlist');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
});

// Single auction listing page (using slug for SEO)
Route::get('/auction/{listing:slug}', [AuctionController::class, 'show'])->name('auction.show');
// show listing (you already have)

// store bid (only for logged-in users)
Route::post('/auction/{listing:slug}/bid', [AuctionController::class, 'storeBid'])
    ->middleware('auth')
    ->name('auction.bid.store');


Route::get('/auction/{id}/{slug}', [AuctionController::class, 'auctionDetailBuyer'])->name('auction.dashboard');
Route::get('/listing/{id}/{slug?}', [ListingController::class, 'listingDetailBuyer'])->name('listing.show');



Route::get('/register', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/step1', [RegisteredUserController::class, 'step1'])->name('register.step1');
Route::post('/register/step2', [RegisteredUserController::class, 'step2'])->name('register.step2');
Route::post('/register/step3', [RegisteredUserController::class, 'step3'])->name('register.step3');
Route::post('/register/back', [RegisteredUserController::class, 'back'])->name('register.back');

// Finish Registration Routes (for users who created basic account)
Route::get('/finish-registration', [RegisteredUserController::class, 'finishRegistration'])->middleware('auth')->name('finish.registration');
Route::post('/finish-registration/membership', [RegisteredUserController::class, 'storeMembership'])->middleware('auth')->name('finish.registration.membership');
Route::get('/finish-registration/complete', [RegisteredUserController::class, 'showCompleteRegistration'])->middleware('auth')->name('finish.registration.complete.show');
Route::post('/finish-registration/complete', [RegisteredUserController::class, 'completeRegistration'])->middleware('auth')->name('finish.registration.complete');


Route::get('/listings/models/{make}', [ListingController::class, 'getModels'])->name('seller.listings.getModels');



// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'userManagement'])->name('admin.users');
    Route::get('/memberships', [AdminController::class, 'membershipManagement'])->name('admin.memberships');
    Route::get('/listing-review', [AdminController::class, 'listingReview'])->name('admin.listing-review');
    Route::get('/active-listings', [AdminController::class, 'activeListings'])->name('admin.active-listings');
    Route::get('/boosts-addons', [AdminController::class, 'boostsAddOns'])->name('admin.boosts-addons');
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/disputes', [AdminController::class, 'disputes'])->name('admin.disputes');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::get('/reports-analytics', [AdminController::class, 'reportsAnalytics'])->name('admin.reports-analytics');
    Route::get('/user-activity-insights', [AdminController::class, 'userActivityInsights'])->name('admin.user-activity-insights');
    Route::get('/revenue-tracking', [AdminController::class, 'revenueTracking'])->name('admin.revenue-tracking');
    Route::get('/revenue-tracking/export', [AdminController::class, 'exportRevenue'])->name('admin.revenue-tracking.export');

    // Action routes
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('admin.users.suspend');
    Route::post('/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
    Route::post('/listings/{listing}/approve', [AdminController::class, 'approveListing'])->name('admin.listings.approve');
    Route::post('/listings/{listing}/reject', [AdminController::class, 'rejectListing'])->name('admin.listings.reject');
    Route::post('/payments/{payment}/release', [AdminController::class, 'releasePayment'])->name('admin.payments.release');
    Route::post('/payments/{payment}/hold', [AdminController::class, 'holdPayment'])->name('admin.payments.hold');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawals.reject');
    
    // Finance/Admin Payout Management
    Route::get('/payouts', [AdminController::class, 'payoutManagement'])->name('admin.payouts');
    Route::post('/payouts/{payout}/update-status', [AdminController::class, 'updatePayoutStatus'])->name('admin.payouts.update-status');
    Route::get('/payment-payout-logs', [AdminController::class, 'paymentPayoutLogs'])->name('admin.payment-payout-logs');
    
    // Invoice Log (per PDF requirements)
    Route::get('/invoice-log', [AdminController::class, 'invoiceLog'])->name('admin.invoice-log');
    Route::get('/invoices/{invoice}/download', [AdminController::class, 'downloadInvoice'])->name('admin.invoice.download');
    
    // Buyer Default & Non-Payment Management (per PDF requirements)
    Route::get('/unpaid-auctions', [AdminController::class, 'unpaidAuctions'])->name('admin.unpaid-auctions');
    Route::get('/buyer-defaults', [AdminController::class, 'buyerDefaults'])->name('admin.buyer-defaults');
    Route::post('/defaults/{default}/resolve-relist', [AdminController::class, 'resolveDefaultByRelist'])->name('admin.defaults.resolve-relist');
    Route::post('/defaults/{default}/offer-second-chance', [AdminController::class, 'offerToSecondHighestBidder'])->name('admin.defaults.offer-second-chance');
    Route::post('/defaults/{default}/close', [AdminController::class, 'closeUnpaidAuction'])->name('admin.defaults.close');
    Route::get('/second-chance-purchases', [AdminController::class, 'secondChancePurchases'])->name('admin.second-chance-purchases');
    Route::post('/second-chance/{secondChance}/generate-invoice', [AdminController::class, 'generateSecondChanceInvoice'])->name('admin.second-chance.generate-invoice');
    
    // Enhanced Admin Routes
    Route::get('/dashboard/analytics', [AdminController::class, 'analyticsDashboard'])->name('admin.dashboard.analytics');
    Route::get('/users/{id}', [AdminController::class, 'viewUser'])->name('admin.users.view');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('/users/{id}/reset-password', [AdminController::class, 'resetUserPassword'])->name('admin.users.reset-password');
    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    
    Route::get('/listings/{id}/approval', [AdminController::class, 'viewListingForApproval'])->name('admin.listings.approval-detail');
    Route::post('/listings/{id}/edit', [AdminController::class, 'editListing'])->name('admin.listings.edit');
    Route::post('/listings/{id}/extend-auction', [AdminController::class, 'extendAuctionTime'])->name('admin.listings.extend-auction');
    Route::post('/listings/{id}/toggle-status', [AdminController::class, 'toggleListingStatus'])->name('admin.listings.toggle-status');
    Route::delete('/listings/{id}', [AdminController::class, 'deleteListing'])->name('admin.listings.delete');
    
    Route::get('/auctions', [AdminController::class, 'auctionManagement'])->name('admin.auctions');
    Route::get('/auctions/{id}/bidding-logs', [AdminController::class, 'viewBiddingLogs'])->name('admin.auctions.bidding-logs');
    Route::post('/auctions/{id}/cancel', [AdminController::class, 'cancelAuction'])->name('admin.auctions.cancel');
    Route::post('/auctions/{id}/toggle-status', [AdminController::class, 'toggleAuctionStatus'])->name('admin.auctions.toggle-status');
    Route::post('/bids/{id}/remove', [AdminController::class, 'removeBid'])->name('admin.bids.remove');
    
    Route::post('/payments/{id}/update-status', [AdminController::class, 'updatePaymentStatus'])->name('admin.payments.update-status');
    Route::post('/payments/{id}/regenerate-invoice', [AdminController::class, 'regenerateInvoice'])->name('admin.payments.regenerate-invoice');
    
    Route::get('/disputes/{id}', [AdminController::class, 'viewDispute'])->name('admin.disputes.view');
    Route::post('/disputes/{id}/update-status', [AdminController::class, 'updateDisputeStatus'])->name('admin.disputes.update-status');
    
    Route::post('/notifications/{id}/resend', [AdminController::class, 'resendNotification'])->name('admin.notifications.resend');
    
    // Email Template Management
    Route::get('/email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('admin.email-templates');
    Route::get('/email-templates/{template}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/email-templates/{template}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('admin.email-templates.update');
    Route::get('/email-templates/{template}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('admin.email-templates.preview');
    Route::post('/email-templates/{template}/restore', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'restoreDefault'])->name('admin.email-templates.restore');
});

// API route for packages
Route::get('/api/packages/{role}', function($role) {
    return \App\Models\Package::forRole($role)->get();
});



// routes/web.php
Route::post('/listing/{id}/buy', [CheckoutController::class, 'buyNow'])->name('listing.buy');


// Note: ChatController only has 'chat' and 'sendMessage' methods
// If chats.index and chats.show routes are needed, add those methods to ChatController
// Route::middleware('auth')->group(function () {
//     Route::get('/chats', [chatController::class, 'index'])->name('chats.index');
//     Route::get('/chats/{chat}', [chatController::class, 'show'])->name('chats.show');
//     Route::post('/chats/{chat}/messages', [MessageController::class, 'store'])->name('messages.store');
// });



// Buyer and Seller routes are now in routes/buyer.php and routes/seller.php

// Include modular route files
require __DIR__.'/seller.php';
require __DIR__.'/buyer.php';

require __DIR__.'/auth.php';
