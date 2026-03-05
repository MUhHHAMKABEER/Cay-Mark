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
        $to = preg_replace('/\D/', '', $to);
        if (strlen($to) < 10) {
            return false;
        }

        if (config('services.twilio.sid') && config('services.twilio.token') && class_exists('Twilio\Rest\Client')) {
            return $this->sendViaTwilio($to, $message);
        }

        Log::info('SMS (no provider configured)', ['to' => $to, 'message' => $message]);
        return true;
    }

    protected function sendViaTwilio(string $to, string $message): bool
    {
        try {
            $client = new \Twilio\Rest\Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $client->messages->create(
                '+' . $to,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message,
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio SMS failed', ['error' => $e->getMessage(), 'to' => $to]);
            return false;
        }
    }
}
