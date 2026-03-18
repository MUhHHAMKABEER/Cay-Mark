<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AdminEmailTemplateUpdateRequest;
use Illuminate\Support\Facades\File;
use App\Services\Admin\AdminEmailTemplateOps;
use Carbon\Carbon;

class EmailTemplateController extends Controller
{
    /**
     * Email Template Management - List all templates
     */
    public function index()
    {
        $templatesPath = resource_path('views/emails');
        $templates = [];

        if (File::exists($templatesPath)) {
            $files = File::files($templatesPath);

            foreach ($files as $file) {
                $filename = $file->getFilename();
                $name = str_replace('.blade.php', '', $filename);

                $templates[] = [
                    'name' => $name,
                    'filename' => $filename,
                    'path' => $file->getPathname(),
                    'modified' => $file->getMTime(),
                ];
            }
        }

        // Group by category
        $categories = [
            'General User' => ['registration-step1', 'registration-complete'],
            'Buyer' => ['auction-won-invoice', 'payment-reminder-6hours', 'payment-reminder-24hours', 'payment-reminder-48hours', 'payment-successful'],
            'Seller' => ['listing-submitted', 'listing-approved', 'listing-rejected', 'auction-ended-sold', 'payout-processing-started', 'auction-ending-soon-24h', 'auction-ending-soon-1h'],
            'Payment & Invoice' => ['payment-successful-invoice'],
            'Account & Security' => ['password-reset', 'password-changed', 'account-deactivated', 'account-reactivated', 'email-updated', 'confirm-email'],
        ];

        $emailFailures = $this->getRecentEmailFailures();

        return view('admin.email-template-management', compact('templates', 'categories', 'emailFailures'));
    }

