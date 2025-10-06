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
        $table->decimal('price', 12, 2)->nullable()->after('year'); // Example: 99999999.99
        $table->integer('odometer')->nullable()->after('price');    // Example: mileage in km or miles
    });
}

public function down(): void
{
    Schema::table('listings', function (Blueprint $table) {
        $table->dropColumn(['price', 'odometer']);
    });
}

};
