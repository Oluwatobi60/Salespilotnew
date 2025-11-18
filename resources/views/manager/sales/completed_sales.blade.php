@extends('manager.layouts.layout')
@section('manager_page_title')
Completed Sales
@endsection
@section('manager_layout_content')
       <!-- partial -->
      <div class="container-fluid page-body-wrapper">
      
        <!-- partial -->
     
          <div class="content-wrapper">
            <!-- Completed Sales content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Completed Sales</h4>
                    <p class="card-description">List of completed sales transactions.</p>
                    
                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <div class="input-group">
                          <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                          </span>
                          <input type="text" class="form-control border-start-0" placeholder="Search by invoice, customer, or staff..." id="searchInput">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <select class="form-select mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()">
                          <option value="today">Today</option>
                          <option value="yesterday">Yesterday</option>
                          <option value="last7">Last 7 Days</option>
                          <option value="last30">Last 30 Days</option>
                          <option value="thisMonth">This Month</option>
                          <option value="lastMonth">Last Month</option>
                          <option value="custom">Custom Range</option>
                        </select>
                        <div id="customRangeInputs" style="display:none;">
                          <div class="input-group mb-1">
                            <span class="input-group-text">From</span>
                            <input type="date" class="form-control" id="customStartDate">
                          </div>
                          <div class="input-group">
                            <span class="input-group-text">To</span>
                            <input type="date" class="form-control" id="customEndDate">
                          </div>
                        </div>
                        <small class="form-text text-muted">Choose a date range to filter sales.</small>
                      </div>
                      <div class="col-md-2">
                        <select class="form-select" id="sellerFilter">
                          <option value="">All Sellers</option>
                          <option value="Alice Johnson">Alice Johnson</option>
                          <option value="Bob Smith">Bob Smith</option>
                          <option value="Carol Williams">Carol Williams</option>
                          <option value="David Brown">David Brown</option>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <select class="form-select" id="statusFilter">
                          <option value="">All Status</option>
                          <option value="Completed">Completed</option>
                          <option value="Pending">Pending</option>
                          <option value="Refunded">Refunded</option>
                        </select>
                      </div>
                    </div>
                    <br>

                    <div class="table-responsive">
                      <table class="table table-striped" id="completedSalesTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Sold by:</th>
                            <th>Total</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>INV-001</td>
                            <td>2025-10-14</td>
                            <td>John Doe</td>
                            <td>Alice Johnson</td>
                            <td>$120.00</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>INV-002</td>
                            <td>2025-10-15</td>
                            <td>Jane Smith</td>
                            <td>Bob Smith</td>
                            <td>$250.50</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>INV-003</td>
                            <td>2025-10-16</td>
                            <td>Michael Brown</td>
                            <td>Carol Williams</td>
                            <td>$89.99</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td>INV-004</td>
                            <td>2025-10-17</td>
                            <td>Sarah Johnson</td>
                            <td>Alice Johnson</td>
                            <td>$175.25</td>
                            <td><span class="badge badge-opacity-warning">Pending</span></td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td>INV-005</td>
                            <td>2025-10-18</td>
                            <td>David Wilson</td>
                            <td>David Brown</td>
                            <td>$320.00</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>1-5</strong> of <strong>5</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        <ul class="pagination pagination-sm mb-0">
                          <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                          </li>
                          <li class="page-item active"><a class="page-link" href="#">1</a></li>
                          <li class="page-item"><a class="page-link" href="#">2</a></li>
                          <li class="page-item"><a class="page-link" href="#">3</a></li>
                          <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                          </li>
                        </ul>
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Completed Sales content ends here -->
          </div>
          <!-- content-wrapper ends -->
          
      </div>
      <!-- page-body-wrapper ends -->
@endsection