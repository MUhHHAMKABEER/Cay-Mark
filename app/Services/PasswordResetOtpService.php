<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * OTP-based password reset (D17).
 * Mirrors EmailChangeVerificationService but for password resets.
 */
class PasswordResetOtpService
{
    private const CACHE_PREFIX  = 'pw_reset_otp:';
    private const RATE_PREFIX   = 'pw_reset_rate:';
    private const TTL_MINUTES   = 10;
    private const MAX_ATTEMPTS  = 2;
    private const LOCKOUT_SECS  = 4 * 60 * 60; // 4 hours

    /**
     * Send a 6-digit OTP to the given email.
     * Throws ValidationException if email not found or rate-limited.
     */
    public function sendOtp(string $email): void
    {
        $email = strtolower(trim($email));
        $user  = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email address.'],
            ]);
        }

        $rateKey = self::RATE_PREFIX . $user->id;
        if (RateLimiter::tooManyAttempts($rateKey, self::MAX_ATTEMPTS)) {
            $wait  = RateLimiter::availableIn($rateKey);
            $hours = (int) ceil($wait / 3600);
            throw ValidationException::withMessages([
                'email' => ["Too many attempts. Please try again in {$hours} hour(s)."],
            ]);
        }
        RateLimiter::hit($rateKey, self::LOCKOUT_SECS);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put(self::CACHE_PREFIX . $user->id, [
            'code'       => $code,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
        ], now()->addMinutes(self::TTL_MINUTES));

        Mail::send('emails.caymark.password-reset', [
            'user'    => $user,
            'code'    => $code,
            'minutes' => self::TTL_MINUTES,
        ], function ($msg) use ($user) {
            $msg->to($user->email, $user->name)
                ->subject('Reset Your CayMark Password');
        });
    }

    /**
     * Verify the OTP for the given email.
     * Returns the User on success, throws ValidationException on failure.
     */
    public function verifyOtp(string $email, string $code): User
    {
        $email = strtolower(trim($email));
        $user  = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => ['No account found.']]);
        }

        $rateKey = self::RATE_PREFIX . $user->id;
        if (RateLimiter::tooManyAttempts($rateKey, self::MAX_ATTEMPTS)) {
            $wait  = RateLimiter::availableIn($rateKey);
            $hours = (int) ceil($wait / 3600);
            throw ValidationException::withMessages([
                'code' => ["Too many attempts. Please try again in {$hours} hour(s)."],
            ]);
        }

        $data = Cache::get(self::CACHE_PREFIX . $user->id);

        if (! $data) {
            throw ValidationException::withMessages(['code' => ['No reset code found. Please request a new one.']]);
        }
        if (isset($data['expires_at']) && $data['expires_at'] < time()) {
            Cache::forget(self::CACHE_PREFIX . $user->id);
            throw ValidationException::withMessages(['code' => ['Code has expired. Request a new one.']]);
        }
        if ((string) $data['code'] !== (string) preg_replace('/\D/', '', $code)) {
            RateLimiter::hit($rateKey, self::LOCKOUT_SECS);
            throw ValidationException::withMessages(['code' => ['Invalid code. Please try again.']]);
        }

        // OTP verified — clear rate limit
        RateLimiter::clear($rateKey);

        return $user;
    }

    /**
     * Reset the password. Clears OTP from cache.
     */
    public function resetPassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();
        Cache::forget(self::CACHE_PREFIX . $user->id);
    }

    public function hasPendingOtp(User $user): bool
    {
        return Cache::has(self::CACHE_PREFIX . $user->id);
    }
}
