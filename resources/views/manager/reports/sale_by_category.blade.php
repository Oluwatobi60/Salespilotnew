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
                              <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter sales by category.</small>
                            </div>
                            <div class="col-sm-4">
                              <select class="form-select form-select-sm" id="categoryFilter" style="font-size:0.85rem;">
                                <option value="">All Categories</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Accessories">Accessories</option>
                                <option value="Apparel">Apparel</option>
                                <option value="Office">Office</option>
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
                                  <th>Net Sales</th>
                                  <th>Items Cost</th>
                                  <th>Gross Profit</th>
                                  <th>Tax</th>
                                  <th>Margin</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>Electronics</td>
                                  <td>245</td>
                                  <td>$12,450.00</td>
                                  <td>$12,000.00</td>
                                  <td>$7,000.00</td>
                                  <td>$5,000.00</td>
                                  <td>$450.00</td>
                                  <td>40.0%</td>
                                </tr>
                                <tr>
                                  <td>2</td>
                                  <td>Accessories</td>
                                  <td>358</td>
                                  <td>$8,950.00</td>
                                  <td>$8,500.00</td>
                                  <td>$4,200.00</td>
                                  <td>$4,300.00</td>
                                  <td>$450.00</td>
                                  <td>50.6%</td>
                                </tr>
                                <tr>
                                  <td>3</td>
                                  <td>Apparel</td>
                                  <td>187</td>
                                  <td>$5,610.00</td>
                                  <td>$5,500.00</td>
                                  <td>$2,800.00</td>
                                  <td>$2,700.00</td>
                                  <td>$110.00</td>
                                  <td>48.2%</td>
                                </tr>
                                <tr>
                                  <td>4</td>
                                  <td>Home & Garden</td>
                                  <td>92</td>
                                  <td>$2,760.00</td>
                                  <td>$2,700.00</td>
                                  <td>$1,500.00</td>
                                  <td>$1,200.00</td>
                                  <td>$60.00</td>
                                  <td>44.4%</td>
                                </tr>
                              </tbody>
                              <tfoot>
                                <tr>
                                  <th>Total</th>
                                  <th>882</th>
                                  <th>$29,770.00</th>
                                  <th>$28,700.00</th>
                                  <th>$15,500.00</th>
                                  <th>$13,200.00</th>
                                  <th>$1,070.00</th>
                                  <th>46.0%</th>
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
@endsection