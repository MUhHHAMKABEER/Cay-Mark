@props([
    'variant' => 'auction-row',
    'count' => 3,
])

@php
    $count = max(1, min((int) $count, 12));
    $gridClass = $variant === 'auction-grid' ? ' cm-skeleton-grid--cards' : '';
    $rowClass = $variant === 'auction-row' ? ' cm-skeleton-auction-card--row' : '';
@endphp

@if($variant === 'table')
    <div {{ $attributes->merge(['class' => 'cm-skeleton-table', 'data-cm-skeleton' => '']) }}>
        @for($i = 0; $i < $count; $i++)
            <div class="cm-skeleton-table-row">
                <span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>
                <span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>
                <span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>
                <span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>
            </div>
        @endfor
    </div>
@elseif($variant === 'profile')
    <div {{ $attributes->merge(['class' => 'cm-skeleton-profile', 'data-cm-skeleton' => '']) }}>
        <div class="cm-skeleton cm-skeleton-profile__avatar"></div>
        <div class="cm-skeleton-profile__lines">
            <span class="cm-skeleton cm-skeleton-block" style="height:1.25rem;width:60%"></span>
            <span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:80%"></span>
            <span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:45%"></span>
        </div>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'cm-skeleton-grid' . $gridClass, 'data-cm-skeleton' => '']) }}>
        @for($i = 0; $i < $count; $i++)
            <div class="cm-skeleton-auction-card{{ $rowClass }}">
                <div class="cm-skeleton cm-skeleton-auction-card__media"></div>
                <div class="cm-skeleton-auction-card__body">
                    <span class="cm-skeleton cm-skeleton-block" style="height:1.25rem;width:75%"></span>
                    <span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:50%"></span>
                    <span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:35%"></span>
                    <span class="cm-skeleton cm-skeleton-block" style="height:2rem;width:40%;margin-top:0.5rem"></span>
                </div>
            </div>
        @endfor
    </div>
@endif
