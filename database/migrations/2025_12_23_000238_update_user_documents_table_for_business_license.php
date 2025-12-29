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
        Schema::table('user_documents', function (Blueprint $table) {
            // Drop the old enum column
            $table->dropColumn('doc_type');
        });

        Schema::table('user_documents', function (Blueprint $table) {
            // Add new enum with updated values including business_license
            $table->enum('doc_type', [
                'id', 
                'business_license'
            ])->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropColumn('doc_type');
        });

        Schema::table('user_documents', function (Blueprint $table) {
            $table->enum('doc_type', ['passport', 'driver_license', 'national_id'])->after('user_id');
        });
    }
};
