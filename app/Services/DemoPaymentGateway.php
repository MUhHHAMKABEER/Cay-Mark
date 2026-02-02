<?php

namespace App\Services;

/**
 * Demo / Sandbox payment gateway for testing without a real bank API.
 * Use test card numbers to simulate success or failure.
 *
 * Test cards (use in sandbox only):
 * - 4242 4242 4242 4242  → Success
 * - 4000 0000 0000 0002  → Card declined
 * - 4000 0000 0000 9995  → Insufficient funds
 */
class DemoPaymentGateway
{
    public const TEST_CARD_SUCCESS = '4242424242424242';
    public const TEST_CARD_DECLINED = '4000000000000002';
    public const TEST_CARD_INSUFFICIENT = '4000000000009995';

    protected bool $sandbox;

    public function __construct(?bool $sandbox = null)
    {
        $this->sandbox = $sandbox ?? config('services.payment.sandbox', true);
    }

    /**
     * Charge a card (sandbox: simulate; live: would call real gateway).
     *
     * @param int $amountCents Amount in cents (e.g. 1999 = $19.99)
     * @param string $cardNumber Card number (spaces stripped)
     * @param string $expiry Expiry e.g. "12/26" or "1226"
     * @param string $cvv CVV/CVC
     * @param string $cardholderName Cardholder name
     * @return array{success: bool, transaction_id: string|null, message: string}
     */
    public function charge(int $amountCents, string $cardNumber, string $expiry, string $cvv, string $cardholderName): array
    {
        $card = preg_replace('/\s+/', '', $cardNumber);

        if ($this->sandbox) {
            return $this->sandboxCharge($card, $amountCents);
        }

        // Live mode: no real gateway configured in this demo – treat as sandbox for now
        return $this->sandboxCharge($card, $amountCents);
    }

    protected function sandboxCharge(string $cardNumber, int $amountCents): array
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        switch ($cardNumber) {
            case self::TEST_CARD_DECLINED:
                return [
                    'success' => false,
                    'transaction_id' => null,
                    'message' => 'Your card was declined. Please try a different card.',
                ];
            case self::TEST_CARD_INSUFFICIENT:
                return [
                    'success' => false,
                    'transaction_id' => null,
                    'message' => 'Insufficient funds. Please try a different card.',
                ];
            case self::TEST_CARD_SUCCESS:
                return [
                    'success' => true,
                    'transaction_id' => 'DEMO-' . strtoupper(uniqid()),
                    'message' => 'Payment successful (sandbox).',
                ];
            default:
                return [
                    'success' => false,
                    'transaction_id' => null,
                    'message' => 'In sandbox mode, use a test card: 4242 4242 4242 4242 (success), 4000 0000 0000 0002 (declined), 4000 0000 0000 9995 (insufficient funds).',
                ];
        }
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
