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
        'amount',
        'method',
        'status',
        'metadata',
    ];

    /**
     * A payment belongs to a subscription.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
