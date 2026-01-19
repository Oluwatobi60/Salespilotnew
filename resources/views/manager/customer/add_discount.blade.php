@extends('manager.layouts.layout')
@section('manager_page_title')
Create Customer Discount
@endsection
@section('manager_layout_content')
      <div class="container-scroller">
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
          <div class="content-wrapper">
            <!-- Discount content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Discount Report</h4>
                    <p class="card-description">View and manage discount transactions.</p>
                    @if(session('success'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                    @if(session('error'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                    <div class="row mb-3">
                      <div class="col-sm-4">
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
                        <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter discounts.</small>
                      </div>
                      <div class="col-sm-4">
                        <select class="form-select form-select-sm" id="staffFilter" style="font-size:0.85rem;">
                          <option value="">All Staff</option>
                          <option value="Staff1">Staff 1</option>
                          <option value="Staff2">Staff 2</option>
                          <option value="Staff3">Staff 3</option>
                        </select>
                      </div>

                      <div class="col-sm-4">
                        <button type="button" class="btn btn-primary" style="min-width: 150px;" id="openAddDiscountBtn"><strong>+ Add Discount</strong></button>
                      </div>

                    </div>

                    <div class="table-responsive">
                      <table class="table table-striped" id="discountTable">
                        <thead>
                          <tr>
                            <th>Discount Name</th>
                            <th>Type</th>
                            <th>Customers Group</th>
                            <th>Amount Discounted</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                           @foreach($discounnts as $discount)
                            <tr>
                              <td>{{ $discount->discount_name }}</td>
                              <td>{{ $discount->type }}</td>
                              <td>{{ $discount->customers_group }}</td>
                              <td>{{ $discount->discount_rate }}</td>
                              <td>
                                <button class="btn btn-sm btn-warning me-1 edit-discount-btn"
                                        data-id="{{ $discount->id }}"
                                        data-name="{{ $discount->discount_name }}"
                                        data-type="{{ $discount->type }}"
                                        data-group="{{ $discount->customers_group }}"
                                        data-rate="{{ $discount->discount_rate }}">
                                  <i class="bi bi-pencil me-2"></i>Edit
                                </button>
                                <form action="#" method="POST" style="display:inline-block;" class="delete-discount-form">
                                  @csrf
                                  @method('DELETE')
                                  <button type="button" class="btn btn-sm btn-danger delete-discount-btn"
                                          data-discount-name="{{ $discount->discount_name }}">
                                    <i class="bi bi-trash me-2"></i>Delete
                                  </button>
                                </form>
                              </td>
                            </tr>
                           @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Discount content ends here -->
          </div>
          <!-- content-wrapper ends -->

        </div>
      <!-- page-body-wrapper ends -->
    </div>


    <!-- Add Staff Side Panel - Outside section to cover entire viewport -->
<div class="side-panel-overlay" id="sidePanelOverlay"></div>
<div class="side-panel" id="addDiscountPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title">
        <i class="bi bi-percent me-2"></i>Add Discount
      </h5>
      <button type="button" class="btn-close" id="closeSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
      <form id="addDiscountForm" action="{{ route('discount.create') }}" method="POST">
        @csrf
        <div class="form-section mb-4">
          <div class="mb-3">
            <label for="discount_name" class="form-label">Discount Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="discount_name" name="discount_name" placeholder="Enter discount name" required value="{{ old('discount_name') }}">
            @error('discount_name')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

            <div class="mb-3">
            <label for="type" class="form-label">Discount Type <span class="text-danger">*</span></label>
            <select class="form-select" id="type" name="type" required>
              <option value="" disabled selected>Select Type</option>
              <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
              <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
            </select>
            @error('type')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

            <div class="mb-3">
            <label for="customers_group" class="form-label">Customers Group <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="customers_group" name="customers_group" placeholder="Enter customers group" required value="{{ old('customers_group') }}">
            @error('customers_group')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <div class="mb-3">
            <label for="discount_rate" class="form-label">Amount Discounted <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="discount_rate" name="discount_rate" placeholder="Enter amount discounted" required value="{{ old('discount_rate') }}">
            @error('discount_rate')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
        </div>
        <div class="side-panel-footer">
          <button type="submit" class="btn btn-primary w-100">Add Discount</button>
        </div>
      </form>
    </div>
  </div>
  <!-- Remove any leftover staff form/footer markup below -->
    </div>
                </h6>
              <div class="row">
                <div class="col-md-6 mb-2">
                  <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                  <select class="form-select" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                  </select>
                  @error('role')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>

                <div class="col-md-6 mb-2">
                  <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                  <select class="form-select" id="status" name="status" required>
                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }} selected>Active</option>
                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="On Leave" {{ old('status') == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                  </select>
                  <div class="invalid-feedback">Please select a status.</div>
              </div>
              </div>
              </div>
              <!-- End Role & Status -->


              <div class="row mb-3">
                <div class="col-md-12 mb-2">
                  <label for="address" class="form-label">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="5" cols="5" placeholder="Enter address (optional)"></textarea>
                </div>
              </div>

              <!-- Profile Photo Section -->
              <div class="form-section mb-3">
                <h6 class="section-title mb-3">
                  <i class="bi bi-camera me-2"></i>Profile Photo <span class="text-muted" style="font-size: 0.85rem; font-weight: normal;">(Optional)</span>
                </h6>
              <div class="row">
                <div class="col-md-12">
                  <div class="photo-upload-area">
                    <input type="file" class="form-control" id="passport_photo" name="passport_photo" accept="image/*">
                    <div id="uploadPlaceholder" class="upload-placeholder">
                      <i class="bi bi-cloud-arrow-up" style="font-size: 2.5rem; color: #6c757d;"></i>
                      <p class="mb-2 mt-2"><strong>Click to upload</strong> or drag and drop</p>
                      <small class="text-muted">JPG, PNG, GIF (max 2MB)</small>
                    </div>
                    <div id="photoPreview" class="photo-preview" style="display: none;">
                      <img id="previewImage" src="" alt="Preview">
                      <button type="button" class="btn btn-sm btn-danger remove-photo-btn" id="removePhoto">
                        <i class="bi bi-trash"></i> Remove Photo
                      </button>
                    </div>
                  </div>
                  @error('passport_photo')
                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              </div>
              <!-- End Profile Photo -->
            </form>
    </div>

  </div>
</div>

<!-- Edit Discount Side Panel -->
<div class="side-panel-overlay" id="editSidePanelOverlay"></div>
<div class="side-panel" id="editDiscountPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title">
        <i class="bi bi-pencil-square me-2"></i>Edit Discount
      </h5>
      <button type="button" class="btn-close" id="closeEditSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
      <form id="editDiscountForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit_discount_id" name="id">
        <div class="form-section mb-4">
          <div class="mb-3">
            <label for="edit_discount_name" class="form-label">Discount Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_discount_name" name="discount_name" placeholder="Enter discount name" required>
          </div>

          <div class="mb-3">
            <label for="edit_type" class="form-label">Discount Type <span class="text-danger">*</span></label>
            <select class="form-select" id="edit_type" name="type" required>
              <option value="" disabled>Select Type</option>
              <option value="percentage">Percentage</option>
              <option value="fixed_amount">Fixed Amount</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="edit_customers_group" class="form-label">Customers Group <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_customers_group" name="customers_group" placeholder="Enter customers group" required>
          </div>

          <div class="mb-3">
            <label for="edit_discount_rate" class="form-label">Amount Discounted <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="edit_discount_rate" name="discount_rate" placeholder="Enter amount discounted" required>
          </div>
        </div>
      </form>
    </div>
    <div class="side-panel-footer">
      <button type="button" class="btn btn-secondary" id="cancelEditDiscount">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </button>
      <button type="submit" form="editDiscountForm" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Update
      </button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('manager_asset/js/discount.js') }}"></script>

<script>
// SweetAlert2 for delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtns = document.querySelectorAll('.delete-discount-btn');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const discountName = this.getAttribute('data-discount-name');
            const form = this.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${discountName}" discount? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection
