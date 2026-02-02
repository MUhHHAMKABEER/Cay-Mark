@forelse($auctions as $listing)
    @php
        $likesCount = $listing->likes_count ?? $listing->watchlisted_by_count ?? 0;
        $liked = isset($likedListingIds) && $likedListingIds->contains($listing->id);
    @endphp
    <div class="vehicle-card bg-white rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row animate-slide-down">
        <!-- Image -->
        <div class="md:w-2/5 relative image-container">
            @php
                $img = $listing->images->first();
                $imgUrl = $img
                    ? (str_contains($img->image_path, '/')
                        ? asset($img->image_path)
                        : asset('uploads/listings/' . $img->image_path))
                    : asset('images/placeholder-car.png');
                
                // Calculate end date from database
                if ($listing->auction_end_time) {
                    $endDate = \Carbon\Carbon::parse($listing->auction_end_time);
                } elseif ($listing->auction_start_time) {
                    $endDate = \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration ?? 7);
                } else {
                    $endDate = \Carbon\Carbon::parse($listing->created_at)->addDays($listing->auction_duration ?? 7);
                }
                $isExpired = \Carbon\Carbon::now()->greaterThanOrEqualTo($endDate);
            @endphp
            <img alt="{{ $listing->title ?? $listing->make . ' ' . $listing->model }}"
                style="height:250px" src="{{ $imgUrl }}"
                class="w-full h-full object-cover rounded-lg cursor-pointer transition-transform duration-300 hover:scale-105"
                onclick="openImageModal('{{ $imgUrl }}')" />
            <div class="image-overlay">
                <button
                    class="view-details-btn bg-white text-primary-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-primary-50 transition-colors"
                    onclick="openImageModal('{{ $imgUrl }}')">
                    View Image
                </button>
            </div>
            <!-- Countdown Timer Badge -->
            @if(!$isExpired)
            <div class="absolute bottom-3 left-3 bg-gradient-to-br from-blue-600/95 to-indigo-700/95 backdrop-blur-md text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-2xl border border-white/20" 
                 id="countdown-detail-{{ $listing->id }}" 
                 data-end-time="{{ $endDate->toIso8601String() }}">
                <div class="flex items-center space-x-1.5">
                    <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="days-detail-{{ $listing->id }}">00</span>
                    <span class="text-white/70">:</span>
                    <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="hours-detail-{{ $listing->id }}">00</span>
                    <span class="text-white/70">:</span>
                    <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="minutes-detail-{{ $listing->id }}">00</span>
                    <span class="text-white/70">:</span>
                    <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="seconds-detail-{{ $listing->id }}">00</span>
                </div>
            </div>
            @else
            <div class="absolute bottom-3 left-3 bg-gray-500/90 backdrop-blur-md text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-xl border border-gray-400/30">
                Auction Ended
            </div>
            @endif
        </div>

        <!-- Info -->
        <div class="p-5 flex-1 flex flex-col">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-lg font-semibold text-secondary-800">
                    {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                </h3>
                <div class="flex space-x-2 items-center">
                    <form action="{{ route('listing.watchlist', $listing->id) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="js-like-toggle inline-flex items-center text-sm {{ $liked ? 'text-red-500' : 'text-gray-400' }} hover:text-red-500 transition-colors"
                            data-url="{{ route('listing.watchlist', $listing->id) }}"
                            data-liked="{{ $liked ? '1' : '0' }}"
                            data-auth="{{ Auth::check() ? '1' : '0' }}"
                            data-unliked-class="text-gray-400"
                            aria-label="Like listing">
                            <span class="material-icons">{{ $liked ? 'favorite' : 'favorite_border' }}</span>
                            <span class="ml-1 text-xs js-like-count">{{ $likesCount }}</span>
                        </button>
                    </form>
                    <span
                        class="material-icons text-gray-400 hover:text-primary-500 cursor-pointer transition-colors">share</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                <div class="flex items-center">
                    <span class="material-icons text-gray-400 text-sm mr-2">speed</span>
                    <div>
                        <p class="text-xs text-gray-500">Odometer</p>
                        <p class="text-sm font-medium">
                            {{ $listing->odometer ? number_format($listing->odometer) . ' miles' : 'N/A' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="material-icons text-gray-400 text-sm mr-2">receipt</span>
                    <div>
                        <p class="text-xs text-gray-500">Title Code</p>
                        <p class="text-sm font-medium">{{ $listing->title_status ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="material-icons text-gray-400 text-sm mr-2">location_on</span>
                    <div>
                        <p class="text-xs text-gray-500">Location</p>
                        <p class="text-sm font-medium">{{ $listing->island ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="material-icons text-gray-400 text-sm mr-2">event</span>
                    <div>
                        <p class="text-xs text-gray-500">Sale Date</p>
                        <p class="text-sm font-medium">
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
                    <div class="flex"><span class="font-medium mr-1">Transmission:</span>
                        <span>{{ $listing->transmission }}</span>
                    </div>
                @endif
                @if ($listing->fuel_type)
                    <div class="flex"><span class="font-medium mr-1">Fuel Type:</span>
                        <span>{{ $listing->fuel_type }}</span>
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
