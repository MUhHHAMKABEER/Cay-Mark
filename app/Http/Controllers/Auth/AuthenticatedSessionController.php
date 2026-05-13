<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use RuntimeException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * If the user's password in DB is not bcrypt (legacy/plain), we verify and upgrade to bcrypt on successful login.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        try {
            $attempt = Auth::attempt($credentials, $remember);
        } catch (RuntimeException $e) {
            // Stored password is not bcrypt (e.g. plain text or old hash)
            if (! str_contains($e->getMessage(), 'does not use the Bcrypt algorithm')) {
                throw $e;
            }
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
            }
            $plainPassword = $request->input('password');
            $storedPassword = $user->password;
            // Legacy: plain text or non-bcrypt stored value
            if ($storedPassword === $plainPassword || hash_equals($storedPassword, $plainPassword)) {
                $user->password = Hash::make($plainPassword);
                $user->save();
                Auth::login($user, $remember);
                $request->session()->regenerate();
                return $this->redirectAfterLogin($user, $request);
            }
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->with('message', 'Your account uses an older password format. Please use "Forgot password" to set a new one.')->onlyInput('email');
        }

        if ($attempt) {
            $request->session()->regenerate();
            $user = Auth::user();
            if (! $user instanceof User) {
                return back()->withErrors(['email' => __('auth.failed')])->onlyInput('email');
            }

            return $this->redirectAfterLogin($user, $request);
        }

        $failedUser = User::where('email', $request->input('email'))->first();
        if ($failedUser && Cache::add('login_fail_notify:'.$failedUser->id, 1, now()->addHour())) {
            try {
                (new \App\Services\NotificationService())->loginAttemptUnsuccessful($failedUser);
            } catch (\Throwable $e) {
            }
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Redirect user to the correct dashboard by role.
     */
    private function redirectAfterLogin(User $user, Request $request): RedirectResponse
    {
        $currentIp = (string) $request->ip();
        if ($user->last_login_ip && $user->last_login_ip !== $currentIp) {
            try {
                (new \App\Services\NotificationService())->loginFromNewDevice($user);
            } catch (\Throwable $e) {
            }
        }

        if (! $user->registration_complete) {
            $cacheKey = 'complete_registration_prompt:'.$user->id.':'.now()->format('Y-m-d');
            if (Cache::add($cacheKey, 1, now()->endOfDay())) {
                try {
                    (new \App\Services\NotificationService())->completeRegistrationReminder($user);
                } catch (\Throwable $e) {
                }
            }
        }

        $user->last_login_ip = $currentIp;
        $user->saveQuietly();

        $role = strtolower(trim($user->role ?? ''));
        $redirectTo = route('dashboard');
        if ($role === 'admin') {
            $redirectTo = route('admin.dashboard');
        } elseif ($role === 'seller') {
            $redirectTo = route('seller.dashboard');
        } elseif ($role === 'buyer') {
            $redirectTo = route('welcome');
        }
        return redirect()->intended($redirectTo);
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
