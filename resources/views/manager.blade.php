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
              @if(!auth()->user()->addby)
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
              @else
              <!-- Branch Manager Dashboard UI -->
              <div class="row">
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                  <div class="dashboard-card stat-card" style="background: #ffffff; border-left: 4px solid #17a2b8;">
                    <div class="stat-icon" style="background: rgba(23, 162, 184, 0.1); color: #17a2b8;">
                      <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div class="stat-number text-info">{{ number_format($totalItemsSold ?? 0) }}</div>
                    <div class="stat-label">Branch Items Sold</div>
                  </div>
                </div>

                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                  <div class="dashboard-card stat-card" style="background: #ffffff; border-left: 4px solid #ffc107;">
                    <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                      <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stat-number text-warning">{{ number_format($numberOfSales ?? 0) }}</div>
                    <div class="stat-label">Branch Transactions</div>
                  </div>
                </div>

                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12">
                   <div class="dashboard-card stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(56, 239, 125, 0.3);">
                     <h5 style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; opacity: 0.9;">
                       <i class="bi bi-bullseye me-1"></i> Branch Daily Target
                     </h5>
                     @php
                        // Calculate a dynamic target for demonstration
                        $target = 100000;
                        $sales = $grossSalesAfterDiscount ?? 0;
                        if($sales > $target) {
                           $target = ceil($sales / 50000) * 50000;
                        }
                        $progress = min(100, $target > 0 ? ($sales / $target) * 100 : 0);
                     @endphp
                     <div class="d-flex justify-content-between align-items-end mb-2">
                       <div style="font-size: 26px; font-weight: bold;">₦{{ number_format($sales, 2) }}</div>
                       <div style="font-size: 13px; font-weight: 500;">Target: ₦{{ number_format($target, 0) }}</div>
                     </div>
                     <div class="progress" style="height: 8px; background-color: rgba(255,255,255,0.2); border-radius: 4px;">
                       <div class="progress-bar bg-white progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%; border-radius: 4px;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                     </div>
                     <div style="font-size: 12px; margin-top: 8px; font-weight: 500;">
                       <i class="bi bi-graph-up me-1"></i>{{ number_format($progress, 1) }}% completed
                     </div>
                   </div>
                </div>
              </div>
              @endif


              <!-- Quick Actions and Recent Activity Row -->
            <div class="row">
              @if(!auth()->user()->addby)
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
              @else
              <!-- Branch Manager Actions & Table -->
              <div class="col-lg-12 mb-4">
                 <div class="dashboard-card" style="background: linear-gradient(to right, #ffffff, #f8f9fa); border: 1px solid #e9ecef; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <h5 class="mb-3" style="color: #495057; font-weight: 600;"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Branch Shortcuts</h5>
                    <div class="d-flex gap-3 flex-wrap">
                      <a href="{{ route('manager.sell_product') }}" class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="bi bi-cart-plus me-2"></i>Start New Sale
                      </a>
                      <a href="{{ route('all_items') }}" class="btn btn-outline-info px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="bi bi-box-seam me-2"></i>Check Inventory
                      </a>
                      <a href="{{ route('manager.completed_sales') }}" class="btn btn-outline-success px-4 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="bi bi-journal-text me-2"></i>Daily Reports
                      </a>
                    </div>
                 </div>
              </div>

              <div class="col-lg-12">
                <div class="dashboard-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-radius: 12px; overflow: hidden; border: none; padding: 0;">
                  <div style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); padding: 18px 25px; color: white;">
                    <h5 class="mb-0" style="font-weight: 600; letter-spacing: 0.5px;"><i class="bi bi-clock-history me-2"></i>Branch Recent Activity</h5>
                  </div>
                  <div class="table-responsive p-4" style="background: #ffffff;">
                    <table class="table table-hover table-borderless align-middle mb-0">
                      <thead style="border-bottom: 2px solid #f0f2f5;">
                        <tr>
                          <th class="text-muted pb-3" style="font-weight: 600; text-transform: uppercase; font-size: 12px;">Receipt ID</th>
                          <th class="text-muted pb-3" style="font-weight: 600; text-transform: uppercase; font-size: 12px;">Customer</th>
                          <th class="text-muted pb-3" style="font-weight: 600; text-transform: uppercase; font-size: 12px;">Amount</th>
                          <th class="text-muted pb-3" style="font-weight: 600; text-transform: uppercase; font-size: 12px;">Date</th>
                          <th class="text-muted pb-3" style="font-weight: 600; text-transform: uppercase; font-size: 12px;">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($recentSales as $sale)
                        <tr style="border-bottom: 1px solid #f8f9fa; transition: background 0.3s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                          <td class="py-3"><span style="font-family: monospace; font-weight: 600; color: #495057;">#{{ $sale->receipt_number }}</span></td>
                          <td class="py-3">
                            <div class="d-flex align-items-center">
                               <div class="bg-light rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 36px; height: 36px; border: 1px solid #e9ecef;">
                                 <i class="bi bi-person text-secondary"></i>
                               </div>
                               <span style="font-weight: 500; color: #343a40;">{{ $sale->customer_name ?? 'Walk-in' }}</span>
                            </div>
                          </td>
                          <td class="py-3" style="font-weight: 700; color: #198754;">₦{{ number_format($sale->total, 2) }}</td>
                          <td class="py-3"><span class="text-muted" style="font-size: 0.9em;"><i class="bi bi-calendar-event me-2 text-primary opacity-50"></i>{{ $sale->created_at->diffForHumans() }}</span></td>
                          <td class="py-3">
                            @if($sale->status === 'completed')
                              <span class="badge" style="background: rgba(40,167,69,0.1); color: #28a745; border: 1px solid rgba(40,167,69,0.2); padding: 8px 12px; border-radius: 6px; font-weight: 600;">Completed</span>
                            @else
                              <span class="badge" style="background: rgba(255,193,7,0.1); color: #ffc107; border: 1px solid rgba(255,193,7,0.2); padding: 8px 12px; border-radius: 6px; font-weight: 600;">{{ ucfirst($sale->status) }}</span>
                            @endif
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox text-muted mb-3 d-block" style="font-size: 40px;"></i>
                            <span class="text-muted" style="font-weight: 500;">No recent sales found for this branch.</span>
                          </td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>

                      <!-- Pagination and Stats -->
                    <div class="row mt-4 align-items-center">
                        <div class="col-md-6">
                            <span class="text-muted" style="font-size: 0.9em; font-weight: 500;">
                                Showing {{  $recentSales->firstItem() ?? 0 }} to {{ $recentSales->lastItem() ?? 0 }} of {{ $recentSales->total() }} entries
                            </span>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            {{ $recentSales->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                  </div>
                </div>
              </div>
              @endif
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
  const isDarkMode = document.documentElement.classList.contains('dark-mode');
  const chartTextColor = isDarkMode ? '#ffffff' : '#6c757d';
  const chartGridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

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
            color: chartTextColor
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: chartGridColor,
            drawBorder: false
          },
          ticks: {
            font: {
              size: 11
            },
            color: chartTextColor,
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
            color: chartGridColor,
            drawBorder: false
          },
          ticks: {
            font: {
              size: 11
            },
            color: chartTextColor,
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
            color: chartTextColor,
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
