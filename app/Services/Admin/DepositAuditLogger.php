<?php

namespace App\Services\Admin;

use App\Models\DepositAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Single write-path for the deposit audit trail.
 *
 * Call  DepositAuditLogger::record(...)  from every admin controller method
 * that touches a buyer's deposit balance.  The method is intentionally
 * wrapped in a try/catch so a logging failure can never abort a financial
 * transaction.
 *
 * Valid action values (matches the DB enum):
 *   deposit_confirmed | deposit_rejected | withdrawal_approved |
 *   withdrawal_rejected | manual_deposit
 */
class DepositAuditLogger
{
    /**
     * Write one audit-log row.
     *
     * @param  string   $action   One of the enum values above.
     * @param  User     $admin    The authenticated admin user.
     * @param  User     $buyer    The buyer whose wallet was affected.
     * @param  float    $amount   Dollar amount involved.
     * @param  string|null $notes Optional admin note.
     * @return DepositAuditLog|null  Returns null only if DB write fails.
     */
    public static function record(
        string  $action,
        User    $admin,
        User    $buyer,
        float   $amount,
        ?string $notes = null
    ): ?DepositAuditLog {
        try {
            $entry = DepositAuditLog::create([
                'admin_id'    => $admin->id,
                'admin_name'  => $admin->name,
                'buyer_id'    => $buyer->id,
                'buyer_name'  => $buyer->name,
                'action'      => $action,
                'amount'      => $amount,
                'notes'       => $notes,
                'performed_at' => now(),
            ]);

            // Mirror to Laravel log for off-DB backup
            Log::info('[DepositAudit] ' . $action, [
                'audit_log_id' => $entry->id,
                'admin_id'     => $admin->id,
                'admin_name'   => $admin->name,
                'buyer_id'     => $buyer->id,
                'buyer_name'   => $buyer->name,
                'amount'       => $amount,
                'notes'        => $notes,
                'performed_at' => $entry->performed_at->toDateTimeString(),
            ]);

            return $entry;
        } catch (\Throwable $e) {
            // Log failure must NEVER crash a financial transaction.
            Log::error('[DepositAudit] FAILED to write audit log', [
                'action'    => $action,
                'admin_id'  => $admin->id,
                'buyer_id'  => $buyer->id,
                'amount'    => $amount,
                'error'     => $e->getMessage(),
            ]);

            return null;
        }
    }
}
