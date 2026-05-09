<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('post_auction_threads', function (Blueprint $table) {
            $table->unsignedInteger('exchanges_count')->default(0)->after('pickup_confirmed_at');
            $table->timestamp('first_exchange_at')->nullable()->after('exchanges_count');
            $table->timestamp('last_exchange_at')->nullable()->after('first_exchange_at');
            $table->boolean('flagged_for_admin')->default(false)->after('last_exchange_at');
            $table->timestamp('flagged_at')->nullable()->after('flagged_for_admin');
            $table->string('flag_reason', 64)->nullable()->after('flagged_at');
            $table->timestamp('seller_ready_at')->nullable()->after('flag_reason');
            $table->timestamp('buyer_completion_confirmed_at')->nullable()->after('seller_ready_at');
        });
    }

    public function down(): void
    {
        Schema::table('post_auction_threads', function (Blueprint $table) {
            $table->dropColumn([
                'exchanges_count',
                'first_exchange_at',
                'last_exchange_at',
                'flagged_for_admin',
                'flagged_at',
                'flag_reason',
                'seller_ready_at',
                'buyer_completion_confirmed_at',
            ]);
        });
    }
};
