@extends('manager.layouts.layout')
@section('manager_page_title')
Sales by Category
@endsection
@section('manager_layout_content')
      <div class="container-fluid page-body-wrapper">
              <div class="content-wrapper">
                  <!-- Sales by Category content starts here -->
                  <div class="row">
                    <div class="col-12 grid-margin stretch-card">
                      <div class="card card-rounded">
                        <div class="card-body">
                          <h4 class="card-title">Sales by Category Report</h4>
                          <p class="card-description">View sales performance across different product categories.</p>
                          <!-- Date and Category Filter Section -->
                          <div class="row mb-3">
                            <div class="col-sm-4">
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
                              <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter sales by category.</small>
                            </div>
                            <div class="col-sm-4">
                              <select class="form-select form-select-sm" id="categoryFilter" style="font-size:0.85rem;">
                                <option value="">All Categories</option>
                              </select>
                            </div>
                          </div>

                          <div class="table-responsive">
                            <table class="table table-striped" id="salesByCategoryTable">
                              <thead>
                                <tr>
                                  <th>S/N</th>
                                  <th>Category</th>
                                  <th>Units Sold</th>
                                  <th>Gross Sales</th>
                                  <th>Discount</th>
                                  <th>Net Sales</th>
                                  <th>Items Cost</th>
                                  <th>Gross Profit</th>
                                  <th>Tax</th>
                                  <th>Margin</th>
                                </tr>
                              </thead>
                              <tbody>
                                @forelse($salesByCategory as $i => $category)
                                <tr>
                                  <td>{{ $i + 1 }}</td>
                                  <td>{{ $category['category_name'] }}</td>
                                  <td>{{ $category['total_quantity_sold'] }}</td>
                                  <td>₦{{ number_format($category['gross_sales'], 2) }}</td>
                                  <td>₦{{ number_format($category['total_discount'] ?? 0, 2) }}</td>
                                  <td>₦{{ number_format($category['total_sales'], 2) }}</td>
                                  <td>₦{{ isset($category['total_cost']) ? number_format($category['total_cost'], 2) : '0.00' }}</td>
                                  <td>₦{{ isset($category['gross_profit']) ? number_format($category['gross_profit'], 2) : '0.00' }}</td>
                                  <td>₦{{ isset($category['tax']) ? number_format($category['tax'], 2) : '0.00' }}</td>
                                  <td>{{ isset($category['margin']) ? number_format($category['margin'], 1) . '%' : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                  <td colspan="10" class="text-center">No sales data available.</td>
                                </tr>
                                @endforelse
                              </tbody>
                              <tfoot>
                                <tr>
                                  <th colspan="3">Total</th>
                                  <th>₦{{ number_format($totals['gross_sales'] ?? 0, 2) }}</th>
                                  <th>₦{{ number_format($totals['total_discount'] ?? 0, 2) }}</th>
                                  <th>₦{{ number_format($totals['net_sales'] ?? 0, 2) }}</th>
                                  <th>₦{{ number_format($totals['items_cost'] ?? 0, 2) }}</th>
                                  <th>₦{{ number_format($totals['gross_profit'] ?? 0, 2) }}</th>
                                  <th>₦{{ number_format($totals['tax'] ?? 0, 2) }}</th>
                                  <th></th>
                                </tr>
                              </tfoot>
                            </table>


                          <hr class="my-3" style="border-top: 2px solid #e0e0e0;">

                          <!-- Bar Chart: Units Sold vs Category -->
                          <div class="mt-4 mb-2">
                            <h5 class="mb-3">Sales Performance by Category</h5>
                            <div style="width: 100%; max-width: 900px; margin: 0 auto;">
                              <canvas id="unitsSoldBarChart" height="220"></canvas>
                            </div>
                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 mt-3" id="barChartLegend">
                              <span class="d-flex align-items-center"><span style="display:inline-block;width:18px;height:18px;background:#36A2EB;border-radius:3px;margin-right:6px;"></span>Electronics</span>
                              <span class="d-flex align-items-center"><span style="display:inline-block;width:18px;height:18px;background:#FFCE56;border-radius:3px;margin-right:6px;"></span>Accessories</span>
                              <span class="d-flex align-items-center"><span style="display:inline-block;width:18px;height:18px;background:#4BC0C0;border-radius:3px;margin-right:6px;"></span>Apparel</span>
                              <span class="d-flex align-items-center"><span style="display:inline-block;width:18px;height:18px;background:#FF6384;border-radius:3px;margin-right:6px;"></span>Home &amp; Garden</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Sales by Category content ends here -->
                </div>
                <!-- content-wrapper ends -->

              </div>
              <!-- main-panel ends -->
            </div>

    <script src="{{ asset('manager_asset/js/sales_by_category.js') }}"></script>
@endsection
