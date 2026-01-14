@extends('manager.layouts.layout')
@section('manager_page_title')
Sales Summary
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/sale_summary.css') }}">
<div class="content-wrapper">
 <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Sales Summary</h4>
                    <p class="card-description">Overview of total sales, number of transactions, and totals by customer or date. Use filters to refine the report.</p>
                    <div class="row mb-3 filter-container">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search sales summary..." id="searchSummary">
                          <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                        <!-- Seller Filter -->
                        {{-- <select class="form-select" id="sellerFilter" style="max-width: 140px;">
                          <option value="">All Sellers</option>
                        </select> --}}
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
                          @forelse($salesSummary as $index => $sale)
                          <tr class="summary-row" 
                              data-date="{{ $sale->sale_date }}"
                              data-seller-ids="{{ implode(',', $sale->seller_ids ?? []) }}">
                            <td>{{ ($salesSummary->currentPage() - 1) * $salesSummary->perPage() + $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('M d, Y') }}</td>
                            <td>₦{{ number_format($sale->gross_sales, 2) }}</td>
                            <td>₦{{ number_format($sale->total_discount, 2) }}</td>
                            <td>₦{{ number_format($sale->cost_of_items, 2) }}</td>
                            <td>₦{{ number_format($sale->net_sales, 2) }}</td>
                            <td>{{ $sale->transaction_count }}</td>
                            <td>₦{{ number_format($sale->gross_profit, 2) }}</td>
                            <td>{{ number_format($sale->margin, 1) }}%</td>
                            <td>₦{{ number_format($sale->taxes, 2) }}</td>
                            <td><span class="badge badge-opacity-success">Completed</span></td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="11" class="text-center py-5">
                              <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h5>No Sales Data</h5>
                                <p class="text-muted">No sales have been completed yet.</p>
                              </div>
                            </td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination -->
                    @if($salesSummary->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{ $salesSummary->firstItem() ?? 0 }}</strong> to <strong>{{ $salesSummary->lastItem() ?? 0 }}</strong> of <strong>{{ $salesSummary->total() }}</strong> entries
                      </div>
                      <nav aria-label="Sales summary pagination">
                        {{ $salesSummary->links('pagination::bootstrap-4') }}
                      </nav>
                    </div>
                    @endif

                    <!-- Charts Section -->
                    <div class="row mt-5">
                      <div class="col-md-6 mb-4">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <h5 class="card-title">Gross Sales</h5>
                            <canvas id="grossSalesLineChart" height="180"></canvas>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 mb-4">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <h5 class="card-title">Cost of Items</h5>
                            <canvas id="costItemsLineChart" height="180"></canvas>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 mb-4">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <h5 class="card-title">Transactions</h5>
                            <canvas id="transactionsLineChart" height="180"></canvas>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 mb-4">
                        <div class="card card-rounded">
                          <div class="card-body">
                            <h5 class="card-title">Gross Profit</h5>
                            <canvas id="grossProfitLineChart" height="180"></canvas>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

  <!-- Sales Summary content ends here -->


<script>
// Pass PHP data to JavaScript (use allSalesData for charts to show all data)
const salesData = @json($allSalesData ?? []);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('manager_asset/js/sale_summary.js') }}"></script>
@endsection
