<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class PostAuctionThread extends Model
{
    use HasFactory;

    public const FLAG_MAX_EXCHANGES = 'max_exchanges';

    public const FLAG_TIMEOUT_48H = 'timeout_48h';

    public const FLAG_MANUAL = 'manual_request';

    public const MAX_EXCHANGES = 3;

    public const NEGOTIATION_WINDOW_HOURS = 48;

    protected $fillable = [
        'invoice_id',
        'listing_id',
        'buyer_id',
        'seller_id',
        'seller_contact_phone',
        'is_unlocked',
        'unlocked_at',
        'pickup_confirmed',
        'pickup_confirmed_at',
        'exchanges_count',
        'first_exchange_at',
        'last_exchange_at',
        'flagged_for_admin',
        'flagged_at',
        'flag_reason',
        'seller_ready_at',
        'buyer_completion_confirmed_at',
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
        'unlocked_at' => 'datetime',
        'pickup_confirmed' => 'boolean',
        'pickup_confirmed_at' => 'datetime',
        'exchanges_count' => 'integer',
        'first_exchange_at' => 'datetime',
        'last_exchange_at' => 'datetime',
        'flagged_for_admin' => 'boolean',
        'flagged_at' => 'datetime',
        'seller_ready_at' => 'datetime',
        'buyer_completion_confirmed_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function pickupDetails(): HasMany
    {
        return $this->hasMany(PickupDetail::class, 'thread_id');
    }

    public function latestPickupDetail(): HasOne
    {
        return $this->hasOne(PickupDetail::class, 'thread_id')->latestOfMany();
    }

    public function thirdPartyPickups(): HasMany
    {
        return $this->hasMany(ThirdPartyPickup::class, 'thread_id');
    }

    public function activeThirdPartyPickup(): HasOne
    {
        return $this->hasOne(ThirdPartyPickup::class, 'thread_id')->where('is_active', true);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(PickupChangeRequest::class, 'thread_id');
    }

    public function deliveryRequests(): HasMany
    {
        return $this->hasMany(PickupDeliveryRequest::class, 'thread_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MessagingThreadEvent::class, 'thread_id');
    }

    public function isUnlocked(): bool
    {
        return $this->is_unlocked && $this->unlocked_at !== null;
    }

    public function unlock(): void
    {
        $this->update([
            'is_unlocked' => true,
            'unlocked_at' => now(),
        ]);
    }

    /**
     * Increment the exchange counter, stamping first/last timestamps.
     */
    public function incrementExchange(): void
    {
        $now = now();
        $this->forceFill([
            'exchanges_count' => $this->exchanges_count + 1,
            'first_exchange_at' => $this->first_exchange_at ?? $now,
            'last_exchange_at' => $now,
        ])->save();
    }

    /**
     * True if the thread has reached the auto-flag conditions and is not yet
     * confirmed/completed.
     */
    public function shouldAutoFlag(): bool
    {
        if ($this->isPickupResolved()) {
            return false;
        }

        if ($this->exchanges_count >= self::MAX_EXCHANGES) {
            return true;
        }

        if (
            $this->first_exchange_at !== null
            && $this->first_exchange_at->lt(now()->subHours(self::NEGOTIATION_WINDOW_HOURS))
        ) {
            return true;
        }

        return false;
    }

    public function isPickupResolved(): bool
    {
        if ($this->pickup_confirmed) {
            return true;
        }

        $latest = $this->latestPickupDetail;

        return $latest !== null && $latest->status === 'confirmed';
    }

    public function flagForAdmin(string $reason): void
    {
        $this->forceFill([
            'flagged_for_admin' => true,
            'flagged_at' => $this->flagged_at ?? now(),
            'flag_reason' => $reason,
        ])->save();
    }

    public function unflag(): void
    {
        $this->forceFill([
            'flagged_for_admin' => false,
        ])->save();
    }

    /**
     * Most recent N events for the side feed.
     */
    public function recentUpdates(int $n = 5): Collection
    {
        return $this->events()->latest()->limit($n)->get();
    }

    public function exchangesRemaining(): int
    {
        return max(0, self::MAX_EXCHANGES - (int) $this->exchanges_count);
    }
}
