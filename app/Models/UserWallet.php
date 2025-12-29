<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'available_balance',
        'locked_balance',
        'total_balance',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
        'total_balance' => 'decimal:2',
    ];

    /**
     * Wallet belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create wallet for user
     */
    public static function getOrCreateForUser($userId)
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'available_balance' => 0,
                'locked_balance' => 0,
                'total_balance' => 0,
            ]
        );
    }

    /**
     * Check if user has sufficient available balance
     */
    public function hasAvailableBalance(float $amount): bool
    {
        return $this->available_balance >= $amount;
    }

    /**
     * Check if user has sufficient total balance (available + locked)
     */
    public function hasTotalBalance(float $amount): bool
    {
        return $this->total_balance >= $amount;
    }

    /**
     * Update total balance (available + locked)
     */
    public function updateTotalBalance()
    {
        $this->total_balance = $this->available_balance + $this->locked_balance;
        $this->save();
    }
}
