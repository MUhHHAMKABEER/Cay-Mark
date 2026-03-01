<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_auction_threads', function (Blueprint $table) {
            $table->string('seller_contact_phone', 32)->nullable()->after('seller_id');
        });
    }

    public function down(): void
    {
        Schema::table('post_auction_threads', function (Blueprint $table) {
            $table->dropColumn('seller_contact_phone');
        });
    }
};
