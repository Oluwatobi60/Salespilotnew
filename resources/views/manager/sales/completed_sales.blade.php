@extends('manager.layouts.layout')
@section('manager_page_title')
Completed Sales
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/completed_sales.css') }}">
       <!-- partial -->
      <div class="container-fluid page-body-wrapper">

        <!-- partial -->

           <div class="content-wrapper">
            <!-- Completed Sales content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-start">
                      <div>
                        <h4 class="card-title mb-0">Completed Sales</h4>
                        <p class="card-description">List of completed sales transactions.</p>
                      </div>
                    </div>

                    <!-- Modern Search and Filter Options -->
                    <div class="row mb-3 filter-container">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search completed sales..." id="searchSales">
                          <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                        <!-- Seller Filter -->
                        <select class="form-select" id="sellerFilter" style="max-width: 140px;">
                        <option value="">All Sellers</option>
                        </select>

                        <!-- Date Range Filter -->
                        <div class="date-filter-wrapper">
                          <select class="form-select" id="dateRangeFilter" style="max-width: 140px;">
                            <option value="">All Dates</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last7">Last 7 Days</option>
                            <option value="last30">Last 30 Days</option>
                            <option value="thisMonth">This Month</option>
                            <option value="lastMonth">Last Month</option>
                            <option value="custom">Custom Range</option>
                          </select>

                          <!-- Custom Date Inputs -->
                          <div id="customDateInputs" class="custom-date-container">
                            <div class="row g-3">
                              <div class="col-md-6">
                                <label for="customStartDate" class="form-label text-muted">From Date</label>
                                <input type="date" class="form-control" id="customStartDate" onchange="performSearch()">
                              </div>
                              <div class="col-md-6">
                                <label for="customEndDate" class="form-label text-muted">To Date</label>
                                <input type="date" class="form-control" id="customEndDate" onchange="performSearch()">
                              </div>
                            </div>
                            <div class="text-center mt-3">
                              <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideCustomDateOverlay()">
                                <i class="bi bi-x"></i> Close
                              </button>
                            </div>
                          </div>
                        </div>

                        <!-- Action Buttons -->
                        <button class="btn btn-outline-primary" id="applyFilters">
                          <i class="bi bi-funnel"></i> Apply
                        </button>
                        <button class="btn btn-outline-secondary" id="clearFilters">
                          <i class="bi bi-x-circle"></i> Clear
                        </button>
                        <button class="btn btn-outline-success" id="exportReport">
                          <i class="bi bi-download"></i> Export
                        </button>
                      </div>
                    </div><br>

                    <div class="table-responsive">
                      <table class="table table-striped" id="completedSalesTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Receipt No.</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Sold by</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($completedSales as $index => $sale)
                          <tr class="sale-row"
                              data-receipt-number="{{ $sale->receipt_number }}"
                              data-discount="{{ $sale->discount ?? 0 }}"
                              data-seller-id="{{ $sale->staff_id ? 'staff_' . $sale->staff_id : ($sale->user_id ? 'user_' . $sale->user_id : 'unknown') }}"
                              style="cursor: pointer;"
                              title="Click to view details">
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $sale->receipt_number }}</strong></td>
                            <td><small>{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}</small></td>
                            <td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td>
                            <td>
                                @if($sale->staff_id && $sale->staff)
                                {{ $sale->staff->fullname }}
                                @elseif($sale->user)
                                {{ $sale->user->name }}
                                @else
                                Unknown
                                @endif
                            </td>
                            <td><span class="badge badge-info">{{ $sale->items_count }} items</span></td>
                            <td><strong class="text-success">₦{{ number_format($sale->total, 2) }}</strong></td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="8" class="text-center py-5">
                              <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h5>No Completed Sales</h5>
                                <p class="text-muted">No sales have been completed yet.</p>
                              </div>
                            </td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{ $completedSales->firstItem() ?? 0 }}</strong> to <strong>{{ $completedSales->lastItem() ?? 0 }}</strong> of <strong>{{ $completedSales->total() }}</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        {{ $completedSales->links('pagination::bootstrap-4') }}
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Panel Overlay -->
            <div class="details-panel-backdrop" id="detailsBackdrop"></div>

            <!-- Sale Details Side Panel -->
            <div class="details-panel" id="detailsPanel">
              <div class="details-panel-header">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">
                    <i class="bi bi-receipt me-2"></i>Sale Details
                  </h5>
                  <button type="button" class="btn btn-sm btn-light" id="closePanelBtn">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
              <div class="details-panel-body">
                <div class="detail-item">
                  <div class="detail-label">Receipt Number</div>
                  <div class="detail-value" id="detailReceiptNumber">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Customer Name</div>
                  <div class="detail-value" id="detailCustomerName">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Sold By</div>
                  <div class="detail-value" id="detailSoldBy">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Sale Date</div>
                  <div class="detail-value" id="detailSaleDate">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Total Items</div>
                  <div class="detail-value" id="detailTotalItems">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Total Amount</div>
                  <div class="detail-value" id="detailTotalAmount">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Discount Applied</div>
                  <div class="detail-value" id="detailDiscount">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Status</div>
                  <div class="detail-value" id="detailStatus">-</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Items in Sale</div>
                  <div class="items-list" id="saleItemsList">
                    <!-- Sale items will be populated here -->
                    <div class="text-muted text-center py-3">
                      <i class="bi bi-hourglass-split"></i> Loading items...
                    </div>
                  </div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Actions</div>
                  <div class="d-flex gap-2 mt-2">
                    <button class="btn btn-primary btn-sm" id="printReceiptBtn">
                      <i class="bi bi-printer"></i> Print Receipt
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" id="exportSaleBtn">
                      <i class="bi bi-download"></i> Export
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Completed Sales content ends here -->

      </div>
      <!-- page-body-wrapper ends -->

      <script src="{{ asset('manager_asset/js/completed_sales.js') }}"></script>
@endsection
