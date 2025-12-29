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
        Schema::table('listings', function (Blueprint $table) {
            // Item Number assigned by admin (format: CM000245)
            $table->string('item_number')->nullable()->unique()->after('id');
            
            // Island location (required per PDF)
            $table->string('island')->nullable()->after('location');
            
            // Interior color (required per PDF)
            $table->string('interior_color')->nullable()->after('color');
            
            // Auction pricing fields (optional per PDF)
            $table->decimal('starting_price', 15, 2)->nullable()->after('price');
            $table->decimal('reserve_price', 15, 2)->nullable()->after('starting_price');
            $table->decimal('buy_now_price', 15, 2)->nullable()->after('reserve_price');
            
            // Cover photo tracking
            $table->foreignId('cover_photo_id')->nullable()->after('seller_id')
                ->constrained('listing_images')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['cover_photo_id']);
            $table->dropColumn([
                'item_number',
                'island',
                'interior_color',
                'starting_price',
                'reserve_price',
                'buy_now_price',
                'cover_photo_id',
            ]);
        });
    }
};
