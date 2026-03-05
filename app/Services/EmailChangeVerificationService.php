<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailChangeVerificationService
{
    private const CACHE_PREFIX = 'email_change:';
    private const TTL_MINUTES = 15;
    private const CODE_LENGTH = 6;

    public function sendCodeToOldEmail(User $user, string $newEmail): void
    {
        $newEmail = strtolower(trim($newEmail));
        if ($newEmail === strtolower(trim($user->email))) {
            throw ValidationException::withMessages(['email' => ['The new email is the same as your current email.']]);
        }

        $code = (string) random_int(
            (int) str_pad('1', self::CODE_LENGTH, '0'),
            (int) str_pad('9', self::CODE_LENGTH, '9')
        );

        Cache::put(self::CACHE_PREFIX . $user->id, [
            'new_email' => $newEmail,
            'code' => $code,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
        ], now()->addMinutes(self::TTL_MINUTES));

        Mail::send('emails.email-change-verification', [
            'user' => $user,
            'code' => $code,
            'new_email' => $newEmail,
            'minutes' => self::TTL_MINUTES,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('CayMark: Verify your email address change');
        });
    }

    public function verifyAndUpdateEmail(User $user, string $code): bool
    {
        $key = self::CACHE_PREFIX . $user->id;
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

        $user->email = $data['new_email'];
        $user->email_verified_at = null;
        $user->save();
        Cache::forget($key);

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
}
