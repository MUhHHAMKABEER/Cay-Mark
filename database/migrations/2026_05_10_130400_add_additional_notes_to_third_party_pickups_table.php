<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('third_party_pickups', function (Blueprint $table) {
            $table->text('additional_notes')->nullable()->after('pickup_type');
        });
    }

    public function down(): void
    {
        Schema::table('third_party_pickups', function (Blueprint $table) {
            $table->dropColumn('additional_notes');
        });
    }
};
