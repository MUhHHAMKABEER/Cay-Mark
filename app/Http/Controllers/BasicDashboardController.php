<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BasicDashboardController extends Controller
{
    /**
     * Basic (Default) Dashboard - Shown before membership selection
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get user documents
        $documents = $user->documents()->orderBy('created_at', 'desc')->get();
        
        // Get notifications
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('dashboard.default', compact('user', 'documents', 'notifications'));
    }

    /**
     * Update email address (allowed in basic dashboard)
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->email = $request->email;
        $user->email_verified_at = null; // Require re-verification
        $user->save();

        return back()->with('success', 'Email address updated successfully. Please verify your new email.');
    }

    /**
     * Change password (allowed in basic dashboard)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = \Hash::make($request->password);
        $user->save();

        // Send password changed email
        try {
            \Mail::send('emails.password-changed', [
                'user' => $user,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Password Changed Successfully');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send password changed email: ' . $e->getMessage());
        }

        return back()->with('success', 'Password changed successfully.');
    }
}

