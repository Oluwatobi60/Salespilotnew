@extends('brm.layouts.layout')

@section('brms_page_title')
Commission History
@endsection

@section('brms_page_content')
<link rel="stylesheet" href="{{ asset('brm_asset/css/commission.css') }}">

<!-- Page Header -->
<div class="page-header">
  <h1><i class="bi bi-clock-history"></i> Commission History</h1>
  <p>Complete record of all your commissions</p>
</div>

<!-- Summary Stats -->
<div class="summary-stats">
  <div class="stat-box">
    <span class="label">Total Commissions</span>
    <span class="value">₦{{ number_format($totalCommissions, 2, '.', ',') }}</span>
  </div>
  <div class="stat-box">
    <span class="label">Pending</span>
    <span class="value pending">₦{{ number_format($pendingCommissions, 2, '.', ',') }}</span>
  </div>
  <div class="stat-box">
    <span class="label">Paid Out</span>
    <span class="value paid">₦{{ number_format($paidCommissions, 2, '.', ',') }}</span>
  </div>
</div>

<!-- Filters -->
<div class="filter-section">
  <form method="GET" class="filter-form">
    <div class="filter-group">
      <select name="status">
        <option value="">All Status</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
      </select>

      <select name="type">
        <option value="">All Types</option>
        <option value="referral" {{ request('type') === 'referral' ? 'selected' : '' }}>Referral</option>
        <option value="renewal" {{ request('type') === 'renewal' ? 'selected' : '' }}>Renewal</option>
        <option value="upgrade" {{ request('type') === 'upgrade' ? 'selected' : '' }}>Upgrade</option>
      </select>

      <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From Date">
      <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="To Date">

      <button type="submit" class="btn-filter">
        <i class="bi bi-funnel"></i> Filter
      </button>
      <a href="{{ route('brm.commissions.history') }}" class="btn-reset">
        <i class="bi bi-arrow-counterclockwise"></i> Reset
      </a>
    </div>
  </form>
</div>

<!-- Commissions Table -->
<div class="history-table-section">
  <div class="table-responsive">
    <table class="commission-table">
      <thead>
        <tr>
          <th>Customer</th>
          <th>Plan</th>
          <th>Type</th>
          <th>Subscription Amount</th>
          <th>Commission Rate</th>
          <th>Commission</th>
          <th>Status</th>
          <th>Date</th>
          <th>Paid On</th>
        </tr>
      </thead>
      <tbody>
        @forelse($commissions as $commission)
          @php
            $customerName = $commission->user->business_name ?? ($commission->user->first_name . ' ' . $commission->user->surname);
            $planName = $commission->userSubscription?->subscriptionPlan?->name ?? 'N/A';
            $statusClass = strtolower($commission->status);
            $typeLabel = ucfirst(str_replace('_', ' ', $commission->commission_type));
          @endphp
          <tr>
            <td><strong>{{ $customerName }}</strong></td>
            <td>{{ $planName }}</td>
            <td>
              <span class="type-badge {{ str_replace('_', '-', $commission->commission_type) }}">
                {{ $typeLabel }}
              </span>
            </td>
            <td>₦{{ number_format($commission->subscription_amount, 2, '.', ',') }}</td>
            <td>{{ $commission->commission_rate }}%</td>
            <td>
              <span class="amount-badge">₦{{ number_format($commission->commission_amount, 2, '.', ',') }}</span>
            </td>
            <td>
              <span class="status-badge {{ $statusClass }}">{{ ucfirst($commission->status) }}</span>
            </td>
            <td>{{ $commission->created_at->format('M d, Y') }}</td>
            <td>
              @if($commission->paid_at)
                {{ $commission->paid_at->format('M d, Y') }}
              @else
                <span style="color: #999;">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" style="text-align: center; padding: 2rem; color: #999;">
              <i class="bi bi-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
              No commissions found matching your filters.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  @if($commissions->hasPages())
    <div class="pagination-section">
      {{ $commissions->links() }}
    </div>
  @endif
</div>

<style>
.summary-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-box {
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.stat-box .label {
  font-size: 0.9rem;
  color: #6c757d;
  font-weight: 500;
}

.stat-box .value {
  font-size: 1.6rem;
  font-weight: 700;
  color: #2c3e50;
}

.stat-box .value.pending {
  color: #ffc107;
}

.stat-box .value.paid {
  color: #28a745;
}

.filter-section {
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.filter-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.filter-form select,
.filter-form input {
  padding: 0.75rem;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  font-size: 0.9rem;
}

.btn-filter, .btn-reset {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
}

.btn-filter {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-filter:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-reset {
  background: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
}

.btn-reset:hover {
  background: #e9ecef;
}

.history-table-section {
  background: white;
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.table-responsive {
  overflow-x: auto;
}

.type-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
}

.type-badge.referral {
  background: #e7f3ff;
  color: #0066cc;
}

.type-badge.renewal {
  background: #fff3e0;
  color: #f57c00;
}

.type-badge.upgrade {
  background: #f3e5f5;
  color: #6a1b9a;
}

.amount-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
}

.status-badge {
  display: inline-block;
  padding: 0.35rem 0.85rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.approved {
  background: #d1ecf1;
  color: #0c5460;
}

.status-badge.paid {
  background: #d4edda;
  color: #155724;
}

.status-badge.rejected {
  background: #f8d7da;
  color: #721c24;
}

.pagination-section {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #dee2e6;
  text-align: center;
}

@media (max-width: 768px) {
  .filter-form {
    grid-template-columns: 1fr;
  }

  .table-responsive {
    font-size: 0.85rem;
  }

  .commission-table th,
  .commission-table td {
    padding: 0.75rem 0.5rem;
  }
}
</style>

@endsection
