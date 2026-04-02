<?php

namespace App\Services;

class ProfileOps
{
    public static function update($request)
    {
        $user = $request->user();
        $data = $request->validated();
        // Name is not editable (not in form). Email change requires verification via Dashboard.
        if (isset($data['email']) && strtolower(trim($data['email'])) !== strtolower(trim($user->email))) {
            $role = strtolower(trim($user->role ?? ''));
            $route = $role === 'seller' ? 'seller.account' : ($role === 'buyer' ? 'buyer.user' : 'dashboard.default');
            return \Illuminate\Support\Facades\Redirect::route($route)
                ->withErrors(['email' => 'To change your email, use Account settings above. A verification code will be sent to your current email first.']);
        }
        if (!empty($data['email'])) {
            $user->email = $data['email'];
        }
        $user->save();

        $user = $request->user();
        $role = strtolower(trim($user->role ?? ''));
        if ($role === 'seller') {
            return \Illuminate\Support\Facades\Redirect::route('seller.account')->with('status', 'profile-updated');
        }
        if ($role === 'buyer') {
            return \Illuminate\Support\Facades\Redirect::route('buyer.user')->with('status', 'profile-updated');
        }
        return \Illuminate\Support\Facades\Redirect::route('dashboard.default')->with('status', 'profile-updated');
    }

    public static function destroy($request)
    {
        $request->validated();

        $user = $request->user();
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return \Illuminate\Support\Facades\Redirect::to('/');
    }
}

