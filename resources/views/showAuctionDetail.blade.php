@extends('layouts.welcome')

@section('content')

<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $vehicle->year ?? '' }} {{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }} for Sale in {{ strtoupper($vehicle->location ?? ($vehicle->city ?? '')) }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        .material-icons {
            vertical-align: middle;
            font-size: 18px;
        }
        .container{
           max-width : 1580px;
        }
        .slider-container {
            position: relative;
            overflow: hidden;
            background: #f6f6f6;
        }
        /* default: hide all slider images; only .active will be shown */
        .slider-image {
            display: none;
            width: 100%;
            height: auto;
            transition: opacity 0.4s ease;
            object-fit: contain;
            cursor: zoom-in;
        }
        .slider-image.active {
            display: block;
            opacity: 1;
        }

        /* Zoom Lens */
        #zoomLens {
            display: none;
            position: absolute;
            border: 2px solid rgba(0,0,0,0.7);
            width: 140px;
            height: 140px;
            pointer-events: none;
            background-repeat: no-repeat;
            z-index: 60;
            border-radius: 6px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
        }

        .sticky-sidebar {
            position: sticky;
            top: 20px;
        }
        /* vehicle-image no longer forces display; slider rules control visibility */
        .vehicle-image {
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .thumbnail {
            border-radius: 0.25rem;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            width: 100%;
            height: 70px;
            object-fit: cover;
        }
        .thumbnail:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .thumbnail.active {
            border: 2px solid #359EFF;
        }
        .bid-section {
            background: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }
        .similar-listing-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .similar-listing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        @media (max-width: 991.98px) {
            .sticky-sidebar {
                position: static;
            }
            #zoomLens { display: none !important; } /* disable lens on small screens */
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">A Better Bid</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Cars</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $vehicle->make ?? 'Vehicle' }}</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none">{{ $vehicle->model ?? 'Model' }}</a></li>
                <li class="breadcrumb-item active">{{ $vehicle->year ?? '' }} {{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }}</li>
            </ol>
        </nav>

        <!-- Watchlist Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center">
                <span class="material-icons me-1">star_outline</span>
                Watchlist
            </button>
        </div>

        <!-- Main Title -->
        <h1 class="h2 mb-4">{{ $vehicle->year ?? '' }} {{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }} for Sale in {{ strtoupper($vehicle->location ?? ($vehicle->city ?? '')) }}</h1>

        <!-- Three Column Layout -->
        <div class="row g-4">
            <!-- Column 1: Images Gallery -->
            <div class="col-lg-6">
                <!-- Image Slider -->
                <div id="vehicleSlider" class="position-relative mb-3">
                    <div class="slider-container">
                        @php
                            $images = $vehicle->images ?? [];
                            if($images && is_iterable($images)) {
                                $first = current($images);
                                if(is_object($first)) {
                                    $images = array_map(function($i) {
                                        return is_object($i) ? ($i->url ?? $i->path ?? $i->image ?? $i->image_path ?? null) : $i;
                                    }, (array)$images);
                                } else {
                                    $images = (array)$images;
                                }
                            } else {
                                $images = [];
                            }
                        @endphp

                        @if(count($images) > 0)
                            @foreach($images as $index => $image)
                                <img src="{{ $image ?? asset('images/placeholder.png') }}"
                                     alt="{{ $vehicle->year ?? '' }} {{ $vehicle->make ?? '' }} {{ $vehicle->model ?? '' }} - Image {{ $index + 1 }}"
                                     class="img-fluid vehicle-image w-100 slider-image {{ $index === 0 ? 'active' : '' }}"
                                     data-index="{{ $index }}">
                            @endforeach
                        @else
                            <img src="{{ asset('images/placeholder.png') }}"
                                 alt="No images available"
                                 class="img-fluid vehicle-image w-100 slider-image active"
                                 data-index="0">
                        @endif
                    </div>

                    <!-- Zoom lens -->
                    <div id="zoomLens" aria-hidden="true"></div>

                    <!-- Controls -->
                    <button class="btn btn-dark position-absolute top-50 start-0 translate-middle-y slider-prev" aria-label="Previous image">
                        <span class="material-icons">chevron_left</span>
                    </button>
                    <button class="btn btn-dark position-absolute top-50 end-0 translate-middle-y slider-next" aria-label="Next image">
                        <span class="material-icons">chevron_right</span>
                    </button>

                    <!-- Image counter -->
                    <div class="position-absolute bottom-0 end-0 m-3 bg-dark text-white px-2 py-1 rounded small">
                        <span id="currentSlide">1</span> / <span id="totalSlides">{{ count($images) ?: 1 }}</span>
                    </div>
                </div>

                <!-- Thumbnails row (optional) -->
                <div class="row g-2 mt-2">
                    @if(count($images) > 0)
                        @foreach($images as $index => $image)
                            <div class="col-3 col-md-2">
                                <img src="{{ $image ?? asset('images/placeholder.png') }}"
                                     alt="Thumbnail {{ $index + 1 }}"
                                     class="img-fluid thumbnail {{ $index === 0 ? 'active' : '' }}"
                                     data-image="{{ $image }}" data-index="{{ $index }}">
                            </div>
                        @endforeach
                    @else
                        <div class="col-3 col-md-2">
                            <img src="{{ asset('images/placeholder.png') }}" alt="No images" class="img-fluid thumbnail active">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Column 2: Bid Information & Action Buttons -->
            <div class="col-lg-3">
                <div class="sticky-sidebar">
                    <!-- Bid Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h5 card-title mb-0">Bid Information</h2>
                                <span class="badge bg-danger d-flex align-items-center">
                                    <span class="material-icons me-1">timer</span>
                                    {{ $vehicle->time_remaining ?? '23h 37m 22s' }}
                                </span>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="text-muted mb-1 small">Current Bid</p>
                                    <p class="h5 text-dark fw-bold">${{ number_format($vehicle->current_bid ?? 525) }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1 small">Bid Status</p>
                                    <p class="text-success fw-semibold">{{ $vehicle->bid_status ?? "You Haven't Bid" }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1 small">Sale Status</p>
                                    <p>{{ $vehicle->sale_status ?? 'On Minimum Bid' }}</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1 small">Seller's Reserve</p>
                                    <p>{{ $vehicle->reserve_met ? 'Met' : 'Not yet met' }}</p>
                                </div>
                            </div>

                            <form action="{{ route('auction.bid.store', $vehicle->id) }}" method="POST">
                                @csrf
                                <p class="text-muted small mb-2">Enter Maximum Bid ($25 Bid Increments)</p>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           name="bid_amount"
                                           class="form-control"
                                           value="{{ ($vehicle->current_bid ?? 525) + 25 }}"
                                           min="{{ ($vehicle->current_bid ?? 525) + 25 }}"
                                           step="25">
                                    <button type="submit" class="btn btn-primary">Bid Now</button>
                                </div>
                                <p class="text-muted small text-center">ALL SALES ARE FINAL, SOLD "AS IS, WHERE IS"</p>
                            </form>
                        </div>
                    </div>

                    <!-- Buy It Now -->
                    <div class="card bg-info bg-opacity-10 border-info mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p class="small mb-0">Buy this lot now before it goes to the auction!</p>
                                <span class="material-icons text-primary">sell</span>
                            </div>
                            <p class="small mb-3">Get it for: <span class="h5 fw-bold text-dark">${{ number_format($vehicle->buy_now_price ?? 2550) }}</span></p>
                            <button class="btn btn-primary w-100">Buy It Now</button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        @auth
                            <button class="btn btn-primary d-flex align-items-center justify-content-center">
                                <span class="material-icons me-2">gavel</span>
                                Place Bid
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary d-flex align-items-center justify-content-center">
                                <span class="material-icons me-2">login</span>
                                Login to Bid
                            </a>
                        @endauth

                        @auth
                            <button class="btn btn-outline-primary d-flex align-items-center justify-content-center">
                                <span class="material-icons me-2">star</span>
                                Add to Watchlist
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary d-flex align-items-center justify-center">
                                <span class="material-icons me-2">star</span>
                                Add to Watchlist
                            </a>
                        @endauth

                        <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center">
                            <span class="material-icons me-2">share</span>
                            Share
                        </button>
                    </div>
                </div>
            </div>

            <!-- Column 3: Vehicle Details, Information, History & Sale Info -->
            <div class="col-lg-3">
                <div class="sticky-sidebar">
                    <!-- Vehicle Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h5 card-title mb-3">Vehicle Details</h2>
                            <div class="row">
                                <div class="col-12">
                                    <dl class="row mb-3">
                                        <dt class="col-sm-6 text-muted">VIN#</dt>
                                        <dd class="col-sm-6">{{ $vehicle->vin ?? '—' }}</dd>

                                        <dt class="col-sm-6 text-muted">Lot#</dt>
                                        <dd class="col-sm-6 text-primary">{{ $vehicle->lot_number ?? '—' }}</dd>

                                        <dt class="col-sm-6 text-muted">Title Code</dt>
                                        <dd class="col-sm-6">{{ $vehicle->title_code ?? '—' }}</dd>

                                        <dt class="col-sm-6 text-muted">Odometer</dt>
                                        <dd class="col-sm-6">{{ $vehicle->mileage ? number_format($vehicle->mileage) . ' mi. Actual' : '—' }}</dd>

                                        <dt class="col-sm-6 text-muted">Primary Damage</dt>
                                        <dd class="col-sm-6">{{ $vehicle->primary_damage ?? 'Unknown' }}</dd>

                                        <dt class="col-sm-6 text-muted">Secondary Damage</dt>
                                        <dd class="col-sm-6">{{ $vehicle->secondary_damage ?? '—' }}</dd>

                                        <dt class="col-sm-6 text-muted">Est. Retail Value</dt>
                                        <dd class="col-sm-6">{{ $vehicle->price ? '$' . number_format($vehicle->price) . ' USD' : '—' }}</dd>

                                        <dt class="col-sm-6 text-muted">Keys</dt>
                                        <dd class="col-sm-6">{{ $vehicle->keys ?? 'Yes (not guaranteed)' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h5 card-title mb-3">Vehicle Information</h2>
                            <dl class="row small">
                                <dt class="col-sm-5 text-muted">Vehicle Type</dt>
                                <dd class="col-sm-7">{{ $vehicle->vehicle_type ?? 'AUTOMOBILE' }}</dd>

                                <dt class="col-sm-5 text-muted">Year</dt>
                                <dd class="col-sm-7">{{ $vehicle->year ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Make</dt>
                                <dd class="col-sm-7">{{ $vehicle->make ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Model</dt>
                                <dd class="col-sm-7">{{ $vehicle->model ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Body Style</dt>
                                <dd class="col-sm-7">{{ $vehicle->body_style ?? 'Sedan' }}</dd>

                                <dt class="col-sm-5 text-muted">Color</dt>
                                <dd class="col-sm-7">{{ $vehicle->color ?? 'White' }}</dd>

                                <dt class="col-sm-5 text-muted">Engine Type</dt>
                                <dd class="col-sm-7">{{ $vehicle->engine ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Cylinders</dt>
                                <dd class="col-sm-7">{{ $vehicle->cylinders ?? '—' }}</dd>

                                <dt class="col-sm-5 text-muted">Transmission</dt>
                                <dd class="col-sm-7">{{ $vehicle->transmission ?? 'Automatic' }}</dd>

                                <dt class="col-sm-5 text-muted">Drive</dt>
                                <dd class="col-sm-7">{{ $vehicle->drive ?? 'All Wheel Drive' }}</dd>

                                <dt class="col-sm-5 text-muted">Fuel</dt>
                                <dd class="col-sm-7">{{ $vehicle->fuel ?? 'Gasoline' }}</dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Sale Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="h5 card-title mb-3">Sale Information</h2>
                            <dl class="row small">
                                <dt class="col-sm-5 text-muted">Auction Location</dt>
                                <dd class="col-sm-7 text-primary">{{ $vehicle->location ?? 'TRENTON, NJ' }}</dd>

                                <dt class="col-sm-5 text-muted">Sale Date</dt>
                                <dd class="col-sm-7">
                                    {{ $vehicle->sale_date ?? 'Monday, October 13, 2025' }}
                                    <a href="#" class="text-primary small ms-1">Add to Calendar</a>
                                </dd>

                                <dt class="col-sm-5 text-muted">Live Auction Starts</dt>
                                <dd class="col-sm-7">{{ $vehicle->auction_time ?? '10:00 AM EST' }}</dd>

                                <dt class="col-sm-5 text-muted">Sale Name</dt>
                                <dd class="col-sm-7">{{ $vehicle->sale_name ?? 'NJ - TRENTON' }}</dd>

                                <dt class="col-sm-5 text-muted">Last Updated</dt>
                                <dd class="col-sm-7">{{ $vehicle->updated_at ? $vehicle->updated_at->format('F j, Y g:i A UTC') : 'October 12, 2025 2:41 AM UTC' }}</dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Vehicle History -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h2 class="h5 card-title mb-0">Vehicle History</h2>
                                <img alt="EpicVin logo" height="20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRTPJunOOb6OBRzT_23hBSlyLMMLn9WRoEeFBAFzIilCX05yimmf0lUW-sY2t9TGBiPZj6xXoy8Ra8nPJa6Ch-Ooxewy1pE_Gd8sAUTgObvgYi-bcxtT090zv5PTTJAGoeS9N7cdSYfT31A9BmFi9DNB57SoeG1RciZ4YB2o0FJ0pFOHXpB9hYI_VBUSMH_9HaY2x4fgstPI4cEks910xNcXLtl72u4frU-gAC1dKxwuys1LgKtspRerX9Uih1Ni0OuoyxDdUWQws"/>
                            </div>
                            <dl class="row small mb-3">
                                <dt class="col-sm-6 text-muted">Sales Records</dt>
                                <dd class="col-sm-6">{{ $vehicle->sales_records ?? '12 records found' }}</dd>

                                <dt class="col-sm-6 text-muted">Odometer Reading</dt>
                                <dd class="col-sm-6">{{ $vehicle->mileage ? number_format($vehicle->mileage) : '60659' }}</dd>

                                <dt class="col-sm-6 text-muted">Previous Sales</dt>
                                <dd class="col-sm-6">{{ $vehicle->previous_sales ?? '12 sales found' }}</dd>

                                <dt class="col-sm-6 text-muted">Ownership History</dt>
                                <dd class="col-sm-6">{{ $vehicle->ownership_history ?? '1 owner found' }}</dd>

                                <dt class="col-sm-6 text-muted">Safety Recalls</dt>
                                <dd class="col-sm-6">{{ $vehicle->safety_recalls ?? '2 records found' }}</dd>

                                <dt class="col-sm-6 text-muted">Accidents</dt>
                                <dd class="col-sm-6">{{ $vehicle->accidents ?? '1 record found' }}</dd>
                            </dl>
                            <button class="btn btn-outline-primary w-100">Get Vehicle History Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Listings Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h2 class="h3 mb-4">Similar Listings</h2>
                <div class="row g-4">
                    <!-- Similar Listing cards (kept as-is) -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card similar-listing-card h-100">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuCGCq-w4UNmCP1kJ1GfP2F2IgYnFJyGkaFXlHQxMxv4cQadUl6tQjKNOkcTPlobDN0kXub_5BKcDI9wk4QjrOSqTorJuy3AljXJ_cspy6IKbr3jyqK33bhXlwTMGn68qBRlsnURUYPxwQxRdGVZDsw6eJNjLmooYcLOiSTZOYfJ05XL7LeOmaYc7J6Nwr8FWTdPzVvRO_cdU9uMsyw2W2Dnr_zweRoGSxhZrqtrDOPrLJLXqgRV-lB-QfMH2kj4RxiwT3LZglTXMAk"
                                 class="card-img-top"
                                 alt="Similar Vehicle 1">
                            <div class="card-body">
                                <h5 class="card-title">2018 BMW 3 Series</h5>
                                <p class="card-text text-muted small">Sedan • 45,200 mi • White</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h6 text-primary mb-0">$32,500</span>
                                    <span class="badge bg-warning text-dark">3 days left</span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <button class="btn btn-outline-primary btn-sm w-100">View Details</button>
                            </div>
                        </div>
                    </div>

                    <!-- ... other similar cards ... -->
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Consolidated Slider + Zoom Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const images = Array.from(document.querySelectorAll('.slider-image'));
        const totalSlidesEl = document.getElementById('totalSlides');
        const currentSlideEl = document.getElementById('currentSlide');
        const nextBtn = document.querySelector('.slider-next');
        const prevBtn = document.querySelector('.slider-prev');
        const zoomLens = document.getElementById('zoomLens');
        const thumbnails = Array.from(document.querySelectorAll('.thumbnail'));
        const sliderWrap = document.getElementById('vehicleSlider');
        let currentIndex = 0;
        const total = Math.max(images.length, 1);

        if (totalSlidesEl) totalSlidesEl.textContent = total;

        function showImage(index) {
            if (!images.length) return;
            images.forEach((img, i) => {
                img.classList.toggle('active', i === index);
            });

            thumbnails.forEach((t, i) => t.classList.toggle('active', i === index));

            if (currentSlideEl) currentSlideEl.textContent = index + 1;
        }

        // init
        showImage(currentIndex);

        // Next / Prev
        if (nextBtn) nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % total;
            showImage(currentIndex);
        });
        if (prevBtn) prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + total) % total;
            showImage(currentIndex);
        });

        // Auto slide (only if >1)
        let autoSlideInterval = null;
        if (images.length > 1) {
            autoSlideInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % total;
                showImage(currentIndex);
            }, 5000);
        }

        // Pause on hover / resume on leave
        if (sliderWrap) {
            sliderWrap.addEventListener('mouseenter', () => {
                if (autoSlideInterval) clearInterval(autoSlideInterval);
            });
            sliderWrap.addEventListener('mouseleave', () => {
                if (images.length > 1) {
                    autoSlideInterval = setInterval(() => {
                        currentIndex = (currentIndex + 1) % total;
                        showImage(currentIndex);
                    }, 5000);
                }
            });
        }

        // Thumbnails click
        if (thumbnails.length) {
            thumbnails.forEach((thumb, idx) => {
                thumb.addEventListener('click', () => {
                    const idxFromData = parseInt(thumb.getAttribute('data-index'));
                    currentIndex = Number.isFinite(idxFromData) ? idxFromData : idx;
                    showImage(currentIndex);
                });
            });
        }

        // Zoom lens logic
        const zoomFactor = 2; // 2x by default

        images.forEach(img => {
            function moveLens(clientX, clientY) {
                const rect = img.getBoundingClientRect();
                const x = clientX - rect.left;
                const y = clientY - rect.top;

                if (x < 0 || y < 0 || x > rect.width || y > rect.height) {
                    zoomLens.style.display = 'none';
                    return;
                }

                const lensW = zoomLens.offsetWidth;
                const lensH = zoomLens.offsetHeight;

                let left = x - lensW / 2;
                let top = y - lensH / 2;

                // clamp inside image area
                left = Math.max(0, Math.min(left, rect.width - lensW));
                top = Math.max(0, Math.min(top, rect.height - lensH));

                // position relative to slider container (slider image is direct child of .slider-container)
                zoomLens.style.display = 'block';
                zoomLens.style.left = (left) + 'px';
                zoomLens.style.top = (top) + 'px';

                // background image & size
                zoomLens.style.backgroundImage = `url("${img.src}")`;
                const bgW = (img.naturalWidth || rect.width) * zoomFactor;
                const bgH = (img.naturalHeight || rect.height) * zoomFactor;
                zoomLens.style.backgroundSize = `${bgW}px ${bgH}px`;

                // background position as percent
                const posXPercent = (x / rect.width) * 100;
                const posYPercent = (y / rect.height) * 100;
                zoomLens.style.backgroundPosition = `${posXPercent}% ${posYPercent}%`;
            }

            // mousemove: call moveLens
            img.addEventListener('mousemove', (e) => {
                moveLens(e.clientX, e.clientY);
            });

            img.addEventListener('mouseleave', () => {
                zoomLens.style.display = 'none';
            });

            // touch support
            img.addEventListener('touchmove', (e) => {
                if (!e.touches || !e.touches[0]) return;
                const t = e.touches[0];
                moveLens(t.clientX, t.clientY);
                e.preventDefault();
            }, { passive: false });

            img.addEventListener('touchend', () => {
                zoomLens.style.display = 'none';
            });
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % total;
                showImage(currentIndex);
            } else if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + total) % total;
                showImage(currentIndex);
            }
        });
    });
    </script>

</body>

@endsection
