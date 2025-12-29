<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caymark:send-payment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder emails for pending invoices (6h, 24h, 48h)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $notificationService = new \App\Services\NotificationService();

        // 6-hour reminder: invoices created 42 hours ago (48h - 6h = 42h)
        $sixHourReminders = Invoice::where('payment_status', 'pending')
            ->whereNotNull('payment_deadline')
            ->whereBetween('payment_deadline', [
                $now->copy()->subHours(6)->subMinutes(5),
                $now->copy()->subHours(6)->addMinutes(5)
            ])
            ->whereDoesntHave('paymentReminders', function ($query) {
                $query->where('type', '6_hour');
            })
            ->with(['buyer', 'listing'])
            ->get();

        $notificationService = new \App\Services\NotificationService();
        
        foreach ($sixHourReminders as $invoice) {
            $this->sendReminder($invoice, '6_hour', 'payment-reminder-6hours', 'Payment Reminder – Action Required for ' . ($invoice->item_name ?? '[VEHICLE_NAME]'));
            $notificationService->paymentReminder6Hours($invoice->buyer, $invoice);
        }

        // 24-hour reminder: invoices created 24 hours ago (48h - 24h = 24h)
        $twentyFourHourReminders = Invoice::where('payment_status', 'pending')
            ->whereNotNull('payment_deadline')
            ->whereBetween('payment_deadline', [
                $now->copy()->subHours(24)->subMinutes(5),
                $now->copy()->subHours(24)->addMinutes(5)
            ])
            ->whereDoesntHave('paymentReminders', function ($query) {
                $query->where('type', '24_hour');
            })
            ->with(['buyer', 'listing'])
            ->get();

        foreach ($twentyFourHourReminders as $invoice) {
            $this->sendReminder($invoice, '24_hour', 'payment-reminder-24hours', 'Payment Reminder – Payment Still Pending for ' . ($invoice->item_name ?? '[VEHICLE_NAME]'));
            $notificationService->paymentReminder24Hours($invoice->buyer, $invoice);
        }

        // 48-hour FINAL NOTICE: invoices past deadline
        $finalNotices = Invoice::where('payment_status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<=', $now)
            ->whereDoesntHave('paymentReminders', function ($query) {
                $query->where('type', '48_hour');
            })
            ->with(['buyer', 'listing'])
            ->get();

        foreach ($finalNotices as $invoice) {
            $this->sendReminder($invoice, '48_hour', 'payment-reminder-48hours', 'FINAL NOTICE — Payment Overdue for ' . ($invoice->item_name ?? '[VEHICLE_NAME]'));
            $notificationService->paymentFinalWarning48Hours($invoice->buyer, $invoice);
        }

        $this->info('Payment reminders sent successfully.');
    }

    protected function sendReminder(Invoice $invoice, string $type, string $template, string $subject)
    {
        try {
            Mail::send('emails.' . $template, [
                'invoice' => $invoice,
                'buyer' => $invoice->buyer,
            ], function ($message) use ($invoice, $subject) {
                $message->to($invoice->buyer->email, $invoice->buyer->name)
                    ->subject($subject);
            });

            // Mark reminder as sent (you may want to create a payment_reminders table)
            // For now, we'll just log it
            Log::info('Payment reminder sent', [
                'invoice_id' => $invoice->id,
                'type' => $type,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment reminder: ' . $e->getMessage(), [
                'invoice_id' => $invoice->id,
                'type' => $type,
            ]);
        }
    }
}

