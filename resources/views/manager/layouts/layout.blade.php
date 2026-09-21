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
    <link rel="stylesheet" href="{{ asset('manager_asset/css/ajax-navigation.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Dark Mode CSS -->
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    <!-- Prevent FOUC -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ app_favicon() }}" />
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
      <img src="{{ app_logo() }}" alt="logo" />
    </a>
  </div>
</div>

<!-- Sidebar Navigation -->
@php
  $manager = Auth::user();
  // Determine user role for feature checks
  $isBusinessCreator = empty($manager->addby); // Owner/Business Creator has no addby

  // Feature slug prefixes based on role
  $posFeature = $isBusinessCreator ? 'pos_system' : 'manager_pos';
  $inventoryFeature = $isBusinessCreator ? 'advanced_inventory' : 'manager_inventory';
  $suppliersFeature = $isBusinessCreator ? 'supplier_management' : 'manager_suppliers';
  $customersFeature = $isBusinessCreator ? 'customer_management' : 'manager_customers';
  $activityLogsFeature = $isBusinessCreator ? 'activity_logs' : 'manager_activity_logs';
  $discountsFeature = $isBusinessCreator ? 'discounts_promotions' : 'manager_discounts';
  $branchesFeature = $isBusinessCreator ? 'multi_branch' : 'manager_view_branches';

  // Individual report features
  $salesSummaryFeature = $isBusinessCreator ? 'sales_summary' : 'manager_sales_summary';
  $salesByStaffFeature = $isBusinessCreator ? 'sales_by_staff' : 'manager_sales_by_staff';
  $salesByItemFeature = $isBusinessCreator ? 'sales_by_item' : 'manager_sales_by_item';
  $salesByCategoryFeature = $isBusinessCreator ? 'sales_by_category' : 'manager_sales_by_category';
  $inventoryValuationFeature = $isBusinessCreator ? 'inventory_valuation' : 'manager_inventory_valuation';
  $discountReportFeature = $isBusinessCreator ? 'discount_report' : 'manager_discount_report';
@endphp
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="{{ route('manager') }}">
        <i class="menu-icon bi bi-house-door-fill"></i>
        <span class="menu-title">Home</span>
      </a>
    </li>
    <li class="nav-item nav-category">Menu</li>

@if(user_has_feature($posFeature, $manager))
  <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.sell_product') }}">
        <i class="menu-icon bi bi-cart-fill"></i>
        <span class="menu-title">Point of Sale (POS)</span>
      </a>
    </li>
@endif


@if(user_has_feature($salesSummaryFeature, $manager) || user_has_feature($salesByStaffFeature, $manager) || user_has_feature($salesByItemFeature, $manager) || user_has_feature($salesByCategoryFeature, $manager))
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
@endif


@php
  // Check if user has any report features to show Reports menu
  $hasAnyReport = user_has_feature($salesSummaryFeature, $manager)
    || user_has_feature($salesByStaffFeature, $manager)
    || user_has_feature($salesByItemFeature, $manager)
    || user_has_feature($salesByCategoryFeature, $manager)
    || user_has_feature($inventoryValuationFeature, $manager)
    || user_has_feature($discountReportFeature, $manager);
@endphp
@if($hasAnyReport)
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
        <i class="menu-icon mdi mdi-card-text-outline"></i>
        <span class="menu-title">Reports Center</span>
        <i class="menu-arrow"></i>
      </a>
     <div class="collapse" id="form-elements">
        <ul class="nav flex-column sub-menu">
          @if(user_has_feature($salesSummaryFeature, $manager))
            <li class="nav-item"><a class="nav-link" href="{{ route('manager.sales_summary') }}">Sales Summary</a></li>
          @endif

          @if(user_has_feature($salesByStaffFeature, $manager))
            <li class="nav-item"><a class="nav-link" href=" {{ route('manager.staff_sales') }} ">Sales by Staff</a></li>
          @endif

          @if(user_has_feature($salesByItemFeature, $manager))
            <li class="nav-item"><a class="nav-link" href="{{ route('manager.sales_by_item') }} ">Sales by Item</a></li>
          @endif

          @if(user_has_feature($salesByCategoryFeature, $manager))
            <li class="nav-item"><a class="nav-link" href="{{ route('manager.sales_by_category') }}">Sales by Category</a></li>
          @endif

          @if(user_has_feature($inventoryValuationFeature, $manager))
            <li class="nav-item"><a class="nav-link" href="{{ route('manager.valuation_report') }}">Inventory Evaluation</a></li>
          @endif

          @if(user_has_feature($discountReportFeature, $manager))
            <li class="nav-item"><a class="nav-link" href="{{ route('manager.discount_report') }}">Discount Report</a></li>
          @endif
        </ul>
      </div>
    </li>
