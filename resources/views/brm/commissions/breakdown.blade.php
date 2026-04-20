@extends('brm.layouts.layout')

@section('brms_page_title')
Commission Breakdown
@endsection

@section('brms_page_content')
{{--  <link rel="stylesheet" href="{{ asset('brm_asset/css/commission.css') }}">  --}}
<link rel="stylesheet" href="{{ asset('brm_asset/css/breakdown.css') }}">

<!-- Page Header -->
<div class="page-header">
  <h1><i class="bi bi-bar-chart"></i> Commission Breakdown</h1>
  <p>Detailed analysis of your commission earnings</p>
</div>

<div class="breakdown-container">
  <!-- By Type -->
  <div class="breakdown-section">
    <h3><i class="bi bi-diagram-2"></i> By Commission Type</h3>
    <div class="breakdown-table">
      <table>
        <thead>
          <tr>
            <th>Type</th>
            <th>Amount</th>
            <th>Percentage</th>
          </tr>
        </thead>
        <tbody>
          @php
            $total = $byType['referral'] + $byType['renewal'] + $byType['upgrade'];
          @endphp
          <tr>
            <td><span class="type-badge referral">Referral</span></td>
            <td><strong>₦{{ number_format($byType['referral'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $total > 0 ? number_format(($byType['referral'] / $total) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $total > 0 ? ($byType['referral'] / $total) * 100 : 0 }}%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
              </div>
            </td>
          </tr>
          <tr>
            <td><span class="type-badge renewal">Renewal</span></td>
            <td><strong>₦{{ number_format($byType['renewal'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $total > 0 ? number_format(($byType['renewal'] / $total) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $total > 0 ? ($byType['renewal'] / $total) * 100 : 0 }}%; background: #ffc107;"></div>
              </div>
            </td>
          </tr>
          <tr>
            <td><span class="type-badge upgrade">Upgrade</span></td>
            <td><strong>₦{{ number_format($byType['upgrade'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $total > 0 ? number_format(($byType['upgrade'] / $total) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $total > 0 ? ($byType['upgrade'] / $total) * 100 : 0 }}%; background: #6a1b9a;"></div>
              </div>
            </td>
          </tr>
          <tr class="total-row">
            <td><strong>Total</strong></td>
            <td><strong>₦{{ number_format($total, 2, '.', ',') }}</strong></td>
            <td><strong>100%</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- By Status -->
  <div class="breakdown-section">
    <h3><i class="bi bi-check-circle"></i> By Status</h3>
    <div class="breakdown-table">
      <table>
        <thead>
          <tr>
            <th>Status</th>
            <th>Amount</th>
            <th>Percentage</th>
          </tr>
        </thead>
        <tbody>
          @php
            $statusTotal = $byStatus['pending'] + $byStatus['approved'] + $byStatus['paid'] + $byStatus['rejected'];
          @endphp
          <tr>
            <td><span class="status-badge pending">Pending</span></td>
            <td><strong>₦{{ number_format($byStatus['pending'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $statusTotal > 0 ? number_format(($byStatus['pending'] / $statusTotal) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $statusTotal > 0 ? ($byStatus['pending'] / $statusTotal) * 100 : 0 }}%; background: #ffc107;"></div>
              </div>
            </td>
          </tr>
          <tr>
            <td><span class="status-badge approved">Approved</span></td>
            <td><strong>₦{{ number_format($byStatus['approved'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $statusTotal > 0 ? number_format(($byStatus['approved'] / $statusTotal) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $statusTotal > 0 ? ($byStatus['approved'] / $statusTotal) * 100 : 0 }}%; background: #17a2b8;"></div>
              </div>
            </td>
          </tr>
          <tr>
            <td><span class="status-badge paid">Paid</span></td>
            <td><strong>₦{{ number_format($byStatus['paid'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $statusTotal > 0 ? number_format(($byStatus['paid'] / $statusTotal) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $statusTotal > 0 ? ($byStatus['paid'] / $statusTotal) * 100 : 0 }}%; background: #28a745;"></div>
              </div>
            </td>
          </tr>
          <tr>
            <td><span class="status-badge rejected">Rejected</span></td>
            <td><strong>₦{{ number_format($byStatus['rejected'], 2, '.', ',') }}</strong></td>
            <td>
              {{ $statusTotal > 0 ? number_format(($byStatus['rejected'] / $statusTotal) * 100, 1) : 0 }}%
              <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $statusTotal > 0 ? ($byStatus['rejected'] / $statusTotal) * 100 : 0 }}%; background: #dc3545;"></div>
              </div>
            </td>
          </tr>
          <tr class="total-row">
            <td><strong>Total</strong></td>
            <td><strong>₦{{ number_format($statusTotal, 2, '.', ',') }}</strong></td>
            <td><strong>100%</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- By Customer -->
  <div class="breakdown-section">
    <h3><i class="bi bi-person-fill"></i> Top Customers by Commission</h3>
    @if($commissionsByCustomer->count())
      <div class="breakdown-table">
        <table>
          <thead>
            <tr>
              <th>Customer</th>
              <th>Total Commission</th>
              <th>Number of Commissions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($commissionsByCustomer as $item)
              @php
                $customerName = $item->user->business_name ?? ($item->user->first_name . ' ' . $item->user->surname);
              @endphp
              <tr>
                <td><strong>{{ $customerName }}</strong></td>
                <td>₦{{ number_format($item->total_commission, 2, '.', ',') }}</td>
                <td>{{ $item->count }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div style="padding: 2rem; text-align: center; color: #999;">
        <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
        No commission data available
      </div>
    @endif
  </div>

  <!-- Monthly Trend -->
  <div class="breakdown-section">
    <h3><i class="bi bi-graph-up"></i> Monthly Trend (Last 12 Months)</h3>
    @if($commissionsByMonth->count())
      <div class="breakdown-table">
        <table>
          <thead>
            <tr>
              <th>Month</th>
              <th>Total Commission</th>
            </tr>
          </thead>
          <tbody>
            @foreach($commissionsByMonth as $item)
              <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $item->month)->format('F Y') }}</td>
                <td>
                  <strong>₦{{ number_format($item->total, 2, '.', ',') }}</strong>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div style="padding: 2rem; text-align: center; color: #999;">
        <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
        No monthly data available
      </div>
    @endif
  </div>
</div>



@endsection
