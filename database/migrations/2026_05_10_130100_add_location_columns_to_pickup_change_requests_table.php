<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pickup_change_requests', function (Blueprint $table) {
            $table->string('request_type', 16)->default('date_time')->after('buyer_id');
            $table->string('requested_location', 255)->nullable()->after('requested_pickup_time');
            $table->text('additional_notes')->nullable()->after('countered_pickup_time');
        });
    }

    public function down(): void
    {
        Schema::table('pickup_change_requests', function (Blueprint $table) {
            $table->dropColumn(['request_type', 'requested_location', 'additional_notes']);
        });
    }
};