@endif

@if(user_has_feature($customersFeature, $manager) || user_has_feature($discountsFeature, $manager))
     <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#crm-menu" aria-expanded="false" aria-controls="crm-menu">
        <i class="menu-icon bi bi-people-fill"></i>
        <span class="menu-title">C R M</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="crm-menu">
        <ul class="nav flex-column sub-menu">
          @if(user_has_feature($customersFeature, $manager))
            <li class="nav-item"> <a class="nav-link" href="{{ route('manager.customers') }}">Customers</a></li>
          @endif
          @if(user_has_feature($discountsFeature, $manager))
            <li class="nav-item"> <a class="nav-link" href="{{ route('manager.add_discount') }}">Discount</a></li>
          @endif
        </ul>
      </div>
    </li>
@endif


    @if($isBusinessCreator)
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#add-staff" aria-expanded="false" aria-controls="add-staff">
        <i class="menu-icon bi bi-person-workspace"></i>
        <span class="menu-title">Users Management</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="add-staff">
        <ul class="nav flex-column sub-menu">
          @if(user_has_feature('manage_staff', $manager))
            <li class="nav-item"> <a class="nav-link" href="{{ route('manager.staff') }}">Staffs</a></li>
          @endif
          @if(user_has_feature('manage_managers', $manager))
            <li class="nav-item"> <a class="nav-link" href="{{ route('manager.manager') }}">Managers</a></li>
          @endif
        </ul>
      </div>
    </li>
    @else
      {{-- Managers can also manage staff if enabled --}}
      @if(user_has_feature('manager_manage_staff', $manager))
        <li class="nav-item">
          <a class="nav-link" href="{{ route('manager.staff') }}">
            <i class="menu-icon bi bi-person-workspace"></i>
            <span class="menu-title">Manage Staff</span>
          </a>
        </li>
      @endif
    @endif

@if(user_has_feature($branchesFeature, $manager))
  <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#add-branches" aria-expanded="false" aria-controls="add-branches">
        <i class="menu-icon bi bi-building"></i>
        <span class="menu-title">{{ $isBusinessCreator ? 'Branches Management' : 'View Branches' }}</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="add-branches">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="{{ route('manager.branches') }} ">Branches</a></li>
          @if($isBusinessCreator)
            <li class="nav-item"> <a class="nav-link" href="{{ route('manager.inventory.allocation') }} ">Allocate Branch</a></li>
          @endif
        </ul>
      </div>
    </li>
@endif




@if(user_has_feature($activityLogsFeature, $manager))
    <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.activity_logs') }}">
        <i class="menu-icon bi bi-activity"></i>
        <span class="menu-title">Activity Logs</span>
      </a>
    </li>
@endif

@if(user_has_feature($inventoryFeature, $manager))
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
          <li class="nav-item"> <a class="nav-link" href="{{ route('manager.units') }}">Units</a></li>
         {{--   <li class="nav-item"> <a class="nav-link" href="views/stock_history.php">Stock History</a></li>  --}}
        </ul>
      </div>
    </li>
@endif

@if(user_has_feature($suppliersFeature, $manager))
  <li class="nav-item">
      <a class="nav-link" href="{{ route('manager.suppliers') }} ">
        <i class="menu-icon bi bi-truck"></i>
        <span class="menu-title">Suppliers</span>
      </a>
    </li>
