<!-- Add New Unit Side Panel Component -->
<div class="unit-panel-overlay" id="unitPanelOverlay"></div>
<div class="unit-side-panel" id="addUnitPanel">
  <div class="unit-panel-header">
    <h5 class="unit-panel-title">
      <i class="mdi mdi-plus-circle"></i> Add New Unit
    </h5>
    <button type="button" class="unit-close-btn" id="closeUnitPanel">
      <i class="mdi mdi-close"></i>
    </button>
  </div>

  <div class="unit-panel-body">
    <form id="addUnitForm">
      @csrf
      <div class="mb-3">
        <label for="newUnitName" class="form-label">Unit Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="newUnitName" name="unit_name" placeholder="Enter unit name (e.g., Kilogram, Piece)" required autocomplete="off">
        <div class="invalid-feedback" id="unitNameError"></div>
        <small class="form-text text-muted">Must be 2-50 characters</small>
      </div>

      <div class="mb-3">
        <label for="newUnitAbbreviation" class="form-label">Abbreviation <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="newUnitAbbreviation" name="abbreviation" placeholder="Enter abbreviation (e.g., kg, pcs)" required autocomplete="off">
        <div class="invalid-feedback" id="unitAbbreviationError"></div>
        <small class="form-text text-muted">Must be 1-10 characters</small>
      </div>
    </form>
  </div>

  <div class="unit-panel-footer">
    <button type="button" class="btn btn-secondary" id="cancelUnitBtn">
      <i class="mdi mdi-close"></i> Cancel
    </button>
    <button type="submit" form="addUnitForm" class="btn btn-primary" id="saveUnitBtn">
      <i class="mdi mdi-content-save"></i> Save
    </button>
  </div>
</div>
