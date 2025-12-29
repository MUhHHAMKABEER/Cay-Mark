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
        Schema::table('invoices', function (Blueprint $table) {
            $table->timestamp('payment_deadline')->nullable()->after('invoice_generated_at');
            $table->boolean('is_overdue')->default(false)->after('payment_status');
            $table->timestamp('overdue_at')->nullable()->after('is_overdue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['payment_deadline', 'is_overdue', 'overdue_at']);
        });
    }
};
