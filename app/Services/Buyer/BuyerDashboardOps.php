<?php

namespace App\Services\Buyer;

use App\Services\EmailChangeVerificationService;
use Illuminate\Validation\ValidationException;

class BuyerDashboardOps
{
    public static function updateEmail($request)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $service = new EmailChangeVerificationService();

        if ($request->filled('code')) {
            $ok = $service->verifyAndUpdateEmail($user, $request->input('code'));
            if (!$ok) {
                return back()->withErrors(['code' => 'Invalid or expired verification code. Please request a new code.'])->withInput();
            }
            return back()->with('success', 'Email address updated successfully. Please verify your new email when you receive the link.');
        }

        try {
            $service->sendCodeToOldEmail($user, $request->input('email'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
        return back()->with('success', 'A verification code has been sent to your current email address. Enter the code below to confirm the change.')
            ->with('email_change_pending', true)->with('email_change_new', $request->input('email'));
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

