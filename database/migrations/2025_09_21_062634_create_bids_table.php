<?php

// database/migrations/xxxx_xx_xx_create_bids_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('active'); // active, retracted, refunded, etc.
            $table->timestamps();
        });

        // optional: cached current/highest bid on listings for faster reads
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'current_bid')) {
                $table->decimal('current_bid', 15, 2)->nullable()->after('price');
            }
        });
    }

    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'current_bid')) {
                $table->dropColumn('current_bid');
            }
        });

        Schema::dropIfExists('bids');
    }
}
