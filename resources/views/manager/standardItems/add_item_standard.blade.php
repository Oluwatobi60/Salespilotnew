@extends('manager.layouts.layout')
@section('manager_page_title')
Add Item Standard
@endsection
@section('manager_layout_content')

 <!-- Modal Overlay -->
    <div class="modal-overlay"></div>

    <!-- Modal Container -->
    <div class="modal-container">
      <!-- Modal Header -->
      <div class="modal-header-custom">
        <h4>
          <i class="mdi mdi-package-variant"></i> Add New Standard Item
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
            Fill in the details below to add a new standard item to your inventory. All required fields are marked with an asterisk (*).
          </p>
        </div>

                    <form class="forms-sample" id="addItemForm" method="POST" action="process_add_item.php" enctype="multipart/form-data">
                      
                      <!-- Section 1: Item Details -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <i class="mdi mdi-information-outline"></i> <strong>Basic Item Details</strong>
                          </h5>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="itemName" class="form-label required-field">Item Name</label>
                                <input type="text" class="form-control" id="itemName" name="item_name" placeholder="Enter item name" required>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="itemCode" class="form-label">Item Code/SKU</label>
                                <input type="text" class="form-control" id="itemCode" name="item_code" placeholder="Auto-generated or enter custom code">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="category" class="form-label required-field">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                  <option value="">Select Category</option>
                                  <option value="electronics">Electronics</option>
                                  <option value="clothing">Clothing</option>
                                  <option value="food">Food & Beverages</option>
                                  <option value="furniture">Furniture</option>
                                  <option value="stationery">Stationery</option>
                                  <option value="other">Other</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="supplier" class="form-label">Supplier</label>
                                <select class="form-select" id="supplier" name="supplier">
                                  <option value="">Select Supplier</option>
                                  <option value="supplier1">Supplier 1</option>
                                  <option value="supplier2">Supplier 2</option>
                                  <option value="supplier3">Supplier 3</option>
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="unit" class="form-label required-field">Unit of Measurement</label>
                                <div class="unit-input-container">
                                  <select class="form-select" id="unit" name="unit" required>
                                    <option value="">Select Unit</option>
                                    <option value="pcs">Piece (pcs)</option>
                                    <option value="ct">Carton (ct)</option>
                                    <option value="cm">Centimeter (cm)</option>
                                    <option value="L">Litre (L)</option>
                                    <option value="g">Gram (g)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="pi">Per item (pi)</option>
                                    <option value="yd">Yard (yd)</option>
                                    <option value="m">Metre (m)</option>
                                    <option value="mm">Millimetre (mm)</option>
                                    <option value="custom">+ Add New Unit</option>
                                  </select>
                                  <div id="customUnitContainer" class="mt-2" style="display: none;">
                                    <div class="input-group">
                                      <input type="text" class="form-control" id="customUnit" placeholder="Enter custom unit (e.g., tons, pieces)">
                                      <input type="text" class="form-control" id="customUnitAbbr" placeholder="Abbreviation (e.g., t, pcs)">
                                      <button type="button" class="btn btn-outline-primary" id="addUnitBtn">
                                        <i class="mdi mdi-plus"></i> Add
                                      </button>
                                    </div>
                                    <small class="form-text text-muted">Enter the unit name and its abbreviation</small>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter or scan barcode">
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter item description"></textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="itemImage" class="form-label">Item Image</label>
                                <input type="file" class="form-control" id="itemImage" name="item_image" accept="image/*">
                                <small class="form-text text-muted">Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Section 2: Sell Toggle (Portable) -->
                      <div class="row mb-4">
                        <div class="col-md-6 col-lg-5">
                          <div class="sell-toggle-container">
                            <div class="d-flex align-items-center justify-content-between">
                              <div class="d-flex align-items-center">
                                <i class="mdi mdi-shopping text-primary me-2"></i>
                                <strong>Available for Sale</strong>
                              </div>
                              <div class="d-flex align-items-center gap-3">
                                <small id="sellToggleText" class="fw-bold">Enabled</small>
                                <div class="form-check form-switch mb-0">
                                  <input class="form-check-input" type="checkbox" id="sellToggle" name="enable_sale" value="1" checked>
                                  <label class="form-check-label" for="sellToggle"></label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Section 3: Pricing -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <i class="mdi mdi-currency-usd"></i> <strong>Pricing</strong>
                          </h5>
                        </div>
                        <div class="card-body">
                          <!-- Pricing Method Selection (Radio Buttons) -->
                          <div class="row mb-4">
                            <div class="col-md-12">
                              <div class="form-group">
                                <label class="form-label required-field">Pricing Method</label>
                                <div class="pricing-methods-row">
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="fixedPricing" name="pricing_type" value="fixed" required checked>
                                    <label for="fixedPricing" class="pricing-method-label">
                                      <i class="mdi mdi-lock"></i>
                                      <span class="method-name">Fixed Pricing</span>
                                    </label>
                                  </div>
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="manualPricing" name="pricing_type" value="manual" required>
                                    <label for="manualPricing" class="pricing-method-label">
                                      <i class="mdi mdi-pencil"></i>
                                      <span class="method-name">Manual Pricing</span>
                                    </label>
                                  </div>
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="marginPricing" name="pricing_type" value="margin" required>
                                    <label for="marginPricing" class="pricing-method-label">
                                      <i class="mdi mdi-percent"></i>
                                      <span class="method-name">Margin Pricing</span>
                                      <small>Auto-calculated by margin</small>
                                    </label>
                                  </div>
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="rangePricing" name="pricing_type" value="range" required>
                                    <label for="rangePricing" class="pricing-method-label">
                                      <i class="mdi mdi-chart-line"></i>
                                      <span class="method-name">Range Pricing</span>
                                      <small>Tiered quantity pricing</small>
                                    </label>
                                  </div>
                                </div>
                                <small class="form-text text-muted">Choose how you want to set the selling price for this item</small>
                              </div>
                            </div>
                          </div>

                          <!-- Pricing Method Descriptions -->
                          <div id="pricingDescription" class="alert alert-light mb-4" style="display: none;">
                            <div id="fixedDesc" class="pricing-desc" style="display: none;">
                              <strong><i class="mdi mdi-lock text-primary"></i> Fixed Pricing:</strong> Set a single, unchanging selling price for this item.
                            </div>
                            <div id="manualDesc" class="pricing-desc" style="display: none;">
                              <strong><i class="mdi mdi-pencil text-warning"></i> Manual Pricing:</strong> Enter only the cost price. Selling prices, taxes, and discounts will be set during individual sales transactions.
                            </div>
                            <div id="marginDesc" class="pricing-desc" style="display: none;">
                              <strong><i class="mdi mdi-percent text-success"></i> Margin Pricing:</strong> Set a profit margin percentage, and selling price will be calculated automatically. Tax rates are included in calculations.
                            </div>
                            <div id="rangeDesc" class="pricing-desc" style="display: none;">
                              <strong><i class="mdi mdi-chart-line text-info"></i> Range Pricing:</strong> Set minimum and maximum price boundaries for flexible pricing within defined limits. Tax rates are included in calculations.
                            </div>
                          </div>

                          <!-- Basic Cost Price (Always visible) -->
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="costPrice" class="form-label required-field">Cost Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="costPrice" name="cost_price" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                                <small class="form-text text-muted">Price you pay to supplier</small>
                              </div>
                            </div>
                            <!-- Dynamic Pricing Fields Container -->
                            <div id="pricingFieldsContainer" class="col-md-8">
                              <!-- Fixed Pricing Fields -->
                              <div id="fixedFields" class="pricing-fields row" style="display: flex;">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="sellingPrice" class="form-label required-field">Fixed Selling Price</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="number" class="form-control" id="sellingPrice" name="selling_price" placeholder="0.00" step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Price you sell to customers</small>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="profitMargin" class="form-label">Profit Margin</label>
                                    <div class="input-group">
                                      <input type="text" class="form-control" id="profitMargin" name="profit_margin" placeholder="0%" readonly>
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <small class="form-text text-muted">Auto-calculated</small>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="potentialProfit" class="form-label">Potential Profit</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="text" class="form-control" id="potentialProfit" name="potential_profit" placeholder="0.00" readonly>
                                    </div>
                                    <small class="form-text text-muted">Per unit profit</small>
                                  </div>
                                </div>
                              </div>

                              

                              <!-- Margin Pricing Fields -->
                              <div id="marginFields" class="pricing-fields row" style="display: none;">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="targetMargin" class="form-label required-field">Target Profit Margin (%)</label>
                                    <div class="input-group">
                                      <input type="number" class="form-control" id="targetMargin" name="target_margin" placeholder="0" step="0.01" min="0" max="1000">
                                      <span class="input-group-text">%</span>
                                    </div>
                                    <small class="form-text text-muted">Desired profit margin percentage</small>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="calculatedPrice" class="form-label">Calculated Selling Price</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="number" class="form-control" id="calculatedPrice" name="calculated_price" placeholder="0.00" readonly>
                                    </div>
                                    <small class="form-text text-muted">Auto-calculated based on margin</small>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="marginProfit" class="form-label">Potential Profit</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="text" class="form-control" id="marginProfit" name="margin_profit" placeholder="0.00" readonly>
                                    </div>
                                    <small class="form-text text-muted">Per unit profit</small>
                                  </div>
                                </div>
                              </div>

                              <!-- Range Pricing Fields -->
                              <div id="rangeFields" class="pricing-fields row" style="display: none;">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="minPrice" class="form-label required-field">Minimum Price</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="number" class="form-control" id="minPrice" name="min_price" placeholder="0.00" step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Lowest selling price</small>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="maxPrice" class="form-label required-field">Maximum Price</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="number" class="form-control" id="maxPrice" name="max_price" placeholder="0.00" step="0.01" min="0">
                                    </div>
                                    <small class="form-text text-muted">Highest selling price</small>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label for="rangePotentialProfit" class="form-label">Potential Profit Range</label>
                                    <div class="input-group">
                                      <span class="input-group-text">₦</span>
                                      <input type="text" class="form-control" id="rangePotentialProfit" name="range_potential_profit" placeholder="0.00 to 0.00" readonly>
                                    </div>
                                    <small class="form-text text-muted">Profit range per unit</small>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Quantity-based Pricing Tiers (for Range Pricing) -->
                          <div id="pricingTiers" style="display: none;">
                            <div class="row">
                              <div class="col-md-12">
                                <h6 class="text-primary mb-3"><i class="mdi mdi-format-list-numbered"></i> Quantity-Based Pricing Tiers</h6>
                                <div class="table-responsive">
                                  <table class="table table-bordered">
                                    <thead class="table-light">
                                      <tr>
                                        <th>Min Quantity</th>
                                        <th>Max Quantity</th>
                                        <th>Price per Unit (₦)</th>
                                        <th>Action</th>
                                      </tr>
                                    </thead>
                                    <tbody id="tierTableBody">
                                      <tr>
                                        <td><input type="number" class="form-control form-control-sm" placeholder="1" min="1"></td>
                                        <td><input type="number" class="form-control form-control-sm" placeholder="10" min="1"></td>
                                        <td><input type="number" class="form-control form-control-sm" placeholder="0.00" step="0.01" min="0"></td>
                                        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTier(this)">Remove</button></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPricingTier()">
                                    <i class="mdi mdi-plus"></i> Add Tier
                                  </button>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Additional Pricing Options (Hidden for Manual Pricing) -->
                          <div id="additionalPricingOptions" class="row">
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
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="discount" class="form-label">Discount (%)</label>
                                <input type="number" class="form-control" id="discount" name="discount" placeholder="0" step="0.01" min="0" max="100" value="0">
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="form-label">Final Price Preview</label>
                                <div class="alert alert-info mb-0" role="alert" style="padding: 8px 12px;">
                                  <strong><span id="finalPrice">₦0.00</span></strong>
                                </div>
                              </div>
                            </div>
                          </div>


                        </div>
                      </div>

                      <!-- Section 3: Stock Details -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="mb-0 d-flex justify-content-between align-items-center">
                            <span>
                              <i class="mdi mdi-warehouse"></i> <strong>Stock Tracking Details</strong>
                            </span>
                            <label class="toggle-switch" title="Toggle Stock Details">
                              <input type="checkbox" id="stockToggleCheckbox" checked onchange="toggleStockDetails()">
                              <span class="toggle-slider"></span>
                            </label>
                          </h5>
                        </div>
                        <div class="card-body" id="stockDetailsContent">
                          <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="openingStock" class="form-label required-field">Stock Quantity</label>
                                <input type="number" class="form-control" id="openingStock" name="opening_stock" placeholder="0" min="0" required>
                                <small class="form-text text-muted"> Stock Quantity to Start With </small>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="lowStockThreshold" class="form-label">Low Stock Alert (Threshold)</label>
                                <input type="number" class="form-control" id="lowStockThreshold" name="low_stock_threshold" placeholder="0" min="0">
                                <small class="form-text text-muted">Alert when stock falls below this level</small>
                              </div>
                            </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                <label for="expiryDate" class="form-label">Expiry Date (if applicable)</label>
                                <input type="date" class="form-control" id="expiryDate" name="expiry_date">
                              </div>
                            </div> 
                          </div>

                          <div class="row">
                            <div class="col-md-8">
                              <div class="form-group">
                                <label for="location" class="form-label">Storage Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Warehouse A, Shelf 3">
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
                    <button type="button" class="btn btn-primary" onclick="submitForm()">
                    <i class="mdi mdi-content-save"></i> Save Item
                    </button>
                </div>


                    </form>
      </div> <!-- End Modal Body -->
      
    </div> <!-- End Modal Container -->
  
       <script src="{{ asset('manager_asset/js/add_item_standard.js') }}"></script>
@endsection

 
