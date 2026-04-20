@extends('superadmin.layouts.layout')
@section('superadmin_page_title', $brm->name . ' - Commissions')

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">{{ $brm->name }} - Commission Summary</h5>
        <p class="text-muted small mb-0">{{ $brm->email }}</p>
    </div>
    <a href="{{ route('superadmin.commissions') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to All
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sa-card">
            <div class="small text-muted">Total Earned</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #667eea;">
                ₦{{ number_format($stats['total'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #f59e0b;">
            <div class="small text-muted">Pending</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #f59e0b;">
                ₦{{ number_format($stats['pending'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #10b981;">
            <div class="small text-muted">Approved</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #10b981;">
                ₦{{ number_format($stats['approved'], 2) }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sa-card" style="border-left: 4px solid #6366f1;">
            <div class="small text-muted">Paid</div>
            <div class="fw-bold" style="font-size: 1.5rem; color: #6366f1;">
                ₦{{ number_format($stats['paid'], 2) }}
            </div>
        </div>
    </div>
</div>

<!-- BRM Details Card -->
<div class="sa-card mb-4">
    <div class="row g-4">
        <div class="col-md-6">
            <div>
                <label class="small text-muted">Email</label>
                <div class="fw-600">{{ $brm->email }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div>
                <label class="small text-muted">Phone</label>
                <div class="fw-600">{{ $brm->phone ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div>
                <label class="small text-muted">Status</label>
                <div>
                    <span class="badge rounded-pill {{ $brm->status ? 'text-bg-success' : 'text-bg-secondary' }}">
                        {{ $brm->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div>
                <label class="small text-muted">Member Since</label>
                <div>{{ $brm->created_at->format('M d, Y') }}</div>
            </div>
        </div>
        <div class="col-12">
            <a href="{{ route('superadmin.brms') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Manage BRM
            </a>
        </div>
    </div>
</div>

<!-- Commissions Table -->
<div class="sa-card">
    <h6 class="fw-bold mb-3">All Commissions</h6>
    @if($commissions->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #f9fafb;">
                    <tr class="text-muted small">
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commissions as $commission)
                        <tr>
                            <td>
                                <a href="{{ route('superadmin.commissions.show', $commission->id) }}" 
                                   class="text-decoration-none fw-600 text-dark">
                                    #{{ $commission->id }}
                                </a>
                            </td>
                            <td>
                                <small class="fw-600">{{ $commission->user->business_name ?? $commission->user->first_name }}</small><br>
                                <small class="text-muted">{{ $commission->user->email }}</small>
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
                            <td>{{ $commission->commission_rate }}%</td>
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
                            <td><small class="text-muted">{{ $commission->created_at->format('M d, Y') }}</small></td>
                            <td>
                                <a href="{{ route('superadmin.commissions.show', $commission->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
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
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 2.5rem; color: #d1d5db;"></i>
            <p class="text-muted mt-3">No commissions found for this BRM</p>
        </div>
    @endif
</div>

@endsection
