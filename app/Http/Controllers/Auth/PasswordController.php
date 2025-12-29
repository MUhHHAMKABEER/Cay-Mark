<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Send password changed email
        try {
            Mail::send('emails.password-changed', [
                'user' => $request->user(),
            ], function ($message) use ($request) {
                $message->to($request->user()->email, $request->user()->name)
                    ->subject('Password Changed Successfully');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send password changed email: ' . $e->getMessage());
        }

        return back()->with('status', 'password-updated');
    }
}
