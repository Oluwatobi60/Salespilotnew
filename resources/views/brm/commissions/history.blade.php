@extends('brm.layouts.layout')

@section('brms_page_title')
Commission History
@endsection

@section('brms_page_content')
<link rel="stylesheet" href="{{ asset('brm_asset/css/history.css') }}">

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


@endsection
