<?php

namespace App\Services;

class ContentFilterService
{
    /**
     * Filter content to block phone numbers, emails, social links, etc.
     * 
     * @param string $content
     * @return array ['is_valid' => bool, 'filtered_content' => string, 'blocked_items' => array]
     */
    public function filterContent(string $content): array
    {
        $blockedItems = [];
        $filteredContent = $content;

        // Phone number patterns (various formats)
        $phonePatterns = [
            '/\b\d{3}[-.\s]?\d{3}[-.\s]?\d{4}\b/', // US format
            '/\b\(\d{3}\)\s?\d{3}[-.\s]?\d{4}\b/', // (123) 456-7890
            '/\b\d{10}\b/', // 1234567890
            '/\b\+?\d{1,3}[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}\b/', // International
        ];

        // Email pattern
        $emailPattern = '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/';

        // Social media and external platform patterns
        $socialPatterns = [
            '/\b(facebook|fb|instagram|ig|twitter|tweet|linkedin|snapchat|tiktok|whatsapp|telegram|signal)\b/i',
            '/\b(www\.|http:\/\/|https:\/\/)[^\s]+/i', // URLs
            '/\b[a-z0-9]+\.(com|net|org|io|co|me|us)\b/i', // Domain names
        ];

        // Check for phone numbers
        foreach ($phonePatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $blockedItems[] = [
                    'type' => 'phone_number',
                    'content' => $matches[0],
                ];
                $filteredContent = preg_replace($pattern, '[BLOCKED: Phone Number]', $filteredContent);
            }
        }

        // Check for emails
        if (preg_match($emailPattern, $content, $matches)) {
            $blockedItems[] = [
                'type' => 'email',
                'content' => $matches[0],
            ];
            $filteredContent = preg_replace($emailPattern, '[BLOCKED: Email Address]', $filteredContent);
        }

        // Check for social links
        foreach ($socialPatterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $blockedItems[] = [
                    'type' => 'social_link',
                    'content' => $matches[0],
                ];
                $filteredContent = preg_replace($pattern, '[BLOCKED: External Link]', $filteredContent);
            }
        }

        return [
            'is_valid' => empty($blockedItems),
            'filtered_content' => $filteredContent,
            'blocked_items' => $blockedItems,
        ];
    }

    /**
     * Validate address field (should not contain contact data).
     *
     * Street-style lines often start with a number, but many valid locations
     * (e.g. neighbourhood or area names, marina berths, rural routes) do not.
     * We only enforce: no phones/emails/links from filterContent, and a
     * minimum meaningful length with at least one letter.
     *
     * @param string $address
     * @return array
     */
    public function validateAddress(string $address): array
    {
        $result = $this->filterContent($address);

        $trimmed = trim($address);
        if ($trimmed === '') {
            $result['is_valid'] = false;
            $result['blocked_items'][] = [
                'type' => 'address_format',
                'content' => 'Address cannot be empty',
            ];

            return $result;
        }

        if (mb_strlen($trimmed) < 5) {
            $result['is_valid'] = false;
            $result['blocked_items'][] = [
                'type' => 'address_format',
                'content' => 'Address is too short',
            ];

            return $result;
        }

        if (! preg_match('/\p{L}/u', $trimmed)) {
            $result['is_valid'] = false;
            $result['blocked_items'][] = [
                'type' => 'address_format',
                'content' => 'Address must include at least one letter',
            ];
        }

        return $result;
    }

    /**
     * Human-readable message when validateAddress() fails.
     *
     * @param  array<string, mixed>  $result  Return value from validateAddress()
     */
    public function addressValidationUserMessage(array $result, string $fieldLabel = 'This address'): string
    {
        if (! empty($result['is_valid'])) {
            return '';
        }

        $types = array_column($result['blocked_items'] ?? [], 'type');
        $hasContact = (bool) array_intersect($types, ['phone_number', 'email', 'social_link']);
        $hasFormat = in_array('address_format', $types, true);

        $formatHint = '';
        foreach ($result['blocked_items'] ?? [] as $b) {
            if (($b['type'] ?? '') === 'address_format' && ! empty($b['content'])) {
                $formatHint = (string) $b['content'];
                break;
            }
        }

        if ($hasContact && $hasFormat) {
            return $fieldLabel.' cannot include phone numbers, emails, or links, and must be at least 5 characters with at least one letter.';
        }
        if ($hasContact) {
            return $fieldLabel.' cannot include phone numbers, email addresses, or web links.';
        }
        if ($formatHint !== '') {
            return $fieldLabel.': '.$formatHint.'.';
        }

        return $fieldLabel.' could not be accepted. Please enter at least 5 characters, including a letter (e.g. area, road, or landmark).';
    }
}

