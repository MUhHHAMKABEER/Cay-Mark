@props([
    'items' => null,
])

@php
    $crumbs = $items ?? ($cmBreadcrumbs ?? []);
@endphp

@if(!empty($crumbs))
<nav class="cm-breadcrumbs" aria-label="Breadcrumb">
    <ol class="cm-breadcrumbs__list">
        @foreach($crumbs as $index => $crumb)
            @php
                $isLast = $loop->last;
                $label = $crumb['label'] ?? '';
                $url = $crumb['url'] ?? null;
            @endphp
            <li class="cm-breadcrumbs__item" @if($isLast) aria-current="page" @endif>
                @if(!$isLast && !empty($url))
                    <a href="{{ $url }}" class="cm-breadcrumbs__link">{{ $label }}</a>
                @else
                    <span class="cm-breadcrumbs__current">{{ $label }}</span>
                @endif
                @unless($isLast)
                    <span class="cm-breadcrumbs__sep" aria-hidden="true">/</span>
                @endunless
            </li>
        @endforeach
    </ol>
</nav>
@endif
