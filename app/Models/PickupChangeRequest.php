<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'pickup_detail_id',
        'buyer_id',
        'requested_pickup_date',
        'requested_pickup_time',
        'status',
        'countered_pickup_date',
        'countered_pickup_time',
        'requested_at',
        'responded_at',
    ];

    protected $casts = [
        'requested_pickup_date' => 'date',
        'requested_pickup_time' => 'datetime',
        'countered_pickup_date' => 'date',
        'countered_pickup_time' => 'datetime',
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Change request belongs to thread
     */
    public function thread()
    {
        return $this->belongsTo(PostAuctionThread::class, 'thread_id');
    }

    /**
     * Change request belongs to pickup detail
     */
    public function pickupDetail()
    {
        return $this->belongsTo(PickupDetail::class, 'pickup_detail_id');
    }

    /**
     * Change request belongs to buyer
     */
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    /**
     * Approve the change request
     */
    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'responded_at' => now(),
        ]);

        // Update pickup detail with new date/time
        $this->pickupDetail->update([
            'pickup_date' => $this->requested_pickup_date ?? $this->pickupDetail->pickup_date,
            'pickup_time' => $this->requested_pickup_time ?? $this->pickupDetail->pickup_time,
            'status' => 'confirmed',
        ]);
    }

    /**
     * Counter with new date/time
     */
    public function counter($date, $time): void
    {
        $this->update([
            'status' => 'countered',
            'countered_pickup_date' => $date,
            'countered_pickup_time' => $time,
            'responded_at' => now(),
        ]);
    }
}
