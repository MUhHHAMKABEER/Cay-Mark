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

    <article class="cm-card bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden
                    flex hover:shadow-md hover:border-gray-200/80 transition-shadow duration-200">

        {{-- LEFT COLUMN · Image (~30%) --}}
        <div class="cm-img-wrap relative flex-none w-[30%] min-w-[200px] max-w-[320px] self-stretch bg-gray-100">
            <img
                src="{{ $imgUrl }}"
                alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
                onerror="this.onerror=null;this.src='{{ asset('images/placeholder-car.png') }}';">

            {{-- Featured badge --}}
            @if($listing->featured)
            <div class="absolute top-0 left-0 z-10">
                <span class="bg-secondary-fixed-dim text-primary text-[9px] font-bold px-2.5 py-1 uppercase tracking-widest">Featured</span>
            </div>
            @endif

            {{-- Countdown timer (homepage style) --}}
            @php $endIso = $endDate ? $endDate->toIso8601String() : null; @endphp
            @if($endIso && !$endDate->isPast())
            <div class="absolute bottom-3 left-3 z-10 bg-white/90 backdrop-blur text-primary px-3 py-1.5 font-mono text-[12px] font-bold shadow-sm flex items-center gap-2" style="border-radius:0">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
                <span class="js-countdown" data-end="{{ $endIso }}">--:--:--</span>
            </div>
            @else
            <div class="absolute bottom-3 left-3 z-10 bg-black/50 text-white/80 px-2.5 py-1 text-[11px] font-bold tracking-wider" style="border-radius:0">Ended</div>
            @endif
        </div>{{-- /left column --}}

        {{-- CENTER COLUMN · Details --}}
        <div class="flex-1 min-w-0 px-5 py-4 flex flex-col justify-center">

            {{-- Title --}}
            <h3 class="font-bold text-slate-900 text-lg leading-snug">
                <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                   class="hover:text-blue-700 transition-colors duration-150">
                    {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                </a>
            </h3>

            {{-- Location --}}
            @if($listing->island)
            <div class="flex items-center gap-1 mt-[5px] mb-4">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                     stroke="#94a3b8" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <span class="text-xs text-slate-400 font-medium">{{ $listing->island }}</span>
            </div>
            @else
            <div class="mb-4"></div>
            @endif

            {{-- 1 × 4 Specs row --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-3.5 mb-3.5 border-b border-gray-100/80">
                <div>
                    <span class="spec-label mb-1">Odometer</span>
                    <p class="text-sm font-bold text-slate-800">
                        @if($listing->odometer)
                            {{ number_format($listing->odometer) }} mi
                            @if($listing->odometer_estimated)<span class="text-amber-500 text-[10px]"> (Est.)</span>@endif
                        @else N/A @endif
                    </p>
                </div>
                <div>
                    <span class="spec-label mb-1">Condition</span>
                    <p class="text-sm font-bold text-slate-800">{{ ucfirst($listing->condition ?? 'N/A') }}</p>
                </div>
                <div>
                    <span class="spec-label mb-1">Title</span>
                    <p class="text-sm font-bold text-slate-800 truncate">{{ $listing->title_status_display ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="spec-label mb-1">Sale Date</span>
                    <p class="text-sm font-bold text-slate-800">
                        {{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Spec tags --}}
            @if($listing->fuel_type || $listing->transmission || $listing->primary_damage)
            <div class="flex flex-wrap gap-1.5">
                @if($listing->fuel_type)
                <span class="inline-flex items-center gap-[4px] bg-gray-100 text-slate-600
                             text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                    @if(strtolower($listing->fuel_type) === 'electric')
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    @else
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 22V9a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v13"/>
                        <path d="M3 14h14"/><path d="M9 22v-4a3 3 0 0 1 6 0v4"/>
                    </svg>
                    @endif
                    {{ strtoupper($listing->fuel_type) }}
                </span>
                @endif
                @if($listing->transmission)
                <span class="inline-flex items-center gap-[4px] bg-gray-100 text-slate-600
                             text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                    {{ strtoupper($listing->transmission) }}
                </span>
                @endif
                @if($listing->primary_damage)
                <span class="inline-flex items-center gap-[4px] bg-amber-50 text-amber-700
                             text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                    {{ strtoupper($listing->primary_damage) }}
                </span>
                @endif
            </div>
            @endif

        </div>{{-- /center column --}}

        {{-- RIGHT COLUMN · Pricing & Action --}}
        <div class="flex-none w-48 border-l border-gray-200/60 flex flex-col
                    items-center justify-center px-5 py-5 gap-3.5">

            <div class="text-center">
                <p class="text-[26px] font-extrabold text-slate-900 leading-none tracking-tight">
                    ${{ number_format($listing->current_bid ?? 0) }}
                </p>
                <span class="spec-label mt-1.5">Current Bid</span>
            </div>

            <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
               class="btn-bid w-full inline-flex items-center justify-center
                      py-2.5 rounded-lg text-sm
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-yellow-500">
                Bid Now
            </a>

            <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
               class="inline-flex items-center gap-1 text-[11px] font-semibold
                      text-slate-400 hover:text-slate-600 transition-colors duration-150">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Details
            </a>

        </div>{{-- /right column --}}

    </article>

@empty
    <div class="py-20 text-center bg-white rounded-xl border border-gray-100 shadow-sm">
        <span class="material-symbols-outlined text-gray-300 text-[60px] mb-3 block">search_off</span>
        <h3 class="text-xl font-bold text-primary uppercase tracking-tight mb-2">No listings found</h3>
        <p class="text-gray-500 text-sm">Try adjusting your filters to find more results.</p>
    </div>
@endforelse
