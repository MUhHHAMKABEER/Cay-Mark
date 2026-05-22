@props([
    'end' => null,
    'listingId' => null,
    'variant' => 'detail',
])

@php
    $endCarbon = null;
    if ($end instanceof \Carbon\Carbon) {
        $endCarbon = $end;
    } elseif ($end) {
        $endCarbon = \Carbon\Carbon::parse($end);
    }

    $isExpired = !$endCarbon || $endCarbon->isPast();
    $endIso = $endCarbon ? $endCarbon->toIso8601String() : '';
@endphp

@if($isExpired)
    @if($variant === 'grid')
        <span {{ $attributes->merge(['class' => 'cm-countdown-ended cm-countdown-ended--grid']) }}>Ended</span>
    @else
        <div {{ $attributes->merge(['class' => 'cm-countdown cm-countdown--detail cm-countdown--ended']) }} data-cm-countdown-ended>
            <div class="cm-countdown__label">Auction Countdown</div>
            <div class="cm-countdown__display" data-cm-countdown-display>Auction Ended</div>
        </div>
    @endif
@else
    <div
        {{ $attributes->merge(['class' => 'cm-countdown cm-countdown--' . $variant]) }}
        data-cm-countdown-end="{{ $endIso }}"
        @if($listingId) data-listing-id="{{ $listingId }}" @endif
    >
        @if($variant === 'grid')
            <div class="cm-countdown__segments" aria-live="polite">
                <span class="cm-countdown__segment" data-cm-unit="days">00</span>
                <span class="cm-countdown__sep" aria-hidden="true">:</span>
                <span class="cm-countdown__segment" data-cm-unit="hours">00</span>
                <span class="cm-countdown__sep" aria-hidden="true">:</span>
                <span class="cm-countdown__segment" data-cm-unit="minutes">00</span>
                <span class="cm-countdown__sep" aria-hidden="true">:</span>
                <span class="cm-countdown__segment" data-cm-unit="seconds">00</span>
            </div>
        @else
            <div class="cm-countdown__label">Auction Countdown</div>
            <div class="cm-countdown__display" data-cm-countdown-display>—</div>
        @endif
    </div>
@endif
