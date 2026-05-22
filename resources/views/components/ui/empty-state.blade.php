@props([
    'variant' => 'generic',
    'title' => null,
    'description' => null,
    'ctaUrl' => null,
    'ctaLabel' => null,
    'icon' => null,
])

@php
    $presets = [
        'no-auctions' => [
            'icon' => 'gavel',
            'title' => 'No auctions found',
            'description' => 'There are no active auctions matching your criteria right now. Check back soon or browse all listings.',
            'cta_label' => 'Browse auctions',
            'cta_url' => url('/marketplaces'),
        ],
        'no-watchlist' => [
            'icon' => 'favorite_border',
            'title' => 'Your watchlist is empty',
            'description' => 'Save auctions you are interested in by tapping the heart icon on any listing.',
            'cta_label' => 'Explore auctions',
            'cta_url' => url('/marketplaces'),
        ],
        'no-notifications' => [
            'icon' => 'notifications_off',
            'title' => 'No notifications yet',
            'description' => 'When you receive bids, listing updates, or payment alerts, they will appear here.',
            'cta_label' => null,
            'cta_url' => null,
        ],
        'no-search-results' => [
            'icon' => 'search_off',
            'title' => 'No results found',
            'description' => 'Try adjusting your search or filters to find what you are looking for.',
            'cta_label' => 'Clear filters',
            'cta_url' => null,
        ],
        'generic' => [
            'icon' => 'inbox',
            'title' => 'Nothing here yet',
            'description' => 'Check back later for new content.',
            'cta_label' => null,
            'cta_url' => null,
        ],
    ];

    $preset = $presets[$variant] ?? $presets['generic'];
    $iconName = $icon ?? $preset['icon'];
    $titleText = $title ?? $preset['title'];
    $descText = $description ?? $preset['description'];
    $ctaLabelText = $ctaLabel ?? $preset['cta_label'];
    $ctaUrlText = $ctaUrl ?? $preset['cta_url'];
@endphp

<div {{ $attributes->merge(['class' => 'cm-empty-state']) }}>
    <div class="cm-empty-state__icon" aria-hidden="true">
        <span class="material-icons-round">{{ $iconName }}</span>
    </div>
    <h3 class="cm-empty-state__title">{{ $titleText }}</h3>
    <p class="cm-empty-state__desc">{{ $descText }}</p>
    @if($ctaLabelText && $ctaUrlText)
        <a href="{{ $ctaUrlText }}" class="cm-empty-state__cta">{{ $ctaLabelText }}</a>
    @elseif($ctaLabelText)
        <button type="button" class="cm-empty-state__cta" data-cm-empty-cta>{{ $ctaLabelText }}</button>
    @endif
    {{ $slot }}
</div>
