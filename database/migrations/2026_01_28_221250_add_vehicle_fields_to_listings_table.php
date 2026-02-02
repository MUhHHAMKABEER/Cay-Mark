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
            // Add missing vehicle fields
            $table->string('drive_type')->nullable()->after('transmission');
            $table->string('cylinders')->nullable()->after('engine_type');
            $table->string('vehicle_type')->nullable()->after('major_category');
            $table->string('body_style')->nullable()->after('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['drive_type', 'cylinders', 'vehicle_type', 'body_style']);
        });
    }
};
