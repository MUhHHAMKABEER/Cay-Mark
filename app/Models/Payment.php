<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'invoice_id',
        'listing_id',
        'seller_id',
        'amount',
        'method',
        'status',
        'metadata',
        'gateway_transaction_id',
        'payment_reference',
        'item_title',
        'item_id',
        'platform_fee_retained',
        'seller_payout_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee_retained' => 'decimal:2',
        'seller_payout_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * A payment belongs to a user (buyer).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A payment belongs to a subscription.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * A payment belongs to an invoice (for buyer payments).
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * A payment belongs to a listing.
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * A payment belongs to a seller.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
