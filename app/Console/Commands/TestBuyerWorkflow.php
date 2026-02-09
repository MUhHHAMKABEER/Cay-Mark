<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\User;
use App\Services\Buyer\AuctionBidOrchestrator;
use App\Services\CommissionService;
use App\Services\InvoiceService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestBuyerWorkflow extends Command
{
    protected $signature = 'caymark:test-buyer-workflow
                            {--email=buyer@gmail.com : Buyer email}
                            {--password=1234567890 : Buyer password}';

    protected $description = 'Test full buyer workflow: login, bid, end auction (DB), process ended, pay. Uses DB transaction and rolls back.';

    public function handle(): int
    {
        $email = $this->option('email');
        $password = $this->option('password');

        $this->info('Starting buyer workflow test (transaction will roll back at end).');
        $this->info("Buyer: {$email}");

        DB::beginTransaction();

        try {
            // 1. Get or create buyer
            $buyer = User::where('email', $email)->first();
            if (!$buyer) {
                $buyer = User::factory()->create([
                    'name' => 'Test Buyer',
                    'username' => 'testbuyer' . uniqid(),
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'buyer',
                    'registration_complete' => true,
                ]);
                $this->info('Created test buyer.');
            } elseif ($buyer->role !== 'buyer' || !$buyer->registration_complete) {
                $buyer->update(['role' => 'buyer', 'registration_complete' => true]);
            }

            // 2. Create seller and listing
            $seller = User::factory()->create([
                'name' => 'Test Seller',
                'username' => 'workflow_seller_' . uniqid(),
                'email' => 'workflow-seller-' . uniqid() . '@test.com',
                'role' => 'seller',
                'registration_complete' => true,
            ]);

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
            $listing->refresh();
            $slug = $listing->getSlugOrGenerate();
            $this->info("Created auction listing (slug: {$slug}).");

            // 3. Login as buyer and place bid
            Auth::login($buyer);
            $bidAmount = 1050; // $1000 + $50 increment for $1k–$4999 range
            $bidRequest = new TestBuyerWorkflowBidRequest(['amount' => $bidAmount]);
            $bidRequest->setUserResolver(fn () => $buyer);
            $bidRequest->headers->set('Accept', 'application/json');
            app()->instance('request', $bidRequest);

            $response = AuctionBidOrchestrator::placeBid($bidRequest, $listing);
            $responseData = $response->getData(true);
            if (empty($responseData['success'])) {
                $this->error('Bid failed: ' . ($responseData['message'] ?? json_encode($responseData)));
                DB::rollBack();
                return Command::FAILURE;
            }
            $this->info("Bid placed: \${$bidAmount}.");

            // 4. End auction (set end time in past)
            $listing->update(['auction_end_time' => now()->subHour()]);
            $this->info('Auction end time set to past.');

            // 5. Process ended auctions
            $invoiceService = new InvoiceService();
            $count = $invoiceService->processEndedAuctions();
            if ($count !== 1) {
                $this->error("Expected 1 invoice, got {$count}.");
                DB::rollBack();
                return Command::FAILURE;
            }
            $this->info('Invoice generated for winner.');

            $listing->refresh();
            $invoice = $listing->invoices()->where('buyer_id', $buyer->id)->first();
            if (!$invoice || $invoice->payment_status !== 'pending') {
                $this->error('No pending invoice for buyer.');
                DB::rollBack();
                return Command::FAILURE;
            }

            // 6. Process payment (demo card) – use a request that provides validated data
            $payData = [
                'invoice_ids' => [$invoice->id],
                'card_number' => '4242424242424242',
                'card_expiry' => '12/28',
                'card_cvv' => '123',
                'cardholder_name' => 'Test Buyer',
            ];
            $payRequest = new TestBuyerWorkflowPaymentRequest($payData);
            $payRequest->setUserResolver(fn () => $buyer);
            app()->instance('request', $payRequest);

            $commissionService = new CommissionService();
            \App\Services\Buyer\BuyerPaymentOps::processPayment($payRequest, $commissionService);

            $invoice->refresh();
            if ($invoice->payment_status !== 'paid') {
                $this->error('Payment did not complete. Invoice still: ' . $invoice->payment_status);
                DB::rollBack();
                return Command::FAILURE;
            }
            $this->info('Payment processed successfully.');

            $this->newLine();
            $this->info('Buyer workflow test passed: login → bid → end auction → invoice → pay.');
        } catch (\Throwable $e) {
            $this->error('Workflow failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            DB::rollBack();
            return Command::FAILURE;
        }

        DB::rollBack();
        $this->info('Transaction rolled back (no data left).');
        return Command::SUCCESS;
    }
}

/**
 * Request stub for bid step so placeBid gets validated data.
 */
class TestBuyerWorkflowBidRequest extends Request
{
    protected $validatedData = [];

    public function __construct(array $data = [])
    {
        parent::__construct([], $data, [], [], [], [], null);
        $this->setMethod('POST');
        $this->validatedData = $data;
    }

    public function validated($key = null, $default = null)
    {
        return $key === null ? $this->validatedData : data_get($this->validatedData, $key, $default);
    }
}

/**
 * Request stub for workflow test so processPayment gets validated data.
 */
class TestBuyerWorkflowPaymentRequest extends Request
{
    protected $validatedData = [];

    public function __construct(array $payData = [])
    {
        parent::__construct([], $payData, [], [], [], [], null);
        $this->setMethod('POST');
        $this->validatedData = $payData;
    }

    public function validated($key = null, $default = null)
    {
        return $key === null ? $this->validatedData : data_get($this->validatedData, $key, $default);
    }
}
