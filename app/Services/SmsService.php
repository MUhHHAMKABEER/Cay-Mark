<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Http\CurlClient;
use Twilio\Rest\Client;

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
        $twilioAvailable = ! empty($sid) && ! empty($token) && class_exists(Client::class);

        if ($twilioAvailable) {
            Log::info('SMS using Twilio', [
                'to_e164' => '+'.$to,
                'from_configured' => ! empty($from),
                'from_preview' => $from ? substr($from, 0, 6).'...' : null,
            ]);

            return $this->sendViaTwilio($to, $message);
        }

        Log::info('SMS (no provider configured): skipping real send', [
            'to_digits' => $to,
            'message_preview' => substr($message, 0, 50).'...',
        ]);

        return true;
    }

    /**
     * Build Twilio CurlClient with CA bundle (fixes Windows "unable to get local issuer certificate").
     */
    protected function makeTwilioCurlClient(): CurlClient
    {
        $opts = [];
        $cainfo = config('services.twilio.cainfo');

        if (is_string($cainfo) && $cainfo !== '' && is_readable($cainfo)) {
            $opts[CURLOPT_CAINFO] = $cainfo;
            $opts[CURLOPT_SSL_VERIFYPEER] = true;
            $opts[CURLOPT_SSL_VERIFYHOST] = 2;
            Log::debug('Twilio HTTP client using CURLOPT_CAINFO', ['path' => $cainfo]);
        } elseif (config('services.twilio.insecure_skip_verify') === true && app()->environment('local')) {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = 0;
            Log::warning('Twilio SSL verification disabled (TWILIO_INSECURE_SSL_SKIP_VERIFY=true and APP_ENV=local). Never use in production.');
        }

        return new CurlClient($opts);
    }

    protected function sendViaTwilio(string $to, string $message): bool
    {
        $e164 = '+'.$to;
        $from = config('services.twilio.from');

        try {
            $client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token'),
                null,
                null,
                $this->makeTwilioCurlClient()
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
            if (str_contains($e->getMessage(), 'SSL certificate') || str_contains($e->getMessage(), 'unable to get local issuer certificate')) {
                Log::warning('Twilio SSL fix: download https://curl.se/ca/cacert.pem and set TWILIO_CAINFO in .env to its full path, or set curl.cainfo in php.ini. For local-only testing you may set TWILIO_INSECURE_SSL_SKIP_VERIFY=true (never in production).');
            }

            return false;
        }
    }
}
