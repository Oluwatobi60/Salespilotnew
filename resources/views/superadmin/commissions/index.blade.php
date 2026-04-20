@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Commission Management')

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Commission Management</h5>
        <p class="text-muted small mb-0">Review and approve BRM commissions</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sa-card">
            <div class="small text-muted">Total Commissions</div>
            <div class="fw-bold" style="font-size: 1.5rem;">{{ $stats['total'] }}</div>
            <div class="small" style="color: #8b5cf6;">₦{{ number_format($stats['total_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #f59e0b;">
            <div class="small text-muted">Pending Approval</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #f59e0b;">{{ $stats['pending'] }}</div>
            <div class="small" style="color: #f59e0b;">₦{{ number_format($stats['pending_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #10b981;">
            <div class="small text-muted">Approved</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #10b981;">{{ $stats['approved'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #6366f1;">
            <div class="small text-muted">Paid</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #6366f1;">{{ $stats['paid'] }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="sa-card mb-4">
    <form method="GET" action="{{ route('superadmin.commissions') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-600">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" 
                   placeholder="BRM or Customer name..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-600">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-600">Type</label>
            <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                <option value="referral" {{ request('type') === 'referral' ? 'selected' : '' }}>Referral</option>
                <option value="renewal" {{ request('type') === 'renewal' ? 'selected' : '' }}>Renewal</option>
                <option value="upgrade" {{ request('type') === 'upgrade' ? 'selected' : '' }}>Upgrade</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-600">BRM</label>
            <select name="brm_id" class="form-select form-select-sm">
                <option value="">All BRMs</option>
                @foreach($brms as $brm)
                    <option value="{{ $brm->id }}" {{ request('brm_id') == $brm->id ? 'selected' : '' }}>
                        {{ $brm->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-600">&nbsp;</label>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('superadmin.commissions') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Commissions Table -->
<div class="sa-card">
    @if($commissions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #f9fafb;">
                    <tr class="text-muted small">
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Commission ID</th>
                        <th>BRM</th>
                        <th>Customer</th>
                        <th>Subscription</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commissions as $commission)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input commission-checkbox" 
                                       value="{{ $commission->id }}">
                            </td>
                            <td>
                                <a href="{{ route('superadmin.commissions.show', $commission->id) }}" 
                                   class="text-decoration-none fw-600 text-dark">
                                    #{{ $commission->id }}
                                </a>
                            </td>
                            <td>
                                <small class="fw-600">{{ $commission->brm->name ?? 'N/A' }}</small><br>
                                <small class="text-muted">{{ $commission->brm->email ?? '' }}</small>
                            </td>
                            <td>
                                <small class="fw-600">{{ $commission->user->business_name ?? $commission->user->first_name }}</small><br>
                                <small class="text-muted">{{ $commission->user->email }}</small>
                            </td>
                            <td>
                                <small>
                                    @if($commission->userSubscription)
                                        {{ $commission->userSubscription->subscriptionPlan->name ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span class="badge" style="background: 
                                    {{ $commission->commission_type === 'referral' ? '#ede9fe' : '' }}
                                    {{ $commission->commission_type === 'renewal' ? '#dcfce7' : '' }}
                                    {{ $commission->commission_type === 'upgrade' ? '#f0f9ff' : '' }};
                                    color: 
                                    {{ $commission->commission_type === 'referral' ? '#6f42c1' : '' }}
                                    {{ $commission->commission_type === 'renewal' ? '#16a34a' : '' }}
                                    {{ $commission->commission_type === 'upgrade' ? '#0284c7' : '' }};">
                                    {{ ucfirst($commission->commission_type) }}
                                </span>
                            </td>
                            <td class="fw-600">₦{{ number_format($commission->commission_amount, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill" style="background:
                                    {{ $commission->status === 'pending' ? '#fef3c7' : '' }}
                                    {{ $commission->status === 'approved' ? '#dcfce7' : '' }}
                                    {{ $commission->status === 'paid' ? '#dbeafe' : '' }}
                                    {{ $commission->status === 'rejected' ? '#fee2e2' : '' }};
                                    color:
                                    {{ $commission->status === 'pending' ? '#b45309' : '' }}
                                    {{ $commission->status === 'approved' ? '#16a34a' : '' }}
                                    {{ $commission->status === 'paid' ? '#0369a1' : '' }}
                                    {{ $commission->status === 'rejected' ? '#dc2626' : '' }};">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $commission->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 150px;">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('superadmin.commissions.show', $commission->id) }}">
                                                <i class="bi bi-eye me-2"></i> View
                                            </a>
                                        </li>
                                        @if($commission->status === 'pending')
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.commissions.approve', $commission->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Approve this commission?')">
                                                        <i class="bi bi-check-circle me-2" style="color: #10b981;"></i> Approve
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.commissions.reject', $commission->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Reject this commission?')">
                                                        <i class="bi bi-x-circle me-2"></i> Reject
                                                    </button>
                                                </form>
                                            </li>
                                        @elseif($commission->status === 'approved')
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.commissions.mark-paid', $commission->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Mark as paid?')">
                                                        <i class="bi bi-check2 me-2" style="color: #0284c7;"></i> Mark Paid
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <small class="text-muted">
                Showing {{ $commissions->firstItem() }} to {{ $commissions->lastItem() }} of {{ $commissions->total() }} commissions
            </small>
            {{ $commissions->links() }}
        </div>

        <!-- Bulk Actions -->
        @if($stats['pending'] > 0)
            <div class="mt-3 pt-3 border-top" id="bulkActions" style="display: none;">
                <form method="POST" action="{{ route('superadmin.commissions.bulk-approve') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="commission_ids" id="selectedIds" value="">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return updateSelectedIds() && confirm('Approve selected commissions?')">
                        <i class="bi bi-check-circle me-1"></i> Approve Selected
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </button>
                </form>
            </div>
        @endif

    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 2.5rem; color: #d1d5db;"></i>
            <p class="text-muted mt-3">No commissions found</p>
        </div>
    @endif
</div>

<script>
// Bulk selection logic
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.commission-checkbox');
    const bulkActions = document.getElementById('bulkActions');

    function updateBulkActionsVisibility() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (bulkActions) bulkActions.style.display = anyChecked ? 'block' : 'none';
    }

    selectAll?.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionsVisibility();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActionsVisibility);
    });
});

function updateSelectedIds() {
    const checked = Array.from(document.querySelectorAll('.commission-checkbox:checked'));
    const ids = checked.map(cb => cb.value);
    document.getElementById('selectedIds').value = JSON.stringify(ids);
    return ids.length > 0;
}

function clearSelection() {
    document.getElementById('selectAll').checked = false;
    document.querySelectorAll('.commission-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('bulkActions').style.display = 'none';
}
</script>

@endsection
