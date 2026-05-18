@extends('brm.layouts.layout')

@section('brms_page_title')
BRM Dashboard
@endsection

@section('brms_page_content')

 <link rel="stylesheet" href="{{ asset('brm_asset/css/dashboard.css') }}">


          <!-- Dashboard Header -->
          <div class="dashboard-header">
            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
              <div>
                <h1><i class="bi bi-speedometer2"></i> BRM Dashboard</h1>
                <p>Welcome back {{ $brm->name }}! Here's your performance overview</p>
              </div>
              <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 1rem 1.5rem; min-width: 250px;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                  <div>
                    <div style="font-size: 0.75rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.25rem;">Your BRM Code</div>
                    <div style="font-size: 1.5rem; font-weight: bold; font-family: 'Courier New', monospace; letter-spacing: 1px;">{{ $brm->referral_code }}</div>
                  </div>
                  <button onclick="copyBrmCode('{{ $brm->referral_code }}')"
                          style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 0.5rem; border-radius: 8px; cursor: pointer; transition: all 0.3s;"
                          onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                          onmouseout="this.style.background='rgba(255,255,255,0.2)'"
                          title="Copy BRM Code">
                    <i class="bi bi-clipboard" style="font-size: 1.2rem;"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <script>
          function copyBrmCode(code) {
            navigator.clipboard.writeText(code).then(function() {
              // Show success feedback
              const btn = event.currentTarget;
              const originalHTML = btn.innerHTML;
              btn.innerHTML = '<i class="bi bi-check-lg" style="font-size: 1.2rem;"></i>';
              btn.style.background = 'rgba(255,255,255,0.4)';

              setTimeout(function() {
                btn.innerHTML = originalHTML;
                btn.style.background = 'rgba(255,255,255,0.2)';
              }, 2000);

              // Optional: Show toast notification
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: 'BRM Code copied!',
                  showConfirmButton: false,
                  timer: 2000
                });
              }
            }).catch(function(err) {
              console.error('Could not copy text: ', err);
            });
          }
          </script>

          <!-- Key Metrics -->
          <div class="row mb-4">
            <div class="col-md-3 mb-3">
              <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;">{{ $totalCustomers }}</div>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Total Customers</p>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-radius: 10px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;">{{ $activeSubscriptions }}</div>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Active Subscriptions</p>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border-radius: 10px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;">{{ $customersThisMonth }}</div>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">This Month</p>
              </div>
            </div>
            <div class="col-md-3 mb-3">
              <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border-radius: 10px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold;">{{ $totalCustomers > 0 ? round(($activeSubscriptions / $totalCustomers) * 100) : 0 }}%</div>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Conversion Rate</p>
              </div>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="stats-grid">
            <div style="background: rgba(13, 110, 253, 0.1); border-left: 4px solid #0d6efd; border-radius: 10px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
              <div style="width: 50px; height: 50px; border-radius: 10px; background: rgba(13, 110, 253, 0.15); color: #0d6efd; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-bell-fill"></i>
              </div>
              <p style="margin: 0; color: #0d6efd; font-size: 1.1rem; font-weight: 500;">You have {{ $customersThisMonth }} customers registered this month</p>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="quick-actions">
            <h4><i class="bi bi-lightning-fill"></i> Quick Actions</h4>
            <div class="action-buttons">
              <a href="{{ route('brm.customers') }}" class="action-btn">
                <i class="bi bi-people-fill"></i>
                <span>View Customers</span>
              </a>
              <a href="{{ route('brm.commissions') }}" class="action-btn">
                <i class="bi bi-cash-stack"></i>
                <span>Commission Report</span>
              </a>
              <a href="{{ route('brm.performance') }}" class="action-btn">
                <i class="bi bi-graph-up"></i>
                <span>View Performance</span>
              </a>
            </div>
          </div>

          <div class="row">
            <!-- Recent Activity -->
            <div class="col-lg-6 mb-4">
              <div class="activity-section">
                <h4><i class="bi bi-clock-history"></i> Recent Activity</h4>
                <ul class="activity-list">
                  @forelse($recentActivity as $activity)
                  <li class="activity-item">
                    <div class="activity-icon {{ $loop->iteration % 2 == 0 ? 'success' : 'new' }}">
                      @if(str_contains(strtolower($activity->action), 'created') || str_contains(strtolower($activity->action), 'added'))
                        <i class="bi bi-person-plus-fill"></i>
                      @elseif(str_contains(strtolower($activity->action), 'login'))
                        <i class="bi bi-check-circle-fill"></i>
                      @elseif(str_contains(strtolower($activity->action), 'updated'))
                        <i class="bi bi-pencil-fill"></i>
                      @else
                        <i class="bi bi-activity"></i>
                      @endif
                    </div>
                    <div class="activity-content">
                      <h6>{{ $activity->user->business_name ?? $activity->user->first_name }}</h6>
                      <p>{{ $activity->action }}</p>
                    </div>
                    <div class="activity-time">{{ $activity->created_at->diffForHumans() }}</div>
                  </li>
                  @empty
                  <li class="activity-item">
                    <p style="text-align: center; color: #999; padding: 1rem;">No recent activity</p>
                  </li>
                  @endforelse
                </ul>
              </div>
            </div>

            <!-- Performance Chart -->
            <div class="col-lg-6 mb-4">
              <div class="chart-section">
                <h4><i class="bi bi-graph-up"></i> Monthly Performance</h4>
                <div class="chart-placeholder">
                  <p><i class="bi bi-bar-chart" style="font-size: 3rem;"></i><br>Chart visualization coming soon</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Leads -->
          <div class="leads-section">
            <h4><i class="bi bi-star-fill"></i> Recent Customers</h4>
            <div class="table-responsive">
              <table class="leads-table">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentCustomers as $customer)
                  <tr>
                    <td>{{ $customer->first_name ?? 'N/A' }} {{ $customer->surname ?? '' }}</td>
                    <td>{{ $customer->business_name ?? 'N/A' }}</td>
                    <td>{{ $customer->email ?? 'N/A' }}</td>
                    <td>
                      @if($customer->currentSubscription)
                        <span class="status-badge qualified">Active</span>
                      @elseif($customer->status == 'active')
                        <span class="status-badge new">New</span>
                      @else
                        <span class="status-badge contacted">Inactive</span>
                      @endif
                    </td>
                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                    <td>
                      <a href="{{ route('brm.customers') }}" class="btn btn-sm btn-primary">View</a>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #999;">No customers yet</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>



        <!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              © {{ date('Y') }} {{ app_name() }}. All rights reserved.
            </span>
          </div>
        </footer>

@endsection

