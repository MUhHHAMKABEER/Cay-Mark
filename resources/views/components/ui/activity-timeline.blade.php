@props([
    'items' => [],
    'title' => 'Recent activity',
    'emptyMessage' => 'No recent activity yet.',
])

@php
    $entries = is_array($items) ? $items : [];
@endphp

<div {{ $attributes->merge(['class' => 'cm-activity-timeline']) }}>
    <div class="cm-activity-timeline__head">
        <h3 class="cm-activity-timeline__title">{{ $title }}</h3>
        <span class="material-icons-round cm-activity-timeline__head-icon" aria-hidden="true">history</span>
    </div>

    @if(count($entries) === 0)
        <p class="cm-activity-timeline__empty">{{ $emptyMessage }}</p>
    @else
        <ol class="cm-activity-timeline__list">
            @foreach($entries as $entry)
                @php
                    $ts = $entry['timestamp'] ?? null;
                    $timeLabel = $ts instanceof \Carbon\CarbonInterface
                        ? $ts->diffForHumans()
                        : ($ts ? \Carbon\Carbon::parse($ts)->diffForHumans() : '');
                @endphp
                <li class="cm-activity-timeline__item">
                    <span class="cm-activity-timeline__icon" aria-hidden="true">
                        <span class="material-icons-round">{{ $entry['icon'] ?? 'circle' }}</span>
                    </span>
                    <div class="cm-activity-timeline__body">
                        @if(! empty($entry['url']))
                            <a href="{{ $entry['url'] }}" class="cm-activity-timeline__desc">{{ $entry['description'] ?? '' }}</a>
                        @else
                            <p class="cm-activity-timeline__desc">{{ $entry['description'] ?? '' }}</p>
                        @endif
                        @if($timeLabel)
                            <time class="cm-activity-timeline__time">{{ $timeLabel }}</time>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    @endif
</div>
