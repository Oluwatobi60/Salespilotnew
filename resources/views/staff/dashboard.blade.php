@extends('staff.layouts.layout')
@section('staff_page_title')
Staff Dashboard
@endsection

@section('staff_layout_content')

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{ asset('staff_asset/css/styles.css') }}">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Replace alert() with SweetAlert2
function showSuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        confirmButtonColor: '#3085d6',
    });
}
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#d33',
    });
}
function showInfo(message) {
    Swal.fire({
        icon: 'info',
        title: 'Info',
        text: message,
        confirmButtonColor: '#3085d6',
    });
}
// Example: Replace alert('Cart saved!') with showSuccess('Cart saved!')
// You can now use showSuccess(), showError(), showInfo() in your JS code
</script>

<div class="container-scroller">



            <!-- Sell content starts here -->
<div class="row">
<div class="col-sm-12">
<div class="home-tab">

<!-- Page Content -->
<div class="dashboard-full-page">
    <div class="dashboard-container">
        <div class="dashboard-header-section">
            <div class="dashboard-title-wrapper">
                <h1 class="dashboard-main-title">
                    <i class="bi bi-speedometer2"></i> Staff Dashboard
                </h1>
                <p class="dashboard-subtitle">Welcome back, <strong>{{ $staff->fullname ?? ($staff->firstname . ' ' . $staff->surname) }}</strong> 👋</p>
                <p class="dashboard-subtitle-secondary">Track your sales performance, customer relationships, and pending orders</p>
            </div>
            <div class="dashboard-date-time">
                <span id="current-date"></span>
            </div>
        </div>

        <div class="dashboard-grid-full">
            <!-- Sales Today Card -->
            <div class="dashboard-card dashboard-card-primary">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <div class="card-label">Today's Sales</div>
                </div>
                <div class="card-value">{{ $completed_sales_today_count }}</div>
                <div class="card-sublabel">Sales completed</div>
                <div class="card-amount">{{ number_format($completed_sales_today_total, 2) }}</div>
                <div class="card-currency">Currency</div>
                <div class="card-note">Completed transactions today</div>
            </div>

            <!-- Sales This Month Card -->
            <div class="dashboard-card dashboard-card-success">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div class="card-label">Monthly Sales</div>
                </div>
                <div class="card-value">{{ $completed_sales_month_count }}</div>
                <div class="card-sublabel">Sales this month</div>
                <div class="card-amount">{{ number_format($completed_sales_month_total, 2) }}</div>
                <div class="card-currency">Currency</div>
                <div class="card-note">This month's transactions</div>
            </div>

            <!-- Saved Orders Card -->
            <div class="dashboard-card dashboard-card-info">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-bookmark-star"></i>
                    </div>
                    <div class="card-label">Saved Orders</div>
                </div>
                <div class="card-value">{{ $saved_orders_count }}</div>
                <div class="card-sublabel">Ready to checkout</div>
                <div class="card-note">Pending orders awaiting finalization</div>
            </div>

            <!-- Customers Added Card -->
            <div class="dashboard-card dashboard-card-warning">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="card-label">Customers Added</div>
                </div>
                <div class="card-value">{{ $new_customers_count }}</div>
                <div class="card-sublabel">Total registered</div>
                <div class="card-note">Customers you've added to the system</div>
            </div>
        </div>

        <div class="dashboard-actions-full">
            <a href="{{ route('staff.sell_product') }}" class="dashboard-action-btn-full primary">
                <i class="bi bi-bag-check"></i>
                <span>Start Sale</span>
                <small>Begin a new transaction</small>
            </a>
            <a href="{{ route('staff.view_saved_carts') }}" class="dashboard-action-btn-full secondary">
                <i class="bi bi-bookmark-star"></i>
                <span>Saved Orders</span>
                <small>Manage pending orders</small>
            </a>
            <a href="{{ route('staff.customers') }}" class="dashboard-action-btn-full secondary">
                <i class="bi bi-people"></i>
                <span>Customers</span>
                <small>View customer details</small>
            </a>
        </div>

        <div class="dashboard-footer-full">
            <p>Your dashboard provides real-time insights into sales activity and customer engagement. Keep an eye on your metrics to optimize performance.</p>
        </div>
    </div>
</div>

<script>
// Update current date/time
document.addEventListener('DOMContentLoaded', function() {
    const dateEl = document.getElementById('current-date');
    if (dateEl) {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateEl.textContent = now.toLocaleDateString('en-US', options);
    }
});
</script>

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



          </div>






<script src="{{ asset('staff_asset/js/sell_product.js') }}"></script>

@endsection
