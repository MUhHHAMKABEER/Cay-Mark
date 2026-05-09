<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessagingThreadEvent extends Model
{
    use HasFactory;

    public const ROLE_BUYER = 'buyer';

    public const ROLE_SELLER = 'seller';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SYSTEM = 'system';

    public const TYPE_SCHEDULE_PROPOSED = 'schedule_proposed';

    public const TYPE_SCHEDULE_RESENT = 'schedule_resent';

    public const TYPE_CHANGE_REQUESTED = 'change_requested';

    public const TYPE_LOCATION_REQUESTED = 'location_requested';

    public const TYPE_CHANGE_APPROVED = 'change_approved';

    public const TYPE_CHANGE_COUNTERED = 'change_countered';

    public const TYPE_DELIVERY_REQUESTED = 'delivery_requested';

    public const TYPE_DELIVERY_RESPONDED = 'delivery_responded';

    public const TYPE_THIRD_PARTY_AUTHORIZED = 'third_party_authorized';

    public const TYPE_PICKUP_CONFIRMED = 'pickup_confirmed';

    public const TYPE_READY_FOR_PICKUP = 'ready_for_pickup';

    public const TYPE_SALE_COMPLETED_CONFIRMED = 'sale_completed_confirmed';

    public const TYPE_OTHER_REQUEST = 'other_request';

    public const TYPE_ISSUE_REPORTED = 'issue_reported';

    public const TYPE_ASSISTANCE_REQUESTED = 'assistance_requested';

    public const TYPE_ADMIN_FLAGGED = 'admin_flagged';

    public const TYPE_ADMIN_UNFLAGGED = 'admin_unflagged';

    protected $fillable = [
        'thread_id',
        'actor_id',
        'actor_role',
        'type',
        'payload',
        'counts_as_exchange',
    ];

    protected $casts = [
        'payload' => 'array',
        'counts_as_exchange' => 'boolean',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(PostAuctionThread::class, 'thread_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Persist a thread event. Returns the saved row.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function record(
        PostAuctionThread $thread,
        ?User $actor,
        string $type,
        array $payload = [],
        bool $countsAsExchange = false,
        ?string $actorRole = null,
    ): self {
        if ($actorRole === null) {
            $actorRole = match (true) {
                $actor === null => self::ROLE_SYSTEM,
                $actor->id === $thread->buyer_id => self::ROLE_BUYER,
                $actor->id === $thread->seller_id => self::ROLE_SELLER,
                default => self::ROLE_ADMIN,
            };
        }

        return self::create([
            'thread_id' => $thread->id,
            'actor_id' => $actor?->id ?? $thread->seller_id,
            'actor_role' => $actorRole,
            'type' => $type,
            'payload' => $payload,
            'counts_as_exchange' => $countsAsExchange,
        ]);
    }
}
