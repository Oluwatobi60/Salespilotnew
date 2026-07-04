@extends('manager.layouts.layout')
@section('manager_page_title', 'Manager Dashboard')

@section('manager_layout_content')

<style>
/* Enhanced Chart Card Styles */
.chart-card-enhanced {
  background: #ffffff;
  border: 1px solid #e8eaed;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  transition: all 0.3s ease;
  height: 100%;
}

.chart-card-enhanced:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 2px solid #f0f2f5;
}

.chart-title {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
  display: flex;
  align-items: center;
}

.chart-title i {
  color: {{ primary_color() }};
  font-size: 20px;
}

.chart-subtitle {
  font-size: 13px;
  color: #6c757d;
  margin: 4px 0 0 0;
}

.chart-period-badge {
  background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }});
  color: white;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.3px;
  box-shadow: 0 2px 6px rgba(13, 110, 253, 0.3);
}

.chart-count-badge {
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.3px;
  box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
}

/* Responsive adjustments */
@media (max-width: 991px) {
  .chart-card-enhanced {
    margin-bottom: 20px;
  }

  .chart-header {
    flex-direction: column;
    gap: 12px;
  }

  .chart-period-badge,
  .chart-count-badge {
    align-self: flex-start;
  }
}
</style>

   @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Subscription Expiry Alert -->
    @if(isset($subscriptionAlert))
        @php
            $alertClass = 'alert-danger';
            $icon = 'mdi-alert-circle';
            $title = 'Subscription Expired!';

            if ($subscriptionAlert['days_remaining'] > 2) {
                $alertClass = 'alert-warning';
                $icon = 'mdi-alert-outline';
                $title = 'Subscription Expiring Soon';
            } elseif ($subscriptionAlert['days_remaining'] > 0) {
                $alertClass = 'alert-danger';
                $icon = 'mdi-alert';
                $title = 'Urgent: Subscription Expiring';
            }
        @endphp

        <div class="alert {{ $alertClass }} alert-dismissible fade show" role="alert" style="border-left: 4px solid {{ $subscriptionAlert['is_urgent'] ? '#dc3545' : '#ffc107' }};">
            <div class="d-flex align-items-start">
                <i class="mdi {{ $icon }} me-3" style="font-size: 24px;"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">{{ $title }}</h5>
                    @if($subscriptionAlert['is_expired'])
                        <p class="mb-2">Your <strong>{{ $subscriptionAlert['plan_name'] }}</strong> subscription has expired on {{ $subscriptionAlert['end_date'] }}.</p>
                        <p class="mb-0">Please renew your subscription to continue using {{ app_name() }} without interruption.</p>
                    @else
                        <p class="mb-2">Your <strong>{{ $subscriptionAlert['plan_name'] }}</strong> subscription will expire in <strong>{{ $subscriptionAlert['days_remaining'] }} {{ $subscriptionAlert['days_remaining'] == 1 ? 'day' : 'days' }}</strong> on {{ $subscriptionAlert['end_date'] }}.</p>
                        <p class="mb-0">Renew now to avoid service interruption and continue enjoying all features.</p>
                    @endif
                    <div class="mt-3">
                        <a href="{{ url('/signup/plan_pricing') }}" class="btn btn-sm {{ $subscriptionAlert['is_urgent'] ? 'btn-danger' : 'btn-warning' }}">
                            <i class="mdi mdi-refresh me-1"></i>Renew Subscription
                        </a>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

   <!-- Welcome Section -->
            @php
                $managerFirstName = trim(Auth::user()->firstname ?? Auth::user()->first_name ?? '');
                $managerSurname   = trim(Auth::user()->surname ?? '');
                $managerDisplay   = $managerFirstName ?: trim(($managerFirstName . ' ' . $managerSurname) ?: (Auth::user()->business_name ?? 'Manager'));
                $hour = now()->hour;
                $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                $currentDate = now()->format('l, F j, Y');
            @endphp
            <div class="welcome-section d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div>
                <h1 class="welcome-title mb-1">{{ $greeting }}, {{ $managerDisplay }}! 👋</h1>
                <p class="welcome-subtitle mb-0">Your comprehensive inventory management solution</p>
              </div>
              <div class="text-end">
                <span class="badge rounded-pill px-3 py-2" style="background: linear-gradient(135deg, {{ primary_color() }}, {{ secondary_color() }}); font-size: 0.85rem; font-weight: 500; letter-spacing: 0.3px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                  <i class="bi bi-calendar3 me-1"></i>{{ $currentDate }}
                </span>
              </div>
            </div>


                <!-- Dashboard Stats Row -->
             <div class="d-flex align-items-center justify-content-end gap-3 flex-wrap mb-3">
                <div class="btn-group btn-group-sm" role="group" aria-label="Quick time filters">
                  <button type="button" class="btn btn-outline-primary timeframe-btn {{ (request('start_date') == date('Y-m-d') && request('end_date') == date('Y-m-d')) ? 'active' : '' }}" data-range="today">Today</button>
                  <button type="button" class="btn btn-outline-primary timeframe-btn {{ (request('start_date') && request('end_date') && \Carbon\Carbon::parse(request('start_date'))->isSameDay(\Carbon\Carbon::now()->startOfWeek()) && \Carbon\Carbon::parse(request('end_date'))->isSameDay(\Carbon\Carbon::now()->endOfWeek())) ? 'active' : '' }}" data-range="week">This Week</button>
                  <button type="button" class="btn btn-outline-primary timeframe-btn {{ (request('start_date') && request('end_date') && \Carbon\Carbon::parse(request('start_date'))->isSameDay(\Carbon\Carbon::now()->startOfMonth()) && \Carbon\Carbon::parse(request('end_date'))->isSameDay(\Carbon\Carbon::now()->endOfMonth())) ? 'active' : '' }}" data-range="month">This Month</button>
                  <button type="button" class="btn btn-outline-primary timeframe-btn {{ (request('start_date') && request('end_date') && \Carbon\Carbon::parse(request('start_date'))->isSameDay(\Carbon\Carbon::now()->startOfYear()) && \Carbon\Carbon::parse(request('end_date'))->isSameDay(\Carbon\Carbon::now()->endOfYear())) ? 'active' : '' }}" data-range="year">This Year</button>
                </div>
                <div class="d-flex align-items-center gap-1" style="font-size: 0.875rem;">
                  <span class="text-muted">From:</span>
                  <input type="text" class="form-control form-control-sm date-picker" id="startDate" placeholder="YYYY-MM-DD" style="width: 110px;" value="{{ $startDate ?? request('start_date') ?? '' }}">
                  <span class="text-muted">To:</span>
                  <input type="text" class="form-control form-control-sm date-picker" id="endDate" placeholder="YYYY-MM-DD" style="width: 110px;" value="{{ $endDate ?? request('end_date') ?? '' }}">
                  <button class="btn btn-sm btn-primary" type="button" id="applyCustomRange" title="Apply date range">
                    <i class="bi bi-search"></i> Go
                  </button>

                </div>

              </div>


              <!-- Stats Cards -->
                   <div class="row">
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-box-seam-fill"></i>
                  </div>
                  <div class="stat-number">{{ number_format($totalItemsSold ?? 0) }}</div>
                  <div class="stat-label">Items Sold</div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                  </div>
                  <div class="stat-number">{{ number_format($numberOfSales ?? 0) }}</div>
                  <div class="stat-label">Number of Sales</div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                  </div>
                  <div class="stat-number">₦{{ number_format($grossSales ?? 0, 2) }}</div>
                  <div class="stat-label">Gross Sales</div>
                </div>
              </div>

              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                  </div>
                  <div class="stat-number">₦{{ number_format($grossProfit ?? 0, 2) }}</div>
                  <div class="stat-label">Gross Profit</div>
                </div>
              </div>
            </div>


              <!-- Quick Actions and Recent Activity Row -->
            <div class="row">
              <div class="col-lg-2">
                <div class="dashboard-card">
                  <h5 class="mb-3">Quick Actions</h5>
                  <button type="button" class="quick-action-btn" id="addItemQuickAction" style="border: none; width: 100%; text-align: center;">
                    <i class="bi bi-box me-2"></i>Add Item
                  </button>
                  <a href="{{ route('manager.sell_product') }}" class="quick-action-btn">
                    <i class="bi bi-plus-circle me-2"></i>New Sale
                  </a>
                   <a href="{{ route('manager.staff') }} " class="quick-action-btn">
                    <i class="bi bi-person-plus me-2"></i>Add New Staff
                  </a>
                  <a href="{{ route('manager.completed_sales') }}" class="quick-action-btn">
                    <i class="bi bi-graph-up me-2"></i>View Reports
                  </a>
                </div>
              </div>
              <div class="col-lg-10">
                <div class="dashboard-card">
                  <h5 class="mb-3">Recent Sales Activity</h5>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Reciept ID</th>
                          <th>Customer</th>
                          <th>Amount</th>
                          <th>Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($recentSales as $sale)
                        <tr>
                          <td>#{{ $sale->receipt_number }}</td>
                          <td>{{ $sale->customer_name ?? 'N/A' }}</td>
                          <td>₦{{ number_format($sale->total, 2) }}</td>
                          <td>{{ $sale->created_at->diffForHumans() }}</td>
                          <td>
                            @if($sale->status === 'completed')
                              <span class="badge bg-success">Completed</span>
                            @else
                              <span class="badge bg-warning text-dark">{{ ucfirst($sale->status) }}</span>
                            @endif
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center">No recent sales found.</td>
                        </tr>
                        @endforelse

                      </tbody>
                    </table>

                      <!-- Pagination and Stats -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <span class="text-muted">
                                Showing {{  $recentSales->firstItem() ?? 0 }} to {{ $recentSales->lastItem() ?? 0 }} of {{ $recentSales->total() }} entries
                            </span>
                        </div>
                        <div class="col-md-6">
                            {{ $recentSales->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Charts Row -->
            <div class="row">
              <div class="col-lg-7">
                <div class="dashboard-card chart-card-enhanced">
                  <div class="chart-header">
                    <div>
                      <h5 class="chart-title"><i class="bi bi-graph-up-arrow me-2"></i>Sales Overview</h5>
                      <p class="chart-subtitle">Daily gross sales performance</p>
                    </div>
                    <div class="chart-period-badge">Last 7 Days</div>
                  </div>
                  <div style="position: relative; height: 320px;">
                    <canvas id="salesOverviewChart"></canvas>
                  </div>
                </div>
              </div>

              <div class="col-lg-5">
                <div class="dashboard-card chart-card-enhanced">
                  <div class="chart-header">
                    <div>
                      <h5 class="chart-title"><i class="bi bi-trophy-fill me-2"></i>Top Products</h5>
                      <p class="chart-subtitle">Best selling items by quantity</p>
                    </div>
                    <span class="chart-count-badge">Top 5</span>
                  </div>
                  <div style="position: relative; height: 320px;">
                    <canvas id="topProductsChart"></canvas>
                  </div>
                </div>
              </div>
            </div>


 <script src="{{ asset('manager_asset/js/dashboard_manager.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Sales Overview Chart (Bar) - Modern Gradient Design
  var salesLabels = @json(array_column($salesOverview ?? [], 'date'));
  var salesData = @json(array_column($salesOverview ?? [], 'gross_sales'));
  var ctxSales = document.getElementById('salesOverviewChart').getContext('2d');

  // Create gradient
  var gradientSales = ctxSales.createLinearGradient(0, 0, 0, 400);
  gradientSales.addColorStop(0, 'rgba(13, 110, 253, 0.9)');
  gradientSales.addColorStop(1, 'rgba(13, 110, 253, 0.3)');

  var salesOverviewChart = new Chart(ctxSales, {
    type: 'bar',
    data: {
      labels: salesLabels,
      datasets: [{
        label: 'Gross Sales',
        data: salesData,
        backgroundColor: gradientSales,
        borderColor: 'rgba(13, 110, 253, 1)',
        borderWidth: 2,
        borderRadius: 8,
        borderSkipped: false,
        hoverBackgroundColor: 'rgba(13, 110, 253, 1)',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 13,
            weight: 'bold'
          },
          bodyFont: {
            size: 14
          },
          callbacks: {
            label: function(context) {
              return 'Sales: ₦' + context.parsed.y.toLocaleString('en-NG', { minimumFractionDigits: 2 });
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false,
            drawBorder: false
          },
          ticks: {
            font: {
              size: 11,
              weight: '500'
            },
            color: '#6c757d'
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
            drawBorder: false
          },
          ticks: {
            font: {
              size: 11
            },
            color: '#6c757d',
            callback: function(value) {
              return '₦' + value.toLocaleString('en-NG');
            }
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      },
      animation: {
        duration: 1000,
        easing: 'easeInOutQuart'
      }
    }
  });

  // Top Products Chart (Horizontal Bar) - Modern Design
  var productLabels = @json(array_column($topProducts ?? [], 'name'));
  var productData = @json(array_column($topProducts ?? [], 'units_sold'));
  var ctxProducts = document.getElementById('topProductsChart').getContext('2d');

  // Modern color palette
  var productColors = [
    { bg: 'rgba(13, 110, 253, 0.8)', border: 'rgba(13, 110, 253, 1)' },
    { bg: 'rgba(25, 135, 84, 0.8)', border: 'rgba(25, 135, 84, 1)' },
    { bg: 'rgba(255, 193, 7, 0.8)', border: 'rgba(255, 193, 7, 1)' },
    { bg: 'rgba(220, 53, 69, 0.8)', border: 'rgba(220, 53, 69, 1)' },
    { bg: 'rgba(111, 66, 193, 0.8)', border: 'rgba(111, 66, 193, 1)' }
  ];

  var topProductsChart = new Chart(ctxProducts, {
    type: 'bar',
    data: {
      labels: productLabels,
      datasets: [{
        label: 'Units Sold',
        data: productData,
        backgroundColor: productColors.map(c => c.bg),
        borderColor: productColors.map(c => c.border),
        borderWidth: 2,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 13,
            weight: 'bold'
          },
          bodyFont: {
            size: 14
          },
          callbacks: {
            label: function(context) {
              return 'Sold: ' + context.parsed.x.toLocaleString() + ' units';
            }
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
            drawBorder: false
          },
          ticks: {
            font: {
              size: 11
            },
            color: '#6c757d',
            callback: function(value) {
              return value.toLocaleString();
            }
          }
        },
        y: {
          grid: {
            display: false,
            drawBorder: false
          },
          ticks: {
            font: {
              size: 11,
              weight: '500'
            },
            color: '#495057',
            callback: function(value, index) {
              var label = this.getLabelForValue(value);
              return label.length > 20 ? label.substring(0, 20) + '...' : label;
            }
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      },
      animation: {
        duration: 1000,
        easing: 'easeInOutQuart'
      }
    }
  });
});
</script>
@endsection
