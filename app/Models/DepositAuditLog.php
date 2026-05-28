<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'admin_name',
        'buyer_id',
        'buyer_name',
        'action',
        'amount',
        'notes',
        'performed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'performed_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForBuyer($query, $buyerId)
    {
        return $query->where('buyer_id', $buyerId);
    }

    public function scopeFromDate($query, $date)
    {
        return $query->where('performed_at', '>=', $date);
    }

    public function scopeToDate($query, $date)
    {
        return $query->where('performed_at', '<=', $date);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Human-readable label for the action value.
     */
    public function actionLabel(): string
    {
        return match($this->action) {
            'deposit_confirmed'   => 'Deposit Confirmed',
            'deposit_rejected'    => 'Deposit Rejected',
            'withdrawal_approved' => 'Withdrawal Approved',
            'withdrawal_rejected' => 'Withdrawal Rejected',
            'manual_deposit'      => 'Manual Deposit',
            default               => ucwords(str_replace('_', ' ', $this->action)),
        };
    }

    /**
     * Tailwind colour classes for the action badge.
     * Green for money-in / approvals; red for rejections.
     */
    public function actionBadgeClass(): string
    {
        return match($this->action) {
            'deposit_confirmed',
            'withdrawal_approved',
            'manual_deposit'    => 'bg-green-50 text-green-800 border border-green-200',
            'deposit_rejected',
            'withdrawal_rejected' => 'bg-red-50 text-red-800 border border-red-200',
            default               => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    }

    /**
     * Icon name (Material Icons) for the action.
     */
    public function actionIcon(): string
    {
        return match($this->action) {
            'deposit_confirmed'   => 'check_circle',
            'deposit_rejected'    => 'cancel',
            'withdrawal_approved' => 'savings',
            'withdrawal_rejected' => 'block',
            'manual_deposit'      => 'add_circle',
            default               => 'info',
        };
    }
}
