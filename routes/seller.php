<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\ListingController;
use App\Http\Controllers\Seller\PayoutMethodController;
use App\Http\Controllers\Seller\PickupPinController;
use App\Http\Controllers\Seller\SupportController;
use App\Http\Controllers\Buyer\NotificationController;
use App\Http\Controllers\chatController;

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
|
| All routes related to seller functionality
|
*/

Route::prefix('seller')->name('seller.')->middleware(['auth', 'seller'])->group(function () {
    
    // Dashboard (overview + tab pages — clean URLs /seller/...)
    Route::get('/dashboard', [App\Http\Controllers\Seller\SellerDashboardController::class, 'dashboard'])
        ->name('dashboard');
    Route::get('/account', [App\Http\Controllers\Seller\SellerDashboardController::class, 'account'])->name('account');
    Route::get('/auctions', [App\Http\Controllers\Seller\SellerDashboardController::class, 'auctions'])->name('auctions');
    Route::get('/submission', [App\Http\Controllers\Seller\SellerDashboardController::class, 'submission'])->name('submission');
    Route::get('/notifications', [App\Http\Controllers\Seller\SellerDashboardController::class, 'notifications'])->name('notifications');
    Route::get('/support', [App\Http\Controllers\Seller\SellerDashboardController::class, 'support'])->name('support');

    Route::post('/dashboard/update-payout', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updatePayout'])
        ->name('dashboard.update-payout');
    Route::post('/dashboard/change-password', [App\Http\Controllers\Seller\SellerDashboardController::class, 'changePassword'])
        ->name('dashboard.change-password');
    Route::post('/dashboard/update-email', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updateEmail'])
        ->name('dashboard.update-email');
    Route::post('/dashboard/update-phone', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updatePhone'])
        ->name('dashboard.update-phone');
    Route::post('/dashboard/confirm-pickup/{listingId}', [App\Http\Controllers\Seller\SellerDashboardController::class, 'confirmPickup'])
        ->name('dashboard.confirm-pickup');

    // Payout Method Setup (REQUIRED before listing creation)
    Route::get('payout-method', [PayoutMethodController::class, 'create'])->name('payout-method');
    Route::post('payout-method', [PayoutMethodController::class, 'store'])->name('payout-method.store');
    
    // Pickup PIN Confirmation
    Route::get('pickup-pin/{listingId}', [PickupPinController::class, 'show'])->name('pickup-pin.show');
    Route::post('pickup-pin/{listingId}/confirm', [PickupPinController::class, 'confirm'])->name('pickup-pin.confirm');
    
    // Payout History
    Route::get('payouts', [PayoutMethodController::class, 'payoutHistory'])->name('payouts');
    
    // Listings
    Route::get('listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('submit-listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('listings/success/{id}', [ListingController::class, 'success'])->name('listings.success');
    Route::get('listings', function () {
        return redirect()->route('seller.auctions');
    })->name('listings.index');
    Route::get('listings/{id}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('listings/{id}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('listings/{id}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::get('listings/{id}', [ListingController::class, 'show'])->name('listings.show');
    Route::get('Auction', [ListingController::class, 'showAuctionLisitng'])->name('Auction.index');
    
    // VIN/HIN Decoder endpoint
    Route::post('decode-vin-hin', [ListingController::class, 'decodeVinHin'])->name('listings.decode-vin-hin');
    
    // Support
    Route::post('support/submit', [SupportController::class, 'store'])->name('support.submit');

    // Notifications (mark read, unread count – same controller as buyer, uses Auth::user())
    Route::post('notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    
    // Messaging Center
    Route::get('messaging', [chatController::class, 'chat'])->name('chat');
    Route::get('messaging/{chatId?}', [chatController::class, 'chat'])->name('chat.show');
    Route::post('messaging/{chat}/message', [chatController::class, 'sendMessage'])->name('chat.message');
});


