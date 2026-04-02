<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Buyer\BuyerDashboardController;
use App\Http\Controllers\Buyer\SupportController;
use App\Http\Controllers\Buyer\NotificationController;
use App\Http\Controllers\chatController;

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
|
| All routes related to buyer functionality
|
*/

Route::prefix('buyer')->name('buyer.')->middleware(['auth', 'buyer'])->group(function () {

    Route::get('/dashboard', [BuyerDashboardController::class, 'dashboardOverview'])->name('dashboard');

    // Unified dashboard tabs (clean URLs)
    Route::get('/user', [BuyerDashboardController::class, 'user'])->name('user');
    Route::get('/auctions', [BuyerDashboardController::class, 'auctions'])->name('auctions');
    Route::get('/saved-items', [BuyerDashboardController::class, 'savedItems'])->name('saved-items');

    // Notifications API-style routes before GET /notifications (hub page)
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

    Route::get('/notifications', [BuyerDashboardController::class, 'notifications'])->name('notifications');

    // Update email and password (used in user tab)
    Route::post('/user/update-email', [BuyerDashboardController::class, 'updateEmail'])->name('user.update-email');
    Route::post('/user/change-password', [BuyerDashboardController::class, 'changePassword'])->name('user.change-password');

    Route::get('/messaging-center', [BuyerDashboardController::class, 'messagingCenter'])->name('messaging-center');
    Route::post('/messaging-center/{chat}/send', [chatController::class, 'sendMessage'])->name('messaging-center.send');

    Route::get('/customer-support', [BuyerDashboardController::class, 'customerSupport'])->name('customer-support');
    Route::post('/customer-support/submit', [SupportController::class, 'store'])->name('customer-support.submit');
});

// Legacy routes (for backward compatibility)
Route::middleware(['auth', 'buyer'])->group(function () {
    Route::post('/dashboard/buyer/update-email', [BuyerDashboardController::class, 'updateEmail'])->name('buyer-dashboard.update-email');
    Route::post('/dashboard/buyer/change-password', [BuyerDashboardController::class, 'changePassword'])->name('buyer-dashboard.change-password');

    Route::get('/buyer/messages', [BuyerDashboardController::class, 'messagingCenter'])->name('buyer.messages');
    Route::post('/buyer/messages/{chat}/send', [chatController::class, 'sendMessage'])->name('buyer.messages.send');
});


