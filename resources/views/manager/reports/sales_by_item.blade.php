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
                    <h4 class="card-title">Sales by Item Report</h4>
                    <p class="card-description">Detailed sales performance for individual products.</p>

                    <!-- Date, Category, and Items Filter Section -->
                    <div class="row mb-3">
                      <div class="col-sm-3">
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
                        <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter sales by item.</small>
                      </div>
                      <div class="col-sm-3">
                        <select class="form-select form-select-sm" id="categoryFilter" style="font-size:0.85rem;">
                          <option value="">All Categories</option>
                          <option value="Electronics">Electronics</option>
                          <option value="Accessories">Accessories</option>
                          <option value="Office">Office</option>
                        </select>
                      </div>
                      <div class="col-sm-3">
                        <select class="form-select form-select-sm" id="itemFilter" style="font-size:0.85rem;">
                          <option value="">All Items</option>
                          <option value="Wireless Mouse">Wireless Mouse</option>
                          <option value="USB-C Cable">USB-C Cable</option>
                          <option value="Bluetooth Headset">Bluetooth Headset</option>
                          <option value="Phone Case">Phone Case</option>
                          <option value="Laptop Stand">Laptop Stand</option>
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
                          <tr>
                            <td>1</td>
                            <td>Wireless Mouse</td>
                            <td>WM-001</td>
                            <td>Electronics</td>
                            <td>125</td>
                            <td>$29.99</td>
                            <td>$23.99</td>
                            <td>$5.00</td>
                            <td>$3,748.75</td>
                            <td>14%</td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>USB-C Cable</td>
                            <td>UC-002</td>
                            <td>Accessories</td>
                            <td>250</td>
                            <td>$12.50</td>
                            <td>$8.75</td>
                            <td>$3.75</td>
                            <td>$3,125.00</td>
                            <td>30%</td>
                          </tr>

                          <tr>
                            <td>3</td>
                            <td>Bluetooth Headset</td>
                            <td>BH-003</td>
                            <td>Electronics</td>
                            <td>87</td>
                            <td>$59.99</td>
                            <td>$45.00</td>
                            <td>$4</td>
                            <td>$5,219.13</td>
                            <td>23%</td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td>Phone Case</td>
                            <td>PC-004</td>
                            <td>Accessories</td>
                            <td>198</td>
                            <td>$15.00</td>
                            <td>$2,970.00</td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td>Laptop Stand</td>
                            <td>LS-005</td>
                            <td>Electronics</td>
                            <td>64</td>
                            <td>$59.99</td>
                            <td>$45.00</td>
                            <td>$4</td>
                            <td>$5,219.13</td>
                            <td>23%</td>
                          </tr>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th colspan="3">Total</th>
                            <th>724</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>$17,942.88</th>
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
          </div>
          <!-- content-wrapper ends -->
          <!-- main-panel ends -->
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->

@endsection