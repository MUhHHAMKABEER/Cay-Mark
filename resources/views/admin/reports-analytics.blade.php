@extends('layouts.admin')
@section('title', 'Reports & Analytics — Admin')
@section('content')
<style>
    :root{--navy:#063466;--navy-light:#e8eef6;}
    .ra-header{background:#fff;border-radius:12px;padding:1.5rem 1.75rem;margin-bottom:1.5rem;border-left:4px solid var(--navy);box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem}
    .ra-header h1{font-size:1.35rem;font-weight:700;color:var(--navy);margin:0 0 .2rem;display:flex;align-items:center;gap:8px}
    .ra-header h1 .material-icons-round{font-size:1.3rem}
    .ra-header p{margin:0;color:#64748b;font-size:.875rem}
    .ra-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem}
    .ra-stat{background:#fff;border-radius:12px;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;gap:1rem}
    .ra-stat-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ra-stat-ico .material-icons-round{font-size:22px}
    .ra-stat-lbl{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em}
    .ra-stat-val{font-size:1.75rem;font-weight:700;color:#0f172a;line-height:1.1;margin-top:2px}
    .ra-stat-sub{font-size:.72rem;color:#94a3b8;margin-top:2px}
    .ra-card{background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(6,52,102,.07);overflow:hidden;margin-bottom:1.25rem}
    .ra-card-hdr{padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .ra-card-hdr h2{font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:6px}
    .ra-card-hdr h2 .material-icons-round{font-size:18px;color:var(--navy)}
    .ra-tabs{display:flex;gap:6px;margin-bottom:1.25rem;flex-wrap:wrap}
    .ra-tab{padding:.45rem 1rem;border-radius:8px;font-size:.8125rem;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;cursor:pointer;text-decoration:none;transition:all .15s}
    .ra-tab.active,.ra-tab:hover{background:var(--navy);color:#fff;border-color:var(--navy)}
    .ra-chart-section{display:none}
    .ra-chart-section.active{display:block}
</style>

<div>
    <div class="ra-header">
        <div>
            <h1><span class="material-icons-round">analytics</span> Reports &amp; Analytics</h1>
            <p>Platform-wide metrics and trends — all data filtered by selected date range</p>
        </div>
        <a href="{{ route('admin.reports-analytics', array_merge(request()->query(), ['export'=>'csv'])) }}"
           style="height:38px;padding:0 1.1rem;border-radius:8px;font-size:.8125rem;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#475569;display:inline-flex;align-items:center;gap:6px;text-decoration:none">
            <span class="material-icons-round" style="font-size:16px">download</span> Export CSV
        </a>
    </div>

    {{-- Date filter --}}
    <form method="GET" action="{{ route('admin.reports-analytics') }}">
        @include('admin.partials.date-filter', ['current' => $dateFilter])
    </form>

    {{-- Stat cards --}}
    <div class="ra-stats">
        <div class="ra-stat">
            <div class="ra-stat-ico" style="background:#e8eef6;color:#063466"><span class="material-icons-round">list_alt</span></div>
            <div>
                <div class="ra-stat-lbl">Listings Submitted</div>
                <div class="ra-stat-val">{{ number_format($listingMetrics['submitted']) }}</div>
                <div class="ra-stat-sub">{{ $listingMetrics['approved'] }} approved · {{ $listingMetrics['rejected'] }} rejected</div>
            </div>
        </div>
        <div class="ra-stat">
            <div class="ra-stat-ico" style="background:#dcfce7;color:#16a34a"><span class="material-icons-round">gavel</span></div>
            <div>
                <div class="ra-stat-lbl">Auctions Completed</div>
                <div class="ra-stat-val">{{ number_format($auctionMetrics['completed']) }}</div>
                <div class="ra-stat-sub">Avg sale ${{ number_format($auctionMetrics['avg_sale'],2) }}</div>
            </div>
        </div>
        <div class="ra-stat">
            <div class="ra-stat-ico" style="background:#dbeafe;color:#2563eb"><span class="material-icons-round">attach_money</span></div>
            <div>
                <div class="ra-stat-lbl">Total Revenue (commissions)</div>
                <div class="ra-stat-val" style="font-size:1.4rem">${{ number_format($revenueTotal,2) }}</div>
                <div class="ra-stat-sub">Buyer + Seller fees</div>
            </div>
        </div>
        <div class="ra-stat">
            <div class="ra-stat-ico" style="background:#ede9fe;color:#7c3aed"><span class="material-icons-round">group</span></div>
            <div>
                <div class="ra-stat-lbl">Total Active Users</div>
                <div class="ra-stat-val">{{ number_format($userMetrics['total_active']) }}</div>
                <div class="ra-stat-sub">{{ $userMetrics['new_in_period'] }} new in period</div>
            </div>
        </div>
    </div>

    {{-- Chart tabs --}}
    <div class="ra-tabs">
        <a class="ra-tab active" onclick="showTab('listings',this)">Listings Trend</a>
        <a class="ra-tab" onclick="showTab('revenue',this)">Revenue Trend</a>
        <a class="ra-tab" onclick="showTab('users',this)">User Growth</a>
    </div>

    <div class="ra-card">
        <div id="section-listings" class="ra-chart-section active" style="padding:1.25rem">
            <canvas id="listingsChart" style="max-height:280px"></canvas>
        </div>
        <div id="section-revenue" class="ra-chart-section" style="padding:1.25rem">
            <canvas id="revenueChart" style="max-height:280px"></canvas>
        </div>
        <div id="section-users" class="ra-chart-section" style="padding:1.25rem">
            <canvas id="usersChart" style="max-height:280px"></canvas>
        </div>
    </div>

    {{-- Breakdown cards --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">
        <div class="ra-card">
            <div class="ra-card-hdr"><h2><span class="material-icons-round">list_alt</span>Listing Breakdown</h2></div>
            <div style="padding:1.25rem">
                @foreach(['submitted'=>['Submitted','#063466'],'approved'=>['Approved','#16a34a'],'rejected'=>['Rejected','#dc2626'],'sold'=>['Sold','#7c3aed']] as $k=>[$label,$color])
                <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #f8fafc;font-size:.875rem">
                    <span style="color:#64748b">{{ $label }}</span>
                    <span style="font-weight:700;color:{{ $color }}">{{ number_format($listingMetrics[$k]) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="ra-card">
            <div class="ra-card-hdr"><h2><span class="material-icons-round">people</span>User Breakdown</h2></div>
            <div style="padding:1.25rem">
                @foreach(['total_active'=>['Total Active','#063466'],'new_in_period'=>['New in Period','#16a34a'],'buyers'=>['Buyers','#2563eb'],'sellers'=>['Sellers','#d97706']] as $k=>[$label,$color])
                <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #f8fafc;font-size:.875rem">
                    <span style="color:#64748b">{{ $label }}</span>
                    <span style="font-weight:700;color:{{ $color }}">{{ number_format($userMetrics[$k]) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function showTab(id, btn) {
    document.querySelectorAll('.ra-chart-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.ra-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('section-'+id).classList.add('active');
    btn.classList.add('active');
}

var navy = '#063466', gold = '#C8A84B', green = '#16a34a';

(function() {
    var listingsLabels = @json($listingsTrend->pluck('date'));
    var listingsData   = @json($listingsTrend->pluck('count'));
    new Chart(document.getElementById('listingsChart'), {
        type: 'line',
        data: { labels: listingsLabels, datasets: [{ label: 'Listings Submitted', data: listingsData, borderColor: navy, backgroundColor: 'rgba(6,52,102,.1)', tension: .3, fill: true, pointRadius: 3 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    var revLabels = @json($revenueTrend->pluck('date'));
    var revData   = @json($revenueTrend->pluck('total'));
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: { labels: revLabels, datasets: [{ label: 'Revenue ($)', data: revData, backgroundColor: gold, borderRadius: 4 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    var uLabels = @json($usersTrend->pluck('date'));
    var uData   = @json($usersTrend->pluck('count'));
    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: { labels: uLabels, datasets: [{ label: 'New Users', data: uData, borderColor: green, backgroundColor: 'rgba(22,163,74,.1)', tension: .3, fill: true, pointRadius: 3 }] },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });
})();
</script>
@endsection
