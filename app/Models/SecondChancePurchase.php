<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecondChancePurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_invoice_id',
        'new_invoice_id',
        'listing_id',
        'buyer_id',
        'seller_id',
        'bid_id',
        'bid_amount',
        'buyer_commission',
        'total_amount_due',
        'status',
        'offered_at',
        'payment_deadline',
        'accepted_at',
        'declined_at',
        'admin_notes',
    ];

    protected $casts = [
        'bid_amount' => 'decimal:2',
        'buyer_commission' => 'decimal:2',
        'total_amount_due' => 'decimal:2',
        'offered_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    /**
     * Second chance purchase belongs to original invoice
     */
    public function originalInvoice()
    {
        return $this->belongsTo(Invoice::class, 'original_invoice_id');
    }

    /**
     * Second chance purchase belongs to new invoice
     */
    public function newInvoice()
    {
        return $this->belongsTo(Invoice::class, 'new_invoice_id');
    }

    /**
     * Second chance purchase belongs to listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Second chance purchase belongs to buyer (second-highest bidder)
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Second chance purchase belongs to seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Second chance purchase belongs to bid
     */
    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    /**
     * Check if payment deadline has passed
     */
    public function isOverdue(): bool
    {
        return $this->payment_deadline && now()->greaterThan($this->payment_deadline);
    }
}
