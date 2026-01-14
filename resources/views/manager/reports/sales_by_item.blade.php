@extends('manager.layouts.layout')
@section('manager_page_title')
Sales by item
@endsection
@section('manager_layout_content')

  <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">
        <!-- partial: Include Sidebar Content -->


          <div class="content-wrapper">
            <!-- Sales by Item content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    @if(request('categoryFilter'))
                      <div class="alert alert-info mb-2" style="font-size:0.95rem;">
                        Showing results for category: <strong>{{ request('categoryFilter') }}</strong>
                      </div>
                    @endif
                    <h4 class="card-title">Sales by Item Report</h4>
                    <p class="card-description">Detailed sales performance for individual products.</p>

                    <!-- Date, Category, and Items Filter Section -->
                    <div class="row mb-3">
                      <div class="col-sm-3">
                        <select class="form-select form-select-sm mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()" style="font-size:0.85rem;">
                          <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                          <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                          <option value="last7" {{ request('date_range') == 'last7' ? 'selected' : '' }}>Last 7 Days</option>
                          <option value="last30" {{ request('date_range') == 'last30' ? 'selected' : '' }}>Last 30 Days</option>
                          <option value="thisMonth" {{ request('date_range') == 'thisMonth' ? 'selected' : '' }}>This Month</option>
                          <option value="lastMonth" {{ request('date_range') == 'lastMonth' ? 'selected' : '' }}>Last Month</option>
                          <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                        <div id="customRangeInputs" style="display:{{ request('date_range') == 'custom' ? 'block' : 'none' }};">
                          <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text" style="font-size:0.85rem;">From</span>
                            <input type="date" class="form-control form-control-sm" id="customStartDate" style="font-size:0.85rem;" value="{{ request('start_date') }}">
                          </div>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text" style="font-size:0.85rem;">To</span>
                            <input type="date" class="form-control form-control-sm" id="customEndDate" style="font-size:0.85rem;" value="{{ request('end_date') }}">
                          </div>
                        </div>
                        <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter sales by item.</small>
                      </div>
                      <div class="col-sm-3">
                        <select class="form-select form-select-sm" id="categoryFilter" style="font-size:0.85rem;">
                          <option value="">All Categories</option>
                        </select>
                      </div>
                      <div class="col-sm-3">
                        <select class="form-select form-select-sm" id="itemFilter" style="font-size:0.85rem;">
                          <option value="">All Items</option>
                        </select>
                      </div>
                    </div>

                    <div class="table-responsive">
                      <table class="table table-striped" id="salesByItemTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Item Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Qty Sold</th>
                            <th>Gross Sales Amount</th>
                            <th>Cost Price</th>
                            <th>Gross Profit</th>
                            <th>Discounts</th>
                            <th>Profit Margin</th>
                          </tr>
                        </thead>
                        <tbody>

                         @forelse ($salesbyitem as $index => $item)
                          <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->sku ?? '-' }}</td>
                            <td>{{ $item->category_name ?? $item->category ?? '-' }}</td>
                            <td>{{ $item->total_quantity_sold ?? 0 }}</td>
                            <td>₦{{ number_format($item->gross_sales ?? 0, 2) }}</td>
                            <td>₦{{ number_format($item->cost_price ?? 0, 2) }}</td>
                            <td>₦{{ number_format($item->gross_profit ?? 0, 2) }}</td>
                            <td>₦{{ number_format($item->total_discount ?? 0, 2) }}</td>
                            <td>{{ $item->profit_margin ?? 0 }}%</td>
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
                        <tfoot>
                          <tr style="font-weight:bold; background:#f8f9fa;">
                            <th colspan="4" class="text-end">Grand Total</th>
                            <th></th>
                            <th>₦{{ number_format($totals['gross_sales'] ?? 0, 2) }}</th>
                            <th>₦{{ number_format($totals['cost_price'] ?? 0, 2) }}</th>
                            <th>₦{{ number_format($totals['gross_profit'] ?? 0, 2) }}</th>
                            <th>₦{{ number_format($totals['total_discount'] ?? 0, 2) }}</th>
                            <th></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                    <br><br><br>
                    <!-- Bar Chart: Qty Sold vs Item Name -->
                    <div class="row mt-5">
                      <div class="col-12">
                        <div class="card card-rounded">
                          <div class="card-body">
                            Space for chart
                            <div style="max-width: 600px; margin-left: 0;">
                              <canvas id="qtySoldBarChart" height="180"></canvas>
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
            <!-- Sales by Item content ends here -->
                      <div class="d-flex justify-content-center mt-4">
                        {{ $salesbyitem->links() }}
                      </div>
          </div>
          <!-- content-wrapper ends -->
          <!-- main-panel ends -->
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->

    <script src="{{ asset('manager_asset/js/sales_by_item.js') }}"></script>
@endsection
