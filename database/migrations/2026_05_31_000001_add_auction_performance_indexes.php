<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes to speed up the every-minute auction processor query and
     * the dashboard tab filters that fire on every page load.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Speed up processEndedAuctions candidate scan
            // (status='approved', no invoice, auction_end_time < now)
            if (! $this->indexExists('listings', 'idx_listings_status_end_time')) {
                $table->index(['status', 'auction_end_time'], 'idx_listings_status_end_time');
            }

            // Speed up seller dashboard Current / Not-Sold tab queries
            if (! $this->indexExists('listings', 'idx_listings_seller_state')) {
                $table->index(['seller_id', 'status', 'listing_state'], 'idx_listings_seller_state');
            }

            // Speed up admin active auctions count
            if (! $this->indexExists('listings', 'idx_listings_status_method')) {
                $table->index(['status', 'listing_method'], 'idx_listings_status_method');
            }
        });

        Schema::table('bids', function (Blueprint $table) {
            // Speed up "buyer has bids on listing X" look-ups
            if (! $this->indexExists('bids', 'idx_bids_listing_status')) {
                $table->index(['listing_id', 'status', 'amount'], 'idx_bids_listing_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_listings_status_end_time');
            $table->dropIndexIfExists('idx_listings_seller_state');
            $table->dropIndexIfExists('idx_listings_status_method');
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_bids_listing_status');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                [$index]
            );
            return count($indexes) > 0;
        } catch (\Throwable) {
            return false;
        }
    }
};
