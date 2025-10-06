@extends('layouts.welcome')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <title>
            {{ ($auctionListing->year ?? '') . ' ' . ($auctionListing->make ?? '') . ' ' . ($auctionListing->model ?? '') }}
            FOR SALE IN {{ strtoupper($auctionListing->location ?? ($auctionListing->city ?? '')) }}</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <script>
            tailwind.config = {
                darkMode: "class",
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                DEFAULT: "#4F46E5", // A vibrant blue
                                light: "#6366F1",
                                dark: "#4338CA"
                            },
                            "background-light": "#F9FAFB",
                            "background-dark": "#111827",
                            "card-light": "#FFFFFF",
                            "card-dark": "#1F2937",
                            "text-light": "#1F2937",
                            "text-dark": "#F9FAFB",
                            "muted-light": "#6B7280",
                            "muted-dark": "#9CA3AF",
                            "border-light": "#E5E7EB",
                            "border-dark": "#374151",
                            "success-light": "#D1FAE5",
                            "success-dark": "#064E3B",
                            "success-text-light": "#065F46",
                            "success-text-dark": "#A7F3D0",
                            "info-light": "#DBEAFE",
                            "info-dark": "#1E3A8A",
                            "info-text-light": "#1E40AF",
                            "info-text-dark": "#BFDBFE",
                            "danger-light": "#FEE2E2",
                            "danger-dark": "#991B1B",
                            "danger-text-light": "#B91C1C",
                            "danger-text-dark": "#FCA5A5",
                        },
                        fontFamily: {
                            sans: ['Inter', 'sans-serif'],
                        },
                        borderRadius: {
                            DEFAULT: "0.5rem",
                        },
                    },
                },
            };
        </script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>

    <body class="bg-background-light dark:bg-background-dark text-text-light dark:text-text-dark">
        @php


            // helper values with fallbacks
            $lotNumber = $auctionListing->lot_number ?? ($auctionListing->lot ?? $auctionListing->id);
            $vin = $auctionListing->vin ?? ($auctionListing->vehicle_vin ?? '—');
            $titleCode = $auctionListing->title_code ?? ($auctionListing->title ?? '—');
            $odometer = $auctionListing->odometer ?? ($auctionListing->mileage ?? null);
            $primaryDamage = $auctionListing->primary_damage ?? ($auctionListing->damage ?? '—');
            $estRetail =
                $auctionListing->est_retail_value ??
                ($auctionListing->estimated_value ?? ($auctionListing->price ?? null));
            $bodyStyle = $auctionListing->body_style ?? ($auctionListing->body ?? '—');
            $vehicleType = $auctionListing->vehicle_type ?? ($auctionListing->type ?? '—');
            $color = $auctionListing->color ?? '—';
            $engine = $auctionListing->engine ?? ($auctionListing->engine_size ?? '—');
            $cylinders = $auctionListing->cylinders ?? ($auctionListing->cyl ?? '—');
            $transmission = $auctionListing->transmission ?? '—';
            $drive = $auctionListing->drive ?? ($auctionListing->drive_type ?? '—');
            $fuel = $auctionListing->fuel ?? '—';
            $auctionHighlights = $auctionListing->auction_highlights ?? ($auctionListing->highlights ?? '—');
            $keys = $auctionListing->keys ?? (($auctionListing->has_keys ? 'Yes' : 'No') ?? '—');
            $specialNote =
                $auctionListing->special_note ?? ($auctionListing->notes ?? 'There are no notes for this Lot');
            $currentBid = $auctionListing->current_bid ?? ($auctionListing->highest_bid ?? 0);
            $recommendedBidText = $auctionListing->recommended_bid ?? null;
            $resultCount = $auctionListing->back_to_results_count ?? ($auctionListing->results_count ?? '—');
        @endphp

        <div class="container mx-auto p-4 lg:p-8">
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h1 class="text-2xl font-bold text-text-light dark:text-text-dark mb-4 sm:mb-0">
                    {{ $auctionListing->year ?? '' }} {{ $auctionListing->make ?? '' }}
                    {{ $auctionListing->model ?? '' }} FOR SALE IN
                    {{ strtoupper($auctionListing->location ?? ($auctionListing->city ?? '')) }}
                </h1>
                <div style="display:flex; align-items:center; justify-content:space-between; font-size:14px; margin-top:16px; border-top:1px solid #e5e7eb; padding-top:12px;">

    <!-- Left section -->
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="#"
           style="display:flex; align-items:center; color:#2563eb; font-weight:500; text-decoration:none; transition:color 0.2s;"
           onmouseover="this.style.color='#1e40af'" onmouseout="this.style.color='#2563eb'">
            <span class="material-icons" style="font-size:16px; margin-right:4px;">add</span>
            Add to watchlist
        </a>
    </div>

    <!-- Center section -->
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="#"
           style="display:flex; align-items:center; color:#2563eb; font-weight:500; text-decoration:none; transition:color 0.2s;"
           onmouseover="this.style.color='#1e40af'" onmouseout="this.style.color='#2563eb'">
            <span class="material-icons" style="font-size:16px; margin-right:4px;">chevron_left</span>
            Prev
        </a>

        <a href="#"
           style="text-align:center; color:#2563eb; font-weight:500; text-decoration:none; transition:color 0.2s;"
           onmouseover="this.style.color='#1e40af'" onmouseout="this.style.color='#2563eb'">
            <span style="display:block; font-size:12px; color:#6b7280;">Back to results</span>
            <span style="display:block; font-size:14px; font-weight:600;">{{ $resultCount }}</span>
        </a>

        <a href="#"
           style="display:flex; align-items:center; color:#2563eb; font-weight:500; text-decoration:none; transition:color 0.2s;"
           onmouseover="this.style.color='#1e40af'" onmouseout="this.style.color='#2563eb'">
            Next
            <span class="material-icons" style="font-size:16px; margin-left:4px;">chevron_right</span>
        </a>
    </div>
