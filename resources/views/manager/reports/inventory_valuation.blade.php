@extends('manager.layouts.layout')
@section('manager_page_title')
Inventory Valuation
@endsection
@section('manager_layout_content')
  <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">
   
          <div class="content-wrapper">
            <!-- Inventory Valuation content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                      <h4 class="card-title">Inventory Valuation Report</h4>
                      <p class="card-description">Current inventory value and stock levels.</p>
                      <form class="row g-2 align-items-center mb-4" id="inventoryFilterForm" style="margin-bottom: 1.5rem !important;">
                        <div class="col-md-4 col-12">
                          <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search Item Name or Category">
                        </div>
                        <div class="col-md-3 col-8">
                          <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                              <span id="selectedCategoryLabel">All Categories</span>
                            </button>
                            <ul class="dropdown-menu w-100 px-2" aria-labelledby="categoryDropdown" style="min-width: 100%;">
                              <li>
                                <label class="dropdown-item mb-1">
                                  <input type="checkbox" class="categoryCheckbox" value="Electronics"> Electronics
                                </label>
                              </li>
                              <li>
                                <label class="dropdown-item mb-1">
                                  <input type="checkbox" class="categoryCheckbox" value="Accessories"> Accessories
                                </label>
                              </li>
                              <li>
                                <label class="dropdown-item mb-1">
                                  <input type="checkbox" class="categoryCheckbox" value="Furniture"> Furniture
                                </label>
                              </li>
                              <li>
                                <label class="dropdown-item mb-1">
                                  <input type="checkbox" class="categoryCheckbox" value="Stationery"> Stationery
                                </label>
                              </li>
                              <li class="text-center mt-2 mb-1">
                                <button type="button" class="btn btn-sm btn-primary w-100" onclick="updateCategoryFilter()">Apply Filter</button>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </form>
                      <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-primary h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Total Inventory Value</span>
                                <span class="fs-5">$13,812.50</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-success h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Total Selling Price Value</span>
                                <span class="fs-5">$18,000.00</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-warning h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Potential Profit</span>
                                <span class="fs-5">$4,187.50</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-info h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Margin</span>
                                <span class="fs-5">23.3%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <br>
                      <div class="table-responsive">
                        <table class="table table-striped" id="inventoryValuationTable">
                          <thead>
                            <tr>
                              <th>S/N</th>
                              <th>Item Name</th>
                              <th>Category</th>
                              <th>Qty In Stock</th>
                              <th>Cost</th>
                              <th>Inventory Value</th>
                              <th>Total Selling Price Value</th>
                              <th>Potential Profit</th>
                              <th>Margin</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>Product A</td>
                              <td>Electronics</td>
                              <td>150</td>
                              <td>$25.00</td>
                              <td>$3,750.00</td>
                              <td>$4,950.00</td>
                              <td>$1,200.00</td>
                              <td>24.2%</td>
                            </tr>
                            <tr>
                              <td>2</td>
                              <td>Product B</td>
                              <td>Accessories</td>
                              <td>85</td>
                              <td>$12.50</td>
                              <td>$1,062.50</td>
                              <td>$1,445.00</td>
                              <td>$382.50</td>
                              <td>26.5%</td>
                            </tr>
                            <tr>
                              <td>3</td>
                              <td>Product C</td>
                              <td>Electronics</td>
                              <td>200</td>
                              <td>$45.00</td>
                              <td>$9,000.00</td>
                              <td>$12,000.00</td>
                              <td>$3,000.00</td>
                              <td>25.0%</td>
                            </tr>
                            <tr>
                              <td>4</td>
                              <td>Product D</td>
                              <td>Furniture</td>
                              <td>60</td>
                              <td>$80.00</td>
                              <td>$4,800.00</td>
                              <td>$6,600.00</td>
                              <td>$1,800.00</td>
                              <td>27.3%</td>
                            </tr>
                            <tr>
                              <td>5</td>
                              <td>Product E</td>
                              <td>Stationery</td>
                              <td>500</td>
                              <td>$2.00</td>
                              <td>$1,000.00</td>
                              <td>$1,500.00</td>
                              <td>$500.00</td>
                              <td>33.3%</td>
                            </tr>
                            <tr>
                              <td>6</td>
                              <td>Product F</td>
                              <td>Accessories</td>
                              <td>120</td>
                              <td>$15.00</td>
                              <td>$1,800.00</td>
                              <td>$2,400.00</td>
                              <td>$600.00</td>
                              <td>25.0%</td>
                            </tr>
                            <tr>
                              <td>7</td>
                              <td>Product G</td>
                              <td>Electronics</td>
                              <td>75</td>
                              <td>$60.00</td>
                              <td>$4,500.00</td>
                              <td>$6,000.00</td>
                              <td>$1,500.00</td>
                              <td>25.0%</td>
                            </tr>
                            <tr>
                              <td>8</td>
                              <td>Product H</td>
                              <td>Furniture</td>
                              <td>40</td>
                              <td>$120.00</td>
                              <td>$4,800.00</td>
                              <td>$6,000.00</td>
                              <td>$1,200.00</td>
                              <td>20.0%</td>
                            </tr>
                            <tr>
                              <td>9</td>
                              <td>Product I</td>
                              <td>Stationery</td>
                              <td>300</td>
                              <td>$3.00</td>
                              <td>$900.00</td>
                              <td>$1,200.00</td>
                              <td>$300.00</td>
                              <td>25.0%</td>
                            </tr>
                            <tr>
                              <td>10</td>
                              <td>Product J</td>
                              <td>Accessories</td>
                              <td>200</td>
                              <td>$8.00</td>
                              <td>$1,600.00</td>
                              <td>$2,200.00</td>
                              <td>$600.00</td>
                              <td>27.3%</td>
                            </tr>
                          </tbody>
                          
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Inventory Valuation content ends here -->
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- main-panel ends -->

@endsection