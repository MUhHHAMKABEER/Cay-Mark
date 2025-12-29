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
            $table->string('vin', 17)->nullable()->after('year');
            $table->boolean('duplicate_vin_flag')->default(false)->after('vin');
        });

        // Create index for faster VIN lookups
        Schema::table('listings', function (Blueprint $table) {
            $table->index('vin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['vin']);
            $table->dropColumn(['vin', 'duplicate_vin_flag']);
        });
    }
};
