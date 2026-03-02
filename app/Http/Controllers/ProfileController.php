<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProfileDestroyRequest;
use App\Services\ProfileOps;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Redirect to role-based dashboard profile tab (no standalone /profile page).
     */
    public function edit(Request $request): RedirectResponse
    {
        $user = $request->user();
        $role = strtolower(trim($user->role ?? ''));

        if ($role === 'seller') {
            return redirect()->route('dashboard.seller', ['tab' => 'user']);
        }
        if ($role === 'buyer') {
            return redirect()->route('dashboard.buyer', ['tab' => 'user']);
        }
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard.default');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        return ProfileOps::update($request);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(ProfileDestroyRequest $request): RedirectResponse
    {
        return ProfileOps::destroy($request);
    }
}
