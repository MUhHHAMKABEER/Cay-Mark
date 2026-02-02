<?php

namespace App\Services\Buyer;

class BuyerDashboardOps
{
    public static function updateEmail($request)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        return back()->with('success', 'Email address updated successfully.');
    }

    public static function changePassword($request)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}

