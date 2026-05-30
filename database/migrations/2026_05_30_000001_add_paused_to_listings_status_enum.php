<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'paused' to listings.status enum so admins can pause/resume auctions.
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE listings MODIFY COLUMN status
             ENUM('pending','approved','rejected','sold','paused')
             DEFAULT 'pending'"
        );
    }

    public function down(): void
    {
        // Revert any paused listings to approved before removing the value
        DB::statement("UPDATE listings SET status = 'approved' WHERE status = 'paused'");

        DB::statement(
            "ALTER TABLE listings MODIFY COLUMN status
             ENUM('pending','approved','rejected','sold')
             DEFAULT 'pending'"
        );
    }
};
