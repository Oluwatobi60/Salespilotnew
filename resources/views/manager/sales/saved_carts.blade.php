@extends('manager.layouts.layout')
@section('manager_page_title')
Saved Carts Item
@endsection

@section('manager_layout_content')

<link rel="stylesheet" href="{{ asset('manager_asset/css/saved_carts_style.css') }}">
 <div class="content-wrapper">
            <!-- Saved Carts content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Saved Carts</h4>
                    <p class="card-description">List of saved shopping carts. Restore a cart to continue checkout or delete it.</p>

                    <!-- Modern Search and Filter Options -->
                    <div class="row mb-3 filter-container">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search by cart ID, customer..." id="searchCarts">
                          <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                        <!-- Staff Filter -->
                        <select class="form-select" id="staffFilter" style="max-width: 140px;">
                          <option value="">All Staff</option>
                          <!-- Staff options will be loaded dynamically if needed -->
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
                        <button class="btn btn-outline-info" onclick="testCartPanel()" title="Test Panel">
                          <i class="bi bi-gear"></i> Test
                        </button>
                      </div>
                    </div><br>
                    <div class="table-responsive">
                      <table class="table table-striped" id="savedCartsTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Transaction ID</th>
                            <th>Created by</th>
                            <th>Items</th>
                            <th>Saved Date</th>
                            <th>Cart Total</th>
                            <th class="text-center">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse($savedCarts as $index => $cart)
                          <tr class="cart-row" data-session-id="{{ $cart->session_id }}" data-clickable="true">
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $cart->session_id }}</strong></td>
                            <td>{{ $cart->user_name ?? 'Unknown User' }}</td>
                            <td><span class="badge badge-info">{{ $cart->items_count }} items</span></td>
                            <td><small>{{ \Carbon\Carbon::parse($cart->created_at)->format('M d, Y h:i A') }}</small></td>
                            <td><strong class="text-success">₦{{ number_format($cart->total, 2) }}</strong></td>
                            <td class="text-center">
                              <a href="#" class="text-danger delete-cart" data-session-id="{{ $cart->session_id }}" title="Delete Cart" onclick="event.stopPropagation();">
                                <i class="bi bi-trash fs-5"></i>
                              </a>
                            </td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="7" class="text-center py-5">
                              <div class="empty-state">
                                <i class="bi bi-cart-x"></i>
                                <h5>No Saved Carts</h5>
                                <p class="text-muted">You don't have any saved carts yet.</p>
                              </div>
                            </td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination -->
                    @if($savedCarts->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{ $savedCarts->firstItem() ?? 0 }}</strong> to <strong>{{ $savedCarts->lastItem() ?? 0 }}</strong> of <strong>{{ $savedCarts->total() }}</strong> entries
                      </div>
                      <nav aria-label="Saved carts pagination">
                        {{ $savedCarts->links('pagination::bootstrap-4') }}
                      </nav>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            <!-- Saved Carts content ends here -->

            <!-- Panel Overlay -->
            <div class="details-panel-backdrop" id="detailsBackdrop"></div>
            <!-- Cart Details Side Panel -->
            <div class="details-panel" id="detailsPanel">
              <div class="details-panel-header">
                <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">
                    <i class="bi bi-cart-check me-2"></i>Cart Details
                  </h5>
                  <button type="button" class="btn btn-sm btn-light" id="closePanelBtn">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
              </div>
              <div class="details-panel-body">
                <div class="detail-item">
                  <div class="detail-label">Customer Name</div>
                  <div class="detail-value" id="detailCartId">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Cart Status</div>
                  <div class="detail-value" id="detailCartStatus">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Created by</div>
                  <div class="detail-value" id="detailCreatedBy">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Saved Date</div>
                  <div class="detail-value" id="detailSavedDate">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Total Items</div>
                  <div class="detail-value" id="detailTotalItems">-</div>
                </div>
                <div class="detail-item">
                  <div class="detail-label">Cart Total</div>
                  <div class="detail-value" id="detailCartTotal">-</div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Items in Cart</div>
                  <div class="items-list" id="cartItemsList">
                    <!-- Cart items will be populated here -->
                  </div>
                </div>

                <div class="detail-item">
                  <div class="detail-label">Actions</div>
                  <div class="d-flex gap-2 mt-2">
                    <button class="btn btn-primary btn-sm" id="restoreCartBtn">
                      <i class="bi bi-arrow-clockwise"></i> Restore Cart
                    </button>
                    <button class="btn btn-outline-danger btn-sm" id="deleteCartBtn">
                      <i class="bi bi-trash"></i> Delete Cart
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <script src="{{ asset('manager_asset/js/saved_carts.js') }}"></script>
@endsection
