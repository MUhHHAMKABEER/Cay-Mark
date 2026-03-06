<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'id_2' to doc_type enum so second government ID can be stored.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE user_documents MODIFY COLUMN doc_type ENUM('id', 'id_2', 'business_license') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: revert to previous enum (would fail if id_2 rows exist)
        DB::statement("ALTER TABLE user_documents MODIFY COLUMN doc_type ENUM('id', 'business_license') NOT NULL");
    }
};
