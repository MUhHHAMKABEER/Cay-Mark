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
        Schema::create('seller_payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // Bank wire transfer details (encrypted)
            $table->string('bank_name'); // Encrypted
            $table->string('account_holder_name'); // Encrypted
            $table->string('account_number'); // Encrypted
            $table->string('routing_number')->nullable(); // Routing or SWIFT
            $table->string('swift_number')->nullable();
            $table->text('additional_instructions')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            
            // Lock status (cannot edit while listings are active)
            $table->boolean('is_locked')->default(false);
            
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_payout_methods');
    }
};
