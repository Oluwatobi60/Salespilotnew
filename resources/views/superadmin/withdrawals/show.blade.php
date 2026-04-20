@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Withdrawal #' . $withdrawal->id)

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Withdrawal #{{ $withdrawal->id }}</h5>
        <p class="text-muted small mb-0">{{ $withdrawal->created_at->format('F d, Y \a\t h:i A') }}</p>
    </div>
    <a href="{{ route('superadmin.withdrawals') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-3">
    <!-- Main Details -->
    <div class="col-lg-8">
        <div class="sa-card mb-3">
            <h6 class="fw-bold mb-3">Withdrawal Request Details</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted">Withdrawal Amount</label>
                        <div class="fw-bold" style="font-size: 1.8rem; color: #667eea;">
                            ₦{{ number_format($withdrawal->amount, 2) }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="small text-muted">Request Date</label>
                        <div class="fw-bold">{{ $withdrawal->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-0">
                        <label class="small text-muted">Reason (Optional)</label>
                        <div class="text-muted">{{ $withdrawal->reason ?? 'No reason provided' }}</div>
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
                        <div class="fw-600">{{ $withdrawal->brm->name }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Email</label>
                        <div>{{ $withdrawal->brm->email }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Phone</label>
                        <div>{{ $withdrawal->brm->phone ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div>
                        <label class="small text-muted">Status</label>
                        <div>
                            <span class="badge rounded-pill {{ $withdrawal->brm->status ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ $withdrawal->brm->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div>
                        <label class="small text-muted">Current Wallet Balance</label>
                        <div class="fw-bold" style="font-size: 1.2rem; color: #10b981;">
                            ₦{{ number_format($withdrawal->brm->wallet_balance ?? 0, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Account Information -->
        <div class="sa-card mb-3">
            <h6 class="fw-bold mb-3">Bank Account Details</h6>
            @if($withdrawal->bankAccount)
                <div class="row g-3">
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Bank Name</label>
                            <div class="fw-600">{{ $withdrawal->bankAccount->bank_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Bank Code</label>
                            <div class="fw-600">{{ $withdrawal->bankAccount->bank_code }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Account Number</label>
                            <div class="fw-600 font-monospace">{{ $withdrawal->bankAccount->account_number }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <label class="small text-muted">Account Name</label>
                            <div class="fw-600">{{ $withdrawal->bankAccount->account_name }}</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <small>No bank account associated with this withdrawal.</small>
                </div>
            @endif
        </div>

        <!-- Payment Timeline -->
        <div class="sa-card">
            <h6 class="fw-bold mb-3">Payment Timeline</h6>
            <div class="small">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex gap-2">
                        <div style="min-width: 30px; text-align: center;">
                            <i class="bi bi-clock-history" style="color: #6c757d; font-size: 1.2rem;"></i>
                        </div>
                        <div>
                            <strong>Withdrawal Requested</strong><br>
                            <span class="text-muted">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                @if($withdrawal->approved_at)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex gap-2">
                            <div style="min-width: 30px; text-align: center;">
                                <i class="bi bi-check-circle-fill" style="color: #10b981; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <strong>Withdrawal Approved</strong><br>
                                <span class="text-muted">{{ $withdrawal->approved_at->format('M d, Y h:i A') }}</span><br>
                                <span class="text-muted small">Amount deducted from wallet balance</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($withdrawal->paid_at)
                    <div>
                        <div class="d-flex gap-2">
                            <div style="min-width: 30px; text-align: center;">
                                <i class="bi bi-check2-circle" style="color: #0284c7; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <strong>Payment Completed</strong><br>
                                <span class="text-muted">{{ $withdrawal->paid_at->format('M d, Y h:i A') }}</span><br>
                                <span class="text-muted small">Bank transfer confirmed</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar - Status & Actions -->
    <div class="col-lg-4">
        <div class="sa-card sticky-top" style="top: 20px;">
            <h6 class="fw-bold mb-3">Withdrawal Status</h6>
            
            <div class="mb-4">
                <span class="badge rounded-pill" style="padding: 8px 12px; font-size: 0.9rem; background:
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
            </div>

            @if($withdrawal->status === 'pending')
                <div class="d-grid gap-2 mb-3">
                    <form method="POST" action="{{ route('superadmin.withdrawals.approve', $withdrawal->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Approve this withdrawal request?\n\nThis will deduct ₦{{ number_format($withdrawal->amount, 2) }} from the BRM\'s wallet balance.')">
                            <i class="bi bi-check-circle me-2"></i> Approve
                        </button>
                    </form>
                    <form method="POST" action="{{ route('superadmin.withdrawals.reject', $withdrawal->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Reject this withdrawal request?\n\nThe funds will remain in the BRM\'s wallet.')">
                            <i class="bi bi-x-circle me-2"></i> Reject
                        </button>
                    </form>
                </div>
                <div class="alert alert-warning alert-sm">
                    <small><strong>Note:</strong> Approving will deduct the amount from the BRM's wallet balance. Ensure the bank account details are correct before approving.</small>
                </div>
            @elseif($withdrawal->status === 'approved')
                <div class="d-grid gap-2 mb-3">
                    <form method="POST" action="{{ route('superadmin.withdrawals.mark-paid', $withdrawal->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm w-100" onclick="return confirm('Mark withdrawal as paid?\n\nThis confirms that the bank transfer has been completed.')">
                            <i class="bi bi-check2 me-2"></i> Mark as Paid
                        </button>
                    </form>
                </div>
                <div class="alert alert-info alert-sm">
                    <small><strong>Note:</strong> Click "Mark as Paid" after you have confirmed the bank transfer to the BRM's account.</small>
                </div>
            @elseif($withdrawal->status === 'paid')
                <div class="alert alert-success alert-sm">
                    <small><strong>Payment Completed:</strong><br>{{ $withdrawal->paid_at->format('M d, Y h:i A') }}</small>
                </div>
            @else
                <div class="alert alert-danger alert-sm">
                    <small><strong>Rejected on:</strong><br>{{ $withdrawal->updated_at->format('M d, Y h:i A') }}</small>
                </div>
            @endif

            <hr>

            <div class="mb-3">
                <label class="small text-muted d-block mb-2">Quick Info</label>
                <div class="small">
                    <div class="mb-2">
                        <strong>Total Withdrawn:</strong><br>
                        ₦{{ number_format($withdrawal->brm->total_withdrawn ?? 0, 2) }}
                    </div>
                    <div>
                        <strong>Total Earned:</strong><br>
                        ₦{{ number_format($withdrawal->brm->total_earned ?? 0, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
