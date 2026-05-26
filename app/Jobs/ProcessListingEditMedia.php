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
 * ProcessListingEditMedia
 * ─────────────────────────────────────────────────────────────────────────
 * Runs on the queue after a seller submits an edited listing.
 *
 * Mirrors the pattern of ProcessListingMedia used for new submissions:
 * the controller pre-moves uploaded files from PHP's temp directory to
 * storage/app/listing-queue/{uuid}/ and dispatches this job so the seller
 * is redirected immediately without waiting for disk I/O.
 *
 * This job:
 *   1. Copies new files from the temp dir to public/uploads/listings/
 *   2. Creates new ListingImage records
 *   3. Deletes old ListingImage records + their disk files (swap pattern)
 *   4. Replaces video_path / cover_photo_id if new media was supplied
 *   5. Cleans up the temp directory
 *
 * Unlike ProcessListingMedia it does NOT fire dispatchSideEffects because
 * this is an edit, not a first submission.
 */
class ProcessListingEditMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum attempts before marking failed. */
    public int $tries = 3;

    /** Seconds before the job is considered timed-out. */
    public int $timeout = 180;

    /** Seconds between retry attempts. */
    public int $backoff = 15;

    /**
     * @param int         $listingId    The listing being edited.
     * @param string      $tempDir      Absolute path to the staging directory.
     * @param array       $staged       ['cover' => 'cover.jpg', 'photos' => [...], 'video' => 'video.mp4']
     * @param array       $oldImageIds  ListingImage IDs to delete after new ones are written.
     * @param string|null $oldVideoPath Existing video filename to delete (or null if none).
     */
    public function __construct(
        public readonly int     $listingId,
        public readonly string  $tempDir,
        public readonly array   $staged,
        public readonly array   $oldImageIds,
        public readonly ?string $oldVideoPath
    ) {}

    public function handle(): void
    {
        $listing = Listing::findOrFail($this->listingId);
        $destDir = public_path('uploads/listings');

        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $coverId = null;

        // ── New cover photo ───────────────────────────────────────────────
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

        // ── New additional photos ─────────────────────────────────────────
        foreach (($this->staged['photos'] ?? []) as $i => $filename) {
            $src = $this->tempDir . DIRECTORY_SEPARATOR . $filename;
            if (!file_exists($src)) continue;

            $ext  = pathinfo($src, PATHINFO_EXTENSION);
            $dest = 'LISTING_IMG_' . ($i + 1) . '_' . microtime(true) . '_' . uniqid() . '.' . $ext;
            if (@copy($src, $destDir . DIRECTORY_SEPARATOR . $dest)) {
                $img = ListingImage::create(['listing_id' => $listing->id, 'image_path' => $dest]);
                if (!$coverId) {
                    $coverId = $img->id; // fallback: first photo becomes cover
                }
            }
        }

        // ── New engine video ──────────────────────────────────────────────
        if (!empty($this->staged['video'])) {
            $src = $this->tempDir . DIRECTORY_SEPARATOR . $this->staged['video'];
            if (file_exists($src)) {
                $ext  = pathinfo($src, PATHINFO_EXTENSION);
                $dest = 'ENGINE_' . microtime(true) . '_' . uniqid() . '.' . $ext;
                if (@copy($src, $destDir . DIRECTORY_SEPARATOR . $dest)) {
                    // Delete old video file from disk before pointing to new one
                    if ($this->oldVideoPath) {
                        $oldFile = $destDir . DIRECTORY_SEPARATOR . $this->oldVideoPath;
                        if (is_file($oldFile)) {
                            @unlink($oldFile);
                        }
                    }
                    $listing->video_path = $dest;
                }
            }
        }

        // ── Swap old images out (safe: new files already written above) ───
        if (!empty($this->oldImageIds)) {
            foreach (ListingImage::whereIn('id', $this->oldImageIds)->get() as $img) {
                $file = $destDir . DIRECTORY_SEPARATOR . $img->image_path;
                if (is_file($file)) {
                    @unlink($file);
                }
                $img->delete();
            }
        }

        // ── Persist cover_photo_id + video_path ───────────────────────────
        if ($coverId) {
            $listing->cover_photo_id = $coverId;
        }
        $listing->save();

        // ── Clean up temp directory ───────────────────────────────────────
        $this->purgeTempDir($this->tempDir);

        Log::info("[ProcessListingEditMedia] Listing #{$this->listingId} media updated successfully.");
    }

    /**
     * Called after all retry attempts are exhausted.
     * The listing's scalar fields are already saved; only media is affected.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("[ProcessListingEditMedia] Failed after {$this->tries} tries for listing #{$this->listingId}.", [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $this->purgeTempDir($this->tempDir);
    }

    // ─────────────────────────────────────────────────────────────────────
    private function purgeTempDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        foreach ((array) glob($dir . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_file($file)) @unlink($file);
        }
        @rmdir($dir);
    }
}
