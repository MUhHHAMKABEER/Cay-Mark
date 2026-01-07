<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'listing_id',
        'bid_id',
        'buyer_id',
        'seller_id',
        'item_name',
        'item_id',
        'winning_bid_amount',
        'buyer_commission',
        'total_amount_due',
        'sale_date',
        'invoice_generated_at',
        'payment_deadline',
        'payment_status',
        'is_overdue',
        'overdue_at',
        'paid_at',
        'pdf_path',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'winning_bid_amount' => 'decimal:2',
        'buyer_commission' => 'decimal:2',
        'total_amount_due' => 'decimal:2',
        'sale_date' => 'date',
        'invoice_generated_at' => 'datetime',
        'payment_deadline' => 'datetime',
        'is_overdue' => 'boolean',
        'overdue_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Invoice belongs to a listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Invoice belongs to winning bid
     */
    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    /**
     * Invoice belongs to buyer
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Invoice belongs to seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Invoice has one payout
     */
    public function payout()
    {
        return $this->hasOne(Payout::class);
    }

    /**
     * Invoice has one post-auction thread
     */
    public function postAuctionThread()
    {
        return $this->hasOne(PostAuctionThread::class);
    }

    /**
     * Invoice has buyer default record
     */
    public function buyerDefault()
    {
        return $this->hasOne(BuyerDefault::class);
    }

    /**
     * Check if payment deadline has passed
     */
    public function isPaymentOverdue(): bool
    {
        return $this->payment_deadline && now()->greaterThan($this->payment_deadline) && $this->payment_status === 'pending';
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Get paid invoices for buyer
     */
    public static function getPaidInvoicesForBuyer($buyerId)
    {
        return static::where('buyer_id', $buyerId)
            ->where('payment_status', 'paid')
            ->with(['listing.images', 'bid'])
            ->latest('paid_at')
            ->get();
    }

    /**
     * Get pending invoices for buyer
     */
    public static function getPendingInvoicesForBuyer($buyerId)
    {
        return static::where('buyer_id', $buyerId)
            ->where('payment_status', 'pending')
            ->with(['listing.images', 'bid'])
            ->latest('invoice_generated_at')
            ->get();
    }

    /**
     * Get paid invoices for seller
     */
    public static function getPaidInvoicesForSeller($sellerId)
    {
        return static::where('seller_id', $sellerId)
            ->where('payment_status', 'paid')
            ->with(['listing.images', 'buyer'])
            ->latest('paid_at')
            ->get();
    }

    /**
     * Get total revenue for seller
     */
    public static function getTotalRevenueForSeller($sellerId): float
    {
        return static::where('seller_id', $sellerId)
            ->where('payment_status', 'paid')
            ->sum('winning_bid_amount');
    }

    /**
     * Check if invoice is paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if invoice is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(): void
    {
        $this->payment_status = 'paid';
        $this->paid_at = now();
        $this->is_overdue = false;
        $this->save();
    }
}
