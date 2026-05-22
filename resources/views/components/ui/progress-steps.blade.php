@props([
    'step' => 1,
    'total' => 3,
    'labels' => [],
])

@php
    $step = max(1, min((int) $step, (int) $total));
    $total = max(1, (int) $total);
    $pct = $total > 0 ? round(($step / $total) * 100) : 0;
    if (empty($labels)) {
        $labels = array_map(fn ($i) => 'Step ' . $i, range(1, $total));
    }
@endphp

<div {{ $attributes->merge(['class' => 'cm-progress-steps']) }} data-cm-progress-root>
    <div class="cm-progress-steps__labels">
        @foreach($labels as $index => $label)
            @php $n = $index + 1; @endphp
            <span class="cm-progress-steps__label {{ $n < $step ? 'is-done' : ($n === $step ? 'is-active' : '') }}"
                  data-cm-progress-label>{{ $label }}</span>
        @endforeach
    </div>
    <div class="cm-progress-steps__track">
        <div class="cm-progress-steps__fill" data-cm-progress-fill style="width: {{ $pct }}%"></div>
    </div>
    <p class="cm-progress-steps__meta" data-cm-progress-meta>Step {{ $step }} of {{ $total }}</p>
</div>
