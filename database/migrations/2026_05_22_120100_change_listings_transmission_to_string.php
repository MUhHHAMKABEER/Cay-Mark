<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('listings') || !Schema::hasColumn('listings', 'transmission')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE listings MODIFY transmission VARCHAR(32) NULL');
        }
    }

    public function down(): void
    {
        // No rollback — enum values may not match prior data.
    }
};
