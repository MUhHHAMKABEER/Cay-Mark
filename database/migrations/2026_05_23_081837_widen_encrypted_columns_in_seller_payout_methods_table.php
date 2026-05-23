<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change encrypted columns from varchar(255) to text so that the
     * AES+base64 JSON envelope produced by Crypt::encryptString() (~268 chars
     * for short inputs, longer for larger payloads) never gets truncated.
     */
    public function up(): void
    {
        Schema::table('seller_payout_methods', function (Blueprint $table) {
            $table->text('bank_name')->change();
            $table->text('account_holder_name')->change();
            $table->text('account_number')->change();
            $table->text('routing_number')->nullable()->change();
            $table->text('swift_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seller_payout_methods', function (Blueprint $table) {
            $table->string('bank_name')->change();
            $table->string('account_holder_name')->change();
            $table->string('account_number')->change();
            $table->string('routing_number')->nullable()->change();
            $table->string('swift_number')->nullable()->change();
        });
    }
};
