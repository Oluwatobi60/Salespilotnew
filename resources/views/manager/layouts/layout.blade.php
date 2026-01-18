<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('manager_page_title')</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- inject:css -->
     <link rel="stylesheet" href="{{ asset('manager_asset/css/style.css') }}">
      <link rel="stylesheet" href="{{ asset('manager_asset/css/home.css') }}">
   <link rel="stylesheet" href="{{ asset('manager_asset/css/sidebar_style.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/css/sell_product.css') }}">
    <link rel="stylesheet" href="{{ asset('manager_asset/css/all_items_style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('manager_asset/images/favicon.png') }}" />
  </head>
  <body class="with-welcome-text">

    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">
        <!-- Include Sidebar Content -->

        </nav>
<!-- Fixed Logo Container -->
<div class="fixed-logo-container">
  <div class="logo-hamburger-wrapper">
    <button id="sidebarToggle" class="navbar-toggler navbar-toggler align-self-center" type="button" aria-label="Toggle sidebar" title="Toggle sidebar">
      <i class="bi bi-list"></i>
    </button>
    <a class="navbar-brand brand-logo" href="{{ route('manager') }}">
      <img src="{{ asset('manager_asset/images/salespilot logo1.png') }}" alt="logo" />
    </a>
  </div>
</div>

<!-- Sidebar Navigation -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="{{ route('manager') }}">
        <i class="menu-icon bi bi-house-door-fill"></i>
        <span class="menu-title">Home</span>
      </a>
    </li>
    <li class="nav-item nav-category">Menu</li>

  <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.sell_product') }}">
        <i class="menu-icon bi bi-cart-fill"></i>
        <span class="menu-title">Sell</span>
      </a>
    </li>


    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon bi bi-wallet-fill"></i>
        <span class="menu-title">Sales</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('manager.completed_sales') }} ">Completed Sales</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('manager.view_saved_carts') }}">Saved Carts</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
        <i class="menu-icon mdi mdi-card-text-outline"></i>
        <span class="menu-title">Reports</span>
        <i class="menu-arrow"></i>
      </a>
     <div class="collapse" id="form-elements">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="{{ route('manager.sales_summary') }}">Sales Summary</a></li>
          <li class="nav-item"><a class="nav-link" href=" {{ route('manager.staff_sales') }} ">Sales by Staff</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('manager.sales_by_item') }} ">Sales by Item</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('manager.sales_by_category') }}">Sales by Category</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('manager.valuation_report') }}">Inventory Valuation</a></li>
      {{--      <li class="nav-item"><a class="nav-link" href="{{ route('manager.taxes') }}">Taxes</a></li>  --}}
          <li class="nav-item"><a class="nav-link" href="{{ route('manager.discount_report') }}">Discount Report</a></li>
        </ul>
      </div>
     <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#crm-menu" aria-expanded="false" aria-controls="crm-menu">
        <i class="menu-icon bi bi-people-fill"></i>
        <span class="menu-title">C R M</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="crm-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('manager.customers') }}">Customers</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('manager.add_discount') }}">Discount</a></li>
        </ul>
      </div>
    </li>

   <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.staff') }}">
        <i class="menu-icon bi bi-person-workspace"></i>
        <span class="menu-title">Staffs</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.activity_logs') }}">
        <i class="menu-icon bi bi-activity"></i>
        <span class="menu-title">Activity Logs</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
       <i class="menu-icon bi bi-shop-window"></i>
        <span class="menu-title">Inventory</span>
        <i class="menu-arrow"></i>
      </a>
     <div class="collapse" id="icons">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('all_items') }}">All items</a></li>
          <li class="nav-item"> <a class="nav-link" href="{{ route('all_categories') }}">Categories</a></li>
         {{--   <li class="nav-item"> <a class="nav-link" href="views/stock_history.php">Stock History</a></li>  --}}
        </ul>
      </div>
    </li>
  <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.suppliers') }} ">
        <i class="menu-icon bi bi-truck"></i>
        <span class="menu-title">Suppliers</span>
      </a>
    </li>



   <li class="nav-item dropdown user-dropdown">
      <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer; display: flex; align-items: center; padding: 15px 20px;">
        <img class="img-xs rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image" style="width: 40px; height: 40px; object-fit: cover;">
      </a>
      <div class="dropdown-menu dropdown-menu-end navbar-dropdown" aria-labelledby="UserDropdown" style="min-width: 250px;">
        <div class="dropdown-header text-center" style="padding: 20px;">
          <img class="img-md rounded-circle" src="assets/images/faces/face8.jpg" alt="Profile image" style="width: 80px; height: 80px; object-fit: cover;">
          <p class="mb-1 mt-3 fw-semibold">Allen Moreno</p>
          <p class="fw-light text-muted mb-0">allenmoreno@gmail.com</p>
        </div>
        <a class="dropdown-item" href="views/profile.php" style="padding: 10px 20px;"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
        <a class="dropdown-item" href="views/settings.php" style="padding: 10px 20px;"><i class="dropdown-item-icon bi bi-gear-wide text-primary me-2"></i> System Preference</a>
        <a class="dropdown-item" href="#" style="padding: 10px 20px;"><i class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
        <a class="dropdown-item" href="views/activity_logs.php" style="padding: 10px 20px;"><i class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i> Activity</a>
        <a class="dropdown-item" href="#" style="padding: 10px 20px;"><i class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
       {{--   <a class="dropdown-item" href="../index.php" style="padding: 10px 20px;"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out</a>  --}}

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="dropdown-item"><i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i> Sign Out</button>
        </form>
      </div>
    </li>
  </ul>
