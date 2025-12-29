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
        Schema::create('post_auction_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_unlocked')->default(false); // Unlocked when payment clears
            $table->timestamp('unlocked_at')->nullable();
            $table->boolean('pickup_confirmed')->default(false);
            $table->timestamp('pickup_confirmed_at')->nullable();
            $table->timestamps();
            
            // Ensure one thread per invoice
            $table->unique('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_auction_threads');
    }
};
