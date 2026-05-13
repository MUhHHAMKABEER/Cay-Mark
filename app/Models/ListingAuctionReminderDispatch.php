<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingAuctionReminderDispatch extends Model
{
    protected $fillable = [
        'listing_id',
        'window',
        'purpose',
        'user_id',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function wasDispatched(int $listingId, string $window, string $purpose, int $userId): bool
    {
        return static::query()
            ->where('listing_id', $listingId)
            ->where('window', $window)
            ->where('purpose', $purpose)
            ->where('user_id', $userId)
            ->exists();
    }

    public static function record(int $listingId, string $window, string $purpose, int $userId): void
    {
        static::query()->firstOrCreate([
            'listing_id' => $listingId,
            'window' => $window,
            'purpose' => $purpose,
            'user_id' => $userId,
        ]);
    }
}
