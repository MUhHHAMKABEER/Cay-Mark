<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Listing;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Full buyer workflow: login → bid → end auction (DB) → process ended → checkout → pay.
 * Requires MySQL for migrations (SQLite fails on ENUM/MODIFY). Alternatively run:
 *   php artisan caymark:test-buyer-workflow
 * against your live DB (uses a transaction and rolls back).
 */
class BuyerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private const BUYER_EMAIL = 'buyer@gmail.com';
    private const BUYER_PASSWORD = '1234567890';

    /**
     * Full buyer workflow: login → bid → auction ends (DB) → process ended → checkout → pay.
     */
    public function test_full_buyer_workflow_login_bid_auction_ends_then_buy(): void
    {
        // 1. Create seller and buyer (using provided buyer credentials)
        $seller = User::factory()->create([
            'name' => 'Test Seller',
            'email' => 'seller@test.com',
            'role' => 'seller',
            'registration_complete' => true,
        ]);

        $buyer = User::factory()->create([
            'name' => 'Test Buyer',
            'email' => self::BUYER_EMAIL,
            'password' => Hash::make(self::BUYER_PASSWORD),
            'role' => 'buyer',
            'registration_complete' => true,
        ]);

        // 2. Create an approved auction listing (future end time)
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'listing_method' => 'auction',
            'status' => 'approved',
            'auction_duration' => 7,
            'major_category' => 'Vehicles',
            'condition' => 'used',
            'year' => '2020',
            'make' => 'Honda',
            'model' => 'Civic',
            'starting_price' => 1000,
            'price' => 1000,
            'auction_start_time' => now()->subDay(),
            'auction_end_time' => now()->addDay(),
        ]);
        $listing->refresh(); // ensure slug is set from boot

        $slug = $listing->getSlugOrGenerate();
        $this->assertNotEmpty($slug, 'Listing should have a slug for bid route');

        // 3. Login as buyer
        $loginResponse = $this->post(route('login'), [
            'email' => self::BUYER_EMAIL,
            'password' => self::BUYER_PASSWORD,
        ]);
        $loginResponse->assertRedirect(route('welcome'));
        $this->assertAuthenticatedAs($buyer);

        // 4. Place a bid (min next bid from 1000 is 1050 per increment table: $1k–$4999 = $50)
        $bidAmount = 1050;
        $bidResponse = $this->postJson(route('auction.bid.store', ['listing' => $slug]), [
            'amount' => $bidAmount,
        ], [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $bidResponse->assertOk();
        $bidResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('bids', [
            'listing_id' => $listing->id,
            'user_id' => $buyer->id,
            'amount' => $bidAmount,
            'status' => 'active',
        ]);

        $listing->refresh();
        $this->assertEquals((float) $bidAmount, (float) $listing->current_bid);

        // 5. End the auction by setting auction_end_time in the past
        $listing->update(['auction_end_time' => now()->subHour()]);

        // 6. Process ended auctions (generate invoice for winner)
        $invoiceService = new InvoiceService();
        $count = $invoiceService->processEndedAuctions();
        $this->assertSame(1, $count, 'Exactly one invoice should be generated');

        $listing->refresh();
        $this->assertSame('sold', $listing->status);

        $invoice = Invoice::where('listing_id', $listing->id)->where('buyer_id', $buyer->id)->first();
        $this->assertNotNull($invoice, 'Buyer should have a pending invoice');
        $this->assertSame('pending', $invoice->payment_status);

        // 7. Visit checkout (single item)
        $checkoutResponse = $this->get(route('buyer.payment.checkout-single', ['invoiceId' => $invoice->id]));
        $checkoutResponse->assertOk();

        // 8. Process payment (demo card 4242...)
        $processResponse = $this->post(route('buyer.payment.process'), [
            'invoice_ids' => [$invoice->id],
            'card_number' => '4242424242424242',
            'card_expiry' => '12/28',
            'card_cvv' => '123',
            'cardholder_name' => 'Test Buyer',
        ]);

        $processResponse->assertRedirect(route('buyer.auctions-won'));
        $processResponse->assertSessionHas('success');

        $invoice->refresh();
        $this->assertSame('paid', $invoice->payment_status);
        $this->assertNotNull($invoice->paid_at);
    }

    /**
     * Buyer can log in with provided credentials and reach welcome (listings).
     */
    public function test_buyer_login_redirects_to_welcome(): void
    {
        User::factory()->create([
            'email' => self::BUYER_EMAIL,
            'password' => Hash::make(self::BUYER_PASSWORD),
            'role' => 'buyer',
            'registration_complete' => true,
        ]);

        $response = $this->post(route('login'), [
            'email' => self::BUYER_EMAIL,
            'password' => self::BUYER_PASSWORD,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('welcome'));
    }
}
