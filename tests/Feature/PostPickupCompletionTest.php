<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Payout;
use App\Models\PostAuctionThread;
use App\Models\User;
use App\Services\Seller\SellerPickupCompletionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * End-to-end coverage for the post-pickup completion flow:
 *  - Seller submits the buyer's 6-digit PIN
 *  - Listing + thread are flipped to confirmed and the PIN is cleared (single-use)
 *  - Payout row is generated (idempotent)
 *  - All further messaging POSTs are rejected once the thread is closed
 */
class PostPickupCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_pin_completes_pickup_creates_payout_and_closes_thread(): void
    {
        [$seller, $buyer, $listing, $invoice] = $this->buildPaidSale();

        $pin = $listing->generatePickupPin();
        $this->assertSame(6, strlen($pin), 'Generated PIN should be 6 digits');

        $thread = PostAuctionThread::create([
            'invoice_id' => $invoice->id,
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'is_unlocked' => true,
            'unlocked_at' => now(),
        ]);

        $result = (new SellerPickupCompletionService())
            ->completeAfterSellerPin($listing->fresh(), $seller, $pin);

        $this->assertTrue($result['success'], 'Completion service should succeed with a valid PIN.');

        $listing->refresh();
        $thread->refresh();

        $this->assertTrue((bool) $listing->pickup_confirmed, 'Listing should be marked as picked up.');
        $this->assertNull($listing->pickup_pin, 'PIN must be cleared after a successful pickup (single-use).');
        $this->assertSame($seller->id, (int) $listing->pickup_confirmed_by);

        $this->assertTrue((bool) $thread->pickup_confirmed, 'Thread should be marked as closed.');
        $this->assertNotNull($thread->pickup_confirmed_at);
        $this->assertNotNull($thread->buyer_completion_confirmed_at, 'Buyer auto-confirmation timestamp should be set.');

        $this->assertDatabaseHas('payouts', [
            'invoice_id' => $invoice->id,
            'listing_id' => $listing->id,
            'seller_id' => $seller->id,
        ]);

        $this->assertDatabaseHas('messaging_thread_events', [
            'thread_id' => $thread->id,
            'type' => 'pickup_confirmed',
            'actor_id' => $seller->id,
        ]);
    }

    public function test_invalid_pin_does_not_close_the_thread(): void
    {
        [$seller, $buyer, $listing, $invoice] = $this->buildPaidSale();
        $listing->generatePickupPin();

        $thread = PostAuctionThread::create([
            'invoice_id' => $invoice->id,
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'is_unlocked' => true,
            'unlocked_at' => now(),
        ]);

        $result = (new SellerPickupCompletionService())
            ->completeAfterSellerPin($listing->fresh(), $seller, '000000');

        $this->assertFalse($result['success']);
        $this->assertFalse((bool) $listing->fresh()->pickup_confirmed);
        $this->assertFalse((bool) $thread->fresh()->pickup_confirmed);
        $this->assertSame(0, Payout::where('invoice_id', $invoice->id)->count());
    }

    public function test_messaging_action_is_rejected_once_thread_is_closed(): void
    {
        [$seller, $buyer, $listing, $invoice] = $this->buildPaidSale();

        $thread = PostAuctionThread::create([
            'invoice_id' => $invoice->id,
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'is_unlocked' => true,
            'unlocked_at' => now(),
            'pickup_confirmed' => true,
            'pickup_confirmed_at' => now(),
        ]);

        $this->actingAs($seller);

        $response = $this->post(route('messaging.thread.send-pickup-details', $thread->id), [
            'pickup_date' => now()->addDay()->toDateString(),
            'pickup_time' => '10:00',
            'street_address' => '123 Test Street',
        ]);

        $response->assertSessionHasErrors('thread');
    }

    public function test_seller_cannot_update_phone_after_pickup_confirmed(): void
    {
        [$seller, $buyer, $listing, $invoice] = $this->buildPaidSale();

        $thread = PostAuctionThread::create([
            'invoice_id' => $invoice->id,
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'is_unlocked' => true,
            'unlocked_at' => now(),
            'pickup_confirmed' => true,
            'pickup_confirmed_at' => now(),
        ]);

        $this->actingAs($seller);

        $response = $this->post(route('messaging.thread.seller-phone', $thread->id), [
            'seller_contact_phone' => '555-0100',
        ]);

        $response->assertSessionHasErrors('seller_contact_phone');
    }

    public function test_duplicate_pickup_pin_submission_is_rejected(): void
    {
        [$seller, $buyer, $listing, $invoice] = $this->buildPaidSale();
        $listing->generatePickupPin();
        $pin = $listing->fresh()->pickup_pin;

        $thread = PostAuctionThread::create([
            'invoice_id' => $invoice->id,
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'is_unlocked' => true,
            'unlocked_at' => now(),
        ]);

        $this->actingAs($seller);
        $this->post(route('messaging.thread.confirm-pickup', $thread->id), [
            'pickup_pin' => $pin,
        ])->assertSessionHasNoErrors();

        $second = $this->post(route('messaging.thread.confirm-pickup', $thread->id), [
            'pickup_pin' => $pin,
        ]);
        $second->assertSessionHasErrors('pickup_pin');
    }

    /**
     * Build a sold listing + paid invoice for a buyer / seller pair.
     *
     * @return array{0: User, 1: User, 2: Listing, 3: Invoice}
     */
    private function buildPaidSale(): array
    {
        $seller = User::factory()->create([
            'name' => 'Pickup Seller',
            'email' => 'pickup.seller@test.com',
            'role' => 'seller',
            'registration_complete' => true,
        ]);

        $buyer = User::factory()->create([
            'name' => 'Pickup Buyer',
            'email' => 'pickup.buyer@test.com',
            'password' => Hash::make('1234567890'),
            'role' => 'buyer',
            'registration_complete' => true,
        ]);

        $listing = Listing::create([
            'seller_id' => $seller->id,
            'listing_method' => 'auction',
            'status' => 'sold',
            'auction_duration' => 7,
            'major_category' => 'Vehicles',
            'condition' => 'used',
            'year' => '2020',
            'make' => 'Toyota',
            'model' => 'Corolla',
            'starting_price' => 5000,
            'price' => 5000,
            'auction_start_time' => now()->subDays(8),
            'auction_end_time' => now()->subDay(),
            'pickup_confirmed' => false,
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'CMTEST'.uniqid(),
            'listing_id' => $listing->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_name' => '2020 Toyota Corolla',
            'item_id' => 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT),
            'winning_bid_amount' => 5000,
            'buyer_commission' => 300,
            'total_amount_due' => 5300,
            'sale_date' => now()->toDateString(),
            'invoice_generated_at' => now(),
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return [$seller, $buyer, $listing, $invoice];
    }
}
