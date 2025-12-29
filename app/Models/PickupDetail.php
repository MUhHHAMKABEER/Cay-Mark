<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'seller_id',
        'pickup_date',
        'pickup_time',
        'street_address',
        'directions_notes',
        'status',
        'submitted_at',
        'accepted_at',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'pickup_time' => 'datetime',
        'submitted_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Pickup detail belongs to thread
     */
    public function thread()
    {
        return $this->belongsTo(PostAuctionThread::class, 'thread_id');
    }

    /**
     * Pickup detail belongs to seller
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Pickup detail has change requests
     */
    public function changeRequests()
    {
        return $this->hasMany(PickupChangeRequest::class, 'pickup_detail_id');
    }

    /**
     * Mark as accepted
     */
    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    /**
     * Mark as confirmed (final)
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
        ]);
    }
}
