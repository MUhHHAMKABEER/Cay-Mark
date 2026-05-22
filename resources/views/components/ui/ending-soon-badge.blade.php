@props(['end' => null])

@php
    $endCarbon = null;
    if ($end instanceof \Carbon\Carbon) {
        $endCarbon = $end;
    } elseif ($end) {
        $endCarbon = \Carbon\Carbon::parse($end);
    }

    $secondsLeft = $endCarbon && $endCarbon->isFuture()
        ? now()->diffInSeconds($endCarbon)
        : null;
    $show = $secondsLeft !== null && $secondsLeft > 0 && $secondsLeft < 3600;
@endphp

@if($show)
    <span {{ $attributes->merge(['class' => 'cm-ending-soon-badge']) }}>Ending Soon</span>
@endif
