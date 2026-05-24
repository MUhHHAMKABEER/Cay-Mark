<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS via Vonage REST API.
     *
     * No SDK — plain HTTPS POST, so no Windows/WAMP SSL certificate issues.
     * Works identically on local and production.
     *
     * Required .env keys:
     *   VONAGE_API_KEY=xxxxxxxx
     *   VONAGE_API_SECRET=xxxxxxxxxxxxxxxx
     *   VONAGE_FROM=CayMark   (alphanumeric sender ID, max 11 chars — or a Vonage virtual number)
     */
    public function send(string $to, string $message): bool
    {
        $digits = preg_replace('/\D/', '', $to);

        Log::info('SMS send attempt', [
            'to_raw'          => $to,
            'to_digits'       => $digits,
            'message_preview' => substr($message, 0, 60),
        ]);

        if (strlen($digits) < 10) {
            Log::warning('SMS rejected: number too short', ['to_digits' => $digits]);
            return false;
        }

        $apiKey    = config('services.vonage.api_key');
        $apiSecret = config('services.vonage.api_secret');
        $from      = config('services.vonage.from', 'CayMark');

        // No credentials configured — log and return true (pure local dev with no account yet)
        if (empty($apiKey) || empty($apiSecret)) {
            Log::info('SMS (Vonage not configured): skipping real send', [
                'to'      => $digits,
                'message' => $message,
            ]);
            return true;
        }

        try {
            // On local/Windows WAMP, SSL verification can fail — skip it only in local env.
            // On the live server, full SSL verification runs normally.
            $sslVerify = !app()->environment('local');

            $response = Http::withOptions(['verify' => $sslVerify])
                ->asForm()
                ->post('https://rest.nexmo.com/sms/json', [
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                    'to'         => $digits,
                    'from'       => $from,
                    'text'       => $message,
                ]);

            $body   = $response->json();
            $msg    = $body['messages'][0] ?? [];
            $status = $msg['status'] ?? null;

            Log::info('Vonage SMS response', ['status' => $status, 'body' => $body]);

            if ($status === '0') {
                Log::info('Vonage SMS sent successfully', ['to' => $digits]);
                return true;
            }

            Log::error('Vonage SMS failed', [
                'to'         => $digits,
                'status'     => $status,
                'error_text' => $msg['error-text'] ?? 'Unknown error',
            ]);

            return false;

        } catch (\Throwable $e) {
            Log::error('Vonage SMS exception', [
                'to'      => $digits,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return false;
        }
    }
}
