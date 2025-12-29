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
            $table->foreignId('original_listing_id')->nullable()->after('seller_id')
                ->constrained('listings')->onDelete('set null');
            $table->timestamp('relisted_at')->nullable()->after('original_listing_id');
            $table->boolean('is_relist')->default(false)->after('relisted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['original_listing_id']);
            $table->dropColumn(['original_listing_id', 'relisted_at', 'is_relist']);
        });
    }
};
