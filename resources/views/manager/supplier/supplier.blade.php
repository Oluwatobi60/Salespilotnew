@extends('manager.layouts.layout')
@section('manager_page_title')
Suppliers Page
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/supplier_style.css') }}">
<!-- Suppliers content starts here -->
<div class="content-wrapper d-flex">
<div class="row">
  <div class="col-12 grid-margin stretch-card">
    <div class="card card-rounded">
      <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-sm-flex justify-content-between align-items-start">
          <div>
            <h4 class="card-title mb-0">Suppliers</h4>
            <p class="card-description">Manage your suppliers and vendor information.</p>
          </div>
          <div class="btn-wrapper">
            <button type="button" class="btn btn-primary text-white me-0" id="openAddSupplierBtn">
              <i class="bi bi-plus"></i> Add Supplier
            </button>
          </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="row mb-3 mt-3 filter-container">
          <div class="col-md-4">
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Search suppliers..." id="searchSuppliers">
              <button class="btn btn-outline-secondary" type="button">
                <i class="bi bi-search"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="table-responsive mt-3">
          <table class="table table-striped" id="suppliersTable" style="width: 100%; min-width: 800px;">
            <thead>
              <tr>
                <th style="width: 50px;">S/N</th>
                <th style="width: 120px;">Date</th>
                <th style="min-width: 180px;">Supplier/Company Name</th>
                <th style="min-width: 160px;">Email</th>
                <th style="min-width: 140px;">Contact Person</th>
                <th style="min-width: 130px;">Phone</th>
                <th style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($suppliers as $index => $supplier)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $supplier->created_at ? $supplier->created_at->format('M d, Y') : 'N/A' }}</td>
                <td class="text-truncate" style="max-width: 200px;" title="{{ $supplier->name }}">{{ $supplier->name }}</td>
                <td class="text-truncate" style="max-width: 150px;" title="{{ $supplier->email }}">{{ $supplier->email }}</td>
                <td class="text-truncate" style="max-width: 120px;" title="{{ $supplier->contact_person ?? 'N/A' }}">{{ $supplier->contact_person ?? 'N/A' }}</td>
                <td>{{ $supplier->phone ?? 'N/A' }}</td>
                <td style="white-space: nowrap;">
                  <div class="d-flex gap-1 justify-content-start align-items-center">
                    <!-- Edit and Delete Buttons -->
                    <button class="btn btn-sm btn-outline-primary edit-btn"
                            style="padding: 0.25rem 0.5rem; line-height: 1; min-width: 32px; height: 28px;"
                            data-id="{{ $supplier->id }}"
                            data-name="{{ $supplier->name }}"
                            data-email="{{ $supplier->email }}"
                            data-contact="{{ $supplier->contact_person }}"
                            data-phone="{{ $supplier->phone }}"
                            data-address="{{ $supplier->address }}"
                            title="Edit Supplier">
                      <i class="bi bi-pencil" style="font-size: 0.875rem;"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-btn"
                            style="padding: 0.25rem 0.5rem; line-height: 1; min-width: 32px; height: 28px;"
                            data-id="{{ $supplier->id }}"
                            data-name="{{ $supplier->name }}"
                            title="Delete Supplier">
                      <i class="bi bi-trash" style="font-size: 0.875rem;"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">
                  <div class="py-4">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-2">No suppliers found</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Suppliers content ends here -->



    <!-- Add Supplier Side Panel -->
    <div class="side-panel-overlay" id="sidePanelOverlay"></div>
    <div class="side-panel" id="addSupplierPanel">
      <div class="side-panel-content">
        <div class="side-panel-header">
          <h5 class="side-panel-title">
            <i class="bi bi-person-plus me-2"></i>Add New Supplier
          </h5>
          <button type="button" class="btn-close" id="closeSidePanel" aria-label="Close"></button>
        </div>
        <div class="side-panel-body">
          <form id="addSupplierForm" action="{{ route('supplier.create') }}" method="POST">
            @csrf
            @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif
            <div class="row mb-3">
              <div class="col-md-6 mb-2">
                <label for="supplier_name" class="form-label">Supplier/Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="supplier_name" name="name" placeholder="Enter supplier or company name" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-2">
                <label for="contact_person" class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" placeholder="Enter contact person" value="{{ old('contact_person') }}">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6 mb-2">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email address" value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="col-md-6 mb-2">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number" value="{{ old('phone') }}">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-12 mb-2">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" value="{{ old('address') }}">
              </div>
            </div>
          </form>
        </div>
        <div class="side-panel-footer">
          <button type="button" class="btn btn-secondary" id="cancelAddSupplier">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="submit" form="addSupplierForm" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Add Supplier
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Supplier Side Panel -->
    <div class="side-panel-overlay" id="editSidePanelOverlay"></div>
    <div class="side-panel" id="editSupplierPanel">
      <div class="side-panel-content">
        <div class="side-panel-header">
          <h5 class="side-panel-title">
            <i class="bi bi-pencil-square me-2"></i>Edit Supplier
          </h5>
          <button type="button" class="btn-close" id="closeEditSidePanel" aria-label="Close"></button>
        </div>
        <div class="side-panel-body">
          <form id="editSupplierForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editSupplierId" name="id">
            <div class="row mb-3">
              <div class="col-md-6 mb-2">
                <label for="edit_supplier_name" class="form-label">Supplier/Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_supplier_name" name="name" placeholder="Enter supplier or company name" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="edit_contact_person" class="form-label">Contact Person</label>
                <input type="text" class="form-control" id="edit_contact_person" name="contact_person" placeholder="Enter contact person">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6 mb-2">
                <label for="edit_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="edit_email" name="email" placeholder="Enter email address" required>
              </div>
              <div class="col-md-6 mb-2">
                <label for="edit_phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="edit_phone" name="phone" placeholder="Enter phone number">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-12 mb-2">
                <label for="edit_address" class="form-label">Address</label>
                <input type="text" class="form-control" id="edit_address" name="address" placeholder="Enter address">
              </div>
            </div>
          </form>
        </div>
        <div class="side-panel-footer">
          <button type="button" class="btn btn-secondary" id="cancelEditSupplier">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="submit" form="editSupplierForm" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Update Supplier
          </button>
        </div>
      </div>
    </div>

<script src="{{ asset('manager_asset/js/supplier.js') }}"></script>


@endsection
