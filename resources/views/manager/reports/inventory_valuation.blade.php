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
                          <select class="form-select form-select-sm" id="categoryFilter">
                            <option value="">All Categories</option>
                            @if($allCategories && $allCategories->count() > 0)
                              @foreach($allCategories as $category)
                                <option value="{{ $category->category_name }}">
                                  {{ $category->category_name }}
                                </option>
                              @endforeach
                            @endif
                          </select>
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
                            @forelse($paginatedItems as $i => $item)
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
                            @empty
                            <tr>
                              <td colspan="9" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3 mb-0">
                                  @if(request('category'))
                                    No items found in category "{{ request('category') }}"
                                  @elseif(request('search'))
                                    No items found matching "{{ request('search') }}"
                                  @else
                                    No inventory items available
                                  @endif
                                </p>
                              </td>
                            </tr>
                            @endforelse
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

    <script src="{{ asset('manager_asset/js/inventory_valuation.js') }}"></script>
@endsection
