<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_ticket_number',
        'user_id',
        'title',
        'message',
        'status',
        'admin_reply',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    const STATUS_OPEN = 'open';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_RESOLVED = 'resolved';

    const STATUS_CLOSED = 'closed';

    public const CATEGORY_OPTIONS_BUYER = [
        'Account',
        'Auctions',
        'Payments & Deposits',
        'Disputes',
        'General Inquiry',
        'Other',
    ];

    public const CATEGORY_OPTIONS_SELLER = [
        'Account',
        'Listings & Auctions',
        'Payouts',
        'Disputes',
        'General Inquiry',
        'Other',
    ];

    public const CATEGORY_OPTIONS_SUPPORT = [
        'Account',
        'Disputes',
        'General Inquiry',
        'Other',
    ];

    public const CATEGORY_OPTIONS_FINANCE = [
        'Payments & Deposits',
        'Payouts',
    ];

    public const CATEGORY_OPTIONS_OPERATIONS = [
        'Auctions',
        'Listings & Auctions',
    ];

    /**
     * Map ticket category → routing queue per Notes for System Issues.
     * - Support: Account / Disputes / General Inquiry / Other
     * - Finance: Payments & Deposits / Payouts
     * - Operations: Auctions / Listings & Auctions
     */
    public static function routingQueueForCategory(?string $category): string
    {
        $category = (string) $category;
        if (in_array($category, self::CATEGORY_OPTIONS_FINANCE, true)) {
            return 'finance';
        }
        if (in_array($category, self::CATEGORY_OPTIONS_OPERATIONS, true)) {
            return 'operations';
        }

        return 'support';
    }

    /**
     * @return list<string>
     */
    public static function categoryOptionsForRole(string $role): array
    {
        return match ($role) {
            User::ROLE_BUYER => self::CATEGORY_OPTIONS_BUYER,
            User::ROLE_SELLER => self::CATEGORY_OPTIONS_SELLER,
            default => [],
        };
    }

    public static function generateUniquePublicTicketNumber(): string
    {
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $n = (string) random_int(100000, 999999);
            if (! self::query()->where('public_ticket_number', $n)->exists()) {
                return $n;
            }
        }

        throw new RuntimeException('Could not generate a unique public ticket number.');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
