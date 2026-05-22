@props([
    'href' => null,
    'label' => 'Post a Listing',
])

@php
    if ($href === null) {
        $user = auth()->user();
        $href = route('register');

        if ($user) {
            if ($user->role === 'seller') {
                $href = route('seller.listings.create');
            } elseif ($user->role === 'buyer') {
                $href = route('register');
            } else {
                $href = route('finish.registration');
            }
        } else {
            $href = route('login', ['redirect' => route('seller.listings.create')]);
        }
    }
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'cm-fab-post-listing']) }}
    aria-label="{{ $label }}"
>
    <span class="cm-fab-post-listing__icon material-icons" aria-hidden="true">add</span>
    <span class="cm-fab-post-listing__label">{{ $label }}</span>
</a>
