<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'amount',
        'status',
        'requested_at',
        'confirmed_at',
        'confirmed_by',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'requested_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    // ── Relationships ───────────────────────────────────────────────────

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    public function scopePendingWire($query)
    {
        return $query->where('status', 'pending_wire');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ── Helpers ────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending_wire';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
