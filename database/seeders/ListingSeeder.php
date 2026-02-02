<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class ListingSeeder extends Seeder
{
    /**
     * Car image URLs from the internet (Unsplash – free to use).
     */
    protected array $carImageUrls = [
        'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1667805630247-28c2a8db1cb4?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1716384277908-0024e397c30c?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1657274259016-e452dee19b21?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1553440569-bcc63803a83d?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1549317661-b2c2d0f7c4e9?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1542362567-3a98c2f4e3e1?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1494976388531-10570e22a2c9?w=800&auto=format&fit=crop',
        'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&auto=format&fit=crop',
    ];

    /**
     * Download image from URL and save to public/uploads/listings/. Returns filename or null on failure.
     */
    protected function downloadCarImage(string $url, string $prefix): ?string
    {
        $uploadDir = public_path('uploads/listings');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $filename = $prefix . '_' . time() . '_' . uniqid() . '.jpg';
        $path = $uploadDir . DIRECTORY_SEPARATOR . $filename;

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(15)
                ->withOptions(['verify' => false])  // WAMP/Windows: avoid cURL error 60 (SSL CA bundle)
                ->get($url);
            if ($response->successful() && strlen($response->body()) > 0) {
                File::put($path, $response->body());
                return $filename;
            }
        } catch (\Throwable $e) {
            $this->command?->warn("Could not download image: {$url} - " . $e->getMessage());
        }

        return null;
    }

    /**
     * Car listing definitions: 5 for seller 3, 5 for seller 10.
     */
    protected function carDefinitions(): array
    {
        return [
            // Seller 3 (seller@gmail.com)
            ['seller_id' => 3, 'year' => '2022', 'make' => 'TOYOTA', 'model' => 'CAMRY', 'trim' => 'XLE', 'color' => 'SILVER', 'odometer' => 18500, 'price' => 24500, 'listing_method' => 'buy_now'],
            ['seller_id' => 3, 'year' => '2020', 'make' => 'HONDA', 'model' => 'CIVIC', 'trim' => 'SPORT', 'color' => 'BLUE', 'odometer' => 32000, 'price' => 19800, 'listing_method' => 'auction', 'starting_price' => 15000, 'reserve_price' => 18500, 'auction_duration' => 72],
            ['seller_id' => 3, 'year' => '2021', 'make' => 'FORD', 'model' => 'F-150', 'trim' => 'XLT', 'color' => 'BLACK', 'odometer' => 22000, 'price' => 38500, 'listing_method' => 'buy_now'],
            ['seller_id' => 3, 'year' => '2019', 'make' => 'NISSAN', 'model' => 'ALTIMA', 'trim' => 'SR', 'color' => 'WHITE', 'odometer' => 41000, 'price' => 16900, 'listing_method' => 'buy_now'],
            ['seller_id' => 3, 'year' => '2023', 'make' => 'CHEVROLET', 'model' => 'MALIBU', 'trim' => 'PREMIER', 'color' => 'RED', 'odometer' => 8500, 'price' => 26900, 'listing_method' => 'auction', 'starting_price' => 22000, 'reserve_price' => 25500, 'auction_duration' => 48],
            // Seller 10 (Aimee Grant - voxolyf@mailinator.com)
            ['seller_id' => 10, 'year' => '2021', 'make' => 'MAZDA', 'model' => 'CX-5', 'trim' => 'GRAND TOURING', 'color' => 'GRAY', 'odometer' => 28000, 'price' => 27900, 'listing_method' => 'buy_now'],
            ['seller_id' => 10, 'year' => '2020', 'make' => 'HYUNDAI', 'model' => 'TUCSON', 'trim' => 'SEL', 'color' => 'GREEN', 'odometer' => 35000, 'price' => 22900, 'listing_method' => 'auction', 'starting_price' => 18000, 'reserve_price' => 21500, 'auction_duration' => 72],
            ['seller_id' => 10, 'year' => '2022', 'make' => 'KIA', 'model' => 'SORENTO', 'trim' => 'SX', 'color' => 'PEARL WHITE', 'odometer' => 19500, 'price' => 34500, 'listing_method' => 'buy_now'],
            ['seller_id' => 10, 'year' => '2018', 'make' => 'SUBARU', 'model' => 'OUTBACK', 'trim' => 'LIMITED', 'color' => 'NAVY', 'odometer' => 52000, 'price' => 24900, 'listing_method' => 'buy_now'],
            ['seller_id' => 10, 'year' => '2023', 'make' => 'VOLKSWAGEN', 'model' => 'TIGUAN', 'trim' => 'SE', 'color' => 'SILVER', 'odometer' => 12000, 'price' => 31900, 'listing_method' => 'auction', 'starting_price' => 28000, 'reserve_price' => 30500, 'auction_duration' => 48],
        ];
    }

    public function run(): void
    {
        $definitions = $this->carDefinitions();
        $urlIndex = 0;

        foreach ($definitions as $def) {
            $listing = Listing::create([
                'seller_id'           => $def['seller_id'],
                'listing_method'      => $def['listing_method'],
                'auction_duration'    => $def['auction_duration'] ?? null,
                'major_category'      => 'VEHICLES',
                'vehicle_type'        => 'CAR',
                'body_style'          => 'SEDAN',
                'subcategory'         => 'CARS',
                'condition'           => 'used',
                'make'                => $def['make'],
                'model'               => $def['model'],
                'trim'                => $def['trim'],
                'year'                => $def['year'],
                'color'               => $def['color'],
                'interior_color'      => 'BLACK',
                'odometer'            => $def['odometer'],
                'fuel_type'           => 'GASOLINE',
                'transmission'        => 'automatic',
                'drive_type'          => 'FWD',
                'title_status'        => 'CLEAN',
                'keys_available'      => true,
                'price'               => $def['price'],
                'starting_price'      => $def['starting_price'] ?? null,
                'reserve_price'       => $def['reserve_price'] ?? null,
                'buy_now_price'       => $def['listing_method'] === 'buy_now' ? $def['price'] : null,
                'island'              => 'NEW PROVIDENCE',
                'status'              => 'approved',
                'expiry_status'       => 'active',
                'expires_at'          => now()->addDays(30),
                'auction_start_time'  => $def['listing_method'] === 'auction' ? now()->subHours(2) : null,
                'auction_end_time'    => $def['listing_method'] === 'auction' ? now()->addHours($def['auction_duration'] ?? 72) : null,
                'item_number'         => Listing::generateItemNumber(),
            ]);

            // Download cover image from internet
            $coverUrl = $this->carImageUrls[$urlIndex % count($this->carImageUrls)];
            $urlIndex++;
            $coverFilename = $this->downloadCarImage($coverUrl, 'SEED_COVER');
            if (!$coverFilename) {
                $coverFilename = $this->downloadCarImage($this->carImageUrls[$urlIndex % count($this->carImageUrls)], 'SEED_COVER');
                $urlIndex++;
            }

            if ($coverFilename) {
                $coverImage = ListingImage::create([
                    'listing_id'  => $listing->id,
                    'image_path'  => $coverFilename,
                ]);
                $listing->update(['cover_photo_id' => $coverImage->id]);
            }

            // Download 2 extra car images per listing
            for ($i = 0; $i < 2; $i++) {
                $extraUrl = $this->carImageUrls[$urlIndex % count($this->carImageUrls)];
                $urlIndex++;
                $extraFilename = $this->downloadCarImage($extraUrl, 'SEED_IMG');
                if ($extraFilename) {
                    ListingImage::create([
                        'listing_id'  => $listing->id,
                        'image_path'  => $extraFilename,
                    ]);
                }
            }
        }
    }
}
