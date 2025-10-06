<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id'); // foreign key to users (sellers)

            // Step 1
            $table->enum('listing_method', ['buy_now', 'auction']);
            $table->integer('auction_duration')->nullable();

            // Step 2
            $table->string('major_category');
            $table->string('subcategory')->nullable();
            $table->string('other_make')->nullable();
            $table->string('other_model')->nullable();

            // Step 3
            $table->enum('condition', ['new', 'used', 'salvaged']);
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('trim')->nullable();
            $table->string('year')->nullable();
            $table->string('color')->nullable();
            $table->string('fuel_type')->nullable();
            $table->enum('transmission', ['automatic', 'manual'])->nullable();
            $table->string('title_status')->nullable();
            $table->string('primary_damage')->nullable();
            $table->string('secondary_damage')->nullable();
            $table->boolean('keys_available')->default(false);

            // Category-specific fields
            $table->string('engine_type')->nullable(); // for boats
            $table->string('hull_material')->nullable();
            $table->string('category_type')->nullable(); // for equipment types

            $table->timestamps();

            // relations
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('listings');
    }
};
