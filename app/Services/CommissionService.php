<?php

namespace App\Services;

class CommissionService
{
    /**
     * Buyer Commission Rate: 6%
     */
    const BUYER_COMMISSION_RATE = 0.06;

    /**
     * Buyer Commission Minimum: $100
     */
    const BUYER_COMMISSION_MIN = 100.00;

    /**
     * Seller Commission Rate: 4%
     */
    const SELLER_COMMISSION_RATE = 0.04;

    /**
     * Seller Commission Minimum: $150
     */
    const SELLER_COMMISSION_MIN = 150.00;

    /**
     * Calculate buyer commission based on final sale price.
     * Formula: max(6% of final sale price, $100)
     * 
     * @param float $finalSalePrice The final sale price
     * @return array ['commission' => float, 'rate' => float, 'minimum' => float, 'formula' => string]
     */
    public function calculateBuyerCommission(float $finalSalePrice): array
    {
        $percentageAmount = $finalSalePrice * self::BUYER_COMMISSION_RATE;
        $commission = max($percentageAmount, self::BUYER_COMMISSION_MIN);

        return [
            'commission' => round($commission, 2),
            'rate' => self::BUYER_COMMISSION_RATE,
            'rate_percentage' => 6,
            'minimum' => self::BUYER_COMMISSION_MIN,
            'percentage_amount' => round($percentageAmount, 2),
            'formula' => 'max(6% of final sale price, $100)',
            'applied_minimum' => $commission === self::BUYER_COMMISSION_MIN && $percentageAmount < self::BUYER_COMMISSION_MIN,
        ];
    }

    /**
     * Calculate seller commission based on final sale price.
     * Formula: max(4% of final sale price, $150)
     * 
     * @param float $finalSalePrice The final sale price
     * @return array ['commission' => float, 'rate' => float, 'minimum' => float, 'formula' => string]
     */
    public function calculateSellerCommission(float $finalSalePrice): array
    {
        $percentageAmount = $finalSalePrice * self::SELLER_COMMISSION_RATE;
        $commission = max($percentageAmount, self::SELLER_COMMISSION_MIN);

        return [
            'commission' => round($commission, 2),
            'rate' => self::SELLER_COMMISSION_RATE,
            'rate_percentage' => 4,
            'minimum' => self::SELLER_COMMISSION_MIN,
            'percentage_amount' => round($percentageAmount, 2),
            'formula' => 'max(4% of final sale price, $150)',
            'applied_minimum' => $commission === self::SELLER_COMMISSION_MIN && $percentageAmount < self::SELLER_COMMISSION_MIN,
        ];
    }

    /**
     * Calculate total invoice amount for buyer (sale price + buyer commission).
     * 
     * @param float $finalSalePrice The final sale price
     * @return array ['sale_price' => float, 'buyer_commission' => float, 'total_due' => float]
     */
    public function calculateBuyerInvoice(float $finalSalePrice): array
    {
        $buyerCommission = $this->calculateBuyerCommission($finalSalePrice);

        return [
            'sale_price' => round($finalSalePrice, 2),
            'buyer_commission' => $buyerCommission['commission'],
            'buyer_fees' => $buyerCommission['commission'], // Alias for invoice display
            'total_due' => round($finalSalePrice + $buyerCommission['commission'], 2),
            'commission_details' => $buyerCommission,
        ];
    }

    /**
     * Calculate seller payout (sale price - seller commission).
     * 
     * @param float $finalSalePrice The final sale price
     * @return array ['sale_price' => float, 'seller_commission' => float, 'net_payout' => float]
     */
    public function calculateSellerPayout(float $finalSalePrice): array
    {
        $sellerCommission = $this->calculateSellerCommission($finalSalePrice);

        return [
            'sale_price' => round($finalSalePrice, 2),
            'seller_commission' => $sellerCommission['commission'],
            'net_payout' => round($finalSalePrice - $sellerCommission['commission'], 2),
            'commission_details' => $sellerCommission,
        ];
    }

    /**
     * Calculate complete transaction breakdown (both buyer and seller).
     * 
     * @param float $finalSalePrice The final sale price
     * @return array Complete breakdown with buyer invoice and seller payout
     */
    public function calculateTransactionBreakdown(float $finalSalePrice): array
    {
        $buyerInvoice = $this->calculateBuyerInvoice($finalSalePrice);
        $sellerPayout = $this->calculateSellerPayout($finalSalePrice);

        return [
            'final_sale_price' => round($finalSalePrice, 2),
            'buyer' => $buyerInvoice,
            'seller' => $sellerPayout,
            'platform_total_commission' => round(
                $buyerInvoice['buyer_commission'] + $sellerPayout['seller_commission'],
                2
            ),
        ];
    }
}
