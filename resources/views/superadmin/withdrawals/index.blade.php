@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Withdrawal Management')

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Withdrawal Management</h5>
        <p class="text-muted small mb-0">Manage BRM withdrawal requests and payment approvals</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sa-card">
            <div class="small text-muted">Pending Withdrawals</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #f59e0b;">{{ $stats['pending'] }}</div>
            <div class="small" style="color: #f59e0b;">₦{{ number_format($stats['pending_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #10b981;">
            <div class="small text-muted">Approved (Unpaid)</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #10b981;">{{ $stats['approved'] }}</div>
            <div class="small" style="color: #10b981;">₦{{ number_format($stats['approved_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #6366f1;">
            <div class="small text-muted">Paid</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #6366f1;">{{ $stats['paid'] }}</div>
            <div class="small" style="color: #6366f1;">₦{{ number_format($stats['paid_amount'], 2) }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #ef4444;">
            <div class="small text-muted">Rejected</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #ef4444;">{{ $stats['rejected'] }}</div>
            <div class="small" style="color: #ef4444;">₦{{ number_format($stats['rejected_amount'], 2) }}</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="sa-card mb-4">
    <form method="GET" action="{{ route('superadmin.withdrawals') }}" class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-600">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" 
                   placeholder="BRM name or email..." value="{{ request('search') }}">
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
            <label class="form-label small fw-600">Date Range</label>
            <input type="date" name="date_from" class="form-control form-control-sm" 
                   value="{{ request('date_from') }}" placeholder="From">
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-600">&nbsp;</label>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i> Filter
                </button>
                <a href="{{ route('superadmin.withdrawals') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Withdrawals Table -->
<div class="sa-card">
    @if($withdrawals->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #f9fafb;">
                    <tr class="text-muted small">
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>Withdrawal ID</th>
                        <th>BRM</th>
                        <th>Amount</th>
                        <th>Bank Account</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $withdrawal)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input withdrawal-checkbox" 
                                       value="{{ $withdrawal->id }}">
                            </td>
                            <td>
                                <a href="{{ route('superadmin.withdrawals.show', $withdrawal->id) }}" 
                                   class="text-decoration-none fw-600 text-dark">
                                    #{{ $withdrawal->id }}
                                </a>
                            </td>
                            <td>
                                <small class="fw-600">{{ $withdrawal->brm->name ?? 'N/A' }}</small><br>
                                <small class="text-muted">{{ $withdrawal->brm->email ?? '' }}</small>
                            </td>
                            <td class="fw-600">₦{{ number_format($withdrawal->amount, 2) }}</td>
                            <td>
                                <small>
                                    @if($withdrawal->bankAccount)
                                        {{ $withdrawal->bankAccount->bank_name }}<br>
                                        <span class="text-muted">{{ substr($withdrawal->bankAccount->account_number, -4) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </small>
                            </td>
                            <td>
                                <span class="badge rounded-pill" style="background:
                                    {{ $withdrawal->status === 'pending' ? '#fef3c7' : '' }}
                                    {{ $withdrawal->status === 'approved' ? '#dcfce7' : '' }}
                                    {{ $withdrawal->status === 'paid' ? '#dbeafe' : '' }}
                                    {{ $withdrawal->status === 'rejected' ? '#fee2e2' : '' }};
                                    color:
                                    {{ $withdrawal->status === 'pending' ? '#b45309' : '' }}
                                    {{ $withdrawal->status === 'approved' ? '#16a34a' : '' }}
                                    {{ $withdrawal->status === 'paid' ? '#0369a1' : '' }}
                                    {{ $withdrawal->status === 'rejected' ? '#dc2626' : '' }};">
                                    {{ ucfirst($withdrawal->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $withdrawal->created_at->format('M d, Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 150px;">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('superadmin.withdrawals.show', $withdrawal->id) }}">
                                                <i class="bi bi-eye me-2"></i> View
                                            </a>
                                        </li>
                                        @if($withdrawal->status === 'pending')
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.withdrawals.approve', $withdrawal->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Approve this withdrawal request?')">
                                                        <i class="bi bi-check-circle me-2" style="color: #10b981;"></i> Approve
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.withdrawals.reject', $withdrawal->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Reject this withdrawal?')">
                                                        <i class="bi bi-x-circle me-2"></i> Reject
                                                    </button>
                                                </form>
                                            </li>
                                        @elseif($withdrawal->status === 'approved')
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.withdrawals.mark-paid', $withdrawal->id) }}" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Mark payment as complete?')">
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
                Showing {{ $withdrawals->firstItem() }} to {{ $withdrawals->lastItem() }} of {{ $withdrawals->total() }} withdrawals
            </small>
            {{ $withdrawals->links() }}
        </div>

        <!-- Bulk Actions -->
        @if($stats['pending'] > 0 || $stats['approved'] > 0)
            <div class="mt-3 pt-3 border-top" id="bulkActions" style="display: none;">
                <form method="POST" action="{{ route('superadmin.withdrawals.bulk-approve') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="withdrawal_ids" id="selectedIds" value="">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return updateSelectedIds() && confirm('Approve selected withdrawals?')">
                        <i class="bi bi-check-circle me-1"></i> Approve Selected
                    </button>
                </form>
                <form method="POST" action="{{ route('superadmin.withdrawals.bulk-mark-paid') }}" style="display: inline; margin-left: 0.5rem;">
                    @csrf
                    <input type="hidden" name="withdrawal_ids" id="selectedIds2" value="">
                    <button type="submit" class="btn btn-sm btn-info" onclick="return updateSelectedIds2() && confirm('Mark selected as paid?')">
                        <i class="bi bi-check2 me-1"></i> Mark Paid
                    </button>
                </form>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()" style="margin-left: 0.5rem;">
                    <i class="bi bi-x-lg me-1"></i> Clear
                </button>
            </div>
        @endif

    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 2.5rem; color: #d1d5db;"></i>
            <p class="text-muted mt-2">No withdrawal requests found.</p>
        </div>
    @endif
</div>

<script>
// Select all functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkActionsVisibility();
});

// Show/hide bulk actions based on selection
document.querySelectorAll('.withdrawal-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActionsVisibility);
});

function updateBulkActionsVisibility() {
    const anyChecked = document.querySelectorAll('.withdrawal-checkbox:checked').length > 0;
    document.getElementById('bulkActions').style.display = anyChecked ? 'block' : 'none';
}

function updateSelectedIds() {
    const selectedIds = Array.from(document.querySelectorAll('.withdrawal-checkbox:checked'))
        .map(cb => cb.value)
        .join(',');
    document.getElementById('selectedIds').value = selectedIds;
    return selectedIds.length > 0;
}

function updateSelectedIds2() {
    const selectedIds = Array.from(document.querySelectorAll('.withdrawal-checkbox:checked'))
        .map(cb => cb.value)
        .join(',');
    document.getElementById('selectedIds2').value = selectedIds;
    return selectedIds.length > 0;
}

function clearSelection() {
    document.querySelectorAll('.withdrawal-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActionsVisibility();
}
</script>

@endsection
