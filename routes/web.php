<?php

// Staging / noindex site: block all crawlers via robots.txt
\Illuminate\Support\Facades\Route::get('robots.txt', function () {
    if (request()->getHost() === 'kaymark.360webcoders.com' || config('app.noindex')) {
        return response("User-agent: *\nDisallow: /", 200, ['Content-Type' => 'text/plain']);
    }
    return response()->file(public_path('robots.txt'));
});

use App\Models\Listing;
use App\Models\Package;
use Illuminate\Http\Request;
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
use App\Http\Controllers\Buyer\NotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorController;




Route::get('/', function () {
    return view('welcome');
})->name("welcome");

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();
    $role = strtolower(trim($user->role ?? ''));

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($role === 'seller') {
        return redirect()->route('seller.dashboard');
    }
    if ($role === 'buyer') {
        return redirect()->route('welcome');
    }

    // Guest / incomplete registration — send to basic dashboard without verified check
    return redirect()->route('dashboard.default');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Legacy /dashboard/seller?tab= → redirect to /seller/* (POST targets kept for old clients)
Route::get('/dashboard/seller', function (Request $request) {
    $tab = $request->query('tab', 'dashboard');
    $map = [
        'dashboard' => 'seller.dashboard',
        'user' => 'seller.account',
        'auctions' => 'seller.auctions',
        'submission' => 'seller.submission',
        'notifications' => 'seller.notifications',
        'support' => 'seller.support',
        'messaging' => 'seller.chat',
    ];
    $target = $map[$tab] ?? 'seller.dashboard';
    if ($tab === 'auctions' && $request->filled('section')) {
        return redirect()->to(route('seller.auctions').'?section='.urlencode((string) $request->query('section')));
    }

    return redirect()->route($target);
})->middleware(['auth', 'seller'])->name('dashboard.seller');
Route::post('/dashboard/seller/update-payout', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updatePayout'])->middleware(['auth', 'seller'])->name('seller-dashboard.update-payout');
Route::post('/dashboard/seller/change-password', [App\Http\Controllers\Seller\SellerDashboardController::class, 'changePassword'])->middleware(['auth', 'seller'])->name('seller-dashboard.change-password');
Route::post('/dashboard/seller/initiate-email-change', [App\Http\Controllers\Seller\SellerDashboardController::class, 'requestEmailChange'])->middleware(['auth', 'seller'])->name('seller.dashboard.initiate-email-change');
Route::post('/dashboard/seller/update-email', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updateEmail'])->middleware(['auth', 'seller'])->name('seller-dashboard.update-email');
Route::post('/dashboard/seller/cancel-email-change', [App\Http\Controllers\Seller\SellerDashboardController::class, 'cancelEmailChange'])->middleware(['auth', 'seller'])->name('seller.dashboard.cancel-email-change');
Route::post('/dashboard/seller/confirm-pickup/{listingId}', [App\Http\Controllers\Seller\SellerDashboardController::class, 'confirmPickup'])->middleware(['auth', 'seller'])->name('seller-dashboard.confirm-pickup');

Route::get('/dashboard/buyer', function (Request $request) {
    $tab = $request->query('tab', 'dashboard');
    $map = [
        'dashboard' => 'buyer.dashboard',
        'user' => 'buyer.user',
        'auctions' => 'buyer.auctions',
        'saved' => 'buyer.saved-items',
        'notifications' => 'buyer.notifications',
        'messaging' => 'buyer.messaging-center',
        'support' => 'buyer.customer-support',
    ];
    $target = $map[$tab] ?? 'buyer.dashboard';
    if ($tab === 'auctions' && $request->filled('section')) {
        return redirect()->to(route('buyer.auctions').'?section='.urlencode((string) $request->query('section')));
    }

    return redirect()->route($target);
})->middleware(['auth', 'buyer'])->name('dashboard.buyer');

Route::get('/dashboard/admin', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'admin'])->name('dashboard.admin');

Route::get('/dashboard/default', [App\Http\Controllers\BasicDashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard.default');
Route::post('/dashboard/default/update-email', [App\Http\Controllers\BasicDashboardController::class, 'updateEmail'])->middleware(['auth'])->name('basic-dashboard.update-email');
Route::post('/dashboard/default/change-password', [App\Http\Controllers\BasicDashboardController::class, 'changePassword'])->middleware(['auth'])->name('basic-dashboard.change-password');
Route::get('/my-documents/{document}', [App\Http\Controllers\BasicDashboardController::class, 'viewDocument'])->middleware(['auth'])->name('user.document.view');

Route::get('/sellers-guide', function () {
    return view('sellers-guide');
})->name('sellers-guide');


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

Route::get('/video-guide', function () {
    return view('video-guide');
})->name('video-guide');

Route::get('/policy', function () {
    return view('policy');
})->name('policy');

Route::get('/terms-of-service', function () {
    return view('policy'); // dedicated terms view can replace this later
})->name('terms.of.service');

Route::get('/privacy-policy', function () {
    return view('policy'); // dedicated privacy view can replace this later
})->name('privacy.policy');

// Tow Provider directory and signup (public)
Route::get('/tow-providers', [App\Http\Controllers\TowProviderController::class, 'index'])->name('tow-provider.index');
Route::get('/tow-providers/signup', [App\Http\Controllers\TowProviderSignupController::class, 'create'])->name('tow-provider.signup');
Route::post('/tow-providers/signup', [App\Http\Controllers\TowProviderSignupController::class, 'store'])->name('tow-provider.signup.store');
Route::get('/tow-providers/signup/thanks', [App\Http\Controllers\TowProviderSignupController::class, 'thanks'])->name('tow-provider.signup.thanks');



Route::middleware(['auth', 'buyer'])->prefix('buyer')->group(function () {
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('buyer.marketplace');
    Route::get('/bids', fn () => redirect()->route('buyer.auctions'))->name('buyer.bids');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('buyer.watchlist');
    Route::get('/purchases', fn () => redirect()->to(route('buyer.auctions').'?section=won'))->name('buyer.purchases');
    Route::get('/purchases/{invoice}', [App\Http\Controllers\Buyer\PurchaseController::class, 'show'])->name('buyer.purchase.show');
    Route::get('/auctions-won', fn () => redirect()->to(route('buyer.auctions').'?section=won'))->name('buyer.auctions-won');
    Route::get('/invoices/{invoice}/download', [App\Http\Controllers\Buyer\PurchaseController::class, 'downloadInvoice'])->name('buyer.invoice.download');

    // (Messaging Center routes are registered globally below under /messaging.)

    // Payment Checkout (Path A: Single item, Path B: Multiple items)
    Route::get('/payment/checkout/{invoiceId}', [App\Http\Controllers\Buyer\PaymentController::class, 'checkoutSingle'])->name('buyer.payment.checkout-single');
    Route::get('/payment/success/{invoice}', [App\Http\Controllers\Buyer\PaymentController::class, 'paymentSuccess'])->name('buyer.payment.success');
    Route::post('/payment/checkout/multiple', [App\Http\Controllers\Buyer\PaymentController::class, 'checkoutMultiple'])->name('buyer.payment.checkout-multiple');
    Route::post('/payment/process', [App\Http\Controllers\Buyer\PaymentController::class, 'processPayment'])->name('buyer.payment.process');

    Route::get('/deposit-withdrawal', [App\Http\Controllers\Buyer\DepositWithdrawalController::class, 'index'])->name('buyer.deposit-withdrawal');
    Route::post('/deposit-withdrawal/add', [App\Http\Controllers\Buyer\DepositWithdrawalController::class, 'addDeposit'])->name('buyer.deposit.add');
    Route::post('/deposit-withdrawal/request', [App\Http\Controllers\Buyer\DepositWithdrawalController::class, 'requestWithdrawal'])->name('buyer.deposit-withdrawal.request');
    Route::get('/profile', [App\Http\Controllers\Buyer\ProfileController::class, 'index'])->name('buyer.profile');
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

// listing.show is defined below with optional slug: /listing/{id}/{slug?}
// Removed duplicate get-models route - already defined above and in seller routes


// Removed subscription/plans route - no longer needed after registration flow
// Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])
//     ->name('subscription.plans');

Route::get('/subscription/simulate', [SubscriptionController::class, 'simulate'])->name('subscription.simulate');

    Route::get('/AuctionPage',[AuctionController::class, 'index'])->name("Auction.index");
    Route::get('/auction-suggest', [AuctionController::class, 'suggest'])->name('auction.suggest');

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

// Buy Now (auth required; role checks handled inside controller)
Route::post('/auction/{listing:slug}/buy-now', [App\Http\Controllers\Buyer\BuyNowController::class, 'process'])
    ->middleware('auth')
    ->name('auction.buy-now');


Route::get('/auction/{id}/{slug}', [AuctionController::class, 'auctionDetailBuyer'])->name('auction.dashboard');
Route::get('/listing/{id}/{slug?}', [ListingController::class, 'listingDetailBuyer'])->name('listing.show');



Route::get('/register', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/step1', [RegisteredUserController::class, 'step1'])->name('register.step1');
Route::post('/register/step2', [RegisteredUserController::class, 'step2'])->name('register.step2');
Route::post('/register/step3', [RegisteredUserController::class, 'step3'])->name('register.step3');
Route::post('/register/back', [RegisteredUserController::class, 'back'])->name('register.back');

// ── GET guards: redirect back-button / direct-URL navigation on POST-only registration step endpoints ──
Route::get('/register/step1', fn () => redirect()->route('register'))->name('register.step1.get');
Route::get('/register/step2', fn () => redirect()->route('register'))->name('register.step2.get');
Route::get('/register/step3', fn () => redirect()->route('register'))->name('register.step3.get');
Route::get('/register/back',  fn () => redirect()->route('register'))->name('register.back.get');

// Upgrade membership (casual seller → business seller) — single-page form
Route::get('/upgrade-membership',  [RegisteredUserController::class, 'upgradeMembership'])->middleware('auth')->name('upgrade.membership');
Route::post('/upgrade-membership', [RegisteredUserController::class, 'processUpgradeMembership'])->middleware('auth')->name('upgrade.membership.submit');

// Finish Registration Routes (for users who created basic account)
Route::get('/finish-registration', [RegisteredUserController::class, 'finishRegistration'])->middleware('auth')->name('finish.registration');
Route::post('/finish-registration/membership', [RegisteredUserController::class, 'storeMembership'])->middleware('auth')->name('finish.registration.membership');
Route::get('/finish-registration/complete', [RegisteredUserController::class, 'showCompleteRegistration'])->middleware('auth')->name('finish.registration.complete.show');
// POST uses a distinct path so nginx/LiteSpeed never confuses it with the cached GET at /finish-registration/complete
Route::post('/finish-registration/submit', [RegisteredUserController::class, 'completeRegistration'])->middleware('auth')->name('finish.registration.complete');

// ── GET guards: redirect back-button / direct-URL navigation on POST-only finish-registration endpoints ──
Route::get('/finish-registration/membership', fn () => redirect()->route('finish.registration'))->middleware('auth')->name('finish.registration.membership.get');
Route::get('/finish-registration/submit',     fn () => redirect()->route('finish.registration'))->middleware('auth')->name('finish.registration.submit.get');

Route::post('/registration/phone/send-code', [RegisteredUserController::class, 'sendPhoneVerificationCode'])->middleware('auth')->name('registration.phone.send-code');
Route::post('/registration/phone/verify', [RegisteredUserController::class, 'verifyPhoneCode'])->middleware('auth')->name('registration.phone.verify');
// Guest phone verification (register page, before user exists)
Route::post('/register/phone/send-code', [RegisteredUserController::class, 'sendGuestPhoneCode'])->name('register.phone.send-code');
Route::post('/register/phone/verify', [RegisteredUserController::class, 'verifyGuestPhoneCode'])->name('register.phone.verify');

Route::get('/listings/models/{make}', [ListingController::class, 'getModels'])->middleware(['auth', 'seller'])->name('seller.listings.getModels');


// Admin 2FA (auth only – no admin middleware so admin can complete 2FA before accessing panel)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('2fa/challenge', [TwoFactorController::class, 'showChallenge'])->name('admin.2fa.challenge');
    Route::post('2fa/verify', [TwoFactorController::class, 'verifyChallenge'])->name('admin.2fa.verify');
    Route::get('2fa/setup', [TwoFactorController::class, 'showSetup'])->name('admin.2fa.setup');
    Route::post('2fa/confirm', [TwoFactorController::class, 'confirmSetup'])->name('admin.2fa.confirm');
});

// Admin Routes (auth + role=admin required, 2FA verified)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'userManagement'])->name('admin.users');
    Route::get('/memberships', [AdminController::class, 'membershipManagement'])->name('admin.memberships');
    Route::get('/listing-review', [AdminController::class, 'listingReview'])->name('admin.listing-review');
    Route::get('/active-listings', [AdminController::class, 'activeListings'])->name('admin.active-listings');
    Route::get('/boosts-addons', [AdminController::class, 'boostsAddOns'])->name('admin.boosts-addons');
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/pending-payments', [AdminController::class, 'pendingPayments'])->name('admin.pending-payments');
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
    Route::get('/security-deposits', [AdminController::class, 'securityDeposits'])->name('admin.security-deposits');
    Route::post('/deposit-requests/{depositRequest}/confirm', [AdminController::class, 'confirmDepositWire'])->name('admin.deposit-requests.confirm');
    Route::post('/deposit-requests/{depositRequest}/reject', [AdminController::class, 'rejectDepositRequest'])->name('admin.deposit-requests.reject');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawals.reject');
    Route::post('/deposits/{user}/add', [AdminController::class, 'adminAddDeposit'])->name('admin.deposits.add');

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

    // Support Tickets Management
    Route::get('/support-tickets', [AdminController::class, 'supportTickets'])->name('admin.support-tickets');
    Route::post('/support-tickets/{ticket}/reply', [AdminController::class, 'replyToTicket'])->name('admin.support-tickets.reply');

    // Email Template Management
    Route::get('/email-templates', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])->name('admin.email-templates');
    Route::get('/email-templates/{template}/edit', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/email-templates/{template}', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])->name('admin.email-templates.update');
    Route::get('/email-templates/{template}/preview', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])->name('admin.email-templates.preview');
    Route::post('/email-templates/{template}/restore', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'restoreDefault'])->name('admin.email-templates.restore');
    Route::post('/email-templates/{template}/toggle', [\App\Http\Controllers\Admin\EmailTemplateController::class, 'toggle'])->name('admin.email-templates.toggle');
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

// Messaging Center (post-auction pickup coordination) — buyer + seller share these routes;
// the controller verifies the authenticated user is the buyer or seller of the invoice.
Route::middleware('auth')->prefix('messaging')->name('messaging.')->group(function () {
    Route::get('/', [App\Http\Controllers\MessagingCenterController::class, 'index'])->name('index');
    Route::get('/thread/{invoiceId}', [App\Http\Controllers\MessagingCenterController::class, 'show'])->name('thread.show');

    Route::post('/thread/{threadId}/send-pickup-details',   [App\Http\Controllers\MessagingCenterController::class, 'sendPickupDetails'])->name('thread.send-pickup-details');
    Route::post('/thread/{threadId}/accept-pickup',         [App\Http\Controllers\MessagingCenterController::class, 'acceptPickupDetails'])->name('thread.accept-pickup');
    Route::post('/thread/{threadId}/request-change',        [App\Http\Controllers\MessagingCenterController::class, 'requestPickupChange'])->name('thread.request-change');
    Route::post('/thread/{threadId}/request-location',      [App\Http\Controllers\MessagingCenterController::class, 'requestLocationChange'])->name('thread.request-location');
    Route::post('/thread/{threadId}/request-delivery',      [App\Http\Controllers\MessagingCenterController::class, 'requestDelivery'])->name('thread.request-delivery');
    Route::post('/change-request/{id}/respond',             [App\Http\Controllers\MessagingCenterController::class, 'respondToChangeRequest'])->name('change.respond');
    Route::post('/delivery-request/{id}/respond',           [App\Http\Controllers\MessagingCenterController::class, 'respondToDeliveryRequest'])->name('delivery.respond');
    Route::post('/thread/{threadId}/authorize-third-party', [App\Http\Controllers\MessagingCenterController::class, 'authorizeThirdPartyPickup'])->name('thread.authorize-third-party');
    Route::post('/thread/{threadId}/confirm-pickup',        [App\Http\Controllers\MessagingCenterController::class, 'confirmPickupWithPin'])->name('thread.confirm-pickup');
    Route::post('/thread/{threadId}/seller-phone',          [App\Http\Controllers\MessagingCenterController::class, 'updateSellerPhone'])->name('thread.seller-phone');

    // extra actions surfaced by the mockups
    Route::post('/thread/{threadId}/other-request',          [App\Http\Controllers\MessagingCenterController::class, 'otherRequest'])->name('thread.other-request');
    Route::post('/thread/{threadId}/report-issue',           [App\Http\Controllers\MessagingCenterController::class, 'reportIssue'])->name('thread.report-issue');
    Route::post('/thread/{threadId}/request-assistance',     [App\Http\Controllers\MessagingCenterController::class, 'requestAssistance'])->name('thread.request-assistance');
    Route::post('/thread/{threadId}/resend-schedule',        [App\Http\Controllers\MessagingCenterController::class, 'resendSchedule'])->name('thread.resend-schedule');
    Route::post('/thread/{threadId}/mark-ready-for-pickup',  [App\Http\Controllers\MessagingCenterController::class, 'markReadyForPickup'])->name('thread.mark-ready');
    Route::post('/thread/{threadId}/confirm-sale-completed', [App\Http\Controllers\MessagingCenterController::class, 'confirmSaleCompleted'])->name('thread.confirm-sale');
});

// Back-compat: legacy post-auction.thread URL redirects to the new Messaging Center route.
Route::redirect('/buyer/post-auction/thread/{invoiceId}', '/messaging/thread/{invoiceId}')
    ->name('post-auction.thread');

// Admin: flagged messaging threads dashboard
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/messaging-flags',                [App\Http\Controllers\Admin\MessagingFlagController::class, 'index'])->name('messaging.flags.index');
    Route::get('/messaging-flags/{threadId}',     [App\Http\Controllers\Admin\MessagingFlagController::class, 'show'])->name('messaging.flags.show');
    Route::post('/messaging-flags/{threadId}/unflag', [App\Http\Controllers\Admin\MessagingFlagController::class, 'unflag'])->name('messaging.flags.unflag');
});

// Include modular route files
require __DIR__.'/seller.php';
require __DIR__.'/buyer.php';

require __DIR__.'/auth.php';
