@extends('manager.layouts.layout')
@section('manager_page_title')
Sell Product
@endsection

@section('manager_layout_content')

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

 <div class="container-scroller">



            <!-- Sell content starts here -->
<div class="row">
<div class="col-sm-12">
<div class="home-tab">

<!-- Page Content -->
<div class="sell-container">
<div class="pos-layout">
<!-- Items Section -->
<div class="items-section">
<div class="items-container">
    <!-- Filter Controls -->
    <div class="filter-controls">
    <div class="filter-left">
        <div class="search-box">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Search items by name..." autocomplete="off">
        <i class="bi bi-x-circle clear-icon" id="clearSearch"></i>
        </div>
    </div>

    <div class="filter-right" style="display: flex; align-items: center; gap: 15px;">
        <div class="category-filter">
        <select id="categoryFilter">
            <option value="">All Categories</option>
            @foreach($categories as $category)
            <option value="{{ $category->category_name }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
        </div>

        <div class="filter-badge">
        <i class="bi bi-box-seam"></i>
        <span id="itemCount">{{ $all_items->count() }} Items</span>
        </div>
    </div>
    </div>

    <div class="items-grid">
    {{-- DEBUG: Total items = {{ $all_items->count() }} --}}
    @forelse($all_items as $item)
        @if($item->item_type == 'standard')
        <!-- Standard Item -->
        {{-- DEBUG: ID={{ $item->id }}, Name={{ $item->item_name }}, Image={{ $item->item_image ?? 'NULL' }} --}}
        <div class="item-card"
                data-id="{{ $item->id }}"
                data-type="standard"
                data-name="{{ $item->item_name }}"
                data-price="{{ $item->final_price ?? $item->selling_price }}"
                data-cost-price="{{ $item->cost_price ?? 0 }}"
                data-stock="{{ $item->current_stock ?? 0 }}"
                data-category="{{ $item->category_name }}"
                data-img="{{ $item->item_image ? asset($item->item_image) : asset('manager_asset/images/salespilot logo1.png') }}">

            @if($item->item_image)
            <img src="{{ asset($item->item_image) }}" alt="{{ $item->item_name }}" onerror="console.error('Image failed:', '{{ $item->item_image }}'); this.src='{{ asset('manager_asset/images/salespilot logo1.png') }}'">
            @else
            <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="{{ $item->item_name }}">
            @endif
            <div class="item-overlay">
            <div class="item-name">{{ $item->item_name }}</div>
            <div class="item-price">₦{{ number_format($item->final_price ?? $item->selling_price, 2) }}</div>
            <div class="item-stock">Stock: {{ $item->current_stock ?? 0 }}</div>
            @if(($item->current_stock ?? 0) == 0)
              <div class="sold-out-badge" style="font-weight:bold; color:#fff; background:#dc3545; padding:6px 14px; border-radius:6px; margin-top:8px; font-size:10px; letter-spacing:1px;">OUT OF STOCK</div>
            @endif
            @if($item->category_name)
              <div class="item-category" style="font-size: 11px; color: #999;">{{ $item->category_name }}</div>
            @endif
            </div>
        </div>
        @else
        <!-- Variant Item - Show parent or variants -->
        @if($item->variants && $item->variants->count() > 0)
            @foreach($item->variants as $variant)
            @if($variant->sell_item)
                <div class="item-card"
                    data-id="{{ $variant->id }}"
                    data-type="variant"
                    data-parent-id="{{ $item->id }}"
                    data-name="{{ $item->item_name }} - {{ $variant->variant_name }}"
                    data-price="{{ $variant->final_price ?? $variant->selling_price ?? 0 }}"
                    data-cost-price="{{ $variant->cost_price ?? $variant->manual_cost_price ?? $variant->margin_cost_price ?? $variant->range_cost_price ?? 0 }}"
                    data-stock="{{ $variant->stock_quantity ?? 0 }}"
                    data-category="{{ $item->category_name }}"
                    data-primary-value="{{ $variant->primary_value ?? '' }}"
                    data-secondary-value="{{ $variant->secondary_value ?? '' }}"
                    data-tertiary-value="{{ $variant->tertiary_value ?? '' }}"
                    data-img="{{ $item->item_image ? asset($item->item_image) : asset('manager_asset/images/salespilot logo1.png') }}">
                @if($item->item_image)
                    <img src="{{ asset($item->item_image) }}" alt="{{ $item->item_name }}" onerror="this.src='{{ asset('manager_asset/images/salespilot logo1.png') }}'">
                @else
                    <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="{{ $item->item_name }}">
                @endif
                <div class="item-overlay">
                    <div class="item-name">{{ $item->item_name }}</div>
                    <div class="item-variant" style="font-size: 11px; margin: 4px 0;">
                      <span style="background: rgba(0, 0, 0, 0.7); color: #fff; padding: 3px 8px; border-radius: 12px; display: inline-block; font-weight: 500; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        {{ $variant->primary_value ?? '' }}
                        @if($variant->secondary_value) / {{ $variant->secondary_value }}@endif
                        @if($variant->tertiary_value) / {{ $variant->tertiary_value }}@endif
                      </span>
                    </div>
                    <div class="item-price">₦{{ number_format($variant->final_price ?? $variant->selling_price ?? 0, 2) }}</div>
                    <div class="item-stock">Stock: {{ $variant->stock_quantity ?? 0 }}</div>
                    @if(($variant->stock_quantity ?? 0) == 0)
                      <div class="sold-out-badge" style="font-weight:bold; color:#fff; background:#dc3545; padding:6px 14px; border-radius:6px; margin-top:8px; font-size:10px; letter-spacing:1px;">OUT OF STOCK</div>
                    @endif
                    @if($item->category_name)
                    <div class="item-category" style="font-size: 11px; color: #999;">{{ $item->category_name }}</div>
                    @endif
                </div>
                </div>
            @endif
            @endforeach
                @else
                    <!-- Variant item without variants configured yet -->
                    <div class="item-card disabled" style="opacity: 0.6; cursor: not-allowed;">
                    @if($item->item_image)
                        <img src="{{ asset($item->item_image) }}" alt="{{ $item->item_name }}" onerror="this.src='{{ asset('manager_asset/images/salespilot logo1.png') }}'">
                    @else
                        <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="{{ $item->item_name }}">
                    @endif
                    <div class="item-overlay">
                        <div class="item-name">{{ $item->item_name }}</div>
                        <div class="item-price" style="color: #999;">No variants</div>
                        <div class="item-stock">Configure variants</div>
                    </div>
                    </div>
                @endif
                @endif
            @empty
                <div class="no-items-message" style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p style="color: #999; margin-top: 20px;">No items available</p>
                <small style="color: #bbb;">Add items to your inventory to start selling</small>
                </div>
            @endforelse
            </div>
        </div>
        </div>

        <!-- Cart Panel -->
        <div class="cart-panel">
        <div class="cart-header">
            <i class="bi bi-cart3"></i> Cart
        </div>

        <!-- Customer Section -->
        <div class="customer-section" id="customerSection">
            <div class="customer-label">Customer</div>
            <div class="customer-display">
            <span class="customer-name" id="customerName">Walk-in Customer</span>
            <button class="customer-change" id="addCustomerBtn">
                <i class="bi bi-person-plus"></i> Change
            </button>
            </div>

            <!-- Customer Dropdown -->
            <div class="customer-dropdown" id="customerDropdown">
            <div class="customer-dropdown-search">
                <input type="text" id="customerSearchInput" placeholder="Search customers..." autocomplete="off">
            </div>
            <div class="customer-dropdown-list" id="customerDropdownList">
                <!-- Customers will be loaded here dynamically -->
            </div>
            </div>
        </div>

        <div class="cart-items" id="cartItems">
            <div class="cart-empty">
            <i class="bi bi-cart-x cart-empty-icon"></i>
            <p>Your cart is empty</p>
            <small>Add items to get started</small>
            </div>
        </div>

        <div class="cart-summary">
            <!-- Add Discount Button -->

            <button class="cart-action-btn" id="addDiscountBtn">
                <i class="bi bi-percent"></i> Add Discount
            </button>


            <!-- Discount Selection Side Panel -->
            <div class="side-panel-overlay" id="discountPanelOverlay" style="display:none;"></div>
            <div class="side-panel" id="discountSidePanel">
              <div class="side-panel-content">
                <div class="side-panel-header d-flex justify-content-between align-items-center">
                  <h5 class="side-panel-title mb-0"><i class="bi bi-percent me-2"></i>Select Discount</h5>
                  <button type="button" class="btn-close" id="closeDiscountPanel" aria-label="Close"></button>
                </div>
                <div class="side-panel-body">
                  <div class="mb-3">
                    <label for="discountSelect" class="form-label">Choose Discount</label>
                    <select id="discountSelect" class="form-select">
                      <option value="" selected disabled>Loading discounts...</option>
                    </select>
                  </div>
                </div>
                <div class="side-panel-footer d-flex justify-content-end gap-2">
                  <button type="button" class="btn btn-secondary" id="cancelDiscountBtn">Cancel</button>
                  <button type="button" class="btn btn-primary" id="applyDiscountBtn" disabled>Apply Discount</button>
                </div>
              </div>
            </div>

            <div class="cart-total">
            <span>Total:</span>
            <span id="cartTotal">₦0.00</span>
            </div>

            <!-- Cart Actions -->
            <div class="cart-actions">
            <button class="cart-action-btn" id="saveCartBtn">
                <i class="bi bi-bookmark"></i> Save
            </button>
            </div>

            <button class="checkout-btn" id="checkoutBtn">
            <i class="bi bi-check-circle"></i> Checkout
            </button>
        </div>
        </div>
    </div>
    </div>
    <!-- End Page Content -->

                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- Add Item Modal -->
    <div class="modal-overlay" id="itemModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="modalTitle"></h3>
          <button class="modal-close" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="item-preview" id="itemPreview">
            <img src="" alt="Item" id="modalItemImg">
            <div class="item-preview-info">
              <div class="item-preview-name" id="modalItemName"></div>
              <div class="item-preview-price" id="modalItemPrice"></div>
              <div class="item-preview-stock" id="modalItemStock"></div>
            </div>
          </div>

          <!-- Selling Price Input (hidden by default, shown for cost-only items) -->
          <div class="form-group" id="sellingPriceGroup" style="display:none;">
            <label class="form-label" for="modalSellingPrice">Selling Price <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="modalSellingPrice" min="0" step="0.01" placeholder="Enter selling price">
          </div>

          <div class="form-group">
            <label class="form-label">Quantity</label>
            <div class="quantity-control">
              <button class="quantity-btn" id="decreaseQty">-</button>
              <input type="number" class="form-control quantity-input" id="itemQuantity" value="1" min="1" autocomplete="off">
              <button class="quantity-btn" id="increaseQty">+</button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Note (Optional)</label>
            <textarea class="form-control" id="itemNote" rows="3" placeholder="Add any special instructions..."></textarea>
          </div>
        </div>
        <button class="add-to-cart-btn" id="addToCartBtn">
          <i class="bi bi-cart-plus"></i> Add to Cart
        </button>
      </div>
    </div>

    <!-- Customer Selection Modal -->
    <div class="modal-overlay" id="customerModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Select Customer</h3>
          <button class="modal-close" id="closeCustomerModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="customer-list" id="customerList">
            <!-- New Customer Option -->
            <div class="customer-list-item new-customer" data-customer-id="new">
              <div class="customer-info">
                <div class="customer-item-name">
                  <i class="bi bi-plus-circle"></i> New Customer
                </div>
                <div class="customer-item-phone">Add a new customer</div>
              </div>
              <i class="bi bi-chevron-right customer-select-icon"></i>
            </div>

            <!-- Walk-in Customer -->
            <div class="customer-list-item" data-customer-id="walk-in" data-customer-name="Walk-in Customer">
              <div class="customer-info">
                <div class="customer-item-name">Walk-in Customer</div>
                <div class="customer-item-phone">Anonymous customer</div>
              </div>
              <i class="bi bi-check-circle customer-select-icon"></i>
            </div>

            <!-- Sample Customers (will be loaded from localStorage/database) -->
            <div class="customer-list-item" data-customer-id="1" data-customer-name="John Doe" data-customer-phone="08012345678">
              <div class="customer-info">
                <div class="customer-item-name">John Doe</div>
                <div class="customer-item-phone">08012345678</div>
              </div>
              <i class="bi bi-check-circle customer-select-icon"></i>
            </div>

            <div class="customer-list-item" data-customer-id="2" data-customer-name="Jane Smith" data-customer-phone="08087654321">
              <div class="customer-info">
                <div class="customer-item-name">Jane Smith</div>
                <div class="customer-item-phone">08087654321</div>
              </div>
              <i class="bi bi-check-circle customer-select-icon"></i>
            </div>

            <div class="customer-list-item" data-customer-id="3" data-customer-name="David Johnson" data-customer-phone="08098765432">
              <div class="customer-info">
                <div class="customer-item-name">David Johnson</div>
                <div class="customer-item-phone">08098765432</div>
              </div>
              <i class="bi bi-check-circle customer-select-icon"></i>
            </div>
          </div>

          <!-- New Customer Form -->
          <form action="{{ route('manager.add_customer') }}" method="POST">
            @csrf
          <div class="new-customer-form" id="newCustomerForm">
            <h4>Add New Customer</h4>
            <div class="form-group">
              <label class="form-label">Customer Name *</label>
              <input type="text" class="form-control" id="newCustomerName" placeholder="Enter customer name" name="customer_name" required>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="newCustomerPhone" placeholder="080XXXXXXXX" name="phone">
              </div>
              <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" id="newCustomerEmail" placeholder="email@example.com" name="email">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Address</label>
              <textarea class="form-control" id="newCustomerAddress" rows="2" placeholder="Enter customer address" name="address"></textarea>
            </div>
            <button type="submit" class="select-customer-btn" id="saveNewCustomerBtn">
              <i class="bi bi-person-plus"></i> Add & Select Customer
            </button>
          </div>
        </form>
        </div>
        <button class="select-customer-btn" id="selectCustomerBtn">
          <i class="bi bi-check-circle"></i> Select Customer
        </button>
      </div>
    </div>

    <!-- Save Cart Modal -->
    <div class="modal-overlay" id="saveCartModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Save Cart</h3>
          <button class="modal-close" id="closeSaveCartModal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="saved-cart-warning">
            <i class="bi bi-info-circle"></i> Save this cart to retrieve it later from the Saved Carts page.
          </div>

          <div class="form-group">
            <label class="form-label">Cart Name *</label>
            <input type="text" class="form-control" id="savedCartName" placeholder="e.g., John's Order, Pending Sale #123" required>
          </div>

          <div class="form-group">
            <label class="form-label">Note (Optional)</label>
            <textarea class="form-control" id="savedCartNote" rows="3" placeholder="Add any notes about this cart..."></textarea>
          </div>

          <div class="saved-carts-list" id="savedCartsList">
            <small>Your saved carts will appear here</small>
          </div>
        </div>
        <button class="select-customer-btn" id="confirmSaveCartBtn">
          <i class="bi bi-bookmark-fill"></i> Save Cart
        </button>
      </div>
    </div>

    <!-- Receipt Modal -->
    <div class="receipt-modal" id="receiptModal">
      <div class="receipt-container">
        <div class="receipt-header">
          <h2><i class="bi bi-receipt"></i> Receipt</h2>
          <div class="business-name">SalesPilot Inventory</div>
          <div class="receipt-date" id="receiptDate"></div>
        </div>

        <div class="receipt-body">
          <div class="receipt-info">
            <div class="receipt-info-row">
              <span class="receipt-info-label">Receipt No:</span>
              <span class="receipt-info-value" id="receiptNumber"></span>
            </div>
            <div class="receipt-info-row">
              <span class="receipt-info-label">Customer:</span>
              <span class="receipt-info-value" id="receiptCustomer"></span>
            </div>
            <div class="receipt-info-row">
              <span class="receipt-info-label">Served By:</span>
              <span class="receipt-info-value">Manager</span>
            </div>
          </div>

          <div class="receipt-items">
            <div class="receipt-items-header">Items Purchased</div>
            <div id="receiptItemsList"></div>
          </div>

          <div class="receipt-totals">
            <div class="receipt-total-row">
              <span>Subtotal:</span>
              <span id="receiptSubtotal">₦0.00</span>
            </div>
            <div class="receipt-total-row">
              <span>Tax (0%):</span>
              <span>₦0.00</span>
            </div>
            <div class="receipt-total-row" id="receiptDiscountRow" style="display:none;">
              <span>Discount:</span>
              <span id="receiptDiscount"></span>
            </div>
            <div class="receipt-total-row grand-total">
              <span>Total:</span>
              <span id="receiptTotal">₦0.00</span>
            </div>
          </div>
        </div>

        <div class="receipt-footer">
          <div class="receipt-actions">
            <button class="receipt-btn receipt-btn-print" id="printReceiptBtn">
              <i class="bi bi-printer"></i> Print
            </button>
            <button class="receipt-btn receipt-btn-close" id="closeReceiptBtn">
              <i class="menu-icon bi bi-cart-fill"></i> Start New Sale
            </button>
          </div>
          <div class="receipt-thank-you">
            <i class="bi bi-heart-fill"></i> Thank you for your purchase!
          </div>





<script src="{{ asset('manager_asset/js/sell_product.js') }}"></script>

@endsection