    /**
     * Get recent email sending failures from laravel.log (last 24 hours).
     *
     * @return array{count: int, lines: array<int, string>}
     */
    private function getRecentEmailFailures(): array
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return ['count' => 0, 'lines' => []];
        }

        $lines = @file($logFile);
        if ($lines === false) {
            return ['count' => 0, 'lines' => []];
        }

        $failureLines = [];
        $cutoff = now()->subDay();

        foreach ($lines as $line) {
            $isFailure = (
                (stripos($line, 'Failed to send') !== false && stripos($line, 'email') !== false) ||
                stripos($line, 'Swift_TransportException') !== false ||
                stripos($line, 'Connection could not be established') !== false ||
                (stripos($line, 'mail') !== false && (stripos($line, 'error') !== false || stripos($line, 'exception') !== false))
            );
            if (!$isFailure) {
                continue;
            }
            $timestamp = null;
            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $m)) {
                try {
                    $timestamp = Carbon::parse($m[1]);
                } catch (\Exception $e) {
                    // ignore
                }
            }
            if ($timestamp && $timestamp->isAfter($cutoff)) {
                $failureLines[] = trim($line);
            }
        }

        $failureLines = array_slice(array_unique($failureLines), -15);

        return [
            'count' => count($failureLines),
            'lines' => $failureLines,
        ];
    }

    /**
     * View/Edit email template
     */
    public function edit($templateName)
    {
        $templatePath = resource_path("views/emails/{$templateName}.blade.php");

        if (!File::exists($templatePath)) {
            return redirect()->route('admin.email-templates')
                ->with('error', 'Template not found.');
        }

        $content = File::get($templatePath);
        $defaultContent = $this->getDefaultTemplate($templateName);
        $simple = $this->parseTemplateForSimpleEditor($content);

        return view('admin.email-template-edit', compact('templateName', 'content', 'defaultContent', 'simple'));
    }

    /**
     * Extract simple editor fields from template HTML (for pre-fill).
     *
     * @return array{email_title: string, heading: string, message: string, button_text: string, button_url: string}
     */
    private function parseTemplateForSimpleEditor(string $content): array
    {
        $simple = [
            'email_title' => '',
            'heading' => '',
            'message' => '',
            'button_text' => '',
            'button_url' => '',
        ];

        if (preg_match('/<title>([^<]*)<\/title>/i', $content, $m)) {
            $simple['email_title'] = trim($m[1]);
        }
        if (preg_match('/<h2[^>]*>([^<]*)<\/h2>/i', $content, $m)) {
            $simple['heading'] = trim(strip_tags($m[1]));
        }
        if (preg_match('/<a[^>]*class="[^"]*button[^"]*"[^>]*href="([^"]*)"[^>]*>([^<]*)<\/a>/i', $content, $m)) {
            $simple['button_url'] = trim($m[1]);
            $simple['button_text'] = trim(strip_tags($m[2]));
        }
        // Message: between first </h2> and first button block (div or a.button)
        if (preg_match('/<\/h2>\s*(.*?)(?=<div style="text-align|<\s*a[^>]*class="[^"]*button)/is', $content, $m)) {
            $block = trim($m[1]);
            $block = preg_replace('/<\/p>\s*<p[^>]*>/i', "\n\n", $block);
            $block = preg_replace('/<br\s*\/?>/i', "\n", $block);
            $simple['message'] = trim(strip_tags($block));
        }

        return $simple;
    }

    /**
     * Update email template
     */
    public function update(AdminEmailTemplateUpdateRequest $request, $templateName)
    {
        return AdminEmailTemplateOps::update($request, $templateName);
    }

    /**
     * Preview email template with sample data so it renders without errors.
     */
    public function preview($templateName)
    {
        $templatePath = resource_path("views/emails/{$templateName}.blade.php");
        if (!File::exists($templatePath)) {
            return redirect()->route('admin.email-templates')->with('error', 'Template not found.');
        }

        $data = $this->getPreviewDataForTemplate($templateName);
        return view("emails.{$templateName}", $data);
    }

    /**
     * Return sample data for email template preview (avoids undefined variable errors).
     *
     * @return array<string, mixed>
     */
    private function getPreviewDataForTemplate(string $templateName): array
    {
        $invoice = (object) [
            'id' => 1,
            'item_name' => '2020 Toyota Camry',
            'item_id' => 'CM000001',
            'invoice_number' => 'INV-PREVIEW',
        ];
        $user = (object) [
            'name' => 'Sample User',
            'first_name' => 'Sample',
            'email' => 'sample@example.com',
        ];
        $listing = (object) [
            'id' => 1,
            'year' => '2020',
            'make' => 'Toyota',
            'model' => 'Camry',
            'item_number' => 'CM000001',
        ];
        $seller = (object) [
            'name' => 'Sample Seller',
            'first_name' => 'Sample',
        ];
        $payment = (object) [
            'id' => 1,
            'amount' => 15000.00,
        ];
        $winningBidAmount = 15000.00;

        $templatesWithData = [
            'auction-won-invoice' => ['invoice' => $invoice],
            'payment-successful' => ['invoice' => $invoice],
            'payment-successful-invoice' => ['invoice' => $invoice],
            'payment-reminder-6hours' => ['invoice' => $invoice],
            'payment-reminder-24hours' => ['invoice' => $invoice],
            'payment-reminder-48hours' => ['invoice' => $invoice],
            'seller-payment-received' => ['invoice' => $invoice, 'seller' => $seller, 'payment' => $payment],
            'auction-ended-sold' => ['listing' => $listing, 'seller' => $seller, 'winningBidAmount' => $winningBidAmount],
            'listing-submitted' => ['listing' => $listing, 'vehicleName' => '2020 Toyota Camry'],
            'listing-approved' => ['listing' => $listing],
            'listing-rejected' => ['listing' => $listing, 'rejectionReason' => 'Sample reason for preview.'],
            'auction-ending-soon-24h' => ['listing' => $listing],
            'auction-ending-soon-1h' => ['listing' => $listing],
            'registration-step1' => ['user' => $user],
            'registration-complete' => ['user' => $user],
            'payout-processing-started' => ['payout' => (object) ['id' => 1], 'listing' => $listing],
            'password-reset' => ['resetUrl' => url('/reset-password/preview-token'), 'token' => 'preview-token'],
            'password-changed' => [],
            'confirm-email' => ['verificationUrl' => url('/verify-email/1/preview-hash'), 'id' => 1, 'hash' => 'preview-hash'],
            'email-updated' => [],
            'email-change-verification' => ['new_email' => 'new@example.com', 'code' => '123456', 'minutes' => 15],
            'account-deactivated' => [],
            'account-reactivated' => [],
        ];

        return $templatesWithData[$templateName] ?? [];
    }

    /**
     * Restore default template
     */
    public function restoreDefault($templateName)
    {
        $defaultContent = $this->getDefaultTemplate($templateName);
        
        if (!$defaultContent) {
            return back()->with('error', 'Default template not available.');
        }

        $templatePath = resource_path("views/emails/{$templateName}.blade.php");
        File::put($templatePath, $defaultContent);

        return back()->with('success', 'Template restored to default.');
    }

    /**
     * Get default template content (placeholder - store defaults separately)
     */
    private function getDefaultTemplate($templateName)
    {
        // In production, store default templates in a separate location
        // For now, return null (admin can manually restore)
        return null;
    }
}

