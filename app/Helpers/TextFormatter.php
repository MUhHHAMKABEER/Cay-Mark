<?php

namespace App\Helpers;

class TextFormatter
{
    /**
     * Convert text to ALL CAPS for vehicle listing fields.
     * Exceptions: payment fields, contact names, pickup messaging
     * 
     * @param string|null $text
     * @return string|null
     */
    public static function toAllCaps(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        return strtoupper(trim($text));
    }

    /**
     * Convert array of values to ALL CAPS.
     * 
     * @param array|null $values
     * @return array|null
     */
    public static function arrayToAllCaps(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return array_map(function ($value) {
            return is_string($value) ? self::toAllCaps($value) : $value;
        }, $values);
    }
}
