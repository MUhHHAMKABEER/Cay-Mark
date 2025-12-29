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
        Schema::create('third_party_pickups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('post_auction_threads')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->string('authorized_name'); // Person or company name
            $table->enum('pickup_type', ['tow_company', 'individual', 'authorized_representative']);
            $table->boolean('is_active')->default(true);
            $table->timestamp('authorized_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('third_party_pickups');
    }
};
