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
            <div class="cm-countdown__inner" aria-live="polite">
                <svg class="cm-countdown__icon" width="10" height="10" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span class="cm-countdown__ends-label">Ends In</span>
                <div class="cm-countdown__blocks">
                    <div class="cm-countdown__block">
                        <span class="cm-countdown__num" data-cm-unit="days">00</span>
                        <span class="cm-countdown__lbl">D</span>
                    </div>
                    <span class="cm-countdown__sep" aria-hidden="true">:</span>
                    <div class="cm-countdown__block">
                        <span class="cm-countdown__num" data-cm-unit="hours">00</span>
                        <span class="cm-countdown__lbl">H</span>
                    </div>
                    <span class="cm-countdown__sep" aria-hidden="true">:</span>
                    <div class="cm-countdown__block">
                        <span class="cm-countdown__num" data-cm-unit="minutes">00</span>
                        <span class="cm-countdown__lbl">M</span>
                    </div>
                    <span class="cm-countdown__sep" aria-hidden="true">:</span>
                    <div class="cm-countdown__block">
                        <span class="cm-countdown__num" data-cm-unit="seconds">00</span>
                        <span class="cm-countdown__lbl">S</span>
                    </div>
                </div>
            </div>
        @else
            <div class="cm-countdown__label">Auction Countdown</div>
            <div class="cm-countdown__display" data-cm-countdown-display>—</div>
        @endif
    </div>
@endif