@endif



   <li class="nav-item">
      <button type="button" class="theme-toggle-btn nav-link" title="Toggle theme" style="border: none; background: transparent;">
          <i class="bi bi-moon-stars-fill fs-5"></i>
      </button>
   </li>

   <li class="nav-item dropdown user-dropdown">
      <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer; display: flex; align-items: center; padding: 15px 20px;">
        <img class="img-xs rounded-circle" src="{{ $manager && $manager->business_logo ? asset('business_logos/' . $manager->business_logo) : asset('manager_asset/assets/images/faces/face8.jpg') }}" alt="Profile image" style="width: 40px; height: 40px; object-fit: cover;">
      </a>
      <div class="dropdown-menu dropdown-menu-end navbar-dropdown" aria-labelledby="UserDropdown" style="min-width: 250px;">
        <div class="dropdown-header text-center" style="padding: 20px;">
          <img class="img-md rounded-circle" src="{{ $manager && $manager->business_logo ? asset('business_logos/' . $manager->business_logo) : asset('manager_asset/assets/images/faces/face8.jpg') }}" alt="Profile image" style="width: 80px; height: 80px; object-fit: cover;">
          <p class="mb-1 mt-3 fw-semibold">{{ $manager && $manager->business_name ? $manager->business_name : 'Business Name' }}</p>
          <p class="fw-light text-muted mb-0">{{ $manager && $manager->email ? $manager->email : 'email@example.com' }}</p>
        </div>
        <a class="dropdown-item" href="{{ route('manager.profile.show') }}" style="padding: 10px 20px;"><i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span class="badge badge-pill badge-danger">1</span></a>
        @if(empty($manager->addby))
          <a class="dropdown-item" href="{{ route('manager.system.preferences') }}"><i class="dropdown-item-icon bi bi-gear-wide text-primary me-2"></i> System Preference</a>
        @endif


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
                Copyright © {{ date('Y') }} {{ app_name() }}. All rights reserved.
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

    <!-- AJAX Navigation Script - Load pages without full refresh -->
    <script src="{{ asset('manager_asset/js/ajax-navigation.js') }}"></script>

    <!-- Page-specific scripts -->
    @yield('page_scripts')

    <!-- Theme Toggle JS -->
    <script src="{{ asset('js/theme-toggle.js') }}"></script>

    <script>
      // Force all sidebar dropdowns to stay closed on page load
      document.addEventListener('DOMContentLoaded', function() {
        var dropdownIds = ['form-elements', 'crm-menu', 'add-staff', 'icons'];
        dropdownIds.forEach(function(id) {
          var el = document.getElementById(id);
          if (el && el.classList.contains('show')) {
            var collapse = bootstrap.Collapse.getOrCreateInstance(el, {toggle: false});
            collapse.hide();
          }
        });
      });
    </script>

    <!-- AJAX Loading Spinner -->
    <div id="ajaxLoadingSpinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.3); z-index: 9999; justify-content: center; align-items: center;">
      <div style="background-color: white; border-radius: ; padding: 2rem; text-align: center; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted">Loading content...</p>
      </div>
    </div>

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

          </div>
        </div>
      </div>
    </div>
    <!-- end of item type selection modal -->

    {{-- AI POS Copilot Chat Interface --}}
   {{--  <x-copilot-modal /> --}}

    {{-- BRM Floating Badge & Contact Modal --}}
    @php
        $brmContact = null;
        if ($isBusinessCreator && $manager->brm_id) {
            $brmContact = $manager->brm;
        }
    @endphp

    @if($brmContact)
    {{-- Floating BRM Badge --}}
    <div id="brmBadge" onclick="document.getElementById('brmContactModal').classList.add('show-modal')" title="Your Relationship Manager" style="
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 2000;
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        color: #fff;
        padding: 10px 18px 10px 12px;
        border-radius: 50px;
        box-shadow: 0 8px 32px rgba(15,52,96,0.45), 0 2px 8px rgba(0,0,0,0.25);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1.5px solid rgba(99,179,237,0.25);
        user-select: none;
    "
    onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 40px rgba(15,52,96,0.55), 0 4px 16px rgba(0,0,0,0.3)';"
    onmouseleave="this.style.transform='';this.style.boxShadow='0 8px 32px rgba(15,52,96,0.45), 0 2px 8px rgba(0,0,0,0.25)';"
    >
        <div style="position:relative;">
            <img
                src="{{ $brmContact->profile_photo ? asset('brm_photos/'.$brmContact->profile_photo) : asset('manager_asset/assets/images/faces/face8.jpg') }}"
                alt="{{ $brmContact->name }}"
                style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid rgba(99,179,237,0.5);"
            >
            <span style="position:absolute;bottom:0;right:0;width:11px;height:11px;background:#48bb78;border-radius:50%;border:2px solid #1a1a2e;"></span>
        </div>
        <div style="line-height:1.2;">
            <div style="font-size:0.65rem;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Your BRM</div>
            <div style="font-size:0.88rem;font-weight:700;color:#fff;">{{ $brmContact->name }}</div>
        </div>
        <i class="bi bi-chat-dots-fill" style="font-size:1rem;color:#63b3ed;margin-left:4px;"></i>
    </div>

    {{-- BRM Contact Modal Overlay --}}
    <div id="brmContactModal" style="
        display:none;
        position:fixed;inset:0;z-index:3000;
        background:rgba(0,0,0,0.55);
        backdrop-filter:blur(4px);
        justify-content:center;
        align-items:center;
        transition:opacity 0.25s;
    " onclick="if(event.target===this)this.classList.remove('show-modal')">
        <div style="
            background: linear-gradient(160deg,#fff 60%,#ebf8ff 100%);
            border-radius: 20px;
            padding: 0;
            width: 100%;
            max-width: 400px;
            margin: 1rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: brmSlideIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
        ">
            {{-- Modal Header --}}
            <div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:28px 24px 20px;text-align:center;position:relative;">
                <button onclick="document.getElementById('brmContactModal').classList.remove('show-modal')" style="
                    position:absolute;top:14px;right:16px;
                    background:rgba(255,255,255,0.12);border:none;color:#fff;
                    width:30px;height:30px;border-radius:50%;cursor:pointer;
                    font-size:1rem;display:flex;align-items:center;justify-content:center;
                    transition:background 0.2s;
                " onmouseenter="this.style.background='rgba(255,255,255,0.25)'" onmouseleave="this.style.background='rgba(255,255,255,0.12)'"
                >&times;</button>

                <img
                    src="{{ $brmContact->profile_photo ? asset('brm_photos/'.$brmContact->profile_photo) : asset('manager_asset/assets/images/faces/face8.jpg') }}"
                    alt="{{ $brmContact->name }}"
                    style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid rgba(99,179,237,0.6);margin-bottom:12px;"
                >
                <h5 style="color:#fff;margin:0 0 4px;font-weight:700;font-size:1.15rem;">{{ $brmContact->name }}</h5>
                <span style="font-size:0.75rem;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:0.08em;">Business Relationship Manager</span>
                <div style="margin-top:10px;">
                    <span style="display:inline-flex;align-items:center;gap:5px;background:rgba(72,187,120,0.2);color:#68d391;border:1px solid rgba(72,187,120,0.35);border-radius:20px;padding:3px 12px;font-size:0.72rem;font-weight:600;">
                        <span style="width:7px;height:7px;background:#48bb78;border-radius:50%;display:inline-block;"></span> Available
                    </span>
                </div>
            </div>

            {{-- Modal Body --}}
            <div style="padding:24px;">
                <p style="font-size:0.82rem;color:#718096;text-align:center;margin:0 0 20px;">Your dedicated manager is here to help. Reach out anytime.</p>

                {{-- Phone --}}
                @if($brmContact->phone)
                <a href="tel:{{ $brmContact->phone }}" style="
                    display:flex;align-items:center;gap:14px;
                    padding:13px 16px;border-radius:12px;
                    background:#f0fff4;border:1px solid #c6f6d5;
                    text-decoration:none;margin-bottom:10px;
                    transition:background 0.2s;
                " onmouseenter="this.style.background='#c6f6d5'" onmouseleave="this.style.background='#f0fff4'">
                    <div style="width:40px;height:40px;background:#48bb78;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-telephone-fill" style="color:#fff;font-size:1rem;"></i>
                    </div>
                    <div>
                        <div style="font-size:0.7rem;color:#2d6a4f;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Call Directly</div>
                        <div style="font-size:0.95rem;color:#276749;font-weight:700;">{{ $brmContact->phone }}</div>
                    </div>
                    <i class="bi bi-arrow-right" style="margin-left:auto;color:#48bb78;"></i>
                </a>
                @endif

                {{-- Email --}}
                @if($brmContact->email)
                <a href="mailto:{{ $brmContact->email }}" style="
                    display:flex;align-items:center;gap:14px;
                    padding:13px 16px;border-radius:12px;
                    background:#ebf8ff;border:1px solid #bee3f8;
                    text-decoration:none;margin-bottom:10px;
                    transition:background 0.2s;
                " onmouseenter="this.style.background='#bee3f8'" onmouseleave="this.style.background='#ebf8ff'">
                    <div style="width:40px;height:40px;background:#4299e1;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-envelope-fill" style="color:#fff;font-size:1rem;"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:0.7rem;color:#2b6cb0;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Send Email</div>
                        <div style="font-size:0.88rem;color:#2c5282;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $brmContact->email }}</div>
                    </div>
                    <i class="bi bi-arrow-right" style="margin-left:auto;color:#4299e1;"></i>
                </a>
                @endif

                {{-- Rate BRM Button --}}
                <a href="mailto:{{ $brmContact->email }}?subject=Rating+for+my+BRM+{{ urlencode($brmContact->name) }}&body=Hi+{{ urlencode($brmContact->name) }},%0A%0AI+would+like+to+share+my+feedback+about+your+service.%0A%0ARating+(1-5):%0AComments:%0A" style="
                    display:flex;align-items:center;justify-content:center;gap:8px;
                    padding:12px;border-radius:12px;
                    background:linear-gradient(135deg,#667eea,#764ba2);
                    color:#fff;text-decoration:none;font-weight:600;font-size:0.9rem;
                    transition:opacity 0.2s;
                " onmouseenter="this.style.opacity='0.88'" onmouseleave="this.style.opacity='1'">
                    <i class="bi bi-star-fill"></i> Rate Your BRM
                </a>
            </div>
        </div>
    </div>

    <style>
        #brmContactModal.show-modal { display: flex !important; }
        @keyframes brmSlideIn {
            from { opacity: 0; transform: scale(0.85) translateY(20px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>
    @endif

  </body>
</html>
