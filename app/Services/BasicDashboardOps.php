<?php

namespace App\Services;

class BasicDashboardOps
{
    public static function updateEmail($request)
    {
        $request->validated();

        $user = \Illuminate\Support\Facades\Auth::user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        return back()->with('success', 'Email address updated successfully. Please verify your new email.');
    }

    public static function changePassword($request)
    {
        $request->validated();

        $user = \Illuminate\Support\Facades\Auth::user();
        $user->password = \Hash::make($request->password);
        $user->save();

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

