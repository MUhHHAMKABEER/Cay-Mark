<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_payout_methods', function (Blueprint $table) {
            $table->text('country')->nullable()->after('swift_number');
            $table->text('card_number')->nullable()->after('country');
            $table->text('card_cvc')->nullable()->after('card_number');
            $table->text('card_expiry')->nullable()->after('card_cvc');
        });
    }

    public function down(): void
    {
        Schema::table('seller_payout_methods', function (Blueprint $table) {
            $table->dropColumn(['country', 'card_number', 'card_cvc', 'card_expiry']);
        });
    }
};
