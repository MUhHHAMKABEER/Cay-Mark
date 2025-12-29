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
        Schema::create('pickup_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('post_auction_threads')->onDelete('cascade');
            $table->foreignId('pickup_detail_id')->constrained('pickup_details')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->date('requested_pickup_date')->nullable();
            $table->time('requested_pickup_time')->nullable();
            $table->enum('status', ['pending', 'approved', 'countered', 'rejected'])->default('pending');
            $table->date('countered_pickup_date')->nullable(); // Seller's counter offer
            $table->time('countered_pickup_time')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_change_requests');
    }
};
