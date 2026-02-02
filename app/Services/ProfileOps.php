<?php

namespace App\Services;

class ProfileOps
{
    public static function update($request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return \Illuminate\Support\Facades\Redirect::route('profile.edit')->with('status', 'profile-updated');
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

