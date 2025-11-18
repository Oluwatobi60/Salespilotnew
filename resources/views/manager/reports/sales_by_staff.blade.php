@extends('manager.layouts.layout')
@section('manager_page_title')
Sales Summary
@endsection
@section('manager_layout_content')
 <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">
        
          <div class="content-wrapper">
            <!-- Sales by Staff content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Sales by Staff Report</h4>
                    <p class="card-description">Track individual staff member sales performance.</p>

                    <!-- Date and Staff Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-4">
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
                        <small class="form-text text-muted">Choose a date range to filter sales by staff.</small>
                      </div>
                      <div class="col-md-4">
                        <select class="form-select form-select-sm" id="staffFilter">
                          <option value="">All Staff</option>
                          <option value="Sarah Johnson">Sarah Johnson</option>
                          <option value="Michael Lee">Michael Lee</option>
                          <option value="Emily Davis">Emily Davis</option>
                          <option value="James Smith">James Smith</option>
                        </select>
                      </div>
                    </div>
                   
                    <div class="table-responsive">
                      <table class="table table-striped" id="salesByStaffTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Staff Name</th>
                            <th>Employee ID</th>
                            <th>Transactions</th>
                            <th>Items Sold</th>
                            <th>Total Sales</th>
                            <th>Customers Registered</th>
                            <th>Performance</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>Sarah Johnson</td>
                            <td>EMP-001</td>
                            <td>142</td>
                            <td>287</td>
                            <td>$18,450.00</td>
                            <td>5</td>
                            <td><span class="badge badge-opacity-success">Excellent</span></td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>Michael Chen</td>
                            <td>EMP-002</td>
                            <td>128</td>
                            <td>245</td>
                            <td>$15,230.00</td>
                            <td>4</td>
                            <td><span class="badge badge-opacity-success">Excellent</span></td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>Emily Rodriguez</td>
                            <td>EMP-003</td>
                            <td>95</td>
                            <td>178</td>
                            <td>$11,890.00</td>
                            <td>3</td>
                            <td><span class="badge badge-opacity-info">Good</span></td>
                          </tr>


                          <tr>
                            <td>4</td>
                            <td>David Williams</td>
                            <td>EMP-004</td>
                            <td>87</td>
                            <td>156</td>
                            <td>$9,780.00</td>
                            <td>3</td>
                            <td><span class="badge badge-opacity-info">Good</span></td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td>Lisa Thompson</td>
                            <td>EMP-005</td>
                            <td>63</td>
                            <td>112</td>
                            <td>$7,340.00</td>
                            <td>2</td>
                            <td><span class="badge badge-opacity-warning">Average</span></td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th> <th>
                            <th>Total</th>
                            <th>515</th>
                            <th>978</th>
                            <th>$62,690.00</th>
                            <th>17</th>
                            <th>-</th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Sales by Staff content ends here -->
          </div>
          <!-- content-wrapper ends -->
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

@endsection