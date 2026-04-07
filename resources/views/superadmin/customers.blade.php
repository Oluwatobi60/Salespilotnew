@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Customers')

@section('superadmin_page_styles')
@media (max-width: 767.98px) {
    .col-hide-sm { display: none !important; }
}
@endSection

@section('superadmin_layout_content')

<!-- Search & Header -->
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">All Customers</h5>
        <p class="text-muted small mb-0">Business creators registered on the platform</p>
    </div>
    <form method="GET" action="{{ route('superadmin.customers') }}" class="d-flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ $search }}"
               class="form-control form-control-sm" style="min-width:180px;max-width:260px;flex:1 1 180px;"
               placeholder="Search name, business, email…">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-search"></i>
        </button>
        @if($search)
            <a href="{{ route('superadmin.customers') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg"></i>
            </a>
        @endif
    </form>
</div>

<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
            <thead style="background:#f8f7ff;">
                <tr>
                    <th class="px-4 py-3 fw-semibold text-secondary col-hide-sm">#</th>
                    <th class="px-3 py-3 fw-semibold text-secondary">Name</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">Business Name</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">Phone</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">BRM</th>
                    <th class="px-3 py-3 fw-semibold text-secondary">Status</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">Current Plan</th>
                    <th class="px-3 py-3 fw-semibold text-secondary text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    @php
                        $sub        = $customer->currentSubscription;
                        $plan       = $sub?->subscriptionPlan;
                        $planName   = $plan?->name ?? 'No Plan';
                        $fullName   = trim(($customer->first_name ?? '') . ' ' . ($customer->surname ?? '')) ?: $customer->email;

                        // Subscription status & days left
                        if ($sub && $sub->end_date) {
                            $daysLeft = (int) now()->startOfDay()->diffInDays($sub->end_date, false);
                            if ($daysLeft > 0) {
                                $subStatus = 'active';
                                $subLabel  = $daysLeft . ' day' . ($daysLeft === 1 ? '' : 's') . ' left';
                            } elseif ($daysLeft === 0) {
                                $subStatus = 'expiring';
                                $subLabel  = 'Expires today';
                            } else {
                                $subStatus = 'expired';
                                $subLabel  = 'Expired';
                            }
                        } else {
                            $subStatus = 'none';
                            $subLabel  = 'No subscription';
                        }
                    @endphp
                    <tr>
                        <td class="px-4 text-muted col-hide-sm">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>

                        <!-- Name + Email -->
                        <td class="px-3">
                            <div class="fw-semibold">{{ $fullName }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">{{ $customer->email }}</div>
                        </td>

                        <!-- Business Name -->
                        <td class="px-3 col-hide-sm">{{ $customer->business_name ?? '—' }}</td>

                        <!-- Phone -->
                        <td class="px-3 col-hide-sm">{{ $customer->phone_number ?? '—' }}</td>

                        <!-- BRM -->
                        <td class="px-3 col-hide-sm">
                            @if($customer->brm)
                                <span class="fw-semibold" style="font-size:0.85rem;">{{ $customer->brm->name }}</span>
                                <div class="text-muted" style="font-size:0.72rem;">{{ $customer->brm->region ?? '' }}</div>
                            @else
                                <span class="text-muted small">Unassigned</span>
                            @endif
                        </td>

                        <!-- Subscription Status -->
                        <td class="px-3">
                            @if($subStatus === 'active')
                                <span class="badge rounded-pill text-bg-success">Active</span>
                                <div class="text-muted mt-1" style="font-size:0.72rem;">{{ $subLabel }}</div>
                            @elseif($subStatus === 'expiring')
                                <span class="badge rounded-pill text-bg-warning">Expiring</span>
                                <div class="text-muted mt-1" style="font-size:0.72rem;">{{ $subLabel }}</div>
                            @elseif($subStatus === 'expired')
                                <span class="badge rounded-pill text-bg-danger">Expired</span>
                            @else
                                <span class="badge rounded-pill text-bg-secondary">None</span>
                            @endif
                        </td>

                        <!-- Current Plan -->
                        <td class="px-3 col-hide-sm">
                            @if($plan)
                                <span class="badge rounded-pill" style="background:#ede9fe;color:#6f42c1;">{{ $planName }}</span>
                            @else
                                <span class="text-muted small">No Plan</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-3 text-center">
                            <a href="{{ route('superadmin.users.show', $customer->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1 d-none d-md-inline"></i><span class="d-none d-md-inline">View</span><i class="bi bi-eye d-md-none"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            No customers found{{ $search ? ' for "' . $search . '"' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
        <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers
            </div>
            {{ $customers->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
