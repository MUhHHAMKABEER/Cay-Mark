<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = $request->user();
    $role = trim(strtolower($user->role ?? ''));
    
    // Admin users bypass registration check and go directly to admin dashboard
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    
    // For other users, check if registration is complete
    if (!$user->isRegistrationComplete() || empty($user->role)) {
        return redirect()->route('dashboard.default');
    }

    // Redirect based on role
    if ($role === 'seller') {
        return redirect()->route('dashboard.seller');
    } elseif ($role === 'buyer') {
        return redirect()->route('welcome'); // listings page
    }

    // fallback in case role is invalid
    return redirect()->route('dashboard.default');
}



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
