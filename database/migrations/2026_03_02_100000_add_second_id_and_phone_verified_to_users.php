<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_type_2', 50)->nullable()->after('id_type');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
        });

        // Allow id_type to store Passport, NIB, Driver's License, Voter's Card, National ID
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            \DB::statement('ALTER TABLE users MODIFY id_type VARCHAR(50) NULL');
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_type_2', 'phone_verified_at']);
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            \DB::statement("ALTER TABLE users MODIFY id_type ENUM('Passport', 'Driver License', 'National ID') NULL");
        }
    }
};
