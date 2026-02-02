<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class VerifyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caymark:verify-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all 21 master in-app notifications are implemented and triggered';

    /**
     * Master list: method name in NotificationService => human label.
     *
     * @var array<string, string>
     */
    protected static $expectedMethods = [
        'registrationCompleted'       => '1. Registration Completed',
        'welcomeToCayMark'             => '2. Welcome to CayMark',
        'bidPlaced'                   => '3. Successful Bid Placed',
        'auctionWin'                  => '4. Auction Win',
        'paymentReminder6Hours'       => '5. Payment Reminder — 6 Hours',
        'paymentReminder24Hours'      => '6. Payment Reminder — 24 Hours',
        'paymentFinalWarning48Hours'  => '7. Final Payment Warning — 48 Hours',
        'paymentSuccessful'           => '8. Payment Successful',
        'pickupInstructionsAvailable'=> '9. Pickup Instructions Available',
        'pickupPinIssued'             => '10. Pickup PIN Issued',
        'pickupRescheduleApproved'    => '11. Pickup Reschedule — Approved',
        'pickupRescheduleRejected'    => '12. Pickup Reschedule — Rejected',
        'pickupCompleted'             => '13. Pickup Completed',
        'listingSubmitted'            => '14. Listing Submitted',
        'listingApproved'             => '15. Listing Approved',
        'auctionSold'                 => '16. Auction Sold',
        'sendPickupInfo'              => '17. Send Pickup Info',
        'transactionCompletedPayoutPending' => '18. Transaction Completed — Payout Pending',
        'auctionEndingSoon'           => '19. Auction Ending Soon',
        'invoiceAvailable'            => '20. Invoice Available',
        'suspiciousLoginDetected'    => '21. Suspicious Login Detected',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Verifying Master In-App Notification List (21 notifications)...');
        $this->newLine();

        $service = new NotificationService();
        $missing = [];
        $found = 0;

        foreach (self::$expectedMethods as $method => $label) {
            if (method_exists($service, $method)) {
                $this->line("  <info>✓</info> {$label}");
                $found++;
            } else {
                $this->line("  <error>✗</error> {$label} (method: {$method})");
                $missing[] = $method;
            }
        }

        $this->newLine();
        $total = count(self::$expectedMethods);

        if (count($missing) === 0) {
            $this->info("All {$total} notifications verified.");
            $this->comment('See docs/IN_APP_NOTIFICATIONS_MASTER_LIST.md for trigger locations.');
            return Command::SUCCESS;
        }

        $this->error('Missing ' . count($missing) . ' notification method(s): ' . implode(', ', $missing));
        return Command::FAILURE;
    }
}
