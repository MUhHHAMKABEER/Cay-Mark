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
        Schema::create('pickup_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('post_auction_threads')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->string('street_address'); // Validated, no contact data
            $table->text('directions_notes')->nullable(); // Optional, validated
            $table->enum('status', ['pending', 'accepted', 'change_requested', 'countered', 'confirmed'])->default('pending');
            $table->timestamp('submitted_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickup_details');
    }
};
