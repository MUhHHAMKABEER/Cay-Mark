<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;
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
            $ctx = [
                'to_e164' => '+'.$to,
                'from_configured' => ! empty($from),
                'from_preview' => $from ? substr((string) $from, 0, 6).'…' : null,
            ];
            if ($this->twilioDebug()) {
                $ctx['twilio_env'] = $this->twilioEnvironmentSnapshot();
            }
            Log::info('SMS using Twilio', $ctx);

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

    protected function twilioDebug(): bool
    {
        return filter_var(config('services.twilio.debug', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Safe snapshot for logs (no secrets).
     */
    protected function twilioEnvironmentSnapshot(): array
    {
        $sid = (string) config('services.twilio.sid');
        $cainfo = config('services.twilio.cainfo');

        return [
            'app_env' => app()->environment(),
            'php_version' => PHP_VERSION,
            'curl_version' => function_exists('curl_version') ? (curl_version()['version'] ?? null) : null,
            'twilio_sid_suffix' => strlen($sid) > 8 ? substr($sid, -8) : '(short)',
            'twilio_cainfo_set' => is_string($cainfo) && $cainfo !== '',
            'twilio_cainfo_readable' => is_string($cainfo) && $cainfo !== '' && is_readable($cainfo),
            'twilio_insecure_skip_verify' => config('services.twilio.insecure_skip_verify') === true,
        ];
    }

    /**
     * Extra Twilio / HTTP fields when the SDK throws RestException (or similar).
     */
    protected function twilioExceptionContext(Throwable $e): array
    {
        $ctx = [
            'exception_class' => get_class($e),
            'error_message' => $e->getMessage(),
            'error_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ];

        if (class_exists(\Twilio\Exceptions\RestException::class) && $e instanceof \Twilio\Exceptions\RestException) {
            $ctx = array_merge($ctx, $this->twilioRestExceptionFields($e));
        }

        $prev = $e->getPrevious();
        if ($prev instanceof Throwable) {
            $ctx['previous_class'] = get_class($prev);
            $ctx['previous_message'] = $prev->getMessage();
        }

        $trace = $e->getTraceAsString();
        if ($this->twilioDebug()) {
            $ctx['trace'] = $trace;
        } else {
            $ctx['trace_preview'] = strlen($trace) > 2000 ? substr($trace, 0, 2000).'…' : $trace;
        }

        return $ctx;
    }

    /**
     * @param \Twilio\Exceptions\RestException $e
     */
    protected function twilioRestExceptionFields(\Twilio\Exceptions\RestException $e): array
    {
        $out = [
            'twilio_http_status' => $e->getStatusCode(),
            'twilio_api_error_code' => $e->getCode(),
            'twilio_more_info' => $e->getMoreInfo(),
        ];
        $details = $e->getDetails();
        if ($details !== []) {
            $out['twilio_details'] = $details;
        }

        return $out;
    }

    protected function sendViaTwilio(string $to, string $message): bool
    {
        $e164 = '+'.$to;
        $from = config('services.twilio.from');

        if ($this->twilioDebug()) {
            Log::info('Twilio SMS create (debug)', array_merge([
                'to' => $e164,
                'from' => $from,
                'body_length' => strlen($message),
            ], $this->twilioEnvironmentSnapshot()));
        }

        try {
            $client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token'),
                null,
                null,
                $this->makeTwilioCurlClient()
            );
            $created = $client->messages->create($e164, [
                'from' => $from,
                'body' => $message,
            ]);
            $sid = is_object($created) && isset($created->sid) ? $created->sid : null;
            Log::info('Twilio SMS sent successfully', [
                'to' => $e164,
                'message_sid' => $sid,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error('Twilio SMS failed', array_merge([
                'to' => $e164,
                'from' => $from,
            ], $this->twilioExceptionContext($e)));

            if ($this->twilioDebug()) {
                Log::error('Twilio SMS failed (duplicate detail for grep)', [
                    'to' => $e164,
                    'from' => $from,
                    'twilio_debug' => true,
                    'full_message' => $e->getMessage(),
                ]);
            }

            if (str_contains($e->getMessage(), 'SSL certificate') || str_contains($e->getMessage(), 'unable to get local issuer certificate')) {
                Log::warning('Twilio SSL fix: download https://curl.se/ca/cacert.pem and set TWILIO_CAINFO in .env to its full path, or set curl.cainfo in php.ini. For local-only testing you may set TWILIO_INSECURE_SSL_SKIP_VERIFY=true (never in production).');
            }

            return false;
        }
    }
}
