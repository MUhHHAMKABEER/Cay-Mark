<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_auction_reminder_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('window', 16);
            $table->string('purpose', 48);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['listing_id', 'window', 'purpose', 'user_id'], 'listing_reminder_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_auction_reminder_dispatches');
    }
};
