<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class VerifyNotifications extends Command
{
    protected $signature = 'caymark:verify-notifications';

    protected $description = 'Verify every catalog notification template has a matching NotificationService method';

    /**
     * Template keys in config/notifications.php that do not map cleanly via Str::camel().
     *
     * @var array<string, string>
     */
    protected const TEMPLATE_KEY_TO_METHOD = [
        'welcome' => 'welcomeToCayMark',
        'auction_won' => 'auctionWin',
        'payment_reminder_6h' => 'paymentReminder6Hours',
        'payment_reminder_24h' => 'paymentReminder24Hours',
        'payment_final_warning_48h' => 'paymentFinalWarning48Hours',
        'login_new_device' => 'loginFromNewDevice',
        'login_attempt_unsuccessful' => 'loginAttemptUnsuccessful',
    ];

    /**
     * Legacy / alias methods that must remain on the service (not tied to a catalog template key).
     *
     * @var list<string>
     */
    protected const REQUIRED_EXTRA_METHODS = [
        'suspiciousLoginDetected',
    ];

    public function handle(): int
    {
        $templates = config('notifications.templates', []);
        if (! is_array($templates) || $templates === []) {
            $this->error('config/notifications.php has no templates.');

            return Command::FAILURE;
        }

        $this->info('Verifying notification catalog ↔ NotificationService methods...');
        $this->newLine();

        $service = new NotificationService;
        $missing = [];
        $found = 0;

        foreach (array_keys($templates) as $templateKey) {
            $method = self::TEMPLATE_KEY_TO_METHOD[$templateKey] ?? Str::camel($templateKey);
            $label = $templateKey.' → '.$method;
            if (method_exists($service, $method)) {
                $this->line("  <info>✓</info> {$label}");
                $found++;
            } else {
                $this->line("  <error>✗</error> {$label}");
                $missing[] = $label;
            }
        }

        foreach (self::REQUIRED_EXTRA_METHODS as $method) {
            $label = '(legacy) '.$method;
            if (method_exists($service, $method)) {
                $this->line("  <info>✓</info> {$label}");
                $found++;
            } else {
                $this->line("  <error>✗</error> {$label}");
                $missing[] = $label;
            }
        }

        $this->newLine();

        if ($missing !== []) {
            $this->error('Missing '.count($missing).' item(s): '.implode('; ', $missing));

            return Command::FAILURE;
        }

        $this->info("All {$found} notification checks passed.");
        $this->comment('See docs/IN_APP_NOTIFICATIONS_MASTER_LIST.md for triggers and channels.');

        return Command::SUCCESS;
    }
}
