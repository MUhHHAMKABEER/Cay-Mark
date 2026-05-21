<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('engine_starts', 8)->nullable()->after('run_and_drive');
            $table->text('additional_notes')->nullable()->after('secondary_damage');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['engine_starts', 'additional_notes']);
        });
    }
};
