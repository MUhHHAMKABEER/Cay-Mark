<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
// app/Models/User.php
protected $fillable = [
    'name','email','password','username','nationality','island',
    'dob','gender','phone','marketing_opt_in','role',
    'id_type','business_license_path','relationship_to_business','registration_complete',
    'is_restricted','restriction_ends_at','restriction_reason'
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
   protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'marketing_opt_in' => 'boolean',
        'registration_complete' => 'boolean',
        'is_restricted' => 'boolean',
        'restriction_ends_at' => 'datetime',
    ];


    const ROLE_BUYER = 'buyer';
    const ROLE_SELLER = 'seller';
    const ROLE_GUEST = 'guest';

    // optional helper method
    public function isSeller()
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isBuyer()
    {
        return $this->role === self::ROLE_BUYER;
    }

public function hasActiveSubscription()
{
    return $this->payments()
        ->where('status', 'paid')
        ->exists();
}

public function payments()
{
    return $this->hasMany(Payment::class);
}
public function buyNows()
{
    return $this->hasMany(BuyNow::class);
}

public function watchlist()
{
    return $this->belongsToMany(Listing::class, 'watchlists')->withTimestamps();
}
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'buyer_id');
    }

    public function sellerInvoices()
    {
        return $this->hasMany(Invoice::class, 'seller_id');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'seller_id');
    }

    public function payoutMethod()
    {
        return $this->hasOne(SellerPayoutMethod::class);
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    /**
     * Check if user has completed registration
     */
    public function isRegistrationComplete()
    {
        return $this->registration_complete === true;
    }

    /**
     * Check if user has a role assigned
     */
    public function hasRole()
    {
        return !empty($this->role);
    }

    /**
     * Check if user can access buyer features
     */
    public function canAccessBuyerFeatures()
    {
        return $this->isRegistrationComplete() && $this->role === self::ROLE_BUYER;
    }

    /**
     * Check if user can access seller features
     */
    public function canAccessSellerFeatures()
    {
        return $this->isRegistrationComplete() && $this->role === self::ROLE_SELLER;
    }

}
