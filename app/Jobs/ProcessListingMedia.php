<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ProcessListingMedia
 * ─────────────────────────────────────────────────────────────────────────
 * Runs on the queue after a seller submits a listing.
 *
 * The controller pre-moves all uploaded files from PHP's temp directory to
 * storage/app/listing-queue/{uuid}/ and then dispatches this job so the
 * seller is redirected immediately without waiting for disk I/O.
 *
 * This job:
 *   1. Copies files from the temp dir to public/uploads/listings/
 *   2. Creates ListingImage records
 *   3. Calls dispatchSideEffects (submission email + in-app notification)
 *   4. Cleans up the temp directory
 *
 * On failure the listing already exists with status=pending (admin can see
 * it) but without images. The failure is logged for investigation.
 */
class ProcessListingMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum attempts before marking failed. */
    public int $tries = 3;

    /** Seconds before the job is considered timed-out. */
    public int $timeout = 180;

    /** Seconds between retry attempts. */
    public int $backoff = 15;

    public function __construct(
        public readonly int    $listingId,
        public readonly string $tempDir,
        public readonly array  $staged   // ['cover' => 'cover.jpg', 'photos' => [...], 'video' => 'video.mp4']
    ) {}

    public function handle(): void
    {
        $listing = Listing::with('seller')->findOrFail($this->listingId);
        $destDir = public_path('uploads/listings');

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $coverId = null;

        // ── Cover photo ───────────────────────────────────────────────
        if (!empty($this->staged['cover'])) {
            $src = $this->tempDir . DIRECTORY_SEPARATOR . $this->staged['cover'];
            if (file_exists($src)) {
                $ext  = pathinfo($src, PATHINFO_EXTENSION);
                $dest = 'COVER_' . microtime(true) . '_' . uniqid() . '.' . $ext;
                if (@copy($src, $destDir . DIRECTORY_SEPARATOR . $dest)) {
                    $img     = ListingImage::create(['listing_id' => $listing->id, 'image_path' => $dest]);
                    $coverId = $img->id;
                }
            }
        }

        // ── Additional photos ─────────────────────────────────────────
        foreach (($this->staged['photos'] ?? []) as $i => $filename) {
            $src = $this->tempDir . DIRECTORY_SEPARATOR . $filename;
            if (!file_exists($src)) continue;

            $ext  = pathinfo($src, PATHINFO_EXTENSION);
            $dest = 'LISTING_IMG_' . ($i + 1) . '_' . microtime(true) . '_' . uniqid() . '.' . $ext;
            if (@copy($src, $destDir . DIRECTORY_SEPARATOR . $dest)) {
                ListingImage::create(['listing_id' => $listing->id, 'image_path' => $dest]);
            }
        }

        // ── Engine video ──────────────────────────────────────────────
        if (!empty($this->staged['video'])) {
            $src = $this->tempDir . DIRECTORY_SEPARATOR . $this->staged['video'];
            if (file_exists($src)) {
                $ext  = pathinfo($src, PATHINFO_EXTENSION);
                $dest = 'ENGINE_' . microtime(true) . '_' . uniqid() . '.' . $ext;
                if (@copy($src, $destDir . DIRECTORY_SEPARATOR . $dest)) {
                    $listing->video_path = $dest;
                }
            }
        }

        // ── Persist cover link + video path ───────────────────────────
        if ($coverId) {
            $listing->cover_photo_id = $coverId;
        }
        $listing->save();

        // ── Email + in-app notification ───────────────────────────────
        Listing::dispatchSideEffects($listing, $listing->seller);

        // ── Clean up temp directory ───────────────────────────────────
        $this->purgeTempDir($this->tempDir);

        Log::info("[ProcessListingMedia] Listing #{$this->listingId} media processed successfully.");
    }

    /**
     * Called after all retry attempts are exhausted.
     * The listing record already exists (metadata + payment saved during the
     * HTTP request), so admin can still review it; images will just be absent.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("[ProcessListingMedia] Failed after {$this->tries} tries for listing #{$this->listingId}.", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Attempt to clean up so temp files don't linger
        $this->purgeTempDir($this->tempDir);
    }

    // ─────────────────────────────────────────────────────────────────
    private function purgeTempDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach ((array) glob($dir . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_file($file)) @unlink($file);
        }
        @rmdir($dir);
    }
}
