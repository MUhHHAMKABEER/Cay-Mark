<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Make these fields nullable since they're not collected in Step 1 registration
            $table->string('nationality')->nullable()->change();
            $table->date('dob')->nullable()->change();
        });
        
        // For enum fields, we need to use raw SQL
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `island` ENUM('New Providence', 'Grand Bahama', 'Abaco', 'Andros', 'Eleuthera', 'Cat Island', 'Exuma', 'Long Island', 'San Salvador', 'Acklins', 'Crooked Island', 'Mayaguana', 'Inagua', 'Rum Cay', 'Bimini') NULL");
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `gender` ENUM('Male', 'Female') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nationality')->nullable(false)->change();
            $table->date('dob')->nullable(false)->change();
        });
        
        // Revert enum fields to NOT NULL
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `island` ENUM('New Providence', 'Grand Bahama', 'Abaco', 'Andros', 'Eleuthera', 'Cat Island', 'Exuma', 'Long Island', 'San Salvador', 'Acklins', 'Crooked Island', 'Mayaguana', 'Inagua', 'Rum Cay', 'Bimini') NOT NULL");
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `gender` ENUM('Male', 'Female') NOT NULL");
    }
};
