<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class SmsService
{
    /**
     * Send an SMS via Twilio.
     *
     * Required .env keys:
     *   TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     *   TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     *   TWILIO_FROM=+12425551234   (E.164 Twilio phone number or Messaging Service SID)
     */
    public function send(string $to, string $message): bool
    {
        // Normalise to E.164 — keep leading + if present, strip everything else
        $to = preg_replace('/[^\d+]/', '', $to);
        if (!str_starts_with($to, '+')) {
            $to = '+' . ltrim($to, '+');
        }

        Log::info('SMS send attempt', [
            'to'              => $to,
            'message_preview' => substr($message, 0, 60),
        ]);

        if (strlen(preg_replace('/\D/', '', $to)) < 10) {
            Log::warning('SMS rejected: number too short', ['to' => $to]);
            return false;
        }

        $accountSid = config('services.twilio.account_sid');
        $authToken  = config('services.twilio.auth_token');
        $from       = config('services.twilio.from');

        // No credentials configured — log and silently succeed (local dev without a Twilio account)
        if (empty($accountSid) || empty($authToken) || empty($from)) {
            Log::info('SMS (Twilio not configured): skipping real send', [
                'to'      => $to,
                'message' => $message,
            ]);
            return true;
        }

        try {
            $client = new Client($accountSid, $authToken);

            $sent = $client->messages->create($to, [
                'from' => $from,
                'body' => $message,
            ]);

            Log::info('Twilio SMS sent', [
                'to'   => $to,
                'sid'  => $sent->sid,
                'status' => $sent->status,
            ]);

            return true;

        } catch (TwilioException $e) {
            Log::error('Twilio SMS failed', [
                'to'      => $to,
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('Twilio SMS exception', [
                'to'      => $to,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return false;
        }
    }
}
