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
        Schema::create('second_chance_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('new_invoice_id')->nullable()->constrained('invoices')->onDelete('set null');
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // Second-highest bidder
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bid_id')->constrained('bids')->onDelete('cascade'); // Second-highest bid
            $table->decimal('bid_amount', 10, 2);
            $table->decimal('buyer_commission', 10, 2);
            $table->decimal('total_amount_due', 10, 2);
            $table->enum('status', ['pending', 'offered', 'accepted', 'paid', 'declined', 'expired'])->default('pending');
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('payment_deadline')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            // Index for finding active offers
            $table->index(['status', 'payment_deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('second_chance_purchases');
    }
};
