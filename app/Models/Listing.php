<?php

namespace App\Models;

use App\Helpers\TextFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_number',
        'seller_id',
        'cover_photo_id',
        'original_listing_id',
        'relisted_at',
        'is_relist',
        'listing_method',
        'auction_duration',
        'major_category',
        'subcategory',
        'other_make',
        'other_model',
        'condition',
        'make',
        'model',
        'trim',
        'year',
        'vin',
        'duplicate_vin_flag',
        'color',
        'interior_color',
        'island',
        'fuel_type',
        'transmission',
        'title_status',
        'primary_damage',
        'secondary_damage',
        'keys_available',
        'engine_type',
        'hull_material',
        'category_type',
        'status',
        'expiry_status',
        'expires_at',
        'auction_start_time',
        'auction_end_time',
        'pickup_pin',
        'pickup_pin_generated_at',
        'pickup_confirmed_at',
        'pickup_confirmed',
        'pickup_confirmed_by',
        'price',
        'starting_price',
        'reserve_price',
        'buy_now_price',
        'odometer',
        'bought',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'rejection_notes',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'auction_start_time' => 'datetime',
        'auction_end_time' => 'datetime',
        'pickup_pin_generated_at' => 'datetime',
        'pickup_confirmed_at' => 'datetime',
        'pickup_confirmed' => 'boolean',
        'rejected_at' => 'datetime',
    ];

    /**
     * Booted method to handle model events.
     */
    protected static function booted()
    {
        static::retrieved(function ($listing) {
            if ($listing->isExpired() && $listing->expiry_status !== 'expired') {
                $listing->expiry_status = 'expired';
                $listing->save();
            }
        });

        // Auto-convert vehicle fields to ALL CAPS on save
        static::saving(function ($listing) {
            $allCapsFields = [
                'make', 'model', 'trim', 'year', 'vin', 'color', 'interior_color',
                'fuel_type', 'transmission', 'title_status', 'primary_damage',
                'secondary_damage', 'engine_type', 'hull_material', 'category_type',
                'other_make', 'other_model', 'major_category', 'subcategory',
            ];

            foreach ($allCapsFields as $field) {
                if (isset($listing->attributes[$field]) && $listing->attributes[$field] !== null) {
                    $listing->attributes[$field] = TextFormatter::toAllCaps($listing->attributes[$field]);
                }
            }
        });
    }

    /**
     * Relationship: A listing has many images.
     */
    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }

    /**
     * Relationship: Cover photo.
     */
    public function coverPhoto()
    {
        return $this->belongsTo(ListingImage::class, 'cover_photo_id');
    }

    /**
     * Relationship: A listing belongs to a seller (user).
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Check if the listing is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function bids()
{
    return $this->hasMany(Bid::class);
}

/**
 * highest active bid
 */
public function highestBid()
{
    return $this->hasOne(Bid::class)->orderByDesc('amount');
}

public function invoices()
{
    return $this->hasMany(Invoice::class);
}

    /**
     * Listing has post-auction threads
     */
    public function postAuctionThreads()
    {
        return $this->hasMany(PostAuctionThread::class);
    }

    /**
     * Generate unique Item Number (format: CM000245).
     * Called by admin when approving listing.
     */
    public static function generateItemNumber(): string
    {
        // Get the highest existing item number
        $lastListing = static::whereNotNull('item_number')
            ->orderByRaw('CAST(SUBSTRING(item_number, 3) AS UNSIGNED) DESC')
            ->first();

        if ($lastListing && $lastListing->item_number) {
            // Extract number part and increment
            $lastNumber = (int) substr($lastListing->item_number, 2);
            $newNumber = $lastNumber + 1;
        } else {
            // Start from 1
            $newNumber = 1;
        }

        // Format as CM000245 (CM + 6 digits)
        return 'CM' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Assign item number to listing (called on approval).
     */
    public function assignItemNumber(): void
    {
        if (!$this->item_number) {
            $this->item_number = static::generateItemNumber();
            $this->save();
        }
    }

    /**
     * Generate pickup PIN (4 digits per PDF requirements).
     */
    public function generatePickupPin(): string
    {
        $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $this->pickup_pin = $pin;
        $this->pickup_pin_generated_at = now();
        $this->save();
        return $pin;
    }

    /**
     * Verify pickup PIN (per PDF requirements).
     * PINs are single-use only and expire after successful validation.
     */
    public function verifyPickupPin(string $pin): bool
    {
        // Check if PIN matches and hasn't been used
        if ($this->pickup_pin === $pin && !$this->pickup_confirmed) {
            // PIN is valid - it will be marked as used when pickup is confirmed
            return true;
        }
        return false;
    }

    /**
     * Confirm pickup with PIN.
     */
    public function confirmPickup(string $pin, $confirmedBy): bool
    {
        if ($this->pickup_pin === $pin && !$this->pickup_confirmed) {
            $this->pickup_confirmed = true;
            $this->pickup_confirmed_at = now();
            $this->pickup_confirmed_by = $confirmedBy;
            // Clear PIN after use (single-use per PDF requirements)
            $this->pickup_pin = null;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Check if seller has active listings (for payout method locking).
     */
    public static function sellerHasActiveListings($sellerId): bool
    {
        return static::where('seller_id', $sellerId)
            ->whereIn('status', ['pending', 'approved'])
            ->where('pickup_confirmed', false)
            ->exists();
    }
}
