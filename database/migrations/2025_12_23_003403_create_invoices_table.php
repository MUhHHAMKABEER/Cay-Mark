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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('bid_id')->nullable()->constrained()->onDelete('set null'); // Winning bid
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            
            // Item details
            $table->string('item_name');
            $table->string('item_id'); // NOT "Lot ID" per PDF
            
            // Financial details
            $table->decimal('winning_bid_amount', 15, 2);
            $table->decimal('buyer_commission', 15, 2); // 6% min $100
            $table->decimal('total_amount_due', 15, 2);
            
            // Dates
            $table->date('sale_date');
            $table->timestamp('invoice_generated_at');
            
            // Payment status
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'overdue'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            
            // Invoice file
            $table->string('pdf_path')->nullable(); // Path to generated PDF
            
            // Additional info
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Store additional data
            
            $table->timestamps();
            
            // Indexes
            $table->index('buyer_id');
            $table->index('seller_id');
            $table->index('listing_id');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
