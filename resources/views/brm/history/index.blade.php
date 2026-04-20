@extends('brm.layouts.layout')

@section('brms_page_title')
BRm Commissions
@endsection

@section('brms_page_content')
 <link rel="stylesheet" href="{{ asset('brm_asset/css/commission_history.css') }}">


          <!-- Page Header -->
          <div class="page-header">
            <h1><i class="bi bi-receipt"></i> Payment History</h1>
            <p>View your commission payment history and transaction records</p>
          </div>

          <!-- Payment Stats -->
          <div class="payment-stats">


          <div class="stat-card total">
              <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <h3>₦450,000</h3>
              <p>Total Earned</p>
            </div>

            <div class="stat-card total">
              <div class="stat-icon">
                <i class="bi bi-cash-coin"></i>
              </div>
              <h3>₦300,000</h3>
              <p>Total Paid Out</p>
            </div>

            <div class="stat-card success">
              <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h3>12</h3>
              <p>Completed Payments</p>
            </div>

            <!-- <div class="stat-card pending">
              <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
              </div>
              <h3>₦150,000</h3>
              <p>Pending Payout</p>
            </div>

            <div class="stat-card recent">
              <div class="stat-icon">
                <i class="bi bi-calendar-check"></i>
              </div>
              <h3>Jan 5, 2026</h3>
              <p>Last Payment</p>
            </div> -->
          </div>

          <!-- Payment History Table -->
          <div class="payment-history-section">
            <h4><i class="bi bi-list-ul"></i> Transaction History</h4>

            <!-- Filter Bar -->
            <div class="filter-bar">
              <div class="filter-group">
                <select id="statusFilter">
                  <option value="all">All Status</option>
                  <option value="completed">Completed</option>
                  <option value="pending">Pending</option>
                  <option value="failed">Failed</option>
                </select>

                <select id="monthFilter">
                  <option value="all">All Months</option>
                  <option value="2026-01">January 2026</option>
                  <option value="2025-12">December 2025</option>
                  <option value="2025-11">November 2025</option>
                  <option value="2025-10">October 2025</option>
                </select>
              </div>

              <button id="exportPayments" class="export-btn">
                <i class="bi bi-download"></i> Export CSV
              </button>
            </div>

            <!-- Payment Table -->
            <div class="table-responsive">
              <table class="payment-table" id="paymentTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="paymentTableBody">
                  <tr data-status="completed" data-month="2026-01">
                    <td>1</td>
                    <td>Jan 5, 2026</td>
                    <td><code>TXN-2026-001</code></td>
                    <td><strong>₦75,000</strong></td>
                    <td><span class="status-badge completed">Completed</span></td>
                    <td><button class="view-receipt-btn"><i class="bi bi-file-text"></i> Receipt</button></td>
                  </tr>

                  <tr data-status="completed" data-month="2025-12">
                    <td>2</td>
                    <td>Dec 5, 2025</td>
                    <td><code>TXN-2025-012</code></td>
                    <td><strong>₦60,000</strong></td>
                    <td><span class="status-badge completed">Completed</span></td>
                    <td><button class="view-receipt-btn"><i class="bi bi-file-text"></i> Receipt</button></td>
                  </tr>

                  <tr data-status="completed" data-month="2025-11">
                    <td>3</td>
                    <td>Nov 5, 2025</td>
                    <td><code>TXN-2025-011</code></td>
                    <td><strong>₦50,000</strong></td>
                    <td><span class="status-badge completed">Completed</span></td>
                    <td><button class="view-receipt-btn"><i class="bi bi-file-text"></i> Receipt</button></td>
                  </tr>

                  <tr data-status="completed" data-month="2025-10">
                    <td>4</td>
                    <td>Oct 5, 2025</td>
                    <td><code>TXN-2025-010</code></td>
                    <td><strong>₦45,000</strong></td>
                    <td><span class="status-badge completed">Completed</span></td>
                    <td><button class="view-receipt-btn"><i class="bi bi-file-text"></i> Receipt</button></td>
                  </tr>

                  <tr data-status="completed" data-month="2025-10">
                    <td>5</td>
                    <td>Sep 5, 2025</td>
                    <td><code>TXN-2025-009</code></td>
                    <td><strong>₦40,000</strong></td>
                    <td><span class="status-badge completed">Completed</span></td>
                    <td><button class="view-receipt-btn"><i class="bi bi-file-text"></i> Receipt</button></td>
                  </tr>

                  <tr data-status="completed" data-month="2025-10">
                    <td>6</td>
                    <td>Aug 5, 2025</td>
                    <td><code>TXN-2025-008</code></td>
                    <td><strong>₦30,000</strong></td>
                    <td><span class="status-badge completed">Completed</span></td>
                    <td><button class="view-receipt-btn"><i class="bi bi-file-text"></i> Receipt</button></td>
                  </tr>
                </tbody>
              </table>
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
