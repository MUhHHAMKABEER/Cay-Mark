<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'message' => 'Notification marked as read']);
        }
        
        return response()->json(['success' => false, 'message' => 'Notification not found or already read'], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }
}
