<?php
// database/migrations/2024_01_01_000001_create_packages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('role', ['buyer', 'seller']);
            $table->decimal('price', 10, 2);
            $table->integer('duration_days')->nullable();
            $table->json('features')->nullable();
            $table->integer('max_listings')->nullable();
            $table->integer('max_listings_per_month')->nullable();
            $table->decimal('auction_bid_limit', 10, 2)->nullable();
            $table->boolean('auction_access')->default(false);
            $table->boolean('marketplace_access')->default(false);
            $table->boolean('seller_dashboard')->default(false);
            $table->boolean('buy_now_feature')->default(false);
            $table->boolean('reserve_pricing')->default(false);
            $table->boolean('account_manager')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
