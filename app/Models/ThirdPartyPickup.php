<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyPickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'buyer_id',
        'authorized_name',
        'pickup_type',
        'is_active',
        'authorized_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'authorized_at' => 'datetime',
    ];

    /**
     * Third party pickup belongs to thread
     */
    public function thread()
    {
        return $this->belongsTo(PostAuctionThread::class, 'thread_id');
    }

    /**
     * Third party pickup belongs to buyer
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
