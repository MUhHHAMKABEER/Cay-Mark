<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetOtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * D17 — OTP-based password reset.
 *
 * Step 1  GET  /forgot-password          → show email form
 * Step 1  POST /forgot-password          → send 6-digit OTP to email
 * Step 2  GET  /forgot-password/verify   → show OTP entry
 * Step 2  POST /forgot-password/verify   → verify OTP, store email in session
 * Step 3  GET  /forgot-password/reset    → show new-password form
 * Step 3  POST /forgot-password/reset    → save new password
 */
class PasswordResetLinkController extends Controller
{
    public function __construct(protected PasswordResetOtpService $otp) {}

    /* ── Step 1 ── */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            $this->otp->sendOtp($request->input('email'));
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Throwable $e) {
            Log::error('[PasswordOTP] sendOtp failed', ['error' => $e->getMessage()]);
            return back()->withInput()
                ->withErrors(['email' => 'Could not send reset code. Please try again or contact support@caymark.co.']);
        }

        return redirect()->route('password.verify')
            ->with('otp_email', $request->input('email'))
            ->with('status', 'A 6-digit reset code has been sent to ' . $request->input('email') . '. Please check your inbox.');
    }

    /* ── Step 2 — OTP verification ── */
    public function showVerify(Request $request): View|RedirectResponse
    {
        if (! session('otp_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.forgot-password-verify');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'string'],
        ]);

        try {
            $user = $this->otp->verifyOtp($request->input('email'), $request->input('code'));
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()->route('password.reset-otp')
            ->with('otp_email',   $user->email)
            ->with('otp_verified', true);
    }

    /* ── Step 3 — New password ── */
    public function showReset(Request $request): View|RedirectResponse
    {
        if (! session('otp_verified')) {
            return redirect()->route('password.request');
        }
        return view('auth.forgot-password-reset');
    }

    public function resetWithOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'confirmed', 'min:8'],
        ]);

        $user = \App\Models\User::where('email', $request->input('email'))->first();
        if (! $user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $this->otp->resetPassword($user, $request->input('password'));

        // Notify user
        try {
            (new \App\Services\NotificationService())->passwordChanged($user);
        } catch (\Throwable) {}

        return redirect()->route('login')
            ->with('status', 'Password updated successfully. You can now sign in with your new password.');
    }
}
