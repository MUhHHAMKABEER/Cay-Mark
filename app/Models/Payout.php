<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'payout_number',
        'invoice_id',
        'listing_id',
        'seller_id',
        'buyer_name',
        'item_title',
        'sale_price',
        'seller_commission',
        'net_payout',
        'sale_date',
        'payout_generated_at',
        'payout_processed_at',
        'status',
        'payment_method',
        'payment_reference',
        'transaction_reference',
        'date_sent',
        'notes',
        'finance_notes',
        'metadata',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'seller_commission' => 'decimal:2',
        'net_payout' => 'decimal:2',
        'sale_date' => 'date',
        'date_sent' => 'date',
        'payout_generated_at' => 'datetime',
        'payout_processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Payout belongs to an invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Payout belongs to a listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Payout belongs to seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Generate unique payout number
     */
    public static function generatePayoutNumber(): string
    {
        return 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
