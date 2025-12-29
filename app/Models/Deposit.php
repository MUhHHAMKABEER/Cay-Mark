<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'status',
        'bid_id',
        'listing_id',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Deposit belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Deposit may be related to a bid
     */
    public function bid()
    {
        return $this->belongsTo(Bid::class);
    }

    /**
     * Deposit may be related to a listing
     */
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
