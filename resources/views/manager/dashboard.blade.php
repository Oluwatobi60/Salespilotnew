@extends('manager.layouts.layout')
@section('manager_page_title', 'Manager Dashboard')

@section('manager_layout_content')
   <!-- Welcome Section -->
            <div class="welcome-section">
              <h1 class="welcome-title">Welcome to SalesPilot</h1>
              <p class="welcome-subtitle">Your comprehensive inventory management solution</p>
            </div>


                <!-- Dashboard Stats Row -->
             <div class="d-flex align-items-center justify-content-end gap-3 flex-wrap mb-3">
                <div class="btn-group btn-group-sm" role="group" aria-label="Quick time filters">
                  <button type="button" class="btn btn-outline-primary timeframe-btn active" data-range="today">Today</button>
                  <button type="button" class="btn btn-outline-primary timeframe-btn" data-range="week">This Week</button>
                  <button type="button" class="btn btn-outline-primary timeframe-btn" data-range="month">This Month</button>
                   <button type="button" class="btn btn-outline-primary timeframe-btn" data-range="year">This Year</button>
                </div>
                <div class="d-flex align-items-center gap-1" style="font-size: 0.875rem;">
                  <span class="text-muted">From:</span>
                  <input type="text" class="form-control form-control-sm date-picker" id="startDate" placeholder="DD/MM/YYYY" style="width: 110px;">
                  <span class="text-muted">To:</span>
                  <input type="text" class="form-control form-control-sm date-picker" id="endDate" placeholder="DD/MM/YYYY" style="width: 110px;">
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
                  <div class="stat-number">2,567</div>
                  <div class="stat-label">Items Sold</div>
                </div>
              </div>
              
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                  </div>
                  <div class="stat-number">1,234</div>
                  <div class="stat-label">Number of Sales</div>
                </div>
              </div>
              
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                  </div>
                  <div class="stat-number">$85,420</div>
                  <div class="stat-label">Gross Sales</div>
                </div>
              </div>
              
              <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="dashboard-card stat-card">
                  <div class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                  </div>
                  <div class="stat-number">$34,168</div>
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
                   <a href="{{ route('manager.add_staff') }}" class="quick-action-btn">
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
                          <th>Sale ID</th>
                          <th>Customer</th>
                          <th>Amount</th>
                          <th>Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>#001</td>
                          <td>John Doe</td>
                          <td>$125.50</td>
                          <td>Today</td>
                          <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                        <tr>
                          <td>#002</td>
                          <td>Jane Smith</td>
                          <td>$89.99</td>
                          <td>Yesterday</td>
                          <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                        <tr>
                          <td>#003</td>
                          <td>Mike Johnson</td>
                          <td>$234.75</td>
                          <td>2 days ago</td>
                          <td><span class="badge bg-warning">Pending</span></td>
                        </tr>
                        <tr>
                          <td>#004</td>
                          <td>Sarah Wilson</td>
                          <td>$56.20</td>
                          <td>3 days ago</td>
                          <td><span class="badge bg-success">Completed</span></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>


            <!-- Charts Row -->
            <div class="row">
              <div class="col-lg-6">
                <div class="dashboard-card">
                  <h5 class="mb-3">Sales Overview</h5>
                  <div style="height: 300px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                    <div class="text-center">
                      <i class="bi bi-bar-chart" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                      <p>Sales Chart Placeholder</p>
                      <small>Integrate with Chart.js or similar</small>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-6">
                <div class="dashboard-card">
                  <h5 class="mb-3">Top Products</h5>
                  <div style="height: 300px; background: #f8f9fa; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                    <div class="text-center">
                      <i class="bi bi-pie-chart" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                      <p>Product Chart Placeholder</p>
                      <small>Integrate with Chart.js or similar</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
@endsection