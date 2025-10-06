<?php
// database/seeders/PackageSeeder.php
namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        // Buyer Packages
        Package::create([
            'title' => 'Basic Buyer',
            'role' => 'buyer',
            'price' => 49.99,
            'duration_days' => 365,
            'auction_bid_limit' => 2000.00,
            'auction_access' => true,
            'marketplace_access' => true,
            'features' => json_encode([
                'Access to Marketplace and Auctions',
                'Auction bidding limit: $2,000',
                'Limited to one bid at a time'
            ])
        ]);

        Package::create([
            'title' => 'Premium Buyer',
            'role' => 'buyer',
            'price' => 99.99,
            'duration_days' => 365,
            'auction_access' => true,
            'marketplace_access' => true,
            'features' => json_encode([
                'Access to Marketplace and Auctions',
                'No auction bidding limit',
                'Unlimited bids'
            ])
        ]);

        // Seller Packages
        Package::create([
            'title' => 'Casual Seller',
            'role' => 'seller',
            'price' => 65.00,
            'max_listings' => 1,
            'marketplace_access' => true,
            'features' => json_encode([
                'Limited to 1 active listing per purchase',
                'Listing duration: 30 days',
                'Access to Marketplace only (no auctions)',
                'Can repurchase the package to list again'
            ])
        ]);

        Package::create([
            'title' => 'Standard Seller',
            'role' => 'seller',
            'price' => 150.00,
            'duration_days' => 365,
            'max_listings_per_month' => 2,
            'auction_access' => true,
            'marketplace_access' => true,
            'seller_dashboard' => true,
            'features' => json_encode([
                'Up to 2 active listings per month',
                'Listings can go into either Marketplace or Auction',
                'Access to seller dashboard',
                'No Buy Now option',
                'No reserve pricing allowed'
            ])
        ]);

        Package::create([
            'title' => 'Advanced Seller',
            'role' => 'seller',
            'price' => 500.00,
            'duration_days' => 365,
            'max_listings_per_month' => 10,
            'auction_access' => true,
            'marketplace_access' => true,
            'seller_dashboard' => true,
            'buy_now_feature' => true,
            'reserve_pricing' => true,
            'account_manager' => true,
            'features' => json_encode([
                'Up to 10 active listings per month',
                'Listings allowed in both Marketplace and Auction',
                'Buy Now feature enabled',
                'Assigned account manager',
                'Can set a reserve price on auction listings'
            ])
        ]);

        Package::create([
            'title' => 'Enterprise Seller',
            'role' => 'seller',
            'price' => 0.00, // Custom pricing
            'duration_days' => 365,
            'auction_access' => true,
            'marketplace_access' => true,
            'seller_dashboard' => true,
            'buy_now_feature' => true,
            'reserve_pricing' => true,
            'account_manager' => true,
            'features' => json_encode([
                'Tailored accounts for large businesses',
                'Unlimited listings',
                'Full dashboard access and all seller features',
                'Custom support options',
                'Listings can go to either Marketplace or Auction'
            ])
        ]);
    }
}
