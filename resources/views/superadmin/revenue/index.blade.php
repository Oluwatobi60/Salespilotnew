@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Revenue Tracking')

@section('superadmin_page_styles')
<style>
    .rev-stat-card {
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .rev-stat-card .icon-bg {
        position: absolute;
        right: -10px;
        top: -10px;
        font-size: 5rem;
        opacity: .12;
        line-height: 1;
    }
    .rev-stat-card .label  { font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; opacity: .85; color:#fff; }
    .rev-stat-card .value  { font-size: 1.75rem; font-weight: 700; line-height: 1.2; color:#fff; }
    .rev-stat-card .badge-change { font-size: .72rem; padding: .3em .6em; }
    .card-rev-blue   { background: linear-gradient(135deg, #2563eb, #3b82f6); }
    .card-rev-green  { background: linear-gradient(135deg, #059669, #10b981); }
    .card-rev-purple { background: linear-gradient(135deg, #7c3aed, #a78bfa); }

    .filter-bar { background: #fff; border-radius: 12px; padding: 1rem 1.25rem; box-shadow: 0 1px 4px rgba(0,0,0,.07); }
    .filter-bar label { font-size: .8rem; color: #6b7280; margin-bottom: .2rem; display: block; }

    .chart-card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 4px rgba(0,0,0,.07); }

    .plan-badge {
        display: inline-block;
        padding: .25em .65em;
        border-radius: 6px;
        font-size: .75rem;
        font-weight: 600;
        background: #e0e7ff;
        color: #3730a3;
    }
    .status-badge {
        font-size: .72rem;
        padding: .3em .6em;
        border-radius: 6px;
        font-weight: 600;
    }
    .status-active    { background: #d1fae5; color: #065f46; }
    .status-expired   { background: #fee2e2; color: #991b1b; }
    .status-cancelled { background: #fef3c7; color: #92400e; }
    .status-pending   { background: #f3f4f6; color: #374151; }

    .rev-table thead th { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
    .rev-table tbody td { font-size: .875rem; vertical-align: middle; }

    .period-note { font-size: .8rem; color: #9ca3af; }

    @media (max-width: 575.98px) {
        .rev-stat-card .value { font-size: 1.35rem; }
        .col-hide-xs { display: none !important; }
    }
    @media (max-width: 767.98px) {
        .col-hide-sm { display: none !important; }
    }
</style>
@endsection

@section('superadmin_layout_content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold">Revenue Tracking</h4>
        <span class="period-note">
            {{ $from->format('M d, Y') }} &ndash; {{ $to->format('M d, Y') }}
            ({{ $from->diffInDays($to) + 1 }} days)
        </span>
    </div>
</div>

{{-- ── Filter Bar ─────────────────────────────────────────────────────── --}}
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('superadmin.revenue') }}" class="row g-3 align-items-end">
        <div class="col-6 col-sm-4 col-md-3">
            <label for="from">From</label>
            <input type="date" id="from" name="from" class="form-control form-control-sm"
                   value="{{ $from->toDateString() }}">
        </div>
        <div class="col-6 col-sm-4 col-md-3">
            <label for="to">To</label>
            <input type="date" id="to" name="to" class="form-control form-control-sm"
                   value="{{ $to->toDateString() }}">
        </div>
        <div class="col-12 col-sm-4 col-md-3">
            <label for="plan_id">Plan</label>
            <select id="plan_id" name="plan_id" class="form-select form-select-sm">
                <option value="">All Plans</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" @selected($planId == $plan->id)>{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-sm-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="{{ route('superadmin.revenue') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
        {{-- Quick range shortcuts --}}
        <div class="col-12 d-flex flex-wrap gap-2">
            @foreach([
                ['Last 7 days',  now()->subDays(6)->toDateString(),  now()->toDateString()],
                ['Last 30 days', now()->subDays(29)->toDateString(), now()->toDateString()],
                ['This month',   now()->startOfMonth()->toDateString(), now()->toDateString()],
                ['Last month',   now()->subMonth()->startOfMonth()->toDateString(), now()->subMonth()->endOfMonth()->toDateString()],
                ['This year',    now()->startOfYear()->toDateString(), now()->toDateString()],
            ] as [$label, $f, $t])
                <a href="{{ route('superadmin.revenue', array_filter(['from' => $f, 'to' => $t, 'plan_id' => $planId])) }}"
                   class="btn btn-sm {{ ($from->toDateString() === $f && $to->toDateString() === $t) ? 'btn-dark' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </form>
</div>

{{-- ── Stat Cards ──────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="rev-stat-card card-rev-blue">
            <div class="icon-bg"><i class="bi bi-currency-dollar"></i></div>
            <div class="label">Period Revenue</div>
            <div class="value">₦{{ number_format($periodRevenue, 2) }}</div>
            @if ($revenueChange !== null)
                <span class="badge badge-change mt-1
                    {{ $revenueChange >= 0 ? 'bg-success' : 'bg-danger' }}">
                    <i class="bi bi-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }}-short"></i>
                    {{ abs($revenueChange) }}% vs prev. period
                </span>
            @endif
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="rev-stat-card card-rev-green">
            <div class="icon-bg"><i class="bi bi-receipt"></i></div>
            <div class="label">Transactions</div>
            <div class="value">{{ number_format($periodTransactions) }}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="rev-stat-card card-rev-purple">
            <div class="icon-bg"><i class="bi bi-graph-up"></i></div>
            <div class="label">Avg per Transaction</div>
            <div class="value">₦{{ number_format($periodAvg, 2) }}</div>
        </div>
    </div>
</div>

{{-- ── Chart + Plan Breakdown ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="chart-card h-100">
            <h6 class="fw-semibold mb-3">Daily Revenue</h6>
            <div style="position:relative; height:280px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="chart-card h-100">
            <h6 class="fw-semibold mb-3">Revenue by Plan</h6>
            @if ($byPlan->isEmpty())
                <p class="text-muted small mt-4 text-center">No data for this period.</p>
            @else
                @php $maxPlan = $byPlan->max('total'); @endphp
                @foreach ($byPlan as $row)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="plan-badge">{{ $row->subscriptionPlan->name ?? 'Unknown' }}</span>
                            <span class="fw-semibold small">₦{{ number_format($row->total, 2) }}</span>
                        </div>
                        <div class="progress" style="height:6px; border-radius:4px;">
                            <div class="progress-bar bg-primary"
                                 style="width: {{ $maxPlan > 0 ? round(($row->total / $maxPlan) * 100) : 0 }}%">
                            </div>
                        </div>
                        <div class="text-muted" style="font-size:.72rem; margin-top:.2rem;">
                            {{ number_format($row->count) }} transaction{{ $row->count != 1 ? 's' : '' }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- ── Transaction History ─────────────────────────────────────────────── --}}
<div class="chart-card">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h6 class="fw-semibold mb-0">Transaction History</h6>
        <span class="text-muted small">{{ $transactions->total() }} records</span>
    </div>

    @if ($transactions->isEmpty())
        <p class="text-center text-muted py-4">No transactions found for the selected period.</p>
    @else
        <div class="table-responsive">
            <table class="table table-hover rev-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="col-hide-sm">#</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th class="col-hide-sm">Plan</th>
                        <th class="col-hide-xs">Duration</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="col-hide-xs">Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $index => $sub)
                        <tr>
                            <td class="col-hide-sm text-muted">
                                {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                            </td>
                            <td style="white-space:nowrap;">
                                {{ $sub->created_at->format('M d, Y') }}<br>
                                <span class="text-muted" style="font-size:.72rem;">{{ $sub->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <div class="fw-medium">
                                    {{ $sub->user->first_name ?? '' }} {{ $sub->user->surname ?? 'N/A' }}
                                </div>
                                <div class="text-muted" style="font-size:.72rem;">
                                    {{ $sub->user->email ?? '' }}
                                </div>
                            </td>
                            <td class="col-hide-sm">
                                <span class="plan-badge">{{ $sub->subscriptionPlan->name ?? 'N/A' }}</span>
                            </td>
                            <td class="col-hide-xs text-muted">
                                {{ $sub->duration_months ?? '—' }}
                                {{ $sub->duration_months ? ($sub->duration_months == 1 ? 'mo' : 'mos') : '' }}
                            </td>
                            <td class="fw-semibold" style="color:#1d4ed8;">
                                ₦{{ number_format($sub->amount_paid, 2) }}
                            </td>
                            <td>
                                <span class="status-badge status-{{ $sub->status }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="col-hide-xs text-muted" style="font-size:.72rem; font-family:monospace;">
                                {{ $sub->payment_reference ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($transactions->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@section('superadmin_page_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json($chartLabels);
    const data   = @json($chartData);

    const ctx = document.getElementById('revenueChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Revenue (₦)',
                data,
                fill: true,
                tension: 0.35,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.10)',
                pointBackgroundColor: '#2563eb',
                pointRadius: labels.length <= 31 ? 4 : 2,
                pointHoverRadius: 6,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '₦' + ctx.parsed.y.toLocaleString('en-NG', { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        maxTicksLimit: 10,
                        font: { size: 11 },
                        color: '#9ca3af',
                        maxRotation: 0,
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        font: { size: 11 },
                        color: '#9ca3af',
                        callback: v => '₦' + v.toLocaleString()
                    }
                }
            }
        }
    });
})();
</script>
@endsection
