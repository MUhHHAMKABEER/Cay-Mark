<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupChangeRequest extends Model
{
    use HasFactory;

    public const TYPE_DATE_TIME = 'date_time';

    public const TYPE_LOCATION = 'location';

    protected $fillable = [
        'thread_id',
        'pickup_detail_id',
        'buyer_id',
        'request_type',
        'requested_pickup_date',
        'requested_pickup_time',
        'requested_location',
        'additional_notes',
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

    public function thread()
    {
        return $this->belongsTo(PostAuctionThread::class, 'thread_id');
    }

    public function pickupDetail()
    {
        return $this->belongsTo(PickupDetail::class, 'pickup_detail_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function isLocationRequest(): bool
    {
        return $this->request_type === self::TYPE_LOCATION;
    }

    public function approve(): void
    {
        $this->update([
            'status' => 'approved',
            'responded_at' => now(),
        ]);

        $detail = $this->pickupDetail;
        if (! $detail) {
            return;
        }

        $payload = [
            'pickup_date' => $this->requested_pickup_date ?? $detail->pickup_date,
            'pickup_time' => $this->requested_pickup_time ?? $detail->pickup_time,
            'status' => 'confirmed',
        ];

        if ($this->isLocationRequest() && ! empty($this->requested_location)) {
            $payload['street_address'] = $this->requested_location;
        }

        $detail->update($payload);
    }

    public function counter($date, $time): void
    {
        $this->update([
            'status' => 'countered',
            'countered_pickup_date' => $date,
            'countered_pickup_time' => $time,
            'responded_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);
    }
}
