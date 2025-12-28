@extends('manager.layouts.layout')
@section('manager_page_title', 'Manager Dashboard')

@section('manager_layout_content')

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

   <!-- Welcome Section -->
            <div class="welcome-section">
              <h1 class="welcome-title">Welcome to SalesPilot</h1>
              <p class="welcome-subtitle">Your comprehensive inventory management solution</p>
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
                  <a href="#" class="quick-action-btn">
                    <i class="bi bi-plus-circle me-2"></i>New Sale
                  </a>
                   <a href="{{--  {{ route('manager.staff') }}  --}}" class="quick-action-btn">
                    <i class="bi bi-person-plus me-2"></i>Add New Staff
                  </a>
                  <a href="{{--  {{ route('manager.completed_sales') }}  --}}" class="quick-action-btn">
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
                          <td>${{ number_format($sale->total, 2) }}</td>
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
              <div class="col-lg-6">
                <div class="dashboard-card">
                  <h5 class="mb-3">Sales Overview</h5>
                  <canvas id="salesOverviewChart" height="300"></canvas>
                </div>
              </div>

              <div class="col-lg-6">
                <div class="dashboard-card">
                  <h5 class="mb-3">Top Products</h5>
                  <canvas id="topProductsChart" height="300"></canvas>
                </div>
              </div>
            </div>


 <script src="{{ asset('manager_asset/js/dashboard_manager.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Sales Overview Chart (Bar) - dynamic data
  var salesLabels = @json(array_column($salesOverview ?? [], 'date'));
  var salesData = @json(array_column($salesOverview ?? [], 'gross_sales'));
  var ctxSales = document.getElementById('salesOverviewChart').getContext('2d');
  var salesOverviewChart = new Chart(ctxSales, {
    type: 'bar',
    data: {
      labels: salesLabels,
      datasets: [{
        label: 'Gross Sales',
        data: salesData,
        backgroundColor: 'rgba(13,110,253,0.7)',
        borderRadius: 6,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        title: { display: false }
      },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true }
      }
    }
  });

  // Top Products Chart (Pie) - dynamic data
  var productLabels = @json(array_column($topProducts ?? [], 'name'));
  var productData = @json(array_column($topProducts ?? [], 'units_sold'));
  var ctxProducts = document.getElementById('topProductsChart').getContext('2d');
  var topProductsChart = new Chart(ctxProducts, {
    type: 'pie',
    data: {
      labels: productLabels,
      datasets: [{
        label: 'Units Sold',
        data: productData,
        backgroundColor: [
          'rgba(13,110,253,0.7)',
          'rgba(25,135,84,0.7)',
          'rgba(255,193,7,0.7)',
          'rgba(220,53,69,0.7)',
          'rgba(111,66,193,0.7)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        title: { display: false }
      }
    }
  });
});
</script>
@endsection
