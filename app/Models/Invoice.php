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
        'original_amount',
        'deposit_applied',
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
        'buyer_commission'   => 'decimal:2',
        'total_amount_due'   => 'decimal:2',
        'original_amount'    => 'decimal:2',
        'deposit_applied'    => 'decimal:2',
        'sale_date'          => 'date',
        'invoice_generated_at' => 'datetime',
        'payment_deadline'   => 'datetime',
        'is_overdue'         => 'boolean',
        'overdue_at'         => 'datetime',
        'paid_at'            => 'datetime',
        'metadata'           => 'array',
    ];

    /**
     * Whether a security deposit was credited against this invoice.
     */
    public function hasDepositApplied(): bool
    {
        return (float) $this->deposit_applied > 0;
    }

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

    /**
     * Admin Sales / Payouts table: pipeline phase and badge classes.
     *
     * @return array{key: string, label: string, badge_class: string}
     */
    public function adminSalesPayoutPipelineStatus(): array
    {
        $listing = $this->relationLoaded('listing') ? $this->listing : $this->listing()->first();
        $payout = $this->relationLoaded('payout') ? $this->payout : $this->payout()->first();

        if ($this->payment_status === 'pending') {
            return [
                'key' => 'awaiting_payment',
                'label' => 'Awaiting Payment',
                'badge_class' => 'bg-amber-50 text-amber-800 border border-amber-200',
            ];
        }

        if ($this->payment_status === 'paid') {
            if ($listing && ! $listing->pickup_confirmed) {
                return [
                    'key' => 'payment_received',
                    'label' => 'Payment Received',
                    'badge_class' => 'bg-sky-50 text-sky-800 border border-sky-200',
                ];
            }

            if ($payout && in_array($payout->status, ['sent', 'paid_successfully'], true)) {
                // All date fields are parsed through Carbon::parse() to handle both
                // string and Carbon instances (completed_at may not be cast in the model).
                $dateForLabel = $payout->completed_at
                    ? \Carbon\Carbon::parse($payout->completed_at)->format('M j, Y')
                    : ($payout->date_sent
                        ? \Carbon\Carbon::parse($payout->date_sent)->format('M j, Y')
                        : ($payout->payout_processed_at
                            ? \Carbon\Carbon::parse($payout->payout_processed_at)->format('M j, Y')
                            : now()->format('M j, Y')));

                return [
                    'key' => 'closed',
                    'label' => 'Closed — '.$dateForLabel,
                    'badge_class' => 'bg-emerald-50 text-emerald-800 border border-emerald-200',
                ];
            }

            return [
                'key' => 'ready_for_payout',
                'label' => 'Ready for Payout',
                'badge_class' => 'bg-blue-50 text-blue-800 border border-blue-200',
            ];
        }

        return [
            'key' => 'other',
            'label' => ucfirst((string) $this->payment_status),
            'badge_class' => 'bg-slate-100 text-slate-700 border border-slate-200',
        ];
    }
}
