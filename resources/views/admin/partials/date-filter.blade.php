@props(['route' => null, 'current' => '30_days', 'dateRanges' => null, 'exportRoute' => null])
@php
    $presets = [
        'today'     => 'Today',
        '7_days'    => 'Last 7 Days',
        '30_days'   => 'Last 30 Days',
        '90_days'   => 'Last 90 Days',
        'this_year' => 'This Year',
        'all_time'  => 'All Time',
        'custom'    => 'Custom Range',
    ];
    $fromVal = request('date_from', '');
    $toVal   = request('date_to',   '');
@endphp
<div style="background:#fff;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;flex-wrap:wrap;align-items:center;gap:.625rem">
    <form method="GET" id="df-form" style="display:contents">
        @foreach ($presets as $key => $label)
            <button type="submit" name="date_filter" value="{{ $key }}"
                style="height:34px;padding:0 .9rem;border-radius:8px;font-size:.8rem;font-weight:600;border:1.5px solid {{ $current === $key ? '#063466' : '#e2e8f0' }};background:{{ $current === $key ? '#063466' : '#fff' }};color:{{ $current === $key ? '#fff' : '#475569' }};cursor:pointer;white-space:nowrap">
                {{ $label }}
            </button>
        @endforeach

        <div id="df-custom-wrap" style="display:{{ $current === 'custom' ? 'flex' : 'none' }};align-items:center;gap:.5rem;flex-wrap:wrap">
            <input type="date" name="date_from" value="{{ $fromVal }}"
                style="height:34px;padding:0 .7rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.8125rem;outline:none">
            <span style="font-size:.8rem;color:#94a3b8">to</span>
            <input type="date" name="date_to" value="{{ $toVal }}"
                style="height:34px;padding:0 .7rem;border:1.5px solid #e2e8f0;border-radius:8px;font-size:.8125rem;outline:none">
            <button type="submit" name="date_filter" value="custom"
                style="height:34px;padding:0 1rem;border-radius:8px;font-size:.8rem;font-weight:600;border:none;background:#063466;color:#fff;cursor:pointer">
                Apply
            </button>
        </div>
    </form>

    @if ($exportRoute)
        <a href="{{ $exportRoute }}&date_filter={{ $current }}&date_from={{ $fromVal }}&date_to={{ $toVal }}"
           style="margin-left:auto;height:34px;padding:0 1rem;border-radius:8px;font-size:.8rem;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#475569;display:inline-flex;align-items:center;gap:5px;text-decoration:none">
            <span class="material-icons-round" style="font-size:15px">download</span> Export CSV
        </a>
    @endif
</div>

<script>
document.querySelectorAll('[name="date_filter"][value="custom"]').forEach(function(btn){
    btn.addEventListener('click', function(e){
        var w = document.getElementById('df-custom-wrap');
        if (w && w.style.display === 'none') { e.preventDefault(); w.style.display='flex'; }
    });
});
</script>
