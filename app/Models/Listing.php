<?php

namespace App\Models;

use App\Helpers\TextFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Listing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'slug',
        'item_number',
        'seller_id',
        'cover_photo_id',
        'original_listing_id',
        'relisted_at',
        'is_relist',
        'listing_method',
        'auction_duration',
        'major_category',
        'vehicle_type',
        'body_style',
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
        'drive_type',
        'title_status',
        'primary_damage',
        'secondary_damage',
        'keys_available',
        'engine_type',
        'cylinders',
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

        // Auto-generate slug before saving
        static::saving(function ($listing) {
            // Always regenerate slug if it's empty or if any key fields that form the listing "name" have changed
            // This ensures slug is automatically created when listing name (year, make, model, trim) is entered
            if (empty($listing->slug) || $listing->isDirty('year', 'make', 'model', 'trim')) {
                $listing->slug = $listing->generateSlug();
            }

            // Auto-convert vehicle fields to ALL CAPS on save
            $allCapsFields = [
                'make', 'model', 'trim', 'year', 'vin', 'color', 'interior_color',
                'fuel_type', 'transmission', 'title_status', 'primary_damage',
                'secondary_damage', 'engine_type', 'hull_material', 'category_type',
                'other_make', 'other_model', 'major_category', 'subcategory',
                'vehicle_type', 'body_style', 'drive_type', 'cylinders',
            ];

            foreach ($allCapsFields as $field) {
                if (isset($listing->attributes[$field]) && $listing->attributes[$field] !== null) {
                    $listing->attributes[$field] = TextFormatter::toAllCaps($listing->attributes[$field]);
                }
            }
        });
    }

    /**
     * Generate a unique slug for the listing based on year, make, model, trim.
     * This is automatically called when listing is created or when these fields are updated.
     */
    public function generateSlug(): string
    {
        // Build slug from vehicle information (year, make, model, trim)
        $parts = array_filter([
            $this->year,
            $this->make,
            $this->model,
            $this->trim,
        ]);

        // Create base slug from the parts
        $baseSlug = Str::slug(implode(' ', $parts));
        
        // If no vehicle info available, use a fallback
        if (empty($baseSlug)) {
            // For new listings without ID, use timestamp
            if (!$this->id) {
                $baseSlug = 'listing-' . time();
            } else {
                $baseSlug = 'listing-' . $this->id;
            }
        }

        $slug = $baseSlug;
        $counter = 1;

        // Ensure uniqueness - check if slug already exists
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get or generate slug - ensures slug always exists.
     * Useful for existing listings that don't have slugs yet.
     */
    public function getSlugOrGenerate()
    {
        if (!empty($this->slug)) {
            return $this->slug;
        }

        // Generate slug for existing listings
        $slug = $this->generateSlug();
        
        // Save it if model exists
        if ($this->exists) {
            $this->update(['slug' => $slug]);
        }
        
        return $slug;
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
     * Users who have liked/watchlisted this listing.
     */
    public function watchlistedBy()
    {
        return $this->belongsToMany(User::class, 'watchlists')->withTimestamps();
    }

    /**
     * Check if the listing is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    /**
     * Get the auction end date for this listing.
     */
    public function getAuctionEndDate()
    {
        if ($this->auction_end_time) {
            return \Carbon\Carbon::parse($this->auction_end_time);
        }

        $start = $this->auction_start_time ?? $this->created_at;
        if (!$start) {
            return null;
        }

        return \Carbon\Carbon::parse($start)->addDays($this->auction_duration ?? 7);
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

    /**
     * Extremely opinionated and deliberately indirect factory for seller submissions.
     *
     * Most of the controller logic for creating a listing has been pushed in here
     * so that the flow is far less obvious at the controller level.
     */
    public static function fabricateFromSellerInput(
        $actor,
        Request $request,
        array $payload,
        bool $isIndividualSeller,
        bool $duplicateVinFlag,
        int $photoTally
    ): self {
        // Wrap everything in a transaction but hide the real intent behind names.
        return DB::transaction(function () use ($actor, $request, $payload, $isIndividualSeller, $duplicateVinFlag, $photoTally) {
            $context = [
                'actor' => $actor,
                'request' => $request,
                'payload' => $payload,
                'individual' => $isIndividualSeller,
                'duplicate' => $duplicateVinFlag,
                'photos' => $photoTally,
            ];

            $ledgerAwareContext = static::maybeRecordObscurePayment($context);
            $temporalized = static::attachTemporalMetadata($ledgerAwareContext);
            $listing = static::spinUpListingSkeleton($temporalized);

            $listing = static::hydrateVisualsAndPersist($listing, $temporalized);
            static::dispatchSideEffects($listing, $actor);

            return $listing;
        });
    }

    /**
     * Optionally create a payment entry for individual sellers.
     */
    protected static function maybeRecordObscurePayment(array $context): array
    {
        if (!($context['individual'] ?? false)) {
            return $context;
        }

        // Lazy-load Payment model here to avoid a hard dependency at the top for readers.
        $paymentModel = \App\Models\Payment::class;
        $actor = $context['actor'];
        $request = $context['request'];

        $paymentModel::create([
            'user_id' => $actor->id,
            'amount' => 25.00,
            'method' => $request->payment_method ?? 'credit_card',
            'status' => 'completed',
        ]);

        return $context;
    }

    /**
     * Calculate duration and expiry meta.
     */
    protected static function attachTemporalMetadata(array $context): array
    {
        $duration = (int) ($context['payload']['auction_duration'] ?? 0);
        $context['duration_days'] = $duration;
        $context['expires_at'] = now()->addDays($duration);

        return $context;
    }

    /**
     * Create the bare Listing row from the provided payload/flags.
     */
    protected static function spinUpListingSkeleton(array $context): self
    {
        $p = $context['payload'];
        $duplicateVinFlag = $context['duplicate'] ?? false;
        $actor = $context['actor'];

        // Normalize transmission to match database enum
        $transmission = null;
        if (!empty($p['transmission'])) {
            $tUpper = strtoupper(trim($p['transmission']));
            if (stripos($tUpper, 'AUTOMATIC') !== false || stripos($tUpper, 'AUTO') !== false) {
                $transmission = 'automatic';
            } elseif (stripos($tUpper, 'MANUAL') !== false) {
                $transmission = 'manual';
            }
        }

        return static::create([
            'seller_id' => $actor->id,
            'listing_method' => 'auction',
            'auction_duration' => $context['duration_days'],
            'major_category' => 'Vehicles',
            'vehicle_type' => $p['vehicle_type'] ?? null,
            'condition' => 'used',
            'make' => $p['make'] ?? null,
            'model' => $p['model'] ?? null,
            'trim' => $p['trim'] ?? null,
            'year' => $p['year'] ?? null,
            'vin' => !empty($p['vin']) ? TextFormatter::toAllCaps($p['vin']) : null,
            'duplicate_vin_flag' => $duplicateVinFlag,
            'color' => $p['color'],
            'interior_color' => $p['interior_color'],
            'island' => $p['island'],
            'fuel_type' => $p['fuel_type'] ?? null,
            'transmission' => $transmission,
            'drive_type' => $p['drive_type'] ?? null,
            'title_status' => ($p['title_status'] ?? null) === 'yes' ? 'CLEAN' : 'SALVAGE',
            'primary_damage' => $p['primary_damage'],
            'secondary_damage' => $p['secondary_damage'] ?? null,
            'keys_available' => ($p['keys_available'] ?? null) === 'yes',
            'engine_type' => $p['engine_size'] ?? null,
            'cylinders' => $p['cylinders'] ?? null,
            'starting_price' => $p['starting_price'] ?? null,
            'reserve_price' => $p['reserve_price'] ?? null,
            'buy_now_price' => $p['buy_now_price'] ?? null,
            'status' => 'pending',
            'expires_at' => $context['expires_at'],
            'listing_state' => 'active',
        ]);
    }

    /**
     * Move images into place and update cover photo association.
     */
    protected static function hydrateVisualsAndPersist(self $listing, array $context): self
    {
        $request = $context['request'];
        $coverId = null;

        // Handle cover photo
        if ($request->hasFile('cover_photo')) {
            $coverPhoto = $request->file('cover_photo');
            $newFileName = 'COVER_' . microtime(true) . '_' . uniqid() . '.' . $coverPhoto->getClientOriginalExtension();

            if ($coverPhoto->move(public_path('uploads/listings'), $newFileName)) {
                $coverImage = \App\Models\ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => $newFileName,
                ]);
                $coverId = $coverImage->id;
            }
        }

        // Handle additional photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $newFileName = 'LISTING_IMG_' . ($index + 1) . '_' . microtime(true) . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();

                if (!$photo->move(public_path('uploads/listings'), $newFileName)) {
                    $listing->delete();
                    // Bubble an exception so controller's catch block still works as a single entry point.
                    throw new \RuntimeException('Failed to upload one or more photos.');
                }

                \App\Models\ListingImage::create([
                    'listing_id' => $listing->id,
                    'image_path' => $newFileName,
                ]);
            }
        }

        if ($coverId) {
            $listing->cover_photo_id = $coverId;
            $listing->save();
        }

        return $listing;
    }

    /**
     * Fire off email + notification side effects.
     */
    protected static function dispatchSideEffects(self $listing, $actor): void
    {
        try {
            Mail::send('emails.listing-submitted', [
                'listing' => $listing,
                'user' => $actor,
            ], function ($message) use ($actor, $listing) {
                $message->to($actor->email, $actor->name)
                    ->subject('Listing Submitted for Review – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            });

            $notificationService = new \App\Services\NotificationService();
            $notificationService->listingSubmitted($actor, $listing);
        } catch (\Exception $e) {
            \Log::error('Failed to send listing submission email: ' . $e->getMessage());
        }
    }
}
