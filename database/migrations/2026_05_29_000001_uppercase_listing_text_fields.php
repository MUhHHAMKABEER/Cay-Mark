<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Uppercase vehicle text fields on all listings.
 *
 * CayMark policy: ALL vehicle detail text is stored and displayed in UPPERCASE.
 * Fields affected: color, interior_color, transmission, drive_type, fuel_type,
 * engine_type, primary_damage, secondary_damage, condition, make, model, trim.
 *
 * This migration is data-only (no schema change) and is safe to re-run;
 * UPPER() on an already-uppercase value is a no-op.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $fields = [
            'color',
            'interior_color',
            'transmission',
            'drive_type',
            'fuel_type',
            'engine_type',
            'primary_damage',
            'secondary_damage',
            'condition',
            'make',
            'model',
            'trim',
        ];

        foreach ($fields as $field) {
            // Only update rows where the field is not null and not already fully uppercase
            DB::statement("
                UPDATE listings
                SET `{$field}` = UPPER(`{$field}`)
                WHERE `{$field}` IS NOT NULL
                  AND `{$field}` != UPPER(`{$field}`)
            ");
        }
    }

    /**
     * Down: no-op — we do not lowercase values on rollback as original
     * mixed-case data cannot be reliably reconstructed.
     */
    public function down(): void
    {
        // Intentionally empty — uppercase data cannot be safely reversed.
    }
};
