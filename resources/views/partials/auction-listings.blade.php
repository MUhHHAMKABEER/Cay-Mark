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

    <div class="vehicle-card bg-white border border-gray-200 flex flex-col md:flex-row" style="border-radius:0">

        {{-- Image --}}
        <div class="img-wrap flex-shrink-0 relative bg-gray-100 md:w-[280px]" style="min-height:220px">
            <img src="{{ $imgUrl }}"
                alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                class="w-full h-full object-cover cursor-pointer absolute inset-0"
                loading="lazy"
                onerror="this.onerror=null;this.src='{{ asset('images/placeholder-car.png') }}';"
                onclick="openImageModal('{{ $imgUrl }}')"/>

            <div class="img-overlay">
                <button onclick="openImageModal('{{ $imgUrl }}')"
                    class="flex items-center gap-1.5 bg-white text-primary font-bold uppercase tracking-widest px-4 py-2 text-[11px] hover:bg-gray-100 transition-colors"
                    style="border-radius:0">
                    <span class="material-symbols-outlined text-[15px]">photo_camera</span>
                    View Photo
                </button>
            </div>

            {{-- Badges --}}
            <div class="absolute top-0 left-0 flex flex-col z-10">
                @if($listing->featured)
                    <span class="bg-secondary-fixed-dim text-primary text-[9px] font-bold px-2.5 py-1 uppercase tracking-widest" style="border-radius:0">Featured</span>
                @endif
                <x-ui.ending-soon-badge :end="$endDate" />
            </div>
            <div class="absolute bottom-0 left-0 right-0 z-10">
                <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />
            </div>
            <div class="absolute top-2 right-2 z-10">
                <x-ui.watchlist-heart :listing="$listing" :in-watchlist="$liked" :likes-count="$likesCount"/>
            </div>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0 flex flex-col">

            {{-- Navy title bar --}}
            <div class="bg-primary px-5 py-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 class="text-white font-bold uppercase tracking-tight leading-snug line-clamp-1" style="font-size:15px">
                        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                    </h3>
                    @if($listing->island)
                    <p class="text-white/50 text-[11px] mt-0.5 flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:11px">location_on</span>
                        {{ $listing->island }}
                    </p>
                    @endif
                </div>
                <button type="button" title="Share"
                    class="flex-shrink-0 flex items-center justify-center w-8 h-8 border border-white/20 text-white/60 hover:text-white hover:border-white/40 transition-colors"
                    style="border-radius:0">
                    <span class="material-symbols-outlined text-[17px]">share</span>
                </button>
            </div>

            {{-- Specs --}}
            <div class="px-5 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 border-b border-gray-100">
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Odometer</p>
                    <p class="text-sm font-semibold text-gray-800">
                        @if($listing->odometer)
                            {{ number_format($listing->odometer) }} mi
                            @if($listing->odometer_estimated)<span class="text-amber-500 text-[10px]"> (Est.)</span>@endif
                        @else N/A @endif
                    </p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Title</p>
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $listing->title_status_display ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Condition</p>
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ ucfirst($listing->condition ?? 'N/A') }}</p>
                </div>
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Sale Date</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Extra chips --}}
            @if($listing->primary_damage || $listing->transmission || $listing->fuel_type)
            <div class="px-5 py-3 flex flex-wrap gap-2 border-b border-gray-100">
                @if($listing->primary_damage)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 text-[11px] font-semibold uppercase tracking-wider" style="border-radius:0">
                        <span class="material-symbols-outlined text-[12px]">warning</span>{{ $listing->primary_damage }}
                    </span>
                @endif
                @if($listing->transmission)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 text-[11px] font-semibold uppercase tracking-wider" style="border-radius:0">
                        <span class="material-symbols-outlined text-[12px]">settings</span>{{ ucfirst(strtolower($listing->transmission)) }}
                    </span>
                @endif
                @if($listing->fuel_type)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-600 text-[11px] font-semibold uppercase tracking-wider" style="border-radius:0">
                        <span class="material-symbols-outlined text-[12px]">local_gas_station</span>{{ is_string($listing->fuel_type) ? ucfirst(strtolower($listing->fuel_type)) : $listing->fuel_type }}
                    </span>
                @endif
            </div>
            @endif

            {{-- Bid + Actions --}}
            <div class="mt-auto px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Current Bid</p>
                    <p class="text-primary font-bold" style="font-size:22px;line-height:1.2">${{ number_format($listing->current_bid ?? 0) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                        class="flex items-center gap-1.5 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest px-5 py-3 text-[11px] hover:bg-[#b8943b] transition-colors whitespace-nowrap"
                        style="border-radius:0">
                        <span class="material-symbols-outlined text-[15px]">gavel</span>
                        Bid Now
                    </a>
                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                        class="flex items-center justify-center w-10 h-10 border border-gray-200 text-gray-500 hover:border-primary hover:text-primary transition-colors"
                        style="border-radius:0"
                        title="View details">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

@empty
    <div class="py-20 text-center border border-gray-200" style="border-radius:0">
        <span class="material-symbols-outlined text-gray-300 text-[60px] mb-3 block">search_off</span>
        <h3 class="text-xl font-bold text-primary uppercase tracking-tight mb-2">No listings found</h3>
        <p class="text-gray-500 text-sm">Try adjusting your filters to find more results.</p>
    </div>
@endforelse
