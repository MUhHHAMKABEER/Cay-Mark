<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->foreignId('completed_by_user_id')->nullable()->after('finance_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable()->after('completed_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('completed_by_user_id');
            $table->dropColumn('completed_at');
        });
    }
};
