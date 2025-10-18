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


Route::get('/dashboard/buyer', function () {
    return view('dashboard.buyer');
})->name('dashboard.buyer');

Route::get('/dashboard/seller', function () {
    return view('dashboard.seller');
})->name('dashboard.seller');

Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
})->name('dashboard.admin');

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



Route::middleware(['auth',
// 'is_buyer'
])->prefix('buyer')->group(function () {
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('buyer.marketplace');
    Route::get('/auctions', [AuctionController::class, 'index'])->name('buyer.auctions');
    Route::get('/bids', [App\Http\Controllers\Buyer\BidController::class, 'index'])->name('buyer.bids');
    Route::get('/watchlist', [App\Http\Controllers\Buyer\WatchlistController::class, 'index'])->name('buyer.watchlist');
    Route::get('/purchases', [App\Http\Controllers\Buyer\PurchaseController::class, 'index'])->name('buyer.purchases');
    Route::get('/escrow', [App\Http\Controllers\Buyer\EscrowController::class, 'index'])->name('buyer.escrow');
    Route::get('/profile', [App\Http\Controllers\Buyer\ProfileController::class, 'index'])->name('buyer.profile');
    Route::get('/notifications', [App\Http\Controllers\Buyer\NotificationController::class, 'index'])->name('buyer.notifications');
    Route::get('/support', [App\Http\Controllers\Buyer\SupportController::class, 'index'])->name('buyer.support');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/first-login', [BuyerController::class, 'markFirstLogin'])->name('user.markFirstLogin');
});



Route::prefix('seller')->name('seller.')->group(function () {
    // Create Listing form
    Route::get('listings/create', [ListingController::class, 'create'])->name('listings.create');

    // Store new Listing
    Route::post('submit-listings', [ListingController::class, 'store'])->name('listings.store');
     Route::get('listings', [ListingController::class, 'showListing'])->name('listings.index');
     Route::get('Auction', [ListingController::class, 'showAuctionLisitng'])->name('Auction.index');
     Route::get('Seller_Chat', [chatController::class, 'chat'])->name('chat');

});

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



Route::prefix('buyer')->middleware(['auth'])->group(function () {
    Route::get('/bids', [BidController::class, 'bids'])->name('buyer.bids');
    Route::get('/watchlist', [BidController::class, 'watchlist'])->name('buyer.watchlist');
});

Route::get('/listing/{id}', [ListingController::class, 'show'])->name('listing.show');
Route::get('/get-models/{make}', [ListingController::class, 'getModels'])->name('get.models');


Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])
    ->name('subscription.plans');

    Route::get('/subscription/simulate', [SubscriptionController::class, 'simulate'])->name('subscription.simulate');

    Route::get('/AuctionPage',[AuctionController::class, 'index'])->name("Auction.index");

    Route::middleware(['auth'])->group(function () {
    Route::post('/listing/{id}/watchlist', [ListingController::class, 'addToWatchlist'])->name('listing.watchlist');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/listing/{listing}/watchlist', [WatchlistController::class, 'toggle'])->name('listing.watchlist');
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
});

// Single auction listing page
Route::get('/auction/{id}', [AuctionController::class, 'show'])->name('auction.show');
// show listing (you already have)

// store bid (only for logged-in users)
Route::post('/auction/{id}/bid', [AuctionController::class, 'storeBid'])
    ->middleware('auth')
    ->name('auction.bid.store');


Route::get('/auction/{id}/{slug}', [AuctionController::class, 'auctionDetailBuyer'])->name('auction.dashboard');
Route::get('/listing/{id}/{slug?}', [ListingController::class, 'listingDetailBuyer'])->name('listing.show');



Route::get('/register', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register/step1', [RegisteredUserController::class, 'step1'])->name('register.step1');
Route::post('/register/step2', [RegisteredUserController::class, 'step2'])->name('register.step2');
Route::post('/register/step3', [RegisteredUserController::class, 'step3'])->name('register.step3');
Route::post('/register/back', [RegisteredUserController::class, 'back'])->name('register.back');


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

    // Action routes
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('admin.users.suspend');
    Route::post('/users/{user}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
    Route::post('/listings/{listing}/approve', [AdminController::class, 'approveListing'])->name('admin.listings.approve');
    Route::post('/listings/{listing}/reject', [AdminController::class, 'rejectListing'])->name('admin.listings.reject');
    Route::post('/payments/{payment}/release', [AdminController::class, 'releasePayment'])->name('admin.payments.release');
    Route::post('/payments/{payment}/hold', [AdminController::class, 'holdPayment'])->name('admin.payments.hold');
});

// API route for packages
Route::get('/api/packages/{role}', function($role) {
    return \App\Models\Package::forRole($role)->get();
});



// routes/web.php
Route::post('/listing/{id}/buy', [CheckoutController::class, 'buyNow'])->name('listing.buy');


Route::middleware('auth')->group(function () {
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats/{chat}/messages', [MessageController::class, 'store'])->name('messages.store');
});



Route::middleware(['auth'])->group(function () {
    // show chat UI (optional chatId)
    Route::get('/seller/Seller_Chat/{chatId?}', [ChatController::class, 'chat'])
        ->name('seller.chat');


});


Route::middleware(['auth'])->group(function () {
    // Existing list page (you showed this earlier)
    Route::get('/buyer/messages', [BuyerMessageController::class, 'index'])->name('buyer.messages');


});



Route::middleware(['auth'])->group(function () {
    // buyer sending
    Route::post('/buyer/messages/{chat}/send', [ChatController::class, 'sendMessage'])
        ->name('buyer.messages.send');

    // seller sending (adjust URL to match your frontend)
    Route::post('/seller/Seller_Chat/{chat}/message', [ChatController::class, 'sendMessage'])
        ->name('seller.chat.message');
});

require __DIR__.'/auth.php';
