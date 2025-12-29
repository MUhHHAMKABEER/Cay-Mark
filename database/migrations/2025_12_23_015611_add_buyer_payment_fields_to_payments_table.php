<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Buyer payment fields (for auction wins)
            $table->foreignId('invoice_id')->nullable()->after('subscription_id')
                ->constrained()->onDelete('cascade');
            $table->foreignId('listing_id')->nullable()->after('invoice_id')
                ->constrained()->onDelete('set null');
            $table->foreignId('seller_id')->nullable()->after('listing_id')
                ->constrained('users')->onDelete('set null');
            
            // Payment details
            $table->string('gateway_transaction_id')->nullable()->after('method');
            $table->string('payment_reference')->nullable()->after('gateway_transaction_id');
            
            // Item details
            $table->string('item_title')->nullable()->after('seller_id');
            $table->string('item_id')->nullable()->after('item_title');
            
            // Financial breakdown
            $table->decimal('platform_fee_retained', 15, 2)->nullable()->after('amount');
            $table->decimal('seller_payout_amount', 15, 2)->nullable()->after('platform_fee_retained');
            
            // Indexes
            $table->index('invoice_id');
            $table->index('listing_id');
            $table->index('seller_id');
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['listing_id']);
            $table->dropForeign(['seller_id']);
            $table->dropColumn([
                'invoice_id',
                'listing_id',
                'seller_id',
                'gateway_transaction_id',
                'payment_reference',
                'item_title',
                'item_id',
                'platform_fee_retained',
                'seller_payout_amount',
            ]);
        });
    }
};