</div>

            </header>
            <main class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
    <div class="relative rounded-lg overflow-hidden mb-2">
        {{-- main image --}}
        <img id="mainImage"
             alt="{{ $auctionListing->make ?? '' }} {{ $auctionListing->model ?? '' }} main image"
             class="w-full h-auto"
             src="{{ $mainImage }}" />

        {{-- zoom badge (click opens lightbox) --}}
        <div id="zoomBtn"
             class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm flex items-center cursor-pointer">
            <span class="material-icons text-base mr-1">zoom_out_map</span> Click image to zoom
        </div>

        <div
            class="absolute top-4 right-4 bg-white dark:bg-card-dark px-3 py-1 rounded-full text-sm flex items-center text-primary-DEFAULT cursor-pointer shadow">
            <span class="material-icons text-base mr-1">add</span> Add to watchlist
        </div>
        <div class="absolute bottom-4 left-4 flex items-center space-x-2">
            <div class="bg-black bg-opacity-75 text-white px-3 py-1 rounded-full text-xs font-semibold">HD
            </div>
        </div>
        <div
            class="absolute bottom-4 right-4 bg-white dark:bg-card-dark px-3 py-1 rounded-full text-sm flex items-center cursor-pointer shadow">
            <span class="material-icons text-base mr-1">fullscreen</span>
            <span class="material-icons text-base mr-1">photo_camera</span>
            See all {{ $images->count() ?: 0 }} photos
        </div>
    </div>

    @php
        // keep your existing normalization (unchanged)
        $images = $auctionListing->images ?? ($auctionListing->listing_images ?? collect());
        if (is_array($images)) {
            $images = collect($images);
        }

        $images = $images
            ->map(function ($img) {
                if (is_object($img)) {
                    $path = $img->url ?? ($img->path ?? ($img->image ?? null));
                } elseif (is_array($img)) {
                    $path = $img['url'] ?? ($img['path'] ?? ($img['image'] ?? null));
                } else {
                    $path = $img;
                }

                if (!$path) {
                    return null;
                }

                if (is_string($path) && \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                    return $path;
                }

                return asset('uploads/listings/' . ltrim($path, '/'));
            })
            ->filter()
            ->values();

        // ensure mainImage variable still available (if not already set earlier)
        $mainImage = $mainImage ?? ($images->first() ?? ($auctionListing->main_image_url ?? ($auctionListing->image ?? asset('images/placeholder.png'))));
    @endphp

    <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-7 gap-2">
        @if ($images->count())
            @foreach ($images as $imgUrl)
                <img
                    src="{{ $imgUrl }}"
                    data-src="{{ $imgUrl }}"
                    alt="Thumbnail of {{ $auctionListing->make ?? '' }} {{ $auctionListing->model ?? '' }}"
                    class="thumb-img rounded-md cursor-pointer {{ $loop->first ? 'border-2 border-primary-DEFAULT' : '' }}" />
            @endforeach
        @else
            {{-- fallback single thumbnail --}}
            <img alt="Thumbnail fallback" class="rounded-md cursor-pointer border-2 border-primary-DEFAULT"
                 src="{{ $mainImage }}" />
        @endif

        <a class="bg-primary-DEFAULT text-white rounded-md flex flex-col items-center justify-center text-center p-2"
            href="#">
            <span class="material-icons">person_add</span>
            <span class="text-xs font-medium">Join Now</span>
        </a>
    </div>
