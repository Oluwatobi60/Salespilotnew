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
                    <div class="d-sm-flex justify-content-between align-items-start">
                      <div>
                        <h4 class="card-title mb-0">Categories</h4>
                        <p class="card-description">Manage your product categories below.</p>
                      </div>
                      <div class="btn-wrapper">
                        <button type="button" class="btn btn-primary text-white me-0" id="addCategoryBtn">
                          <i class="bi bi-plus"></i> Add Category
                        </button>
                      </div>
                    </div>

                    <!-- Modern Search and Filter Section -->
                    <div class="row mb-3 filter-container">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" placeholder="Search categories..." id="searchCategories">
                          <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                          </button>
                        </div>
                      </div>
                      <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                        <!-- Filter by Items Count -->
                        <select class="form-select" id="itemsFilter" style="max-width: 140px;">
                          <option value="">All Items</option>
                          <option value="0-10">0-10 Items</option>
                          <option value="11-25">11-25 Items</option>
                          <option value="26-50">26+ Items</option>
                        </select>

                        <!-- Filter by Margin Range -->
                        <select class="form-select" id="marginFilter" style="max-width: 140px;">
                          <option value="">All Margins</option>
                          <option value="0-15">0-15%</option>
                          <option value="16-25">16-25%</option>
                          <option value="26+">26%+</option>
                        </select>

                        <!-- Action Buttons -->
                        <button class="btn btn-outline-primary" id="applyFilters">
                          <i class="bi bi-funnel"></i> Apply
                        </button>
                        <button class="btn btn-outline-secondary" id="clearFilters">
                          <i class="bi bi-x-circle"></i> Clear
                        </button>
                        <button class="btn btn-outline-success" id="exportCategories">
                          <i class="bi bi-download"></i> Export
                        </button>
                      </div>
                    </div><br>



                <div class="table-responsive">
                  <table class="table table-striped" id="categoriesTable">
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
                                    style="padding: 0.25rem 0.5rem; line-height: 1; min-width: 32px; height: 28px;"
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->category_name }}"
                                    title="Edit Category">
                              <i class="bi bi-pencil" style="font-size: 0.875rem;"></i>
                            </button>

                            <form action="{{ route('category.delete', ['id' => $category->id]) }}" method="POST" style="display: inline; margin: 0;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-outline-danger"
                                      style="padding: 0.25rem 0.5rem; line-height: 1; min-width: 32px; height: 28px;"
                                      onclick="return confirm('Are you sure you want to delete this category?')">
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

<script>
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
