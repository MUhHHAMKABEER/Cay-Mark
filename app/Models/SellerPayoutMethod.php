<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SellerPayoutMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'routing_number',
        'swift_number',
        'additional_instructions',
        'is_active',
        'is_verified',
        'is_locked',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /**
     * Encrypt sensitive fields when saving.
     */
    protected static function booted()
    {
        static::saving(function ($payoutMethod) {
            // Encrypt sensitive banking information
            if ($payoutMethod->isDirty('bank_name')) {
                $payoutMethod->bank_name = Crypt::encryptString($payoutMethod->bank_name);
            }
            if ($payoutMethod->isDirty('account_holder_name')) {
                $payoutMethod->account_holder_name = Crypt::encryptString($payoutMethod->account_holder_name);
            }
            if ($payoutMethod->isDirty('account_number')) {
                $payoutMethod->account_number = Crypt::encryptString($payoutMethod->account_number);
            }
            if ($payoutMethod->isDirty('routing_number') && $payoutMethod->routing_number) {
                $payoutMethod->routing_number = Crypt::encryptString($payoutMethod->routing_number);
            }
            if ($payoutMethod->isDirty('swift_number') && $payoutMethod->swift_number) {
                $payoutMethod->swift_number = Crypt::encryptString($payoutMethod->swift_number);
            }
        });
    }

    /**
     * Decrypt sensitive fields when retrieving.
     */
    public function getBankNameAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value; // Return as-is if decryption fails (for old records)
        }
    }

    public function getAccountHolderNameAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getAccountNumberAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getRoutingNumberAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function getSwiftNumberAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return $value;
        }
    }

    /**
     * Relationship: Payout method belongs to a user (seller).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payout method can be edited (not locked).
     */
    public function canBeEdited(): bool
    {
        return !$this->is_locked;
    }

    /**
     * Lock payout method (when seller has active listings).
     */
    public function lock(): void
    {
        $this->is_locked = true;
        $this->save();
    }

    /**
     * Unlock payout method (when seller has no active listings).
     */
    public function unlock(): void
    {
        $this->is_locked = false;
        $this->save();
    }
}
