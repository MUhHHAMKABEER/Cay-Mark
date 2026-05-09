<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messaging_thread_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('post_auction_threads')->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users');
            $table->string('actor_role', 16);
            $table->string('type', 48);
            $table->json('payload')->nullable();
            $table->boolean('counts_as_exchange')->default(false);
            $table->timestamps();

            $table->index(['thread_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messaging_thread_events');
    }
};
