<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Structured, queryable audit trail for every admin action on the
     * security-deposit system.  Names are snapshotted at the time of the
     * action so historical records remain accurate even after user renames.
     */
    public function up(): void
    {
        Schema::create('deposit_audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action
            $table->unsignedBigInteger('admin_id');
            $table->string('admin_name');           // snapshot — never changes

            // Which buyer was affected
            $table->unsignedBigInteger('buyer_id');
            $table->string('buyer_name');           // snapshot

            // What happened
            $table->enum('action', [
                'deposit_confirmed',    // admin confirmed wire → wallet credited
                'deposit_rejected',     // admin rejected pending wire request
                'withdrawal_approved',  // admin approved buyer withdrawal
                'withdrawal_rejected',  // admin rejected buyer withdrawal
                'manual_deposit',       // admin manually added funds (correction / exception)
            ]);

            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();

            $table->timestamp('performed_at');

            $table->timestamps(); // created_at == performed_at; updated_at for integrity

            // Fast lookups
            $table->index('admin_id');
            $table->index('buyer_id');
            $table->index('action');
            $table->index('performed_at');

            // Soft FK constraints — no cascade so logs survive deleted users
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_audit_logs');
    }
};
