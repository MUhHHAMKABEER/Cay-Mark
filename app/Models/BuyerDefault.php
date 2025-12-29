<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerDefault extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'listing_id',
        'bid_id',
        'invoice_amount',
        'deposit_penalty_amount',
        'deposit_penalty_percentage',
        'status',
        'resolution_type',
        'defaulted_at',
        'restriction_ends_at',
        'admin_notes',
    ];

    protected $casts = [
        'invoice_amount' => 'decimal:2',
        'deposit_penalty_amount' => 'decimal:2',
        'deposit_penalty_percentage' => 'decimal:2',
        'defaulted_at' => 'datetime',
        'restriction_ends_at' => 'datetime',
    ];

    /**
     * Default belongs to user (buyer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Default belongs to invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Default belongs to listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    /**
     * Default belongs to bid
     */
    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    /**
     * Check if restriction period has ended
     */
    public function isRestrictionActive(): bool
    {
        return $this->restriction_ends_at && now()->lessThan($this->restriction_ends_at);
    }
}
