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
    
    // Dashboard
    Route::get('/dashboard', [BuyerDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/update-email', [BuyerDashboardController::class, 'updateEmail'])->name('dashboard.update-email');
    Route::post('/dashboard/change-password', [BuyerDashboardController::class, 'changePassword'])->name('dashboard.change-password');

    // Messages
    Route::get('messages', [BuyerMessageController::class, 'index'])->name('messages');
    Route::post('messages/{chat}/send', [chatController::class, 'sendMessage'])->name('messages.send');

    // Support
    Route::post('support/submit', [SupportController::class, 'store'])->name('support.submit');
});

// Legacy routes (for backward compatibility)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/buyer', [BuyerDashboardController::class, 'index'])->name('dashboard.buyer');
    Route::post('/dashboard/buyer/update-email', [BuyerDashboardController::class, 'updateEmail'])->name('buyer-dashboard.update-email');
    Route::post('/dashboard/buyer/change-password', [BuyerDashboardController::class, 'changePassword'])->name('buyer-dashboard.change-password');
    
    Route::get('/buyer/messages', [BuyerMessageController::class, 'index'])->name('buyer.messages');
    Route::post('/buyer/messages/{chat}/send', [chatController::class, 'sendMessage'])->name('buyer.messages.send');
});


