{{-- CayMark UI kit — Section 6: mobile FAB (auction filters wired on auction page) --}}
@php
    $hideFab = request()->is('admin', 'admin/*')
        || request()->routeIs(
            'seller.dashboard',
            'seller.listings.create',
            'seller.listings.edit',
            'seller.listings.store',
            'seller.listings.update'
        );
@endphp
@if (! $hideFab)
    <x-ui.fab-post-listing />
@endif
