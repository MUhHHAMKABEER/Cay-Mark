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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_restricted')->default(false)->after('registration_complete');
            $table->timestamp('restriction_ends_at')->nullable()->after('is_restricted');
            $table->text('restriction_reason')->nullable()->after('restriction_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_restricted', 'restriction_ends_at', 'restriction_reason']);
        });
    }
};