</div>

{{-- Lightbox modal (hidden by default) --}}
<div id="imageModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4">
    <button id="modalClose" class="absolute top-4 right-4 z-50 rounded-full bg-white p-2 shadow">
        <span class="material-icons">close</span>
    </button>

    <button id="modalPrev" class="absolute left-4 md:left-8 z-50 rounded-full bg-white p-2 shadow">
        <span class="material-icons">chevron_left</span>
    </button>

    <button id="modalNext" class="absolute right-4 md:right-8 z-50 rounded-full bg-white p-2 shadow">
        <span class="material-icons">chevron_right</span>
    </button>

    <img id="modalImage" src="{{ $mainImage }}" alt="Zoomed image" class="max-h-[90vh] max-w-full rounded-md shadow-lg" />
</div>

{{-- Inline script: thumbnail swap + lightbox navigation --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // gather gallery array from Blade-provided images (strings)
    let gallery = @json($images->all() ?? []);
    const mainImage = document.getElementById('mainImage');
    const zoomBtn = document.getElementById('zoomBtn');
    const thumbs = Array.from(document.querySelectorAll('.thumb-img'));
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalClose = document.getElementById('modalClose');
    const modalPrev = document.getElementById('modalPrev');
    const modalNext = document.getElementById('modalNext');

    // if gallery empty but mainImage defined, seed gallery so lightbox still works
    if (!gallery.length && mainImage && mainImage.src) {
        gallery = [mainImage.src];
    }

    // helper to find current index in gallery for a src
    function findIndexBySrc(src) {
        return gallery.findIndex(g => g === src);
    }

    // thumbnail click swaps main image
    thumbs.forEach(thumb => {
        thumb.addEventListener('click', function () {
            const src = this.dataset.src || this.src;
            if (!src) return;

            // update main image
            if (mainImage) {
                mainImage.src = src;
            }

            // update active border
            thumbs.forEach(t => t.classList.remove('border-2', 'border-primary-DEFAULT'));
            this.classList.add('border-2', 'border-primary-DEFAULT');
        });
    });

    // open modal (from zoom badge or main image click)
    function openModalWith(src) {
        if (!src) return;
        modalImage.src = src;
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        // mark current index
        modal.currentIndex = findIndexBySrc(src);
        if (modal.currentIndex === -1) modal.currentIndex = 0;
    }

    if (zoomBtn) zoomBtn.addEventListener('click', () => openModalWith(mainImage.src));
    if (mainImage) mainImage.addEventListener('click', () => openModalWith(mainImage.src));

    // close modal
    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    }
    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        // close if clicking backdrop (not image)
        if (e.target === modal) closeModal();
    });

    // prev / next handlers
    function showAtIndex(i) {
        if (!gallery.length) return;
        if (i < 0) i = gallery.length - 1;
        if (i >= gallery.length) i = 0;
        modal.currentIndex = i;
        modalImage.src = gallery[i];
        // also sync main image and active thumb border
        if (mainImage) mainImage.src = gallery[i];
        thumbs.forEach(t => t.classList.remove('border-2', 'border-primary-DEFAULT'));
        const activeThumb = thumbs.find(t => (t.dataset.src || t.src) === gallery[i]);
        if (activeThumb) activeThumb.classList.add('border-2', 'border-primary-DEFAULT');
    }

    modalPrev.addEventListener('click', function (e) {
        e.stopPropagation();
        showAtIndex((modal.currentIndex || 0) - 1);
    });

    modalNext.addEventListener('click', function (e) {
        e.stopPropagation();
        showAtIndex((modal.currentIndex || 0) + 1);
    });

    // keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (modal.classList.contains('hidden')) return;
        if (e.key === 'ArrowLeft') {
            showAtIndex((modal.currentIndex || 0) - 1);
        } else if (e.key === 'ArrowRight') {
            showAtIndex((modal.currentIndex || 0) + 1);
        } else if (e.key === 'Escape') {
            closeModal();
        }
    });
});
</script>

                <div class="space-y-6">
                    <div
                        class="bg-card-light dark:bg-card-dark p-4 rounded-lg border border-border-light dark:border-border-dark">
                        <h2 class="text-lg font-semibold mb-3 flex items-center">
                            Auction Vehicle Details
                            <span
                                class="material-icons text-muted-light dark:text-muted-dark ml-2 text-base cursor-pointer">info</span>
                        </h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Lot Number:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $lotNumber }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-muted-light dark:text-muted-dark">VIN:</span>
                                <div class="text-right">
                                    <span
                                        class="font-medium text-text-light dark:text-text-dark">{{ $vin }}</span>
                                    <a class="text-primary-DEFAULT text-xs block hover:underline" href="#">Get History
                                        Report</a>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-muted-light dark:text-muted-dark">Title Code:</span>
                                <div class="text-right">
                                    <span
                                        class="font-medium text-text-light dark:text-text-dark">{{ $titleCode }}</span>
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark text-base cursor-pointer ml-1">info</span>
                                    @if (($auctionListing->title_pending_days ?? 0) >= 90)
                                        <div
                                            class="bg-danger-light text-danger-text-light dark:bg-danger-dark dark:text-danger-text-dark text-xs px-2 py-1 rounded-md mt-1">
                                            TITLE IS PENDING FOR {{ $auctionListing->title_pending_days ?? '90+' }}+ DAYS
                                            <span class="material-icons text-xs align-middle">error_outline</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Odometer:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">
                                    {{ $odometer ? number_format($odometer) . ' mi (ACTUAL)' : '—' }}
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Primary Damage:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $primaryDamage }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Est. Retail Value:</span>
                                <span class="font-medium text-text-light dark:text-text-dark flex items-center">
                                    @if ($estRetail)
                                        ${{ number_format($estRetail, 2) }} USD
                                    @else
                                        —
                                    @endif
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer">info</span>
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Body Style:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $bodyStyle }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Vehicle Type:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $vehicleType }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Color:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $color }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Engine:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $engine }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Cylinders:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $cylinders }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Transmission:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $transmission }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Drive:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $drive }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Fuel:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $fuel }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Auction Highlights:</span>
                                <span class="font-medium text-text-light dark:text-text-dark flex items-center">
                                    {{ $auctionHighlights }}
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer">info</span>
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Keys:</span>
                                <span class="font-medium text-text-light dark:text-text-dark flex items-center">
                                    {{ $keys }}
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer">info</span>
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark">Special Note:</span>
                                <span class="font-medium text-text-light dark:text-text-dark">{{ $specialNote }}</span>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-success-light dark:bg-success-dark border border-green-200 dark:border-green-800 p-4 rounded-lg">
                        <h2 class="font-semibold text-success-text-light dark:text-success-text-dark mb-1">Auction In
                            Progress</h2>
                        <p class="text-sm text-success-text-light dark:text-success-text-dark mb-3">
                            {{ $auctionListing->auction_status_message ?? 'Preliminary bidding is now closed. To place a live bid, please sign in or register' }}
                        </p>
                        <button
                            class="w-full bg-white dark:bg-card-dark text-primary-DEFAULT font-semibold py-2 px-4 rounded-full border border-primary-DEFAULT hover:bg-primary-light hover:text-white dark:hover:bg-primary-dark transition-colors flex items-center justify-center">
                            <span class="material-icons mr-2">notifications</span> SET ALERT
                        </button>
                    </div>

                    <div
                        class="bg-card-light dark:bg-card-dark p-4 rounded-lg border border-border-light dark:border-border-dark">
                        <h2 class="text-lg font-semibold mb-4">Bid Information</h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark flex items-center">
                                    Bid Status:
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer">info</span>
                                </span>
                                <span id="bidStatus"
                                    class="font-medium text-text-light dark:text-text-dark">{{ $auctionListing->bid_status_label ?? "You Haven't Bid" }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark flex items-center">
                                    Sale Status:
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer">info</span>
                                </span>
                                <span id="saleStatus"
                                    class="font-medium text-text-light dark:text-text-dark">{{ $auctionListing->sale_status_label ?? 'On Minimum Bid' }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-light dark:text-muted-dark flex items-center">
                                    Recommended Bid:
                                    <span
                                        class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer">info</span>
                                </span>

                                {{-- recommended bid element --}}
                                <div id="recommendedBidWrap">
                                    @if ($recommendedBidText)
                                        <span id="recommendedBid"
                                            class="font-medium text-primary-DEFAULT">{{ $recommendedBidText }}</span>
                                    @else
                                        <a id="recommendedBid" class="font-medium text-primary-DEFAULT hover:underline"
                                            href="#">{{ $auctionListing->login_cta_text ?? 'Login or Register to view' }}</a>
                                    @endif
                                </div>
                            </div>

                            <div class="flex justify-between items-start">
                                <span class="text-muted-light dark:text-muted-dark">Current Bid:</span>
                                <div class="text-right">
                                    <span id="currentBid"
                                        class="text-2xl font-bold text-text-light dark:text-text-dark">${{ number_format($currentBid, 2) }}</span>
                                    <p id="reserveText" class="text-xs text-muted-light dark:text-muted-dark">
                                        {{ $auctionListing->reserve_met ? 'Reserve met' : 'Seller Reserve Not Yet Met' }}
                                        <span
                                            class="material-icons text-muted-light dark:text-muted-dark ml-1 text-base cursor-pointer align-middle">info</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Bid form for logged-in users (unchanged) --}}
                        @auth
                            <form id="bidForm" class="mt-4 space-y-3">
                                @csrf
                                <div class="flex gap-2">
                                    <input id="bidAmount" name="amount" type="number" step="0.01" min="0"
                                        class="w-full rounded-md border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary-DEFAULT focus:border-primary-DEFAULT text-sm p-2"
                                        placeholder="Enter your bid amount" />
                                    <button type="submit"
                                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm">
                                        Place Bid
                                    </button>
                                </div>
                                <p id="bidError" class="text-xs text-red-500 hidden"></p>
                                <p id="bidSuccess" class="text-xs text-green-600 hidden"></p>
                            </form>
                        @else
                            <div class="mt-4">
                                <button onclick="document.getElementById('loginModal').classList.remove('hidden')"
                                    class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors text-sm">
                                    Login to Bid
                                </button>
                            </div>
                        @endauth

                        <p class="text-xs text-muted-light dark:text-muted-dark mt-4 text-center">All bids are legally
                            binding, and sales are "as-is" and final. <a class="text-primary-DEFAULT hover:underline"
                                href="#">{{ $auctionListing->learn_more_link_text ?? 'Learn More' }}</a></p>
                    </div>


                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            @auth
                            const form = document.getElementById('bidForm');
                            const input = document.getElementById('bidAmount');
                            const bidError = document.getElementById('bidError');
                            const bidSuccess = document.getElementById('bidSuccess');
                            const currentBidEl = document.getElementById('currentBid');

                            // new elements
                            const bidStatusEl = document.getElementById('bidStatus');
                            const saleStatusEl = document.getElementById('saleStatus');
                            const recommendedBidEl = document.getElementById('recommendedBid');
                            const reserveTextEl = document.getElementById('reserveText');

                            form.addEventListener('submit', async function(e) {
                                e.preventDefault();
                                bidError.classList.add('hidden');
                                bidSuccess.classList.add('hidden');

                                const amount = parseFloat(input.value);
                                if (isNaN(amount) || amount <= 0) {
                                    bidError.textContent = 'Please enter a valid bid amount.';
                                    bidError.classList.remove('hidden');
                                    return;
                                }

                                try {
                                    const token = document.querySelector('input[name="_token"]').value;
                                    const res = await fetch("{{ route('auction.bid.store', $auctionListing->id) }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': token,
                                            'Accept': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            amount
                                        })
                                    });

                                    const json = await res.json();

                                    if (!res.ok) {
                                        // handle validation error (Laravel -> 422) or other errors
                                        const errMsg = (json?.errors?.amount && json.errors.amount[0]) || json
                                            ?.message || 'Failed to place bid';
                                        bidError.textContent = errMsg;
                                        bidError.classList.remove('hidden');
                                        return;
                                    }

                                    // success — update UI values (use server-provided values if available)
                                    // expected: json.currentBid, json.bid, json.reserve_met, json.bid_status_label, json.sale_status_label, json.recommendedBidText
                                    const currentBid = json.currentBid ?? json.bid?.amount ?? null;
                                    if (currentBid !== null) {
                                        // ensure formatted as string with 2 decimals (server may already return formatted)
                                        const formatted = (typeof currentBid === 'number') ? currentBid.toFixed(2) :
                                            currentBid;
                                        currentBidEl.textContent = '$' + formatted;
                                    }

                                    // update reserve text if server supplied boolean
                                    if (typeof json.reserve_met !== 'undefined') {
                                        reserveTextEl.textContent = json.reserve_met ? 'Reserve met' :
                                            'Seller Reserve Not Yet Met';
                                    }

                                    // update bid status label (server can return a better label)
                                    if (json.bid_status_label) {
                                        bidStatusEl.textContent = json.bid_status_label;
                                    } else {
                                        // fallback: show user-friendly message
                                        bidStatusEl.textContent = 'You have placed a bid';
                                    }

                                    // update sale status if supplied
                                    if (json.sale_status_label) {
                                        saleStatusEl.textContent = json.sale_status_label;
                                    }

                                    // update recommended bid (can be text or html)
                                    if (json.recommendedBidText) {
                                        // replace inner element content (string)
                                        recommendedBidEl.textContent = json.recommendedBidText;
                                        // if you expect HTML, use recommendedBidEl.innerHTML = json.recommendedBidText;
                                    }

                                    // show success
                                    const displayBid = currentBid ?? json.bid?.amount ?? '';
                                    bidSuccess.textContent = 'Bid placed successfully' + (displayBid ? ': $' + (
                                            typeof displayBid === 'number' ? displayBid.toFixed(2) : displayBid) :
                                        '');
                                    bidSuccess.classList.remove('hidden');

                                    // clear input
                                    input.value = '';
                                } catch (err) {
                                    bidError.textContent = 'An error occurred. Try again.';
                                    bidError.classList.remove('hidden');
                                    console.error(err);
                                }
                            });
                        @endauth
                        });
                    </script>



                    <div
                        class="bg-card-light dark:bg-card-dark p-4 rounded-lg border border-border-light dark:border-border-dark">
                        <h2 class="text-lg font-semibold mb-4 flex items-center">Get Alerts for Similar Vehicles <span
                                class="material-icons text-muted-light dark:text-muted-dark ml-2 text-base cursor-pointer">info</span>
                        </h2>
                        <div class="mb-4">
                            <label class="text-sm font-medium text-muted-light dark:text-muted-dark">Select
                                Frequency:</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="flex items-center">
                                    <input checked=""
                                        class="form-radio text-primary-DEFAULT focus:ring-primary-DEFAULT"
                                        name="frequency" type="radio" />
                                    <span class="ml-2 text-sm text-text-light dark:text-text-dark">Daily</span>
                                </label>
                                <label class="flex items-center">
                                    <input class="form-radio text-primary-DEFAULT focus:ring-primary-DEFAULT"
                                        name="frequency" type="radio" />
                                    <span class="ml-2 text-sm text-text-light dark:text-text-dark">Weekly</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-4">
                            <input
                                class="w-full rounded-md border-border-light dark:border-border-dark bg-background-light dark:bg-background-dark focus:ring-primary-DEFAULT focus:border-primary-DEFAULT text-sm"
                                placeholder="Email" type="email" />
                        </div>
                        <button
                            class="w-full bg-white dark:bg-card-dark text-primary-DEFAULT font-semibold py-2 px-4 rounded-full border border-primary-DEFAULT hover:bg-primary-light hover:text-white dark:hover:bg-primary-dark transition-colors flex items-center justify-center">
                            <span class="material-icons mr-2">notifications</span> SET ALERT
                        </button>
                    </div>
                </div>
            </main>
        </div>

    </body>

    </html>
@endsection
