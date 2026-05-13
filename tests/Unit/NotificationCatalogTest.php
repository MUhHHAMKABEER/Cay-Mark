<?php

namespace Tests\Unit;

use App\Services\Notifications\NotificationMessageBuilder;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationCatalogTest extends TestCase
{
    public function test_catalog_templates_resolve_to_service_methods(): void
    {
        $templates = config('notifications.templates', []);
        $this->assertNotEmpty($templates, 'config/notifications.php must define templates.');

        $map = [
            'welcome' => 'welcomeToCayMark',
            'auction_won' => 'auctionWin',
            'payment_reminder_6h' => 'paymentReminder6Hours',
            'payment_reminder_24h' => 'paymentReminder24Hours',
            'payment_final_warning_48h' => 'paymentFinalWarning48Hours',
            'login_new_device' => 'loginFromNewDevice',
            'login_attempt_unsuccessful' => 'loginAttemptUnsuccessful',
        ];

        $service = new \App\Services\NotificationService;
        foreach (array_keys($templates) as $key) {
            $method = $map[$key] ?? Str::camel($key);
            $this->assertTrue(
                method_exists($service, $method),
                "Template {$key} must map to NotificationService::{$method}()"
            );
        }
    }

    public function test_message_builder_renders_placeholders(): void
    {
        $body = NotificationMessageBuilder::render('bid_placed', [
            'listing_number' => 'CM0001',
            'vehicle_name' => '2020 FORD F-150',
            'support_email' => 'support@caymark.co',
        ]);
        $this->assertStringContainsString('CM0001', $body);
        $this->assertStringContainsString('F-150', $body);
        $this->assertStringContainsString('CayMark', $body);
    }
}
