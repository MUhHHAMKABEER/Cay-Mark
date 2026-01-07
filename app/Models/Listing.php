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

    /**
     * Get the highest bid amount for this listing
     */
    public function getHighestBidAmount(): float
    {
        return $this->bids()->max('amount') ?? $this->starting_price ?? 0;
    }

    /**
     * Get the winning invoice (paid invoice)
     */
    public function getWinningInvoice()
    {
        return $this->invoices()->where('payment_status', 'paid')->first();
    }

    /**
     * Get current bid (winning invoice amount or highest bid)
     */
    public function getCurrentBid(): float
    {
        $winningInvoice = $this->getWinningInvoice();
        if ($winningInvoice) {
            return $winningInvoice->winning_bid_amount;
        }
        return $this->getHighestBidAmount();
    }

    /**
     * Check if listing is awaiting pickup confirmation
     */
    public function isAwaitingPickup(): bool
    {
        $winningInvoice = $this->getWinningInvoice();
        return $winningInvoice && !$this->pickup_confirmed;
    }

    /**
     * Get final price (from winning invoice)
     */
    public function getFinalPrice(): float
    {
        $winningInvoice = $this->getWinningInvoice();
        return $winningInvoice ? $winningInvoice->winning_bid_amount : 0;
    }

    /**
     * Check if rejected listing can still be edited (72-hour window)
     */
    public function canBeEdited(): bool
    {
        if ($this->status !== 'rejected') {
            return false;
        }
        
        $rejectedAt = $this->rejected_at ?? $this->updated_at;
        $deadline = $rejectedAt->copy()->addHours(72);
        
        return now()->lt($deadline);
    }

    /**
     * Get hours remaining for editing rejected listing
     */
    public function getEditHoursRemaining(): int
    {
        if (!$this->canBeEdited()) {
            return 0;
        }
        
        $rejectedAt = $this->rejected_at ?? $this->updated_at;
        $deadline = $rejectedAt->copy()->addHours(72);
        
        return now()->diffInHours($deadline);
    }

    /**
     * Get edit deadline for rejected listing
     */
    public function getEditDeadline()
    {
        if ($this->status !== 'rejected') {
            return null;
        }
        
        $rejectedAt = $this->rejected_at ?? $this->updated_at;
        return $rejectedAt->copy()->addHours(72);
    }

    /**
     * Scope: Get current auctions for seller (active + awaiting PIN)
     */
    public function scopeCurrentAuctionsForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId)
            ->where(function($q) {
                $q->where('status', 'active')
                    ->orWhere('status', 'pending')
                    ->orWhere(function($subQ) {
                        $subQ->where('status', 'sold')
                            ->whereHas('invoices', function($inv) {
                                $inv->where('payment_status', 'paid');
                            })
                            ->where('pickup_confirmed', false);
                    });
            });
    }

    /**
     * Scope: Get past auctions for seller (completed with pickup confirmed)
     */
    public function scopePastAuctionsForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId)
            ->where('status', 'sold')
            ->where('pickup_confirmed', true);
    }

    /**
     * Scope: Get rejected listings for seller
     */
    public function scopeRejectedForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId)
            ->where('status', 'rejected');
    }

    /**
     * Scope: Get listings where buyer has placed bids
     */
    public function scopeWithBuyerBids($query, $buyerId)
    {
        return $query->whereHas('bids', function($q) use ($buyerId) {
            $q->where('user_id', $buyerId);
        });
    }

    /**
     * Get user's highest bid on this listing
     */
    public function getUserHighestBid($userId): ?float
    {
        return $this->bids()->where('user_id', $userId)->max('amount');
    }

    /**
     * Check if user is winning this listing
     */
    public function isUserWinning($userId): bool
    {
        $invoice = $this->invoices()
            ->where('buyer_id', $userId)
            ->where('payment_status', 'pending')
            ->first();
        
        return $invoice !== null;
    }

    /**
     * Get pending invoice for user
     */
    public function getPendingInvoiceForUser($userId)
    {
        return $this->invoices()
            ->where('buyer_id', $userId)
            ->where('payment_status', 'pending')
            ->first();
    }
}
