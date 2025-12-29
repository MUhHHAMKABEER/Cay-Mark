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
        Schema::create('buyer_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('bid_id')->nullable()->constrained('bids')->onDelete('set null');
            $table->decimal('invoice_amount', 10, 2);
            $table->decimal('deposit_penalty_amount', 10, 2)->default(0);
            $table->decimal('deposit_penalty_percentage', 5, 2)->default(0); // Configurable percentage
            $table->enum('status', ['pending', 'restricted', 'resolved', 'second_chance'])->default('pending');
            $table->enum('resolution_type', ['relist', 'second_chance', 'closed'])->nullable();
            $table->timestamp('defaulted_at');
            $table->timestamp('restriction_ends_at')->nullable(); // 14 days from default
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            
            // Index for finding overdue defaults
            $table->index(['status', 'defaulted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_defaults');
    }
};
