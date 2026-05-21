<?php

namespace App\Helpers;

class ListingDisplayHelper
{
    public static function maskedIdentifier(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return 'N/A';
        }

        $len = strlen($value);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        return substr($value, 0, $len - 4) . '****';
    }

    public static function displayValue(mixed $value, string $suffix = ''): string
    {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return (string) $value . $suffix;
    }

    public static function formatOdometer(?int $odometer, bool $estimated = false): string
    {
        if ($odometer === null) {
            return 'N/A';
        }

        $text = number_format($odometer) . ' km';
        if ($estimated) {
            $text .= ' (Estimated)';
        }

        return $text;
    }
}
