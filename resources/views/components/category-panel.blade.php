<!-- Add New Category Side Panel Component -->
<div class="category-panel-overlay" id="categoryPanelOverlay"></div>
<div class="category-side-panel" id="addCategoryPanel">
  <div class="category-panel-header">
    <h5 class="category-panel-title">
      <i class="mdi mdi-plus-circle"></i> Add New Category
    </h5>
    <button type="button" class="category-close-btn" id="closeCategoryPanel">
      <i class="mdi mdi-close"></i>
    </button>
  </div>

  <div class="category-panel-body">
    <form id="addCategoryForm">
      @csrf
      <div class="mb-3">
        <label for="newCategoryName" class="form-label">Category Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="newCategoryName" name="category_name" placeholder="Enter category name" required autocomplete="off">
        <div class="invalid-feedback" id="categoryNameError"></div>
        <small class="form-text text-muted">Must be 5-100 characters</small>
      </div>
    </form>
  </div>

  <div class="category-panel-footer">
    <button type="button" class="btn btn-secondary" id="cancelCategoryBtn">
      <i class="mdi mdi-close"></i> Cancel
    </button>
    <button type="submit" form="addCategoryForm" class="btn btn-primary" id="saveCategoryBtn">
      <i class="mdi mdi-content-save"></i> Save
    </button>
  </div>
</div>
