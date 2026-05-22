@props([
    'listing' => null,
    'listingId' => null,
    'inWatchlist' => false,
    'route' => null,
    'loggedIn' => null,
    'loginRoute' => null,
    'variant' => 'icon',
    'likesCount' => null,
])

@php
    $id = $listingId ?? ($listing->id ?? null);
    $watchlistRoute = $route ?? ($id ? route('listing.watchlist', $id) : '#');
    $isLoggedIn = $loggedIn ?? Auth::check();
    $loginUrl = $loginRoute ?? route('login');
    $saved = (bool) $inWatchlist;
    $count = $likesCount ?? ($listing->likes_count ?? $listing->watchlisted_by_count ?? null);
@endphp

@if($variant === 'button')
    <button
        type="button"
        {{ $attributes->merge(['class' => 'cm-watchlist-heart cm-watchlist-heart--button action-btn' . ($saved ? ' active' : '')]) }}
        data-cm-watchlist-heart
        data-url="{{ $watchlistRoute }}"
        data-in-watchlist="{{ $saved ? '1' : '0' }}"
        data-auth="{{ $isLoggedIn ? '1' : '0' }}"
        data-login-url="{{ $loginUrl }}"
        data-variant="button"
        aria-pressed="{{ $saved ? 'true' : 'false' }}"
        aria-label="{{ $saved ? 'Remove from watchlist' : 'Add to watchlist' }}"
    >
        <span class="material-icons cm-watchlist-heart__icon" data-cm-watchlist-icon>{{ $saved ? 'favorite' : 'favorite_border' }}</span>
        <span data-cm-watchlist-label>{{ $saved ? 'Added to Watchlist' : 'Add to Watchlist' }}</span>
    </button>
@else
    <button
        type="button"
        {{ $attributes->merge(['class' => 'cm-watchlist-heart cm-watchlist-heart--icon' . ($saved ? ' is-active' : '')]) }}
        data-cm-watchlist-heart
        data-url="{{ $watchlistRoute }}"
        data-in-watchlist="{{ $saved ? '1' : '0' }}"
        data-auth="{{ $isLoggedIn ? '1' : '0' }}"
        data-login-url="{{ $loginUrl }}"
        data-variant="icon"
        aria-pressed="{{ $saved ? 'true' : 'false' }}"
        aria-label="{{ $saved ? 'Remove from watchlist' : 'Save to watchlist' }}"
    >
        <span class="material-icons cm-watchlist-heart__icon" data-cm-watchlist-icon>{{ $saved ? 'favorite' : 'favorite_border' }}</span>
        @if($count !== null)
            <span class="cm-watchlist-heart__count" data-cm-watchlist-count>{{ $count }}</span>
        @endif
    </button>
@endif
