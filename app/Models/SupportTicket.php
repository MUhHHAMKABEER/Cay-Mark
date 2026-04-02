<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
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

    const TITLE_OPTIONS = [
        'Account Issue',
        'Payment Issue',
        'Listing Issue',
        'Auction Issue',
        'Technical Problem',
        'Membership Issue',
        'Payout Issue',
        'Other',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
