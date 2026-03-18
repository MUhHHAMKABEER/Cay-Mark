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
            $table->timestamp('rejected_at')->nullable()->after('status');
            $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
            $table->string('rejection_reason')->nullable()->after('rejected_by');
            $table->text('rejection_notes')->nullable()->after('rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejected_by', 'rejection_reason', 'rejection_notes']);
        });
    }
};
