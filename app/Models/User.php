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
    'dob','gender','phone','marketing_opt_in'
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

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }



    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

}
