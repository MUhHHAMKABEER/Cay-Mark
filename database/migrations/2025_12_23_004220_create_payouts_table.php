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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->string('payout_number')->unique();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade'); // Link to invoice
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            // Financial details
            $table->decimal('sale_price', 15, 2);
            $table->decimal('seller_commission', 15, 2); // 4% min $150
            $table->decimal('net_payout', 15, 2); // sale_price - commission
            
            // Dates
            $table->date('sale_date');
            $table->timestamp('payout_generated_at');
            $table->timestamp('payout_processed_at')->nullable();
            
            // Payout status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->enum('payment_method', ['bank_transfer', 'check', 'paypal'])->nullable();
            $table->string('payment_reference')->nullable();
            
            // Additional info
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('seller_id');
            $table->index('listing_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
