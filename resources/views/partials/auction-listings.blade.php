@forelse($auctions as $listing)
    @php
        $likesCount = $listing->likes_count ?? $listing->watchlisted_by_count ?? 0;
        $liked = isset($likedListingIds) && $likedListingIds->contains($listing->id);
        $img = $listing->images->first();
        if (!$img) {
            $imgUrl = asset('images/placeholder-car.png');
        } else {
            $p = $img->image_path ?? '';
            $imgUrl = str_starts_with($p,'http') ? $p
                : (str_contains($p,'/') ? asset(ltrim($p,'/'))
                : (str_starts_with($p,'listings/') ? asset('storage/'.$p)
                : asset('uploads/listings/'.$p)));
        }
        $endDate = $listing->getAuctionEndDate();
    @endphp

    <div class="vehicle-card bg-white border border-outline-variant rounded-xl overflow-hidden flex flex-col md:flex-row">

        {{-- Image --}}
        <div class="md:w-2/5 xl:w-1/3 flex-shrink-0 img-wrap bg-surface-container-high min-h-[220px] md:min-h-0">
            <img src="{{ $imgUrl }}"
                alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                class="w-full h-full object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                style="min-height:220px; max-height:280px"
                loading="lazy"
                onerror="this.onerror=null;this.src='{{ asset('images/placeholder-car.png') }}';"
                onclick="openImageModal('{{ $imgUrl }}')"/>

            <div class="img-overlay">
                <button onclick="openImageModal('{{ $imgUrl }}')"
                    class="bg-white text-primary font-label-md text-label-md px-4 py-2 rounded-lg hover:bg-surface-container transition-colors">
                    View Photo
                </button>
            </div>

            <div class="absolute top-3 left-3 flex flex-col gap-1 z-10">
                @if($listing->featured)
                    <span class="bg-secondary-fixed-dim text-primary text-[10px] font-bold px-2 py-0.5 rounded-sm uppercase tracking-widest">Featured</span>
                @endif
                <x-ui.ending-soon-badge :end="$endDate" />
            </div>
            <div class="absolute bottom-3 left-3 z-10">
                <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />
            </div>
            <div class="absolute top-3 right-3 z-10">
                <x-ui.watchlist-heart :listing="$listing" :in-watchlist="$liked" :likes-count="$likesCount"/>
            </div>
        </div>

        {{-- Info --}}
        <div class="flex-1 p-5 flex flex-col min-w-0">
            <div class="flex items-start justify-between gap-3 mb-3">
                <h3 class="font-headline-sm text-headline-sm text-primary line-clamp-2 min-w-0">
                    {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                </h3>
                <button type="button" title="Share" class="flex-shrink-0 text-on-surface-variant hover:text-primary transition-colors mt-0.5">
                    <span class="material-symbols-outlined text-[20px]">share</span>
                </button>
            </div>

            {{-- Key specs grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-outline text-[16px] flex-shrink-0 mt-0.5">speed</span>
                    <div class="min-w-0">
                        <p class="text-label-sm font-label-sm text-on-surface-variant">Odometer</p>
                        <p class="text-body-sm font-body-sm text-on-surface truncate">
                            @if($listing->odometer)
                                {{ number_format($listing->odometer) }} mi
                                @if($listing->odometer_estimated)<span class="text-amber-600 text-[11px]"> (Est.)</span>@endif
                            @else N/A @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-outline text-[16px] flex-shrink-0 mt-0.5">receipt</span>
                    <div class="min-w-0">
                        <p class="text-label-sm font-label-sm text-on-surface-variant">Title</p>
                        <p class="text-body-sm font-body-sm text-on-surface truncate">{{ $listing->title_status_display }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-outline text-[16px] flex-shrink-0 mt-0.5">location_on</span>
                    <div class="min-w-0">
                        <p class="text-label-sm font-label-sm text-on-surface-variant">Location</p>
                        <p class="text-body-sm font-body-sm text-on-surface truncate">{{ $listing->island ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <span class="material-symbols-outlined text-outline text-[16px] flex-shrink-0 mt-0.5">event</span>
                    <div class="min-w-0">
                        <p class="text-label-sm font-label-sm text-on-surface-variant">Sale Date</p>
                        <p class="text-body-sm font-body-sm text-on-surface truncate">
                            {{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Extra details --}}
            @if($listing->primary_damage || $listing->transmission || $listing->fuel_type)
            <div class="flex flex-wrap gap-2 mb-4">
                @if($listing->primary_damage)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container text-on-surface-variant text-label-sm font-label-sm rounded-lg">
                        <span class="material-symbols-outlined text-[12px]">warning</span>{{ $listing->primary_damage }}
                    </span>
                @endif
                @if($listing->transmission)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container text-on-surface-variant text-label-sm font-label-sm rounded-lg">
                        <span class="material-symbols-outlined text-[12px]">settings</span>{{ ucfirst(strtolower($listing->transmission)) }}
                    </span>
                @endif
                @if($listing->fuel_type)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-surface-container text-on-surface-variant text-label-sm font-label-sm rounded-lg">
                        <span class="material-symbols-outlined text-[12px]">local_gas_station</span>{{ is_string($listing->fuel_type) ? ucfirst(strtolower($listing->fuel_type)) : $listing->fuel_type }}
                    </span>
                @endif
            </div>
            @endif

            {{-- Bid + Actions --}}
            <div class="mt-auto pt-4 border-t border-outline-variant flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Current Bid</p>
                    <p class="font-headline-md text-headline-md text-primary">${{ number_format($listing->current_bid ?? 0) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                        class="flex-1 sm:flex-none text-center bg-secondary-container text-on-secondary-container font-label-md text-label-md px-6 py-2.5 rounded-lg hover:bg-secondary transition-colors">
                        Bid Now
                    </a>
                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                        class="border border-outline-variant text-on-surface-variant px-3 py-2.5 rounded-lg hover:bg-surface-container hover:text-primary transition-colors"
                        title="View details">
                        <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

@empty
    <div class="text-center py-20">
        <span class="material-symbols-outlined text-outline text-[64px] mb-3 block">search_off</span>
        <h3 class="font-headline-sm text-headline-sm text-primary mb-2">No listings found</h3>
        <p class="text-body-md font-body-md text-on-surface-variant">Try adjusting your filters to find more results.</p>
    </div>
@endforelse
