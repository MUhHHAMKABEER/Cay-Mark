<?php

namespace App\Services\Notifications;

use InvalidArgumentException;

class NotificationMessageBuilder
{
    /**
     * Render a catalog template body with {placeholder} replacements.
     *
     * @param  array<string, string|int|float|null>  $replacements
     */
    public static function render(string $templateKey, array $replacements = []): string
    {
        $body = config("notifications.templates.{$templateKey}.body");
        if (! is_string($body) || $body === '') {
            throw new InvalidArgumentException("Missing notification template body: {$templateKey}");
        }

        $out = $body;
        foreach ($replacements as $key => $value) {
            $out = str_replace('{'.$key.'}', $value === null ? '' : (string) $value, $out);
        }

        return $out;
    }

    public static function audience(string $templateKey): ?string
    {
        $aud = config("notifications.templates.{$templateKey}.audience");

        return is_string($aud) ? $aud : null;
    }
}
