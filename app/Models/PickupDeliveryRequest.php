<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupDeliveryRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'thread_id',
        'buyer_id',
        'delivery_address',
        'preferred_date',
        'preferred_time',
        'additional_notes',
        'status',
        'response_notes',
        'submitted_at',
        'responded_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime',
        'submitted_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function thread()
    {
        return $this->belongsTo(PostAuctionThread::class, 'thread_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function approve(?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'response_notes' => $notes,
            'responded_at' => now(),
        ]);
    }

    public function reject(?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'response_notes' => $notes,
            'responded_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
