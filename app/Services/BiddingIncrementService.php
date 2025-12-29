<?php

namespace App\Services;

class BiddingIncrementService
{
    /**
     * CayMark Official Bidding Increment Table (per PDF)
     * Defines the minimum increment required for each price range
     * Format: [max_price => increment]
     */
    private static array $incrementTable = [
        999 => 25,           // $0 – $999: $25 increments
        4999 => 50,          // $1,000 – $4,999: $50 increments
        24999 => 100,        // $5,000 – $24,999: $100 increments
        49999 => 250,        // $25,000 – $49,999: $250 increments
        99999 => 500,        // $50,000 – $99,999: $500 increments
        PHP_INT_MAX => 1000, // $100,000+: $1,000 increments
    ];

    /**
     * Get the required increment for a given current bid amount.
     * 
     * @param float $currentBid
     * @return float Required increment amount
     */
    public function getIncrementForBid(float $currentBid): float
    {
        foreach (self::$incrementTable as $maxPrice => $increment) {
            if ($currentBid <= $maxPrice) {
                return (float) $increment;
            }
        }

        // Fallback (should never reach here)
        return 10000.00;
    }

    /**
     * Calculate the minimum next bid amount.
     * 
     * @param float $currentBid
     * @return float Minimum next bid
     */
    public function calculateMinimumNextBid(float $currentBid): float
    {
        $increment = $this->getIncrementForBid($currentBid);
        return round($currentBid + $increment, 2);
    }

    /**
     * Validate if a bid amount follows the increment rules.
     * 
     * @param float $currentBid Current highest bid
     * @param float $newBid Proposed new bid
     * @return array ['valid' => bool, 'required' => float, 'message' => string]
     */
    public function validateBidIncrement(float $currentBid, float $newBid): array
    {
        $minimumRequired = $this->calculateMinimumNextBid($currentBid);
        $increment = $this->getIncrementForBid($currentBid);

        if ($newBid < $minimumRequired) {
            return [
                'valid' => false,
                'required' => $minimumRequired,
                'increment' => $increment,
                'message' => 'Your bid must follow the required increment for this price range. Minimum bid: $' . number_format($minimumRequired, 2),
            ];
        }

        // Check if bid matches increment pattern
        $difference = $newBid - $currentBid;
        $remainder = fmod($difference, $increment);

        // Allow if it's exactly the increment or a multiple
        if ($remainder < 0.01) { // Account for floating point precision
            return [
                'valid' => true,
                'required' => $minimumRequired,
                'increment' => $increment,
                'message' => 'Valid bid',
            ];
        }

        // If not exact increment, check if it's at least the minimum
        if ($newBid >= $minimumRequired) {
            return [
                'valid' => true,
                'required' => $minimumRequired,
                'increment' => $increment,
                'message' => 'Valid bid (exceeds minimum)',
            ];
        }

        return [
            'valid' => false,
            'required' => $minimumRequired,
            'increment' => $increment,
            'message' => 'Your bid must follow the required increment for this price range.',
        ];
    }

    /**
     * Get all increment rules (for display purposes).
     * 
     * @return array
     */
    public function getIncrementTable(): array
    {
        $table = [];
        $previousMax = 0;

        foreach (self::$incrementTable as $maxPrice => $increment) {
            if ($maxPrice === PHP_INT_MAX) {
                $table[] = [
                    'range' => '$' . number_format($previousMax + 1, 0) . '+',
                    'increment' => '$' . number_format($increment, 0),
                ];
            } else {
                $table[] = [
                    'range' => '$' . number_format($previousMax + 1, 0) . ' - $' . number_format($maxPrice, 0),
                    'increment' => '$' . number_format($increment, 0),
                ];
                $previousMax = $maxPrice;
            }
        }

        return $table;
    }
}
