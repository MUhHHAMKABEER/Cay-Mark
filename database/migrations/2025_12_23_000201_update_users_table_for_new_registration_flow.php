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
        Schema::table('users', function (Blueprint $table) {
            // Make role nullable (users have no role until registration complete)
            $table->string('role')->nullable()->change();
            
            // Add new fields for registration flow
            $table->enum('id_type', ['Passport', 'Driver License', 'National ID'])->nullable()->after('phone');
            $table->string('business_license_path')->nullable()->after('id_type');
            $table->enum('relationship_to_business', [
                'Owner', 
                'Founder', 
                'Shareholder', 
                'Employee', 
                'Authorized Representative', 
                'Manager'
            ])->nullable()->after('business_license_path');
            $table->boolean('registration_complete')->default(false)->after('relationship_to_business');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(false)->default('buyer')->change();
            $table->dropColumn([
                'id_type',
                'business_license_path',
                'relationship_to_business',
                'registration_complete'
            ]);
        });
    }
};
