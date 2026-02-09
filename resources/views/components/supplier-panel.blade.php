<!-- Supplier Panel Component -->
<div class="supplier-panel-overlay" id="supplierPanelOverlay"></div>
<div class="supplier-side-panel" id="addSupplierPanel">
  <div class="supplier-panel-header">
    <h5 class="supplier-panel-title">
      <i class="mdi mdi-truck"></i> Add New Supplier
    </h5>
    <button type="button" class="supplier-close-btn" id="closeSupplierPanel">
      <i class="mdi mdi-close"></i>
    </button>
  </div>

  <div class="supplier-panel-body">
    <form id="addSupplierForm">
      @csrf
      <div class="mb-3">
        <label for="newSupplierName" class="form-label">Supplier/Company Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="newSupplierName" name="name" placeholder="Enter supplier or company name" required autocomplete="off">
        <div class="invalid-feedback" id="supplierNameError"></div>
      </div>

      <div class="mb-3">
        <label for="newSupplierEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="newSupplierEmail" name="email" placeholder="Enter email address" required autocomplete="off">
        <div class="invalid-feedback" id="supplierEmailError"></div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="newSupplierContact" class="form-label">Contact Person</label>
          <input type="text" class="form-control" id="newSupplierContact" name="contact_person" placeholder="Enter contact person" autocomplete="off">
        </div>
        <div class="col-md-6 mb-3">
          <label for="newSupplierPhone" class="form-label">Phone Number</label>
          <input type="tel" class="form-control" id="newSupplierPhone" name="phone" placeholder="Enter phone number" autocomplete="off">
        </div>
      </div>

      <div class="mb-3">
        <label for="newSupplierAddress" class="form-label">Address</label>
        <textarea class="form-control" id="newSupplierAddress" name="address" rows="2" placeholder="Enter supplier address" autocomplete="off"></textarea>
      </div>
    </form>
  </div>

  <div class="supplier-panel-footer">
    <button type="button" class="btn btn-secondary" id="cancelSupplierBtn">
      <i class="mdi mdi-close"></i> Cancel
    </button>
    <button type="submit" form="addSupplierForm" class="btn btn-primary" id="saveSupplierBtn">
      <i class="mdi mdi-content-save"></i> Save
    </button>
  </div>
</div>
