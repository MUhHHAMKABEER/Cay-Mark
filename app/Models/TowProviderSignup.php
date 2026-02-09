<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TowProviderSignup extends Model
{
    protected $table = 'tow_provider_signups';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'business_name',
        'license_path',
        'license_filename',
        'terms_accepted_at',
        'amount_cents',
        'payment_status',
        'payment_reference',
        'status',
    ];

    protected $casts = [
        'terms_accepted_at' => 'datetime',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getAmountDollarsAttribute(): float
    {
        return round($this->amount_cents / 100, 2);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
