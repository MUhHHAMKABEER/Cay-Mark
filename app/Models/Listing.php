<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'seller_id',
        'listing_method',
        'auction_duration',
        'major_category',
        'subcategory',
        'other_make',
        'other_model',
        'condition',
        'make',
        'model',
        'trim',
        'year',
        'color',
        'fuel_type',
        'transmission',
        'title_status',
        'primary_damage',
        'secondary_damage',
        'keys_available',
        'engine_type',
        'hull_material',
        'category_type',
        'status',
        'expiry_status',
        'expires_at', // new column
          'price',
    'odometer',
     'expiry_status',
     'bought', 
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Booted method to handle model events.
     */
    protected static function booted()
    {
        static::retrieved(function ($listing) {
            if ($listing->isExpired() && $listing->expiry_status !== 'expired') {
                $listing->expiry_status = 'expired';
                $listing->save();
            }
        });
    }

    /**
     * Relationship: A listing has many images.
     */
    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }

    /**
     * Relationship: A listing belongs to a seller (user).
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Check if the listing is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function bids()
{
    return $this->hasMany(Bid::class);
}

/**
 * highest active bid
 */
public function highestBid()
{
    return $this->hasOne(Bid::class)->orderByDesc('amount');
}

}
