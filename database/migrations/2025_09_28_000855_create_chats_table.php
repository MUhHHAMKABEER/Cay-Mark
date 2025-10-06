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
      Schema::create('chats', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('buyer_id');
    $table->unsignedBigInteger('seller_id');
    $table->unsignedBigInteger('listing_id');
    $table->timestamps();

    $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
