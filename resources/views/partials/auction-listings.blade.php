@forelse($auctions as $listing)
    @php
        $likesCount = $listing->likes_count ?? $listing->watchlisted_by_count ?? 0;
        $liked = isset($likedListingIds) && $likedListingIds->contains($listing->id);
    @endphp
    <div class="vehicle-card bg-white rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row animate-slide-down gap-0 min-w-0">
        <!-- Image -->
        <div class="md:w-2/5 relative image-container min-h-[250px] bg-gray-100">
            @php
                $img = $listing->images->first();
                if (!$img) {
                    $imgUrl = asset('images/placeholder-car.png');
                } else {
                    $p = $img->image_path ?? '';
                    if (str_starts_with($p, 'http')) {
                        $imgUrl = $p;
                    } elseif (str_contains($p, '/')) {
                        $imgUrl = asset(ltrim($p, '/'));
                    } elseif (str_starts_with($p, 'listings/')) {
                        $imgUrl = asset('storage/' . $p);
                    } else {
                        $imgUrl = asset('uploads/listings/' . $p);
                    }
                }
                $endDate = $listing->getAuctionEndDate();
            @endphp
            <img alt="{{ $listing->title ?? $listing->make . ' ' . $listing->model }}"
                style="height:250px" src="{{ $imgUrl }}"
                class="w-full h-full object-cover rounded-lg cursor-pointer transition-transform duration-300 hover:scale-105"
                loading="lazy"
                onerror="this.onerror=null; this.src='{{ asset('images/placeholder-car.png') }}';"
                onclick="openImageModal('{{ $imgUrl }}')" />
            <div class="image-overlay">
                <button
                    class="view-details-btn bg-white text-primary-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-primary-50 transition-colors"
                    onclick="openImageModal('{{ $imgUrl }}')">
                    View Image
                </button>
            </div>
            <div class="absolute top-3 right-3 z-10">
                <x-ui.watchlist-heart
                    :listing="$listing"
                    :in-watchlist="$liked"
                    :likes-count="$likesCount"
                />
            </div>
            <div class="absolute top-3 left-3 flex flex-col gap-1 z-10">
                <x-ui.ending-soon-badge :end="$endDate" />
            </div>
            <div class="absolute bottom-3 left-3 z-10">
                <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />
            </div>
        </div>

        <!-- Info -->
        <div class="p-5 flex-1 flex flex-col min-w-0">
            <div class="flex justify-between items-start gap-2 mb-2">
                <h3 class="text-lg font-semibold text-secondary-800 break-words line-clamp-2 min-w-0">
                    {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                </h3>
                <div class="flex space-x-2 items-center">
                    <span
                        class="material-icons text-gray-400 hover:text-primary-500 cursor-pointer transition-colors">share</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2 min-w-0">
                <div class="flex items-center">
                    <span class="material-icons text-gray-400 text-sm mr-2">speed</span>
                    <div>
                        <p class="text-xs text-gray-500">Odometer</p>
                        <p class="text-sm font-medium">
                            @if($listing->odometer)
                                {{ number_format($listing->odometer) }} miles
                                @if($listing->odometer_estimated)
                                    <span class="text-amber-600 font-medium">(Est.)</span>
                                    <span class="material-icons text-gray-400 cursor-help align-middle ml-0.5" title="This is an estimated odometer reading and may be subject to change." style="font-size: 14px;">info</span>
                                @endif
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center min-w-0">
                    <span class="material-icons text-gray-400 text-sm mr-2 shrink-0">receipt</span>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">Title</p>
                        <p class="text-sm font-medium truncate">{{ $listing->title_status_display }}</p>
                    </div>
                </div>
                <div class="flex items-center min-w-0">
                    <span class="material-icons text-gray-400 text-sm mr-2 shrink-0">location_on</span>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">Location</p>
                        <p class="text-sm font-medium truncate">{{ $listing->island ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center min-w-0">
                    <span class="material-icons text-gray-400 text-sm mr-2 shrink-0">event</span>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500">Sale Date</p>
                        <p class="text-sm font-medium truncate" title="{{ $listing->sale_date ? '' : 'Sale date not set for this listing.' }}">
                            {{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional details -->
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                @if ($listing->primary_damage)
                    <div class="flex"><span class="font-medium mr-1">Primary Damage:</span>
                        <span>{{ $listing->primary_damage }}</span>
                    </div>
                @endif
                @if ($listing->secondary_damage)
                    <div class="flex"><span class="font-medium mr-1">Secondary Damage:</span>
                        <span>{{ $listing->secondary_damage }}</span>
                    </div>
                @endif
                @if ($listing->transmission)
                    <div class="flex min-w-0"><span class="font-medium mr-1 shrink-0">Transmission:</span>
                        <span class="truncate">{{ ucfirst(strtolower($listing->transmission ?? '')) }}</span>
                    </div>
                @endif
                @if ($listing->fuel_type)
                    <div class="flex min-w-0"><span class="font-medium mr-1 shrink-0">Fuel Type:</span>
                        <span class="truncate">{{ is_string($listing->fuel_type) ? ucfirst(strtolower($listing->fuel_type)) : $listing->fuel_type }}</span>
                    </div>
                @endif
            </div>

            <div
                class="mt-4 pt-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Current Bid</p>
                    <p class="text-2xl font-bold text-green-600 price-tag">
                        ${{ number_format($listing->current_bid ?? 0) }}
                    </p>
                </div>
                <div class="mt-3 sm:mt-0 flex space-x-2">
                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                        class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-md flex-1 sm:flex-none text-center">
                        Bid Now
                    </a>
                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                        class="border border-gray-300 text-secondary-700 font-medium py-2.5 px-3 rounded-lg hover:bg-gray-50 transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-sm">
                        <span class="material-icons text-lg">visibility</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-10">
        <span class="material-icons text-gray-400 text-6xl mb-4">search_off</span>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No listings found</h3>
        <p class="text-gray-500">Try adjusting your filters to find more results.</p>
    </div>
@endforelse
