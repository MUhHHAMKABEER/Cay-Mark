<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\BuyerDashboardController;
use App\Http\Controllers\Buyer\BuyerMessageController;
use App\Http\Controllers\Buyer\SupportController;
use App\Http\Controllers\chatController;

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
|
| All routes related to buyer functionality
|
*/

Route::prefix('buyer')->name('buyer.')->middleware(['auth'])->group(function () {
    
    // Separate Dashboard Pages
    Route::get('/user', [BuyerDashboardController::class, 'user'])->name('user');
    Route::get('/auctions', [BuyerDashboardController::class, 'auctions'])->name('auctions');
    Route::get('/saved-items', [BuyerDashboardController::class, 'savedItems'])->name('saved-items');
    Route::get('/notifications', [BuyerDashboardController::class, 'notifications'])->name('notifications');
    
    // Update email and password (used in user page)
    Route::post('/user/update-email', [BuyerDashboardController::class, 'updateEmail'])->name('user.update-email');
    Route::post('/user/change-password', [BuyerDashboardController::class, 'changePassword'])->name('user.change-password');

    // Legacy dashboard route - redirect to user page
    Route::get('/dashboard', function() {
        return redirect()->route('buyer.user');
    })->name('dashboard');

    // Messages (Messaging Center)
    Route::get('/messaging-center', [BuyerMessageController::class, 'index'])->name('messaging-center');
    Route::post('/messaging-center/{chat}/send', [chatController::class, 'sendMessage'])->name('messaging-center.send');

    // Support
    Route::get('/customer-support', [SupportController::class, 'index'])->name('customer-support');
    Route::post('/customer-support/submit', [SupportController::class, 'store'])->name('customer-support.submit');
});

// Legacy routes (for backward compatibility)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/buyer', [BuyerDashboardController::class, 'index'])->name('dashboard.buyer');
    Route::post('/dashboard/buyer/update-email', [BuyerDashboardController::class, 'updateEmail'])->name('buyer-dashboard.update-email');
    Route::post('/dashboard/buyer/change-password', [BuyerDashboardController::class, 'changePassword'])->name('buyer-dashboard.change-password');
    
    Route::get('/buyer/messages', [BuyerMessageController::class, 'index'])->name('buyer.messages');
    Route::post('/buyer/messages/{chat}/send', [chatController::class, 'sendMessage'])->name('buyer.messages.send');
});


