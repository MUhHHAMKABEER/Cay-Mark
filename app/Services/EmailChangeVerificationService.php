<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class EmailChangeVerificationService
{
    private const CACHE_PREFIX = 'email_change:';
    private const TTL_MINUTES = 10;
    private const CODE_LENGTH = 6;
    private const RATE_KEY_PREFIX = 'email_change_rate:';
    private const MAX_ATTEMPTS = 2;
    private const DECAY_SECONDS = 4 * 60 * 60; // 4 hours

    /**
     * Initiate email change: validate password, check uniqueness, rate-limit,
     * then send OTP to the NEW email address.
     */
    public function sendCodeToNewEmail(User $user, string $newEmail, string $password): void
    {
        $newEmail = strtolower(trim($newEmail));

        if ($newEmail === strtolower(trim((string) $user->email))) {
            throw ValidationException::withMessages(['new_email' => ['The new email is the same as your current email.']]);
        }

        if (DB::table('users')->where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
            throw ValidationException::withMessages(['new_email' => ['This email address is already in use by another account.']]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages(['password' => ['The password you entered is incorrect.']]);
        }

        $rateKey = self::RATE_KEY_PREFIX . $user->id;
        if (RateLimiter::tooManyAttempts($rateKey, self::MAX_ATTEMPTS)) {
            $availableIn = RateLimiter::availableIn($rateKey);
            $hours = (int) ceil($availableIn / 3600);
            throw ValidationException::withMessages([
                'new_email' => ["Too many attempts. Please try again in {$hours} hour(s)."],
            ]);
        }
        RateLimiter::hit($rateKey, self::DECAY_SECONDS);

        $code = str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);

        Cache::put(self::CACHE_PREFIX . $user->id, [
            'new_email'  => $newEmail,
            'code'       => $code,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
        ], now()->addMinutes(self::TTL_MINUTES));

        // Send OTP to the NEW email address
        Mail::send('emails.caymark.email-change-verification', [
            'user'      => $user,
            'code'      => $code,
            'new_email' => $newEmail,
            'minutes'   => self::TTL_MINUTES,
        ], function ($message) use ($newEmail, $user) {
            $message->to($newEmail, $user->name)
                ->subject('CayMark: Confirm your new email address');
        });

        // Notify OLD email that a change was requested
        try {
            Mail::send('emails.caymark.email-change-notice-old', [
                'user'      => $user,
                'new_email' => $newEmail,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('CayMark: Email change requested on your account');
            });
        } catch (\Throwable) {
            // Non-blocking — log if needed
        }
    }

    /**
     * Legacy method kept for backwards compatibility.
     * Sends code to the OLD email (original flow).
     */
    public function sendCodeToOldEmail(User $user, string $newEmail): void
    {
        $newEmail = strtolower(trim($newEmail));
        if ($newEmail === strtolower(trim((string) $user->email))) {
            throw ValidationException::withMessages(['email' => ['The new email is the same as your current email.']]);
        }

        $code = str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);

        Cache::put(self::CACHE_PREFIX . $user->id, [
            'new_email'  => $newEmail,
            'code'       => $code,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
        ], now()->addMinutes(self::TTL_MINUTES));

        Mail::send('emails.caymark.email-change-verification', [
            'user'      => $user,
            'code'      => $code,
            'new_email' => $newEmail,
            'minutes'   => self::TTL_MINUTES,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('CayMark: Verify your email address change');
        });
    }

    /**
     * Verify the OTP, update the email, and notify both addresses.
     */
    public function verifyAndUpdateEmail(User $user, string $code): bool
    {
        $key  = self::CACHE_PREFIX . $user->id;
        $data = Cache::get($key);

        if (!$data || !isset($data['code'], $data['new_email'])) {
            return false;
        }
        if (isset($data['expires_at']) && $data['expires_at'] < time()) {
            Cache::forget($key);
            return false;
        }
        if ((string) $data['code'] !== (string) $code) {
            return false;
        }

        $oldEmail = $user->email;
        $newEmail = $data['new_email'];

        $user->email              = $newEmail;
        $user->email_verified_at  = null;
        $user->save();
        Cache::forget($key);

        // Notify old email that the change completed
        try {
            Mail::send('emails.caymark.email-changed-old', [
                'user'      => $user,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
            ], function ($message) use ($oldEmail, $user) {
                $message->to($oldEmail, $user->name)
                    ->subject('CayMark: Your email address has been changed');
            });
        } catch (\Throwable) {}

        return true;
    }

    public function hasPendingChange(User $user): bool
    {
        return Cache::has(self::CACHE_PREFIX . $user->id);
    }

    public function getPendingNewEmail(User $user): ?string
    {
        $data = Cache::get(self::CACHE_PREFIX . $user->id);
        return $data['new_email'] ?? null;
    }

    public function cancelPendingChange(User $user): void
    {
        Cache::forget(self::CACHE_PREFIX . $user->id);
    }
}
