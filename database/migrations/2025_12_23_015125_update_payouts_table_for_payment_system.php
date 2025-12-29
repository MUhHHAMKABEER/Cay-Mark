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
        Schema::table('payouts', function (Blueprint $table) {
            // Add buyer name (optional per PDF)
            $table->string('buyer_name')->nullable()->after('seller_id');
            
            // Add item title
            $table->string('item_title')->nullable()->after('buyer_name');
            
            // Update status enum to match PDF requirements
            $table->dropColumn('status');
        });
        
        Schema::table('payouts', function (Blueprint $table) {
            $table->enum('status', ['pending', 'processing', 'sent', 'on_hold', 'paid_successfully'])->default('pending')->after('net_payout');
        });
        
        Schema::table('payouts', function (Blueprint $table) {
            // Transaction reference number
            $table->string('transaction_reference')->nullable()->after('status');
            
            // Date funds were sent
            $table->date('date_sent')->nullable()->after('payout_processed_at');
            
            // Finance notes
            $table->text('finance_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payouts', function (Blueprint $table) {
            $table->dropColumn([
                'buyer_name',
                'item_title',
                'transaction_reference',
                'date_sent',
                'finance_notes',
            ]);
        });
    }
};
