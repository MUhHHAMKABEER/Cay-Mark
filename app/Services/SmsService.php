<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS. Configure .env with TWILIO_* for real sending; otherwise logs and returns true for testing.
     */
    public function send(string $to, string $message): bool
    {
        $rawTo = $to;
        $to = preg_replace('/\D/', '', $to);

        Log::info('SMS send attempt', [
            'to_raw' => $rawTo,
            'to_digits' => $to,
            'to_length' => strlen($to),
            'message_length' => strlen($message),
        ]);

        if (strlen($to) < 10) {
            Log::warning('SMS rejected: number too short', [
                'to_digits' => $to,
                'length' => strlen($to),
                'min_required' => 10,
            ]);
            return false;
        }

        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');
        $twilioAvailable = !empty($sid) && !empty($token) && class_exists('Twilio\Rest\Client');

        if ($twilioAvailable) {
            Log::info('SMS using Twilio', [
                'to_e164' => '+' . $to,
                'from_configured' => !empty($from),
                'from_preview' => $from ? substr($from, 0, 6) . '...' : null,
            ]);
            return $this->sendViaTwilio($to, $message);
        }

        Log::info('SMS (no provider configured): skipping real send', [
            'to_digits' => $to,
            'message_preview' => substr($message, 0, 50) . '...',
        ]);
        return true;
    }

    protected function sendViaTwilio(string $to, string $message): bool
    {
        $e164 = '+' . $to;
        $from = config('services.twilio.from');

        try {
            $client = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $client->messages->create($e164, [
                'from' => $from,
                'body' => $message,
            ]);
            Log::info('Twilio SMS sent successfully', ['to' => $e164]);
            return true;
        } catch (\Throwable $e) {
            Log::error('Twilio SMS failed', [
                'to' => $e164,
                'from' => $from,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
