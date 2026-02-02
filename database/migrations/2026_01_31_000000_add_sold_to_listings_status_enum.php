<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'sold' to listings.status enum so ended auctions can be marked sold.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'sold') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only revert if no rows use 'sold'
        DB::statement("ALTER TABLE listings MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
