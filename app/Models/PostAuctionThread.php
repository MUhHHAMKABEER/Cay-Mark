<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAuctionThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'listing_id',
        'buyer_id',
        'seller_id',
        'is_unlocked',
        'unlocked_at',
        'pickup_confirmed',
        'pickup_confirmed_at',
    ];

    protected $casts = [
        'is_unlocked' => 'boolean',
        'unlocked_at' => 'datetime',
        'pickup_confirmed' => 'boolean',
        'pickup_confirmed_at' => 'datetime',
    ];

    /**
     * Thread belongs to an invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Thread belongs to a listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Thread belongs to buyer
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Thread belongs to seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Thread has pickup details
     */
    public function pickupDetails()
    {
        return $this->hasMany(PickupDetail::class, 'thread_id');
    }

    /**
     * Thread has latest pickup detail
     */
    public function latestPickupDetail()
    {
        return $this->hasOne(PickupDetail::class, 'thread_id')->latestOfMany();
    }

    /**
     * Thread has third party pickups
     */
    public function thirdPartyPickups()
    {
        return $this->hasMany(ThirdPartyPickup::class, 'thread_id');
    }

    /**
     * Thread has active third party pickup
     */
    public function activeThirdPartyPickup()
    {
        return $this->hasOne(ThirdPartyPickup::class, 'thread_id')->where('is_active', true);
    }

    /**
     * Thread has change requests
     */
    public function changeRequests()
    {
        return $this->hasMany(PickupChangeRequest::class, 'thread_id');
    }

    /**
     * Check if thread is unlocked (payment cleared)
     */
    public function isUnlocked(): bool
    {
        return $this->is_unlocked && $this->unlocked_at !== null;
    }

    /**
     * Unlock thread when payment clears
     */
    public function unlock(): void
    {
        $this->update([
            'is_unlocked' => true,
            'unlocked_at' => now(),
        ]);
    }
}
