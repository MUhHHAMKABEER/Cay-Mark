<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\ListingController;
use App\Http\Controllers\Seller\PayoutMethodController;
use App\Http\Controllers\Seller\PickupPinController;
use App\Http\Controllers\Seller\SupportController;
use App\Http\Controllers\chatController;

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
|
| All routes related to seller functionality
|
*/

Route::prefix('seller')->name('seller.')->middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Seller\SellerDashboardController::class, 'index'])
        ->name('dashboard');
    Route::post('/dashboard/update-payout', [App\Http\Controllers\Seller\SellerDashboardController::class, 'updatePayout'])
        ->name('dashboard.update-payout');
    Route::post('/dashboard/change-password', [App\Http\Controllers\Seller\SellerDashboardController::class, 'changePassword'])
        ->name('dashboard.change-password');
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
    Route::get('listings', [ListingController::class, 'showListing'])->name('listings.index');
    Route::get('Auction', [ListingController::class, 'showAuctionLisitng'])->name('Auction.index');
    
    // VIN/HIN Decoder endpoint
    Route::post('decode-vin-hin', [ListingController::class, 'decodeVinHin'])->name('listings.decode-vin-hin');
    
    // Support
    Route::post('support/submit', [SupportController::class, 'store'])->name('support.submit');
    
    // Chat
    Route::get('Seller_Chat', [chatController::class, 'chat'])->name('chat');
    Route::get('Seller_Chat/{chatId?}', [chatController::class, 'chat'])->name('chat.show');
    Route::post('Seller_Chat/{chat}/message', [chatController::class, 'sendMessage'])->name('chat.message');
});


