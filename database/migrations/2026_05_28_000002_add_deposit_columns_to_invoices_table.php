<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add deposit-tracking columns to the invoices table.
     *
     * original_amount  – the full invoice total before any deposit credit
     *                    (winning_bid_amount + buyer_commission)
     * deposit_applied  – the security-deposit amount deducted from the invoice
     *                    (0 when no deposit was applied)
     *
     * After migration:
     *   total_amount_due = original_amount - deposit_applied
     *
     * The seller is NEVER affected by these columns; seller payouts always
     * read from winning_bid_amount.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Original full amount (bid + buyer commission), never changes.
            $table->decimal('original_amount', 15, 2)
                  ->default(0)
                  ->after('total_amount_due')
                  ->comment('Full invoice total before deposit credit (bid + commission)');

            // Deposit credit applied at invoice generation; 0 = no deposit.
            $table->decimal('deposit_applied', 15, 2)
                  ->default(0)
                  ->after('original_amount')
                  ->comment('Security-deposit amount deducted from total_amount_due');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'deposit_applied']);
        });
    }
};