</nav>

        <!-- Main Dashboard Content Area -->
        <div class="main-panel">
                <div class="content-wrapper">

                @yield('manager_layout_content')

                </div>



                 <!-- Footer -->
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">

              <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">
                Copyright © 2025. All rights reserved.
              </span>
            </div>
          </footer>
        </div>
      </div>
    </div>

    <!-- plugins:js -->
    <script src="{{ asset('manager_asset/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <!-- <script src="assets/js/off-canvas.js"></script> Commented out to avoid conflicts -->
  <script src="{{ asset('manager_asset/js/template.js') }}"></script>
    <!-- <script src="assets/js/hoverable-collapse.js"></script> Commented out to avoid conflicts -->
    <!-- endinject -->

    <!-- Bootstrap JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS - Load globally for all pages -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Include Sidebar Scripts -->
    <script src="{{ asset('manager_asset/js/sidebar_scripts.js') }}"></script>

    <!-- Minimal Bootstrap Collapse Test Script and Add Item Modal Fallback -->

    <script src="{{ asset('manager_asset/js/sidebar1.js') }}"></script>
    <!-- Modal for selecting item type - Properly positioned at body level -->
    <div class="modal fade" id="itemTypeModal" tabindex="-1" aria-labelledby="itemTypeModalLabel" aria-hidden="true" style="z-index: 1055;">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 900px; max-height: 90vh;">
        <div class="modal-content" style="border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); border-radius: 15px; max-height: 90vh;">
          <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none; padding: 1rem 2rem; border-radius: 15px 15px 0 0; flex-shrink: 0;">
            <h5 class="modal-title" id="itemTypeModalLabel" style="font-weight: 600; font-size: 1.25rem;">
              <i class="bi bi-box-seam me-2"></i>Select Item Type
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" style="overflow-y: auto; padding: 1.5rem; flex: 1 1 auto;">
            <div class="row g-4">
              <!-- Left Column - Item Type Options -->
              <div class="col-md-4">
                <div class="list-group" style="display: flex; flex-direction: column; gap: 0.75rem;">
                  <button type="button" class="list-group-item list-group-item-action d-flex align-items-center p-3 item-option active"
                          data-type="standard" onclick="showItemDetails('standard')" style="border: 2px solid #007bff; background-color: #e3f2fd; border-radius: 10px; transition: all 0.3s ease;">
                    <i class="bi bi-box-seam text-primary me-3" style="font-size: 1.75rem;"></i>
                    <div>
                      <h6 class="mb-0 text-primary" style="font-weight: 600;">Standard Item</h6>
                      <small class="text-muted">Simple single product</small>
                    </div>
                  </button>

                  <button type="button" class="list-group-item list-group-item-action d-flex align-items-center p-3 item-option"
                          data-type="variant" onclick="showItemDetails('variant')" style="border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease;">
                    <i class="bi bi-grid-3x3 text-success me-3" style="font-size: 1.75rem;"></i>
                    <div>
                      <h6 class="mb-0 text-success" style="font-weight: 600;">Variant Item</h6>
                      <small class="text-muted">Multiple variations</small>
                    </div>
                  </button>

                 {{--   <button type="button" class="list-group-item list-group-item-action d-flex align-items-center p-3 item-option"
                          data-type="bundled" onclick="showItemDetails('bundled')" style="border: 2px solid #e0e0e0; border-radius: 10px; transition: all 0.3s ease;">
                    <i class="bi bi-collection text-warning me-3" style="font-size: 1.75rem;"></i>
                    <div>
                      <h6 class="mb-0 text-warning" style="font-weight: 600;">Bundled Item</h6>
                      <small class="text-muted">Package of products</small>
                    </div>
                  </button>  --}}
                </div>
              </div>

              <!-- Right Column - Item Details -->
              <div class="col-md-8">
                <div class="item-details-container" style="background-color: #f8f9fa; border-radius: 10px; padding: 1.25rem;">
                  <!-- Standard Item Details -->
                  <div id="standard-details" class="item-details active" style="display: block; opacity: 1;">
                    <div class="d-flex align-items-start mb-3">
                      <i class="bi bi-box-seam text-primary me-3" style="font-size: 3rem;"></i>
                      <div>
                        <h4 class="text-primary mb-2">
                          <i class="bi bi-check-circle-fill me-1"></i> Standard Item
                        </h4>
                        <p class="text-dark mb-3">
                          A simple product with a single SKU, tracked individually with its own price and stock quantity.
                        </p>
                      </div>
                    </div>

                    <div class="mb-3">
                      <h6><strong>Best for:</strong></h6>
                      <ul class="text-muted mb-3">
                        <li>Products without variations (no size, color, or model options)</li>
                        <li>Individual items with unique barcodes</li>
                        <li>Simple inventory tracking</li>
                      </ul>
                    </div>

                    <div class="mb-4">
                      <h6><strong>Examples:</strong></h6>
                      <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary-subtle text-primary border">Laptop Model XYZ</span>
                        <span class="badge bg-primary-subtle text-primary border">Office Chair</span>
                        <span class="badge bg-primary-subtle text-primary border">USB Cable 2m</span>
                        <span class="badge bg-primary-subtle text-primary border">Water Bottle</span>
                        <span class="badge bg-primary-subtle text-primary border">Notebook A4</span>
                      </div>
                    </div>

                   <a href="{{ route('manager.add_item_standard') }}" class="text-decoration-none text-white">  <button class="btn btn-primary"  style="padding: 0.75rem 1.5rem;">
                      <i class="bi bi-plus-circle me-1"></i> Create Standard Item
                    </button></a>
                  </div>

                  <!-- Variant Item Details -->
                  <div id="variant-details" class="item-details" style="display: none; opacity: 0;">
                    <div class="d-flex align-items-start mb-3">
                      <i class="bi bi-grid-3x3 text-success me-3" style="font-size: 3rem;"></i>
                      <div>
                        <h4 class="text-success mb-2">
                          <i class="bi bi-grid-fill me-1"></i> Variant Item
                        </h4>
                        <p class="text-dark mb-3">
                          A product available in multiple variations (e.g., different sizes, colors, or styles). Each variant has its own SKU, price, and stock level.
                        </p>
                      </div>
                    </div>

                    <div class="mb-3">
                      <h6><strong>Best for:</strong></h6>
                      <ul class="text-muted mb-3">
                        <li>Products with multiple size options (S, M, L, XL)</li>
                        <li>Items available in different colors or patterns</li>
                        <li>Products with different specifications or models</li>
                        <li>Tracking inventory per variant combination</li>
                      </ul>
                    </div>

                    <div class="mb-4">
                      <h6><strong>Examples:</strong></h6>
                      <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-success-subtle text-success border">T-Shirt (Red/Blue/Green, S/M/L/XL)</span>
                        <span class="badge bg-success-subtle text-success border">Shoes (Size 6-12, Black/White)</span>
                        <span class="badge bg-success-subtle text-success border">Phone Case (iPhone/Samsung, Colors)</span>
                        <span class="badge bg-success-subtle text-success border">Jeans (Size 28-38, Regular/Slim)</span>
                      </div>
                    </div>

                    <a href="{{ route('manager.add_item_variant')}}" class="text-decoration-none text-white"><button class="btn btn-success"  style="padding: 0.75rem 1.5rem;">
                      <i class="bi bi-plus-circle me-1"></i> Create Variant Item
                    </button></a>
                  </div>

                  <!-- Bundled Item Details -->
                  <div id="bundled-details" class="item-details" style="display: none; opacity: 0;">
                    <div class="d-flex align-items-start mb-3">
                      <i class="bi bi-collection text-warning me-3" style="font-size: 3rem;"></i>
                      <div>
                        <h4 class="text-warning mb-2">
                          <i class="bi bi-box2-fill me-1"></i> Bundled Item
                        </h4>
                        <p class="text-dark mb-3">
                          A package or combo containing multiple existing products sold together as one unit. Selling a bundle automatically deducts stock from all included items.
                        </p>
                      </div>
                    </div>

                    <div class="mb-3">
                      <h6><strong>Best for:</strong></h6>
                      <ul class="text-muted mb-3">
                        <li>Product packages or gift sets</li>
                        <li>Promotional combos and special offers</li>
                        <li>Starter kits or complete sets</li>
                        <li>Value packs with multiple related items</li>
                      </ul>
                    </div>

                    <div class="mb-4">
                      <h6><strong>Examples:</strong></h6>
                      <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-warning-subtle text-warning border">Office Starter Kit (Pen + Notepad + Stapler)</span>
                        <span class="badge bg-warning-subtle text-warning border">Gaming Bundle (Mouse + Keyboard + Headset)</span>
                        <span class="badge bg-warning-subtle text-warning border">Skincare Set (Cleanser + Toner + Moisturizer)</span>
                        <span class="badge bg-warning-subtle text-warning border">Back to School Pack</span>
                      </div>
                    </div>

                    <a href="#" class="text-decoration-none text-white"><button class="btn btn-warning text-dark"  style="padding: 0.75rem 1.5rem;">
                      <i class="bi bi-plus-circle me-1"></i> Create Bundled Item
                    </button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="border-top: 1px solid #dee2e6; background-color: #f8f9fa; padding: 1rem 1.5rem; flex-shrink: 0;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 0.75rem 1.5rem;">Cancel</button>
          {{--  <button type="button" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">
              <i class="bi bi-arrow-right me-1"></i>Continue
            </button> --}}
          </div>
        </div>
      </div>
    </div>
    <!-- end of item type selection modal -->

  </body>
</html>
