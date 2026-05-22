@props([
    'user' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $user = $user ?? auth()->user();
    $sizes = [
        'sm' => ['box' => '2rem', 'text' => '0.6875rem'],
        'md' => ['box' => '2.5rem', 'text' => '0.8125rem'],
        'lg' => ['box' => '3rem', 'text' => '1rem'],
    ];
    $dim = $sizes[$size] ?? $sizes['md'];

    $name = trim((string) ($user?->name ?? ''));
    $parts = $name !== '' ? preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) : [];
    if (count($parts) >= 2) {
        $initials = mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[count($parts) - 1], 0, 1));
    } elseif (count($parts) === 1) {
        $initials = mb_strtoupper(mb_substr($parts[0], 0, min(2, mb_strlen($parts[0]))));
    } else {
        $initials = '?';
    }

    $palette = [
        ['bg' => '#0a1628', 'fg' => '#f5d061'],
        ['bg' => '#1a365d', 'fg' => '#ffffff'],
        ['bg' => '#b8860b', 'fg' => '#0a1628'],
        ['bg' => '#063466', 'fg' => '#fde68a'],
        ['bg' => '#334155', 'fg' => '#fcd34d'],
    ];
    $colorIndex = $user ? abs(crc32((string) $user->id)) % count($palette) : 0;
    $colors = $palette[$colorIndex];

    $imageUrl = null;
    if ($user) {
        foreach (['profile_image', 'profile_image_path', 'avatar', 'avatar_path', 'photo_path'] as $col) {
            if (! empty($user->{$col})) {
                $path = $user->{$col};
                $imageUrl = str_starts_with($path, 'http') ? $path : asset('storage/' . ltrim($path, '/'));
                break;
            }
        }
    }
@endphp

<span
    {{ $attributes->merge(['class' => 'cm-avatar cm-avatar--' . $size . ' ' . $class]) }}
    style="--cm-avatar-size: {{ $dim['box'] }}; --cm-avatar-font: {{ $dim['text'] }}; --cm-avatar-bg: {{ $colors['bg'] }}; --cm-avatar-fg: {{ $colors['fg'] }};"
    title="{{ $name ?: 'Account' }}"
    aria-hidden="{{ $imageUrl ? 'true' : 'false' }}"
>
    @if($imageUrl)
        <img src="{{ $imageUrl }}" alt="" class="cm-avatar__img" loading="lazy" />
    @else
        <span class="cm-avatar__initials" aria-label="{{ $name ?: 'User' }}">{{ $initials }}</span>
    @endif
</span>
