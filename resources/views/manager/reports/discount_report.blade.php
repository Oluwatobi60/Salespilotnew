@extends('manager.layouts.layout')
@section('manager_page_title')
Discount Report
@endsection
@section('manager_layout_content')
      <div class="container-scroller">
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
          <div class="content-wrapper">
            <!-- Discount content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Discount Report</h4>
                    <p class="card-description">View and manage discount transactions.</p>
                    <div class="row mb-3">
                      <div class="col-sm-4">
                        <select class="form-select form-select-sm mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()" style="font-size:0.85rem;">
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
                            <span class="input-group-text" style="font-size:0.85rem;">From</span>
                            <input type="date" class="form-control form-control-sm" id="customStartDate" style="font-size:0.85rem;">
                          </div>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text" style="font-size:0.85rem;">To</span>
                            <input type="date" class="form-control form-control-sm" id="customEndDate" style="font-size:0.85rem;">
                          </div>
                        </div>
                        <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter discounts.</small>
                      </div>
                      <div class="col-sm-4">
                        <select class="form-select form-select-sm" id="staffFilter" style="font-size:0.85rem;">
                          <option value="">All Staff</option>
                          <option value="Staff1">Staff 1</option>
                          <option value="Staff2">Staff 2</option>
                          <option value="Staff3">Staff 3</option>
                        </select>
                      </div>
                    </div>
                   
                    <div class="table-responsive">
                      <table class="table table-striped" id="discountTable">
                        <thead>
                          <tr>
                            <th>Discount Name</th>
                            <th>Times Used</th>
                            <th>Amount Discounted</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Over 100k purchase</td>
                            <td>3</td>
                            <td>&#8358; 2,000.00</td>
                          </tr>
                          <tr>
                            <td>First Time Customer</td>
                            <td>5</td>
                            <td>&#8358; 12,000.00</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Discount content ends here -->
          </div>
          <!-- content-wrapper ends -->
       
        </div>
      <!-- page-body-wrapper ends -->
    </div>
@endsection