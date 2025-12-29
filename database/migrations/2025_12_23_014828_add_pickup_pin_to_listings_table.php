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
            // Pickup PIN system
            $table->string('pickup_pin', 6)->nullable()->after('auction_end_time');
            $table->timestamp('pickup_pin_generated_at')->nullable()->after('pickup_pin');
            $table->timestamp('pickup_confirmed_at')->nullable()->after('pickup_pin_generated_at');
            $table->boolean('pickup_confirmed')->default(false)->after('pickup_confirmed_at');
            $table->foreignId('pickup_confirmed_by')->nullable()->after('pickup_confirmed')
                ->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['pickup_confirmed_by']);
            $table->dropColumn([
                'pickup_pin',
                'pickup_pin_generated_at',
                'pickup_confirmed_at',
                'pickup_confirmed',
                'pickup_confirmed_by',
            ]);
        });
    }
};
