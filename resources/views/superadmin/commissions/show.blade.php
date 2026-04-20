@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Commission #' . $commission->id)

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Commission #{{ $commission->id }}</h5>
        <p class="text-muted small mb-0">{{ $commission->created_at->format('F d, Y \a\t h:i A') }}</p>
    </div>
    <a href="{{ route('superadmin.commissions') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-3">
    <!-- Main Details -->
    <div class="col-lg-8">
        <div class="sa-card mb-3">
            <h6 class="fw-bold mb-3">Commission Details</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted">Amount</label>
                        <div class="fw-bold" style="font-size: 1.8rem; color: #667eea;">
                            ₦{{ number_format($commission->commission_amount, 2) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted">Commission Rate</label>
                        <div class="fw-bold" style="font-size: 1.5rem;">{{ $commission->commission_rate }}%</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted">Subscription Amount</label>
                        <div class="fw-bold">₦{{ number_format($commission->subscription_amount, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted">Type</label>
                        <div>
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
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-0">
                        <label class="small text-muted">Notes</label>
                        <div class="text-muted">{{ $commission->notes ?? 'No notes' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BRM Information -->
        <div class="sa-card mb-3">
            <h6 class="fw-bold mb-3">BRM Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Name</label>
                        <div class="fw-600">{{ $commission->brm->name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Email</label>
                        <div>{{ $commission->brm->email }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Phone</label>
                        <div>{{ $commission->brm->phone ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Status</label>
                        <div>
                            <span class="badge rounded-pill {{ $commission->brm->status ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $commission->brm->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <a href="{{ route('superadmin.commissions.brm-summary', $commission->brm->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View All Commissions
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="sa-card mb-3">
            <h6 class="fw-bold mb-3">Customer Information</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Business Name</label>
                        <div class="fw-600">{{ $commission->user->business_name ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Contact Person</label>
                        <div class="fw-600">{{ $commission->user->first_name }} {{ $commission->user->surname }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Email</label>
                        <div>{{ $commission->user->email }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Phone</label>
                        <div>{{ $commission->user->phone ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Information -->
        @if($commission->userSubscription)
            <div class="sa-card">
                <h6 class="fw-bold mb-3">Subscription Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Plan</label>
                            <div class="fw-600">{{ $commission->userSubscription->subscriptionPlan->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Duration</label>
                            <div>{{ $commission->userSubscription->duration_months }} months</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Start Date</label>
                            <div>{{ $commission->userSubscription->start_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">End Date</label>
                            <div>{{ $commission->userSubscription->end_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div>
                            <label class="small text-muted">Status</label>
                            <div>
                                <span class="badge rounded-pill {{ $commission->userSubscription->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ ucfirst($commission->userSubscription->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar - Status & Actions -->
    <div class="col-lg-4">
        <div class="sa-card sticky-top" style="top: 20px;">
            <h6 class="fw-bold mb-3">Commission Status</h6>
            
            <div class="mb-4">
                <span class="badge rounded-pill" style="padding: 8px 12px; font-size: 0.9rem; background:
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
            </div>

            @if($commission->status === 'pending')
                <div class="d-grid gap-2 mb-3">
                    <form method="POST" action="{{ route('superadmin.commissions.approve', $commission->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Approve this commission?')">
                            <i class="bi bi-check-circle me-2"></i> Approve Commission
                        </button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.commissions.reject', $commission->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Reject this commission?')">
                            <i class="bi bi-x-circle me-2"></i> Reject
                        </button>
                    </form>
                </div>
                <div class="alert alert-warning alert-sm">
                    <small><strong>Note:</strong> Once approved, commission will be added to BRM's wallet.</small>
                </div>
            @elseif($commission->status === 'approved')
                <div class="d-grid gap-2 mb-3">
                    <form method="POST" action="{{ route('superadmin.commissions.mark-paid', $commission->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm w-100" onclick="return confirm('Mark as paid?')">
                            <i class="bi bi-check2 me-2"></i> Mark as Paid
                        </button>
                    </form>
                </div>
                <div class="alert alert-info alert-sm">
                    <small><strong>Note:</strong> Mark as paid to complete this commission.</small>
                </div>
            @elseif($commission->status === 'paid')
                <div class="alert alert-success alert-sm">
                    <small><strong>Paid at:</strong> {{ $commission->paid_at->format('M d, Y h:i A') }}</small>
                </div>
            @else
                <div class="alert alert-danger alert-sm">
                    <small><strong>Rejected on:</strong> {{ $commission->updated_at->format('M d, Y h:i A') }}</small>
                </div>
            @endif

            <hr>

            <div class="mb-3">
                <label class="small text-muted d-block mb-2">Timeline</label>
                <div class="small">
                    <div class="mb-2">
                        <strong>Created:</strong><br>
                        {{ $commission->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div>
                        <strong>Last Updated:</strong><br>
                        {{ $commission->updated_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
