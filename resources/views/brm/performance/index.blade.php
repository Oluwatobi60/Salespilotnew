@extends('brm.layouts.layout')

@section('brms_page_title')
BRm Commissions
@endsection

@section('brms_page_content')
 <link rel="stylesheet" href="{{ asset('brm_asset/css/performance.css') }}">


          <!-- Page Header -->
          <div class="page-header">
            <h1><i class="bi bi-graph-up"></i> Performance Dashboard</h1>
            <p>Track your sales performance and achievements</p>
          </div>

          <!-- Performance Stats -->
          <div class="performance-stats">
            <div class="stat-card customers">
              <div class="stat-icon">
                <i class="bi bi-people-fill"></i>
              </div>
              <h3>{{ $totalCustomers }}</h3>
              <p>Total Customers</p>
              <div class="stat-trend positive">
                <i class="bi bi-arrow-up"></i> Customers acquired
              </div>
            </div>

            <div class="stat-card conversions">
              <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h3>{{ $thisMonthConversions }}</h3>
              <p>This Month Conversions</p>
              <div class="stat-trend {{ $conversionTrend >= 0 ? 'positive' : 'negative' }}">
                <i class="bi {{ $conversionTrend >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i> {{ abs($conversionTrend) }}% from last month
              </div>
            </div>

            <div class="stat-card revenue">
              <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
              </div>
              <h3>₦{{ number_format($thisMonthRevenue, 0) }}</h3>
              <p>Total Revenue Generated</p>
              <div class="stat-trend {{ $revenueTrend >= 0 ? 'positive' : 'negative' }}">
                <i class="bi {{ $revenueTrend >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }}"></i> {{ abs($revenueTrend) }}% from last month
              </div>
            </div>

            <div class="stat-card rate">
              <div class="stat-icon">
                <i class="bi bi-percent"></i>
              </div>
              <h3>{{ $conversionRate }}%</h3>
              <p>Conversion Rate</p>
              <div class="stat-trend positive">
                <i class="bi bi-target"></i> Customer conversion
              </div>
            </div>
          </div>

          <!-- Charts -->
          <div class="charts-row">
            <div class="chart-card">
              <h4><i class="bi bi-graph-up"></i> Monthly Performance</h4>
              <div class="chart-placeholder">
                <i class="bi bi-bar-chart"></i>
                <p>Sales performance chart visualization</p>
              </div>
            </div>

            <div class="chart-card">
              <h4><i class="bi bi-pie-chart"></i> Customer Distribution</h4>
              <div class="chart-placeholder">
                <i class="bi bi-pie-chart-fill"></i>
                <p>Customer plan distribution chart</p>
              </div>
            </div>
          </div>

          <div class="row">
            <!-- Leaderboard -->
            <div class="col-lg-6 mb-4">
              <div class="leaderboard-section">
                <h4><i class="bi bi-trophy-fill"></i> BRM Leaderboard</h4>
                <ul class="leaderboard-list">
                  @forelse($leaderboard as $index => $leader)
                    @php
                      $rankClass = 'regular';
                      if ($index === 0) $rankClass = 'gold';
                      elseif ($index === 1) $rankClass = 'silver';
                      elseif ($index === 2) $rankClass = 'bronze';
                    @endphp
                    <li class="leaderboard-item">
                      <div class="leaderboard-rank {{ $rankClass }}">{{ $index + 1 }}</div>
                      <div class="leaderboard-info">
                        <h6>{{ $leader['name'] }}</h6>
                        <p>{{ $leader['customers'] }} customers • {{ $leader['conversionRate'] }}% conversion</p>
                      </div>
                      <div class="leaderboard-value">₦{{ number_format($leader['totalCommission'], 0) }}</div>
                    </li>
                  @empty
                    <li class="leaderboard-item text-center text-muted py-4">
                      <p>No BRM data available</p>
                    </li>
                  @endforelse
                </ul>
              </div>
            </div>

            <!-- Achievements -->
            <div class="col-lg-6 mb-4">
              <div class="achievements-section">
                <h4><i class="bi bi-award-fill"></i> Achievements & Badges</h4>
                <div style="text-align: center; margin-bottom: 1rem; color: #6c757d;">
                  <small>{{ $achievements['unlockedCount'] }} of {{ $achievements['totalCount'] }} unlocked</small>
                </div>
                <div class="achievements-grid">
                  @forelse($achievements['list'] as $achievement)
                    <div class="achievement-badge {{ !$achievement['unlocked'] ? 'achievement-locked' : '' }}" 
                         style="background: {{ $achievement['unlocked'] ? $achievement['gradient'] : '#e9ecef' }}; {{ !$achievement['unlocked'] ? 'opacity: 0.6;' : '' }}">
                      <i class="bi {{ $achievement['icon'] }}"></i>
                      <h6>{{ $achievement['title'] }}</h6>
                      <p>{{ $achievement['description'] }}</p>
                    </div>
                  @empty
                    <p class="text-muted">No achievements yet</p>
                  @endforelse
                </div>
              </div>
            </div>
          </div>


        <!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              © 2026 SalesPilot. All rights reserved.
            </span>
          </div>
        </footer>

@endsection
