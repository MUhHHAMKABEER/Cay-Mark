<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                return $this->redirectAfterLogin($user);
            }
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->with('message', 'Your account uses an older password format. Please use "Forgot password" to set a new one.')->onlyInput('email');
        }

        if ($attempt) {
            $request->session()->regenerate();
            return $this->redirectAfterLogin(Auth::user());
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Redirect user to the correct dashboard by role.
     */
    private function redirectAfterLogin($user): RedirectResponse
    {
        $role = strtolower(trim($user->role ?? ''));
        $redirectTo = route('dashboard');
        if ($role === 'admin') {
            $redirectTo = route('admin.dashboard');
        } elseif ($role === 'seller') {
            $redirectTo = route('dashboard.seller');
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
