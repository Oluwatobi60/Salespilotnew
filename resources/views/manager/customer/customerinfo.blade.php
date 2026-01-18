@extends('manager.layouts.layout')
@section('manager_page_title')
Customer Information
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/customer_style.css') }}">
   <div class="content-wrapper">
            <!-- Customers content starts here -->

            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            confirmButtonColor: '#d33'
                        });
                    });
                </script>
            @endif

            @if($errors->any())
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error!',
                            html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
                            confirmButtonColor: '#d33'
                        });
                    });
                </script>
            @endif

            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h4 class="card-title mb-0">Customers</h4>
                      {{--  <button type="button" class="btn btn-primary" style="min-width: 150px;" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><strong>+ Add Customer</strong></button>  --}}
                    </div>
                    <p class="card-description">Manage your customer database.</p>

                    <!-- Search and Filter Options -->
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search customers..." id="customerSearchInput">
                          <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                        <!-- Staff Filter -->
                        <select class="form-select" id="staffFilter" style="max-width: 140px;">
                          <option value="">All Staff</option>
                        </select>


                        <button class="btn btn-outline-success" id="exportCustomers">
                          <i class="bi bi-download"></i> Export
                        </button>
                      </div>
                    </div><br>

                    <div class="table-responsive">
                      <table class="table table-striped" id="customersTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date Registered</th>
                            <th>Added by</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $index => $customer)
                            <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $customer->customer_name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone_number }}</td>
                            <td>{{ $customer->created_at }}</td>
                            <td>
                              @if($customer->user)
                                {{ $customer->user->name }}
                              @elseif($customer->staff)
                                {{ $customer->staff->fullname }}
                              @else
                                -
                              @endif
                            </td>
                            <td>
                              <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm btn-info view-btn" data-customer-id="{{ $customer->id }}" title="View Details">
                                  <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-primary edit-btn" data-customer-id="{{ $customer->id }}" title="Edit Customer">
                                  <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn"
                                        data-customer-id="{{ $customer->id }}"
                                        data-customer-name="{{ $customer->customer_name }}"
                                        title="Delete Customer">
                                  <i class="bi bi-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                            <div class="py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">No Customer found</p>
                            </div>
                            </td>
                        </tr>
                            @endforelse

                        </tbody>
                      </table>
                    </div>

                       <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{ $customers->firstItem() ?? 0 }}-{{ $customers->lastItem() ?? 0 }}</strong> of <strong>{{ $customers->total() }}</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        {{ $customers->links('pagination::bootstrap-5') }}
                      </nav>
                    </div>

                    <!-- Panel Backdrop -->
                    <div class="panel-backdrop" id="panelBackdrop"></div>

                    <!-- Customer Details Side Panel -->
                    <div class="customer-details-panel" id="customerDetailsPanel">
                      <div class="customer-details-container">
                        <div class="customer-details-header">
                          <h5 class="customer-details-title">
                            <i class="bi bi-person-circle"></i>Customer Details
                          </h5>
                          <button class="close-details-btn" id="closeDetailsBtn">
                            <i class="bi bi-x-lg"></i>
                          </button>
                        </div>

                        <div class="customer-details-content">
                          <!-- Modern Tab Navigation -->
                          <div class="customer-tabs">
                            <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                              <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                                  <i class="bi bi-person me-1"></i>Overview
                                </button>
                              </li>
                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                                  <i class="bi bi-envelope me-1"></i>Contact
                                </button>
                              </li>
                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases" type="button" role="tab" aria-controls="purchases" aria-selected="false">
                                  <i class="bi bi-bag me-1"></i>Orders
                                </button>
                              </li>
                              <li class="nav-item" role="presentation">
                                <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                                  <i class="bi bi-clock-history me-1"></i>Activity
                                </button>
                              </li>
                            </ul>
                          </div>

                          <!-- Tab Content -->
                          <div class="tab-content" id="customerTabContent">
                            <!-- Overview Tab -->
                            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                              <div class="info-section">
                                <div class="info-item">
                                  <div class="info-label">Customer ID</div>
                                  <div class="info-value" id="detailCustomerId">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Full Name</div>
                                  <div class="info-value" id="detailCustomerName">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Total Orders</div>
                                  <div class="info-value text-primary" id="detailTotalOrders">0</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Total Spent</div>
                                  <div class="info-value text-success" id="detailTotalSpent">₦0.00</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Account Status</div>
                                  <div class="info-value">
                                    <span class="badge bg-success" id="detailCustomerStatus">Active</span>
                                  </div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Last Purchase</div>
                                  <div class="info-value" id="detailLastPurchase">Never</div>
                                </div>
                              </div>
                            </div>

                            <!-- Contact Info Tab -->
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                              <div class="info-section">
                                <div class="info-item">
                                  <div class="info-label">Email Address</div>
                                  <div class="info-value" id="detailCustomerEmail">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Phone Number</div>
                                  <div class="info-value" id="detailCustomerPhone">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Address</div>
                                  <div class="info-value" id="detailCustomerAddress">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Date Registered</div>
                                  <div class="info-value" id="detailRegistrationDate">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Added by</div>
                                  <div class="info-value" id="detailAddedBy">-</div>
                                </div>
                                <div class="info-item">
                                  <div class="info-label">Last Updated</div>
                                  <div class="info-value" id="detailLastUpdated">-</div>
                                </div>
                              </div>
                            </div>

                            <!-- Purchase History Tab -->
                            <div class="tab-pane fade" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                              <div class="purchase-table">
                                <table class="table table-hover">
                                  <thead>
                                    <tr>
                                      <th>Order ID</th>
                                      <th>Date</th>
                                      <th>Items</th>
                                      <th>Total Amount</th>
                                      <th>Status</th>
                                      <th>Actions</th>
                                    </tr>
                                  </thead>
                                  <tbody id="purchase-history">
                                    <tr>
                                      <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-bag fa-2x mb-2"></i>
                                        <div>No purchase history available</div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>

                            <!-- Activity Log Tab -->
                            <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                              <div class="activity-timeline" id="activity-timeline">
                                <div class="activity-item">
                                  <div class="activity-date">Today</div>
                                  <div class="activity-title">Customer profile viewed</div>
                                  <div class="activity-description">Customer details were accessed by admin</div>
                                </div>
                                <div class="activity-item">
                                  <div class="activity-date">Yesterday</div>
                                  <div class="activity-title">Profile updated</div>
                                  <div class="activity-description">Customer information was updated</div>
                                </div>
                                <div class="activity-item">
                                  <div class="activity-date">3 days ago</div>
                                  <div class="activity-title">Account created</div>
                                  <div class="activity-description">Customer account was successfully created</div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="customer-actions">
                          <button class="btn btn-primary" id="editCustomerBtn">
                            <i class="bi bi-pencil me-1"></i>Edit Customer
                          </button>
                          <button class="btn btn-info" id="viewOrdersBtn">
                            <i class="bi bi-receipt me-1"></i>View Orders
                          </button>
                          <button class="btn btn-success" id="sendEmailBtn">
                            <i class="bi bi-envelope me-1"></i>Send Email
                          </button>
                          <button class="btn btn-danger" id="deleteCustomerBtn">
                            <i class="bi bi-trash me-1"></i>Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Customers content ends here -->

            <!-- Edit Customer Side Panel -->
            <div class="edit-customer-panel" id="editCustomerPanel">
              <div class="customer-details-container">
                <div class="customer-details-header">
                  <h5 class="customer-details-title">
                    <i class="bi bi-pencil-square me-2"></i>Edit Customer
                  </h5>
                  <button class="close-details-btn" id="closeEditPanelBtn">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>

                <div class="customer-details-content p-4">
                  <form id="editCustomerForm">
                    <input type="hidden" id="editCustomerId" name="customer_id">

                    <div class="mb-3">
                      <label for="editCustomerName" class="form-label">Customer Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="editCustomerName" name="customer_name" required>
                    </div>

                    <div class="mb-3">
                      <label for="editCustomerEmail" class="form-label">Email Address</label>
                      <input type="email" class="form-control" id="editCustomerEmail" name="email">
                    </div>

                    <div class="mb-3">
                      <label for="editCustomerPhone" class="form-label">Phone Number</label>
                      <input type="tel" class="form-control" id="editCustomerPhone" name="phone_number">
                    </div>

                    <div class="mb-3">
                      <label for="editCustomerAddress" class="form-label">Address</label>
                      <textarea class="form-control" id="editCustomerAddress" name="address" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                      <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                      </button>
                      <button type="submit" class="btn btn-primary">
                        <span id="editCustomerSpinner" class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
                        <i class="bi bi-check-circle me-1"></i>Update Customer
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Edit Panel Backdrop -->
            <div class="panel-backdrop" id="editPanelBackdrop"></div>
          </div>

          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
          <script src="{{ asset('manager_asset/js/customer.js') }}"></script>
@endsection
