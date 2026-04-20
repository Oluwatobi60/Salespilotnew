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
              <h3>145</h3>
              <p>Total Customers</p>
              <div class="stat-trend positive">
                <i class="bi bi-arrow-up"></i> +12% from last month
              </div>
            </div>

            <div class="stat-card conversions">
              <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h3>23</h3>
              <p>This Month Conversions</p>
              <div class="stat-trend positive">
                <i class="bi bi-arrow-up"></i> +18% from last month
              </div>
            </div>

            <div class="stat-card revenue">
              <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
              </div>
              <h3>₦4.5M</h3>
              <p>Total Revenue Generated</p>
              <div class="stat-trend positive">
                <i class="bi bi-arrow-up"></i> +25% from last month
              </div>
            </div>

            <div class="stat-card rate">
              <div class="stat-icon">
                <i class="bi bi-percent"></i>
              </div>
              <h3>68%</h3>
              <p>Conversion Rate</p>
              <div class="stat-trend positive">
                <i class="bi bi-arrow-up"></i> +5% from last month
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
                  <li class="leaderboard-item">
                    <div class="leaderboard-rank gold">1</div>
                    <div class="leaderboard-info">
                      <h6>You</h6>
                      <p>145 customers • 68% conversion</p>
                    </div>
                    <div class="leaderboard-value">₦450K</div>
                  </li>

                  <li class="leaderboard-item">
                    <div class="leaderboard-rank silver">2</div>
                    <div class="leaderboard-info">
                      <h6>Sarah Johnson</h6>
                      <p>132 customers • 65% conversion</p>
                    </div>
                    <div class="leaderboard-value">₦420K</div>
                  </li>

                  <li class="leaderboard-item">
                    <div class="leaderboard-rank bronze">3</div>
                    <div class="leaderboard-info">
                      <h6>Michael Chen</h6>
                      <p>118 customers • 62% conversion</p>
                    </div>
                    <div class="leaderboard-value">₦385K</div>
                  </li>

                  <li class="leaderboard-item">
                    <div class="leaderboard-rank regular">4</div>
                    <div class="leaderboard-info">
                      <h6>Emily Davis</h6>
                      <p>105 customers • 58% conversion</p>
                    </div>
                    <div class="leaderboard-value">₦340K</div>
                  </li>

                  <li class="leaderboard-item">
                    <div class="leaderboard-rank regular">5</div>
                    <div class="leaderboard-info">
                      <h6>James Wilson</h6>
                      <p>98 customers • 55% conversion</p>
                    </div>
                    <div class="leaderboard-value">₦320K</div>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Achievements -->
            <div class="col-lg-6 mb-4">
              <div class="achievements-section">
                <h4><i class="bi bi-award-fill"></i> Achievements & Badges</h4>
                <div class="achievements-grid">
                  <div class="achievement-badge">
                    <i class="bi bi-trophy-fill"></i>
                    <h6>Top Performer</h6>
                    <p>Rank #1 This Month</p>
                  </div>

                  <div class="achievement-badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="bi bi-star-fill"></i>
                    <h6>100 Customers</h6>
                    <p>Century Club</p>
                  </div>

                  <div class="achievement-badge" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="bi bi-lightning-fill"></i>
                    <h6>Fast Closer</h6>
                    <p>20 Deals in a Month</p>
                  </div>

                  <div class="achievement-badge" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="bi bi-graph-up-arrow"></i>
                    <h6>Growth Leader</h6>
                    <p>+25% Month Growth</p>
                  </div>

                  <div class="achievement-badge" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="bi bi-gem"></i>
                    <h6>Premium Seller</h6>
                    <p>10 Enterprise Deals</p>
                  </div>

                  <div class="achievement-badge" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                    <i class="bi bi-fire"></i>
                    <h6>Hot Streak</h6>
                    <p>7 Days Consecutive</p>
                  </div>
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
