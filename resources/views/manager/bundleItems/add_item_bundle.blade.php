@extends('manager.layouts.layout')
@section('manager_page_title')
Add Item Bundle
@endsection
@section('manager_layout_content')

   <!-- Modal Overlay -->
    <div class="modal-overlay"></div>

    <!-- Modal Container -->
    <div class="modal-container">
      <!-- Modal Header -->
      <div class="modal-header-custom">
        <h4>
          <i class="mdi mdi-package-variant-closed"></i> Add New Bundled Item
        </h4>
        <button type="button" class="close-btn" onclick="closeModal()" title="Close">
          <i class="mdi mdi-close"></i>
        </button>
      </div>

         <!-- Modal Body -->
      <div class="modal-body-custom">
        <div class="intro-text">
          <p>
            <i class="mdi mdi-information-outline"></i>
            Create a bundle package containing multiple existing products sold together as one unit.
            Selling a bundle automatically deducts stock from all included items.
          </p>
        </div>

        <form class="forms-sample" id="addBundleForm" method="POST" action="{{ route('bundle.create') }}" enctype="multipart/form-data">
            @csrf
          <!-- Section 1: Bundle Details -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="mdi mdi-information-outline"></i> <strong>Bundle Information</strong>
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="bundleName" class="form-label required-field">Bundle Name</label>
                    <input type="text" class="form-control" id="bundleName" name="bundle_name" placeholder="Enter bundle name" required value="{{ old('bundle_name') }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="bundleCode" class="form-label">Bundle Code/SKU</label>
                    <input type="text" class="form-control" id="bundleCode" name="bundle_code" placeholder="Auto-generated or enter custom code" value="{{ old('bundle_code') }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="category" class="form-label required-field">Category</label>
                    <select class="form-select" id="category" name="category" required>
                      <option value="">Select Category</option>
                      <option value="bundles">Bundles & Packages</option>
                      <option value="starter-kits">Starter Kits</option>
                      <option value="combo-offers">Combo Offers</option>
                      <option value="gift-sets">Gift Sets</option>
                      <option value="promotional">Promotional Packs</option>
                      <option value="office-supplies">Office Supply Sets</option>
                      <option value="tech-bundles">Tech Bundles</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="supplier" class="form-label">Primary Supplier</label>
                    <select class="form-select" id="supplier" name="supplier_id">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>

                </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="unit" class="form-label required-field">Unit of Measurement</label>
                      <select class="form-select" id="unit" name="unit_id">
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                            @endforeach
                         </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="barcode" class="form-label">Bundle Barcode</label>
                    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter or scan bundle barcode" value="{{ old('barcode') }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="description" class="form-label">Bundle Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe what's included in this bundle and its benefits">{{ old('description') }}</textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="bundleImage" class="form-label">Bundle Image</label>
                    <input type="file" class="form-control" id="bundleImage" name="bundle_image" accept="image/*">
                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Bundle Items -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="mdi mdi-package-variant"></i> <strong>Bundle Components</strong>
              </h5>
            </div>
            <div class="card-body">
              <div class="alert alert-info" role="alert">
                <i class="mdi mdi-information me-2"></i>
                <strong>Select the items to include in this bundle:</strong> Each item's stock will be deducted when the bundle is sold.
                You can add multiple quantities of the same item if needed.
              </div>

              <div class="table-responsive">
                <table class="table bundle-table" id="bundleItemsTable">
                  <thead>
                    <tr>
                      <th width="25%">Product</th>
                      <th width="15%">Available Stock</th>
                      <th width="15%">Quantity in Bundle</th>
                      <th width="15%">Unit Cost</th>
                      <th width="15%">Subtotal</th>
                      <th width="15%">Action</th>
                    </tr>
                  </thead>
                  <tbody id="bundleItemsBody">
                    <tr class="bundle-item-row">
                      <td>
                        <select class="form-select product-select" name="bundle_items[]" onchange="updateItemInfo(this)" required>
                          <option value="">Select Product</option>
                          <!-- Dynamic options from database -->
                          <option value="1" data-cost="500" data-stock="50">Wireless Mouse</option>
                          <option value="2" data-cost="1200" data-stock="30">Keyboard</option>
                          <option value="3" data-cost="800" data-stock="25">Mouse Pad</option>
                          <option value="4" data-cost="2500" data-stock="15">Headset</option>
                        </select>
                      </td>
                      <td>
                        <input type="text" class="form-control available-stock" placeholder="0" readonly>
                      </td>
                      <td>
                        <input type="number" class="form-control bundle-quantity" name="bundle_quantities[]" placeholder="1" min="1" value="1" onchange="calculateSubtotal(this)" required>
                      </td>
                      <td>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control unit-cost" placeholder="0.00" readonly>
                        </div>
                      </td>
                      <td>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control subtotal" placeholder="0.00" readonly>
                        </div>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBundleItem(this)" title="Remove Item">
                          <i class="mdi mdi-delete"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-3">
                <button type="button" class="btn btn-success btn-sm" onclick="addBundleItem()">
                  <i class="mdi mdi-plus"></i> Add Another Item
                </button>
                <small class="text-muted">Minimum 2 items required for a bundle</small>
              </div>

              <!-- Bundle Cost Summary -->
              <div class="pricing-summary">
                <h6><i class="mdi mdi-calculator"></i> Bundle Cost Calculation</h6>
                <div class="pricing-row">
                  <span>Total Item Cost:</span>
                  <span id="totalItemCost">₦0.00</span>
                </div>
                <div class="pricing-row">
                  <span>Assembly/Packaging Cost:</span>
                  <div class="input-group" style="width: 150px;">
                    <span class="input-group-text">₦</span>
                    <input type="number" class="form-control" id="assemblyFee" name="assembly_fee" placeholder="0.00" step="0.01" min="0" value="0" onchange="calculateBundlePricing()">
                  </div>
                </div>
                <div class="pricing-row">
                  <strong>Total Bundle Cost:</strong>
                  <strong id="totalBundleCost">₦0.00</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Bundle Pricing -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="mdi mdi-currency-usd"></i> <strong>Bundle Pricing</strong>
              </h5>
            </div>
            <div class="card-body">
              <div class="alert alert-warning" role="alert">
                <i class="mdi mdi-lightbulb-outline me-2"></i>
                <strong>Bundle Pricing Strategy:</strong> Set a competitive price that offers savings compared to buying items individually.
                Consider the convenience value customers get from the bundled package.
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bundleSellingPrice" class="form-label required-field">Bundle Selling Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₦</span>
                      <input type="number" class="form-control" id="bundleSellingPrice" name="bundle_selling_price" placeholder="0.00" step="0.01" min="0" required onchange="calculateBundlePricing()">
                    </div>
                    <small class="form-text text-muted">Price customers pay for the bundle</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="individualTotal" class="form-label">Individual Items Total</label>
                    <div class="input-group">
                      <span class="input-group-text">₦</span>
                      <input type="text" class="form-control" id="individualTotal" placeholder="0.00" readonly>
                    </div>
                    <small class="form-text text-muted">If bought separately</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bundleSavings" class="form-label">Customer Savings</label>
                    <div class="input-group">
                      <span class="input-group-text">₦</span>
                      <input type="text" class="form-control" id="bundleSavings" placeholder="0.00" readonly>
                    </div>
                    <small class="form-text text-muted">Discount amount</small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bundleMargin" class="form-label">Profit Margin</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="bundleMargin" placeholder="0%" readonly>
                      <span class="input-group-text">%</span>
                    </div>
                    <small class="form-text text-muted">Bundle profit margin</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="bundleProfit" class="form-label">Bundle Profit</label>
                    <div class="input-group">
                      <span class="input-group-text">₦</span>
                      <input type="text" class="form-control" id="bundleProfit" placeholder="0.00" readonly>
                    </div>
                    <small class="form-text text-muted">Profit per bundle</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="taxRate" class="form-label">Tax Rate</label>
                    <select class="form-select" id="taxRate" name="tax_rate">
                      <option value="0">No Tax (0%)</option>
                      <option value="5">VAT 5%</option>
                      <option value="7.5">VAT 7.5%</option>
                      <option value="10">VAT 10%</option>
                      <option value="15">VAT 15%</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 4: Stock Details -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="mdi mdi-warehouse"></i> <strong>Bundle Stock Management</strong>
              </h5>
            </div>
            <div class="card-body">
              <div class="alert alert-info" role="alert">
                <i class="mdi mdi-information me-2"></i>
                <strong>Note:</strong> Bundle stock is limited by the available stock of individual items.
                The maximum bundles you can create is determined by the item with the lowest available stock.
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="maxPossibleBundles" class="form-label">Maximum Possible Bundles</label>
                    <input type="text" class="form-control" id="maxPossibleBundles" placeholder="0" readonly>
                    <small class="form-text text-muted">Based on current stock levels</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="initialBundleStock" class="form-label required-field">Initial Bundle Stock</label>
                    <input type="number" class="form-control" id="initialBundleStock" name="initial_bundle_stock" placeholder="0" min="0" required>
                    <small class="form-text text-muted">Number of bundles to make available</small>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="lowStockAlert" class="form-label">Low Stock Alert</label>
                    <input type="number" class="form-control" id="lowStockAlert" name="low_stock_alert" placeholder="5" min="0">
                    <small class="form-text text-muted">Alert when bundles fall below this level</small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label for="storageLocation" class="form-label">Storage Location</label>
                    <input type="text" class="form-control" id="storageLocation" name="storage_location" placeholder="e.g., Warehouse B, Bundle Section">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="expiryDate" class="form-label">Expiry Date (if applicable)</label>
                    <input type="date" class="form-control" id="expiryDate" name="expiry_date">
                  </div>
                </div>
              </div>
            </div>
          </div>

              <!-- Action Buttons (Sticky Footer) -->
      <div class="action-buttons">
        <button type="reset" class="btn btn-light" onclick="resetForm()">
          <i class="mdi mdi-refresh"></i> Reset
        </button>
        <button type="button" class="btn btn-secondary" onclick="closeModal()">
          <i class="mdi mdi-close"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary" >
          <i class="mdi mdi-content-save"></i> Save Bundle
        </button>
      </div>
    </div>
        </form>
      </div>



      <script src="{{ asset('manager_asset/js/add_item_bunble.js') }}"></script>
@endsection
