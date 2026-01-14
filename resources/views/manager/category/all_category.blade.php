@extends('manager.layouts.layout')
@section('manager_page_title')
Add Categories
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/category_style.css') }}">
<!-- Success and Error Messages -->
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

   <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <!-- Header Section -->
                    <div class="d-sm-flex justify-content-between align-items-start mb-2">
                      <div>
                        <h4 class="card-title mb-0">Categories</h4>
                        <p class="card-description">Manage your product categories below.</p>
                      </div>
                    </div>

                    <!-- Search and Action Section -->
                    <div class="row mb-3 align-items-center g-2">
                      <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                          </span>
                          <input type="text" class="form-control" placeholder="Search categories..." id="searchCategories">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="d-flex justify-content-end gap-2">
                          <button type="button" class="btn btn-primary" id="addCategoryBtn">
                            <i class="bi bi-plus"></i> Add Category
                          </button>
                          <button class="btn btn-outline-success" id="exportCategories" title="Export Categories">
                            <i class="bi bi-download"></i> Export
                          </button>
                        </div>
                      </div>
                    </div>



                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="categoriesTable">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Category</th>
                        <th>Items</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $index => $category)
                        <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $category->category_name }}</td>
                        <td>{{ $category->items_count ?? 0 }}</td>
                        <td style="white-space: nowrap;">
                          <div class="d-flex gap-1 justify-content-start align-items-center">
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                    style=""
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->category_name }}"
                                    title="Edit Category">
                              <i class="bi bi-pencil" style="font-size: 0.875rem;"></i>
                            </button>

                            <form action="{{ route('category.delete', ['id' => $category->id]) }}" method="POST" style="display: inline; margin: 0;" class="delete-category-form">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-sm btn-outline-danger delete-category-btn"
                                      style="padding: 0.25rem 0.5rem; line-height: 1; min-width: 32px; height: 28px;"
                                      data-category-name="{{ $category->category_name }}">
                                <i class="bi bi-trash" style="font-size: 0.875rem;"></i>
                              </button>
                            </form>
                          </div>
                        </td>
                      </tr>
                        @endforeach


                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- content-wrapper ends -->
        </div>



<!-- Add Category Side Panel - Outside section to cover entire viewport -->
<div class="side-panel-overlay" id="sidePanelOverlay"></div>
<div class="side-panel" id="addCategoryPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title">
        <i class="bi bi-plus me-2"></i>Add Category
      </h5>
      <button type="button" class="btn-close" id="closeSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
      <form id="addCategoryForm" method="POST" action="{{ route('category.create') }}">
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
        <div class="mb-3">
          <label for="addCategoryName" class="form-label">Category Name</label>
          <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="addCategoryName" name="category_name" value="{{ old('category_name') }}" required>
          @error('category_name')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </form>
    </div>
    <div class="side-panel-footer">
      <button type="button" class="btn btn-secondary" id="cancelAddCategory">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </button>
      <button type="submit" form="addCategoryForm" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Save Category
      </button>
    </div>
  </div>
</div>

<!-- Edit Category Side Panel -->
<div class="side-panel" id="editCategoryPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title">
        <i class="bi bi-pencil me-2"></i>Edit Category
      </h5>
      <button type="button" class="btn-close" id="closeEditSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
      <form id="editCategoryForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="editCategoryId" name="category_id">
        @if(session('edit_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('edit_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="mb-3">
          <label for="editCategoryName" class="form-label">Category Name</label>
          <input type="text" class="form-control" id="editCategoryName" name="category_name" required>
        </div>
      </form>
    </div>
    <div class="side-panel-footer">
      <button type="button" class="btn btn-secondary" id="cancelEditCategory">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </button>
      <button type="submit" form="editCategoryForm" class="btn btn-primary">
        <i class="bi bi-check-circle me-1"></i>Update Category
      </button>
    </div>
  </div>
</div>

<script src="{{ asset('manager_asset/js/category.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert2 for delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtns = document.querySelectorAll('.delete-category-btn');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryName = this.getAttribute('data-category-name');
            const form = this.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${categoryName}" category? This action cannot be undone!`,
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

// Reopen modal if there are validation errors
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        var addCategoryPanel = document.getElementById('addCategoryPanel');
        var sidePanelOverlay = document.getElementById('sidePanelOverlay');
        if (addCategoryPanel && sidePanelOverlay) {
            addCategoryPanel.classList.add('active');
            sidePanelOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    });
@endif
</script>

@endsection
