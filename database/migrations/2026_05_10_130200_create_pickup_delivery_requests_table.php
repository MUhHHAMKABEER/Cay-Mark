<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pickup_delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('post_auction_threads')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->string('delivery_address');
            $table->date('preferred_date')->nullable();
            $table->time('preferred_time')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('status', 16)->default('pending');
            $table->text('response_notes')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['thread_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_delivery_requests');
    }
};
