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
                                <span class="fs-5">₦{{ number_format($totalInventoryValue ?? 0, 2) }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-success h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Total Selling Price Value</span>
                                <span class="fs-5">₦{{ number_format($totalSellingValue ?? 0, 2) }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-warning h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Potential Profit</span>
                                <span class="fs-5">₦{{ number_format($totalPotentialProfit ?? 0, 2) }}</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                          <div class="card text-white bg-info h-100">
                            <div class="card-body py-3 px-2">
                              <div class="d-flex flex-column align-items-start">
                                <span class="fw-bold fs-6">Margin</span>
                                <span class="fs-5">{{ number_format($overallMargin ?? 0, 2) }}%</span>
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
                            @foreach($paginatedItems as $i => $item)
                            <tr>
                              <td>{{ ($paginatedItems->firstItem() ?? 0) + $i }}</td>
                              <td>{{ $item['item_name'] }}</td>
                              <td>{{ $item['category_name'] }}</td>
                              <td>{{ $item['quantity'] }}</td>
                              <td>₦{{ number_format($item['cost_price'], 2) }}</td>
                              <td>₦{{ number_format($item['inventory_value'], 2) }}</td>
                              <td>₦{{ number_format($item['total_selling_value'], 2) }}</td>
                              <td>₦{{ number_format($item['potential_profit'], 2) }}</td>
                              <td>{{ number_format($item['margin'], 2) }}%</td>
                            </tr>
                            @endforeach
                                                    </tbody>
                                                  </table>


                    <!-- Pagination and Stats -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <span class="text-muted">
                                Showing {{  $paginatedItems->firstItem() ?? 0 }} to {{ $paginatedItems->lastItem() ?? 0 }} of {{ $paginatedItems->total() }} entries
                            </span>
                        </div>
                        <div class="col-md-6">
                            {{ $paginatedItems->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
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
