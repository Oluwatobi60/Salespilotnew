@extends('superadmin.layouts.layout')

@section('superadmin_page_title')Subscription Renewals@endsection

@section('superadmin_page_styles')
<style>
    .renewal-badge { font-size: .72rem; padding: 3px 9px; border-radius: 20px; font-weight: 600; }
    .badge-on  { background:#d1fae5; color:#065f46; }
    .badge-off { background:#f3f4f6; color:#6b7280; }
    .tbl th { font-size:.78rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
    .tbl td { font-size:.85rem; vertical-align:middle; }
    .toggle-form { display:inline; }
    .exp-soon { background:#fef9c3; }
    .exp-today { background:#fee2e2; }
</style>
@endsection

@section('superadmin_layout_content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0">Subscription Renewals</h4>
        <p class="text-muted mb-0" style="font-size:.82rem;">Manage auto-renewal for every subscriber</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        {{-- Manual: send reminder emails --}}
        <form method="POST" action="{{ route('superadmin.subscriptions.send-reminders') }}">
            @csrf
            <div class="input-group input-group-sm">
                <input type="number" name="days" class="form-control" value="7" min="1" max="30"
                       style="width:70px;" title="Remind subscribers expiring within N days">
                <button class="btn btn-outline-warning" type="submit" title="Send expiry reminder emails now">
                    <i class="bi bi-envelope-exclamation me-1"></i>Send Reminders
                </button>
            </div>
        </form>
        {{-- Manual: process renewals now --}}
        <form method="POST" action="{{ route('superadmin.subscriptions.process-renewals') }}">
            @csrf
            <button class="btn btn-sm btn-outline-success"
                    onclick="return confirm('Run auto-renewals now? This will renew expired subscriptions and send confirmation emails.')">
                <i class="bi bi-arrow-clockwise me-1"></i>Process Auto-Renewals
            </button>
        </form>
    </div>
</div>

{{-- ── Stat cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="sa-card text-center">
            <div class="text-muted mb-1" style="font-size:.75rem;">Total Subscribers</div>
            <div class="fw-bold" style="font-size:1.6rem;">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card text-center">
            <div class="text-muted mb-1" style="font-size:.75rem;">Active</div>
            <div class="fw-bold text-success" style="font-size:1.6rem;">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card text-center">
            <div class="text-muted mb-1" style="font-size:.75rem;">Auto-Renew On</div>
            <div class="fw-bold text-primary" style="font-size:1.6rem;">{{ $stats['auto_renew'] }}</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="sa-card text-center">
            <div class="text-muted mb-1" style="font-size:.75rem;">Expiring ≤ 7 Days</div>
            <div class="fw-bold text-warning" style="font-size:1.6rem;">{{ $stats['expiring_7d'] }}</div>
        </div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="sa-card mb-4">
    <form method="GET" action="{{ route('superadmin.subscriptions') }}"
          class="d-flex flex-wrap gap-2 align-items-center">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search name / email / business…"
               value="{{ request('search') }}" style="min-width:200px; flex:1 1 200px; max-width:300px;">
        <select name="status" class="form-select form-select-sm" style="width:auto;">
            <option value="">All Statuses</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="expired"   {{ request('status') === 'expired'   ? 'selected' : '' }}>Expired</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <select name="auto_renew" class="form-select form-select-sm" style="width:auto;">
            <option value="">Auto-Renew: All</option>
            <option value="1" {{ request('auto_renew') === '1' ? 'selected' : '' }}>On</option>
            <option value="0" {{ request('auto_renew') === '0' ? 'selected' : '' }}>Off</option>
        </select>
        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a href="{{ route('superadmin.subscriptions') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
    </form>
</div>

{{-- ── Bulk actions ── --}}
<div id="bulkBar" class="sa-card mb-3 d-none">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <span class="text-muted small" id="bulkCount">0 selected</span>
        <form method="POST" action="{{ route('superadmin.subscriptions.bulk-toggle') }}" id="bulkForm">
            @csrf
            <div id="bulkIds"></div>
            <input type="hidden" name="auto_renew" id="bulkAutoRenewVal">
            <button type="button" class="btn btn-sm btn-success" onclick="bulkAction(1)">
                <i class="bi bi-check2-circle me-1"></i>Enable Auto-Renew
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="bulkAction(0)">
                <i class="bi bi-x-circle me-1"></i>Disable Auto-Renew
            </button>
        </form>
        <button class="btn btn-sm btn-link text-muted" onclick="clearSelection()">Deselect all</button>
    </div>
</div>

{{-- ── Table ── --}}
<div class="sa-card p-0">
    <div class="table-responsive">
        <table class="table tbl mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3"><input type="checkbox" id="selectAll" title="Select all"></th>
                    <th>Customer</th>
                    <th>Plan</th>
                    <th>Duration</th>
                    <th>Start</th>
                    <th>Expiry</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Auto-Renew</th>
                    <th>Last Renewed</th>
                    <th class="pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($subscriptions as $sub)
                @php
                    $daysLeft  = $sub->end_date ? now()->startOfDay()->diffInDays($sub->end_date, false) : null;
                    $rowClass  = '';
                    if ($sub->status === 'active') {
                        if ($daysLeft !== null && $daysLeft === 0) $rowClass = 'exp-today';
                        elseif ($daysLeft !== null && $daysLeft <= 7) $rowClass = 'exp-soon';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="ps-3">
                        <input type="checkbox" class="sub-check" value="{{ $sub->id }}">
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $sub->user->business_name ?? '—' }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $sub->user->email ?? '—' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold"
                              style="text-transform:capitalize;">
                            {{ $sub->subscriptionPlan->name ?? '—' }}
                        </span>
                    </td>
                    <td>{{ $sub->duration_months }}mo</td>
                    <td>{{ $sub->start_date?->format('d M Y') }}</td>
                    <td>
                        {{ $sub->end_date?->format('d M Y') }}
                        @if($sub->status === 'active' && $daysLeft !== null)
                            @if($daysLeft === 0)
                                <span class="badge bg-danger ms-1">Today</span>
                            @elseif($daysLeft <= 7)
                                <span class="badge bg-warning text-dark ms-1">{{ $daysLeft }}d left</span>
                            @endif
                        @endif
                    </td>
                    <td>₦{{ number_format($sub->amount_paid, 0) }}</td>
                    <td>
                        @if($sub->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif($sub->status === 'expired')
                            <span class="badge bg-secondary">Expired</span>
                        @else
                            <span class="badge bg-danger">Cancelled</span>
                        @endif
                    </td>
                    <td>
                        <span class="renewal-badge {{ $sub->auto_renew ? 'badge-on' : 'badge-off' }}">
                            {{ $sub->auto_renew ? '⟳ On' : '— Off' }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.78rem;">
                        {{ $sub->last_renewed_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="pe-3">
                        <form method="POST"
                              action="{{ route('superadmin.subscriptions.toggle', $sub) }}"
                              class="toggle-form">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm {{ $sub->auto_renew ? 'btn-outline-secondary' : 'btn-outline-primary' }}"
                                    title="{{ $sub->auto_renew ? 'Disable auto-renew' : 'Enable auto-renew' }}">
                                <i class="bi {{ $sub->auto_renew ? 'bi-toggle2-on' : 'bi-toggle2-off' }}"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No subscriptions found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($subscriptions->hasPages())
        <div class="px-3 py-3 border-top">
            {{ $subscriptions->links() }}
        </div>
    @endif
</div>

<script>
(function () {
    const checkboxes = () => document.querySelectorAll('.sub-check');
    const bulkBar    = document.getElementById('bulkBar');
    const bulkCount  = document.getElementById('bulkCount');
    const selectAll  = document.getElementById('selectAll');

    function updateBulkBar() {
        const checked = [...checkboxes()].filter(c => c.checked);
        if (checked.length > 0) {
            bulkBar.classList.remove('d-none');
            bulkCount.textContent = checked.length + ' selected';
        } else {
            bulkBar.classList.add('d-none');
        }
    }

    document.querySelectorAll('.sub-check').forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    selectAll.addEventListener('change', function () {
        checkboxes().forEach(cb => { cb.checked = this.checked; });
        updateBulkBar();
    });

    window.clearSelection = function () {
        checkboxes().forEach(cb => { cb.checked = false; });
        selectAll.checked = false;
        updateBulkBar();
    };

    window.bulkAction = function (val) {
        const checked = [...checkboxes()].filter(c => c.checked);
        if (!checked.length) return;

        const container = document.getElementById('bulkIds');
        container.innerHTML = '';
        checked.forEach(c => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'ids[]';
            inp.value = c.value;
            container.appendChild(inp);
        });
        document.getElementById('bulkAutoRenewVal').value = val;
        document.getElementById('bulkForm').submit();
    };
})();
</script>
@endsection
