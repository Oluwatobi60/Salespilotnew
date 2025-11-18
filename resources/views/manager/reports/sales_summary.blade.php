@extends('manager.layouts.layout')
@section('manager_page_title')
Sales Summary
@endsection
@section('manager_layout_content')
    <div class="container-fluid page-body-wrapper">
        <!-- partial:layouts/sidebar_content.php -->

          <div class="content-wrapper">
            <!-- Sales Summary content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Sales Summary</h4>
                    <p class="card-description">Overview of total sales, number of transactions, and totals by customer or date. Use filters to refine the report.</p>

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                      <div class="col-sm-3 col-6">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                          <input type="text" class="form-control form-control-sm border-start-0" placeholder="Search by date, status, or customer..." id="searchInput">
                        </div>
                      </div>
                      <div class="col-sm-4 col-6">
                        <select class="form-select form-select-sm mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()">
                          <option value="today">Today</option>
                          <option value="yesterday">Yesterday</option>
                          <option value="last7">Last 7 Days</option>
                          <option value="last30">Last 30 Days</option>
                          <option value="thisMonth">This Month</option>
                          <option value="lastMonth">Last Month</option>
                          <option value="custom">Custom Range</option>
                        </select>
                        <div id="customRangeInputs" style="display:none;">
                          <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text">From</span>
                            <input type="date" class="form-control form-control-sm" id="customStartDate">
                          </div>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" class="form-control form-control-sm" id="customEndDate">
                          </div>
                        </div>
                        <small class="form-text text-muted">Choose a date range to filter sales summary.</small>
                        <script>
                          function toggleCustomRangeInputs() {
                            var range = document.getElementById('dateRangeFilter');
                            var customInputs = document.getElementById('customRangeInputs');
                            if (range && customInputs) {
                              customInputs.style.display = range.value === 'custom' ? 'block' : 'none';
                            }
                          }
                          document.addEventListener('DOMContentLoaded', function() {
                            toggleCustomRangeInputs();
                          });
                        </script>
                      </div>
                      <div class="col-sm-3 col-12 mt-2 mt-sm-0">
                        <select class="form-select form-select-sm" id="staffFilter">
                          <option value="">All Staff</option>
                          <option value="Alice Johnson">Alice Johnson</option>
                          <option value="Bob Smith">Bob Smith</option>
                          <option value="Carol Williams">Carol Williams</option>
                          <option value="David Brown">David Brown</option>
                        </select>
                      </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                      <table class="table table-striped" id="salesSummaryTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Date</th>
                            <th>Gross Sales</th>
                            <th>Discount</th>
                            <th>Cost of items</th>
                            <th>Net Sales</th>
                            <th>Transactions</th>
                            <th>Gross Profit</th>
                            <th>Margin</th>
                            <th>Taxes</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>2025-10-20</td>
                            <td>$2,450.00</td>
                            <td>$120.00</td>
                            <td>$1,200.00</td>
                            <td>$1,130.00</td>
                            <td>15</td>
                            <td>$1,130.00</td>
                            <td>46.1%</td>
                            <td>$98.00</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>2025-10-19</td>
                            <td>$1,890.50</td>
                            <td>$80.00</td>
                            <td>$1,050.00</td>
                            <td>$1,010.50</td>
                            <td>12</td>
                            <td>$760.50</td>
                            <td>40.2%</td>
                            <td>$75.00</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>2025-10-18</td>
                            <td>$3,120.75</td>
                            <td>$100.00</td>
                            <td>$1,800.00</td>
                            <td>$1,220.75</td>
                            <td>18</td>
                            <td>$1,220.75</td>
                            <td>39.1%</td>
                            <td>$120.00</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                        </tbody>
                      </table>
</div>
</div>
    
@endsection