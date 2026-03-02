<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Auth middleware is applied to these routes in web.php (admin 2FA group).
     */

    /**
     * Show 2FA challenge (enter code) – required after login for admin.
     */
    public function showChallenge(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (!$this->isAdmin($user)) {
            return redirect()->route('dashboard.default');
        }
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.2fa.setup');
        }
        return view('auth.two-factor-challenge');
    }

    /**
     * Verify 2FA code and allow access to admin.
     */
    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();
        if (!$this->isAdmin($user) || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('dashboard.default');
        }

        $valid = (new Google2FA())->verifyKey(
            $user->two_factor_secret,
            $request->code
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'The provided two-factor code was invalid.']);
        }

        $request->session()->put('2fa_verified_at', now()->timestamp);
        $request->session()->put('2fa_verified_user_id', $user->id);

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Show 2FA setup (QR + confirm with code) – first time for admin.
     */
    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (!$this->isAdmin($user)) {
            return redirect()->route('dashboard.default');
        }
        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('admin.dashboard');
        }

        $google2fa = new Google2FA();
        $secret = $user->two_factor_secret ?? $google2fa->generateSecretKey();

        if (empty($user->two_factor_secret)) {
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.two-factor-setup', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirm 2FA setup with a current code, then enable.
     */
    public function confirmSetup(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();
        if (!$this->isAdmin($user) || empty($user->two_factor_secret)) {
            return redirect()->route('dashboard.default');
        }

        $valid = (new Google2FA())->verifyKey(
            $user->two_factor_secret,
            $request->code
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'The provided code was invalid. Please try again.']);
        }

        $user->update(['two_factor_confirmed_at' => now()]);
        $request->session()->put('2fa_verified_at', now()->timestamp);
        $request->session()->put('2fa_verified_user_id', $user->id);

        return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication is now enabled.');
    }

    private function isAdmin($user): bool
    {
        return $user && strtolower(trim($user->role ?? '')) === 'admin';
    }
}
