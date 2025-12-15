@extends('manager.layouts.layout')
@section('manager_page_title')
Add Item Variant
@endsection
@section('manager_layout_content')

<style>
  .variant-cost-price, .variant-sell-price {
    font-weight: 600;
  }
  .variant-table tbody td {
    vertical-align: middle;
  }
  .variant-table .input-group-sm {
    margin-bottom: 0.25rem;
  }
  .variant-table small {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
  }
  .variant-table .form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
  }
</style>

     <!-- Modal Body -->
      <div class="modal-body-custom">
        <div class="intro-text">
          <p>
            <i class="mdi mdi-information-outline"></i>
            Create a new item with multiple variants (e.g., different sizes, colors, or specifications). Define the base item details and add specific variants with their own pricing and stock levels.
          </p>
        </div>
        <form class="forms-sample" id="addVariantForm" method="POST" action="{{ route('variant.create') }}" enctype="multipart/form-data">
          @csrf

          <!-- Section 1: Base Item Details -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="mdi mdi-information-outline"></i> <strong>Base Item Details</strong>
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="itemName" class="form-label required-field">Base Item Name</label>
                    <input type="text" class="form-control" id="itemName" name="item_name" placeholder="Enter base item name (e.g., T-Shirt)" value="{{ old('item_name') }}" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="itemCode" class="form-label">Base Item Code/SKU</label>
                    <input type="text" class="form-control" id="itemCode" name="item_code" placeholder="Auto-generated or enter custom code" value="{{ old('item_code') }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="category" class="form-label required-field">Category</label>
                    <select class="form-select" id="category" name="category" required>
                                  <option value="">Select Category</option>
                                   @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                                      {{ $category->category_name }}
                                    </option>
                                  @endforeach
                                </select>
                    <small class="form-text text-muted">Select existing or type new category name</small>
                  </div>
                </div>
                  <div class="col-md-6">
                              <div class="form-group">
                                <label for="supplier" class="form-label">Supplier</label>
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
                    <div class="unit-input-container">
                       <select class="form-select" id="unit" name="unit_id">
                                  <option value="">Select Unit</option>
                                  @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                      {{ $unit->name }}
                                    </option>
                                  @endforeach
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
                    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter or scan barcode" value="{{ old('barcode') }}">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="description" class="form-label">Base Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter base item description (common for all variants)">{{ old('description') }}</textarea>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="itemImage" class="form-label">Base Item Image</label>
                    <input type="file" class="form-control" id="itemImage" name="item_image" accept="image/*">
                    <small class="form-text text-muted">Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Section 2: Variant Configuration (Initially Hidden) -->
          <div class="card mb-4" id="variantSection" style="display: none;">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="mdi mdi-palette"></i> <strong>Variant Configuration</strong>
              </h5>
              <div class="d-flex align-items-center gap-3">
                <div class="form-check form-switch d-flex align-items-center">
                  <input class="form-check-input" type="checkbox" id="masterSellToggle" checked>
                  <label class="form-check-label ms-2" for="masterSellToggle">
                    <span class="master-toggle-text">Sell all Items</span>
                  </label>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="reconfigureVariants()">
                  <i class="mdi mdi-cog"></i> Reconfigure
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive variant-table">
                <table class="table table-bordered" id="variantTable">
                  <thead>
                    <tr>
                      <th style="width: 15%;">Variant</th>
                      <th class="text-center align-middle" style="width: 10%;">Sell Item</th>
                      <th style="width: 15%;">Cost Price</th>
                      <th style="width: 15%;">Sell Price</th>
                      <th style="width: 12%;">Stock</th>
                      <th style="width: 13%;">Low Stock</th>
                      <th style="width: 10%;">Action</th>
                    </tr>
                  </thead>
                  <tbody id="variantTableBody">
                    <!-- Variant rows will be generated by the modal configuration -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Add Variant Button (Initially Visible) -->
          <div class="card mb-4" id="addVariantCard">
            <div class="card-body text-center">
              <button type="button" class="btn btn-outline-primary btn-lg" onclick="showVariantModal()">
                <i class="mdi mdi-plus-circle"></i> Add Variant
              </button>
              <p class="text-muted mt-2 mb-0">Click to configure product variants (sizes, colors, etc.)</p>
            </div>
          </div>

          <!-- Action Buttons (Sticky Footer) -->
          <div class="action-buttons">
            <button type="reset" class="btn btn-light">
              <i class="mdi mdi-refresh"></i> Reset
            </button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">
              <i class="mdi mdi-close"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="mdi mdi-content-save"></i> Save Item
            </button>
          </div>

        </form>
      </div>

    <!-- Variant Configuration Modal -->
    <div class="modal-overlay" id="variantModalOverlay" style="display: none;">
      <div class="modal-container">
        <div class="modal-header">
          <h4 class="modal-title">
            <i class="mdi mdi-palette"></i> Configure Product Variants
          </h4>
          <button type="button" class="close-btn" onclick="closeVariantModal()">
            <i class="mdi mdi-close"></i>
          </button>
        </div>
        <div class="modal-body">
          <form id="variantConfigForm">
            <!-- Set 1 - Primary (Required) -->
            <div class="variant-set-group mb-4" id="variantSet1">
              <h6 class="variant-set-title">
                <i class="mdi mdi-numeric-1-circle text-primary"></i> Primary Variant Set <span class="text-danger">*</span>
              </h6>
              <div class="form-group mb-3">
                <label for="variantSetName1" class="form-label required-field">Set Name</label>
                <input type="text" class="form-control" id="variantSetName1" placeholder="e.g., Size, Color, Material" required>
                <small class="form-text text-muted">This will be your primary variant type</small>
              </div>
              <div class="form-group mb-3">
                <label class="form-label required-field">Options (Max 30)</label>
                <div class="options-container" id="optionsContainer1">
                  <div class="option-input-group">
                    <div class="input-group mb-2">
                      <input type="text" class="form-control option-input" placeholder="Enter option (e.g., Small)" required onchange="updateCombinationPreview()" onkeydown="handleOptionInputKeydown(event, 1)">
                      <button type="button" class="btn btn-outline-success add-option-btn" onclick="addOptionInput(1)" title="Add another option">
                        <i class="mdi mdi-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <small class="form-text text-muted">
                  <span id="optionCount1">1</span>/30 options | Click + to add more options
                </small>
              </div>
            </div>

            <!-- Set 2 - Secondary (Optional) -->
            <div class="variant-set-group mb-4" id="variantSet2">
              <h6 class="variant-set-title">
                <i class="mdi mdi-numeric-2-circle text-info"></i> Secondary Variant Set
                <button type="button" class="btn btn-sm btn-outline-success ms-2" id="addSet2Btn" onclick="enableVariantSet(2)">
                  <i class="mdi mdi-plus"></i> Add
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="removeSet2Btn" onclick="disableVariantSet(2)" style="display: none;">
                  <i class="mdi mdi-minus"></i> Remove
                </button>
              </h6>
              <div class="variant-set-content" id="variantSet2Content" style="display: none;">
                <div class="form-group mb-3">
                  <label for="variantSetName2" class="form-label">Set Name</label>
                  <input type="text" class="form-control" id="variantSetName2" placeholder="e.g., Color, Material">
                  <small class="form-text text-muted">Optional secondary variant type</small>
                </div>
                <div class="form-group mb-3">
                  <label class="form-label">Options (Max 30)</label>
                  <div class="options-container" id="optionsContainer2">
                    <div class="option-input-group">
                      <div class="input-group mb-2">
                        <input type="text" class="form-control option-input" placeholder="Enter option (e.g., Red)" onchange="updateCombinationPreview()" onkeydown="handleOptionInputKeydown(event, 2)">
                        <button type="button" class="btn btn-outline-success add-option-btn" onclick="addOptionInput(2)" title="Add another option">
                          <i class="mdi mdi-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <small class="form-text text-muted">
                    <span id="optionCount2">1</span>/30 options | Click + to add more options
                  </small>
                </div>
              </div>
            </div>

            <!-- Set 3 - Tertiary (Optional) -->
            <div class="variant-set-group mb-0" id="variantSet3">
              <h6 class="variant-set-title">
                <i class="mdi mdi-numeric-3-circle text-warning"></i> Tertiary Variant Set
                <button type="button" class="btn btn-sm btn-outline-success ms-2" id="addSet3Btn" onclick="enableVariantSet(3)">
                  <i class="mdi mdi-plus"></i> Add
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger ms-1" id="removeSet3Btn" onclick="disableVariantSet(3)" style="display: none;">
                  <i class="mdi mdi-minus"></i> Remove
                </button>
              </h6>
              <div class="variant-set-content" id="variantSet3Content" style="display: none;">
                <div class="form-group mb-3">
                  <label for="variantSetName3" class="form-label">Set Name</label>
                  <input type="text" class="form-control" id="variantSetName3" placeholder="e.g., Style, Pattern">
                  <small class="form-text text-muted">Optional tertiary variant type</small>
                </div>
                <div class="form-group mb-0">
                  <label class="form-label">Options (Max 30)</label>
                  <div class="options-container" id="optionsContainer3">
                    <div class="option-input-group">
                      <div class="input-group mb-2">
                        <input type="text" class="form-control option-input" placeholder="Enter option (e.g., Classic)" onchange="updateCombinationPreview()" onkeydown="handleOptionInputKeydown(event, 3)">
                        <button type="button" class="btn btn-outline-success add-option-btn" onclick="addOptionInput(3)" title="Add another option">
                          <i class="mdi mdi-plus"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                  <small class="form-text text-muted">
                    <span id="optionCount3">1</span>/30 options | Click + to add more options
                  </small>
                </div>
              </div>
            </div>

            <!-- Combination Preview -->
            <div class="mt-4" id="combinationPreview" style="display: none;">
              <div class="alert alert-info">
                <i class="mdi mdi-information"></i>
                <strong>Combination Preview:</strong>
                <span id="combinationCount">0</span> variants will be generated
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeVariantModal()">
            <i class="mdi mdi-close"></i> Cancel
          </button>
          <button type="button" class="btn btn-primary" onclick="configureVariants()">
            <i class="mdi mdi-check"></i> Configure Variants
          </button>
        </div>
      </div>
    </div>

    <!-- Overlay for variant settings -->
    <div id="variantSettingsOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:20000; align-items:center; justify-content:center;">
      <div style="background:#fff; border-radius:20px; max-width:900px; width:98vw; min-width:340px; margin:auto; box-shadow:0 8px 40px rgba(0,0,0,0.25); position:relative;">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
          <h5 class="mb-0"><i class="mdi mdi-cog"></i> Edit Variant</h5>
          <button type="button" class="btn btn-light btn-sm" onclick="closeVariantSettingsOverlay()"><i class="mdi mdi-close"></i></button>
        </div>
        <ul class="nav nav-tabs px-3 pt-3" id="variantSettingsTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricingTabPane" type="button" role="tab" aria-controls="pricingTabPane" aria-selected="true">Pricing</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stockTabPane" type="button" role="tab" aria-controls="stockTabPane" aria-selected="false">Stocking</button>
          </li>
        </ul>
        <div class="tab-content p-3" id="variantSettingsTabContent">
          <div class="tab-pane fade show active" id="pricingTabPane" role="tabpanel" aria-labelledby="pricing-tab">
            <form id="variantPricingForm">
              <div class="form-group mb-3">
                <label for="variantName" class="form-label">Variant Name</label>
                <input type="text" class="form-control" id="variantName" name="variant_name" required>
              </div>
              <div class="form-group mb-3">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" class="form-control" id="sku" name="sku" readonly>
              </div>
              <ul class="nav nav-tabs mb-3" id="pricingMethodTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="fixed-tab" data-bs-toggle="tab" data-bs-target="#fixedPricingPane" type="button" role="tab" aria-controls="fixedPricingPane" aria-selected="true">Fixed Pricing</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manualPricingPane" type="button" role="tab" aria-controls="manualPricingPane" aria-selected="false">Manual Pricing</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="margin-tab" data-bs-toggle="tab" data-bs-target="#marginPricingPane" type="button" role="tab" aria-controls="marginPricingPane" aria-selected="false">Margin Pricing</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="range-tab" data-bs-toggle="tab" data-bs-target="#rangePricingPane" type="button" role="tab" aria-controls="rangePricingPane" aria-selected="false">Range Pricing</button>
                </li>
              </ul>
              <div class="tab-content" id="pricingMethodTabContent">
                <div class="tab-pane fade show active" id="fixedPricingPane" role="tabpanel" aria-labelledby="fixed-tab">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="costPrice" class="form-label required-field">Cost Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="costPrice" name="cost_price" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <small class="form-text text-muted">Price you pay to supplier</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="sellingPrice" class="form-label required-field">Fixed Selling Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="sellingPrice" name="selling_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Price you sell to customers</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="profitMargin" class="form-label">Profit Margin</label>
                        <div class="input-group">
                          <input type="text" class="form-control" id="profitMargin" name="profit_margin" placeholder="0%" readonly>
                          <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted">Auto-calculated</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="potentialProfit" class="form-label">Potential Profit</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="potentialProfit" name="potential_profit" placeholder="0.00" readonly>
                        </div>
                        <small class="form-text text-muted">Per unit profit</small>
                      </div>
                    </div>
                    <div id="additionalPricingOptions" class="row mt-3">
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
              {{--    <div class="col-md-4">
                  <div class="form-group">
                    <label for="discount" class="form-label">Discount (%)</label>
                    <input type="number" class="form-control" id="discount" name="discount" placeholder="0" step="0.01" min="0" max="100" value="0">
                  </div>
                </div>  --}}
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
                <div class="tab-pane fade" id="manualPricingPane" role="tabpanel" aria-labelledby="manual-tab">
                  <div class="form-group mb-3">
                    <label for="manualCostPrice" class="form-label required-field">Cost Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₦</span>
                      <input type="number" class="form-control" id="manualCostPrice" name="manual_cost_price" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    <small class="form-text text-muted">Price you pay to supplier. Selling price set at sale time.</small>
                  </div>
                </div>

                <div class="tab-pane fade" id="marginPricingPane" role="tabpanel" aria-labelledby="margin-tab">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="marginCostPrice" class="form-label required-field">Cost Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="marginCostPrice" name="margin_cost_price" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="targetMargin" class="form-label required-field">Target Profit Margin (%)</label>
                        <div class="input-group">
                          <input type="number" class="form-control" id="targetMargin" name="target_margin" placeholder="0" step="0.01" min="0" max="1000">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="calculatedPrice" class="form-label">Calculated Selling Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="calculatedPrice" name="calculated_price" placeholder="0.00" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="marginProfit" class="form-label">Potential Profit</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="marginProfit" name="margin_profit" placeholder="0.00" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="rangePricingPane" role="tabpanel" aria-labelledby="range-tab">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="minPrice" class="form-label required-field">Minimum Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="minPrice" name="min_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="maxPrice" class="form-label required-field">Maximum Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="maxPrice" name="max_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="rangePotentialProfit" class="form-label">Potential Profit Range</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="rangePotentialProfit" name="range_potential_profit" placeholder="0.00 to 0.00" readonly>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="pricingTiers" class="mt-3">
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
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3"><i class="mdi mdi-content-save"></i> Save Pricing</button>
          </form>
        </div>
          <div class="tab-pane fade" id="stockTabPane" role="tabpanel" aria-labelledby="stock-tab">
            <form id="variantStockForm">
              <div class="form-group mb-3">
                <label for="stockQuantity" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control" id="stockQuantity" name="stock_quantity" min="0" required>
              </div>
              <div class="form-group mb-3">
                <label for="lowStockThreshold" class="form-label">Low Stock Alert (Threshold)</label>
                <input type="number" class="form-control" id="lowStockThreshold" name="low_stock_threshold" min="0">
              </div>
              <div class="form-group mb-3">
                <label for="expiryDate" class="form-label">Expiry Date (if applicable)</label>
                <input type="date" class="form-control" id="expiryDate" name="expiry_date">
              </div>
              <div class="form-group mb-3">
                <label for="location" class="form-label">Storage Location</label>
                <input type="text" class="form-control" id="location" name="location">
              </div>
              <button type="submit" class="btn btn-primary w-100"><i class="mdi mdi-content-save"></i> Save Stocking</button>
          </form>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Variant Pricing Modal -->
    <div class="modal-overlay" id="pricingModalOverlay" style="display: none;">
      <div class="modal-container" style="max-width: 1000px;">
        <div class="modal-header-custom">
          <h4 class="modal-title">
            <i class="mdi mdi-currency-usd text-success"></i>
            Set Pricing for <span id="modalVariantName">Variant</span>
          </h4>
          <button type="button" class="close-btn" onclick="closePricingModal()" title="Close">
            <i class="mdi mdi-close"></i>
          </button>
        </div>

        <div class="modal-body-custom">
          <form id="variantPricingForm">
            <input type="hidden" id="currentVariantIndex" value="">

            <!-- Pricing Methods Selection -->
            <div class="form-group">
              <label class="form-label"><strong>Choose Pricing Method:</strong></label>
              <div class="pricing-methods-row">
                <div class="pricing-method-option">
                  <input type="radio" id="fixedPricing" name="pricing_method" value="fixed" checked>
                  <label for="fixedPricing" class="pricing-method-label">
                    <i class="mdi mdi-lock"></i>
                    <span>Fixed</span>
                  </label>
                </div>
                <div class="pricing-method-option">
                  <input type="radio" id="manualPricing" name="pricing_method" value="manual">
                  <label for="manualPricing" class="pricing-method-label">
                    <i class="mdi mdi-pencil"></i>
                    <span>Manual</span>
                  </label>
                </div>
                <div class="pricing-method-option">
                  <input type="radio" id="marginPricing" name="pricing_method" value="margin">
                  <label for="marginPricing" class="pricing-method-label">
                    <i class="mdi mdi-percent"></i>
                    <span>Margin</span>
                  </label>
                </div>
                <div class="pricing-method-option">
                  <input type="radio" id="rangePricing" name="pricing_method" value="range">
                  <label for="rangePricing" class="pricing-method-label">
                    <i class="mdi mdi-chart-line"></i>
                    <span>Range</span>
                  </label>
                </div>
              </div>
              <div class="pricing-help-text mt-2">
                <strong><i class="mdi mdi-lock text-primary"></i> Fixed Pricing:</strong> Set a single, unchanging selling price for this variant.<br>
                <strong><i class="mdi mdi-pencil text-warning"></i> Manual Pricing:</strong> Only cost price is required. Selling price, taxes, and discounts will be set during individual sales transactions.<br>
                <strong><i class="mdi mdi-percent text-success"></i> Margin Pricing:</strong> Set a profit margin percentage, and selling price will be calculated automatically.<br>
                <strong><i class="mdi mdi-chart-line text-info"></i> Range Pricing:</strong> Set minimum and maximum price boundaries for flexible pricing.
              </div>
            </div>

            <!-- Basic Cost Price (Always visible) -->
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="modalCostPrice" class="form-label required-field">Cost Price</label>
                  <div class="input-group">
                    <span class="input-group-text">₦</span>
                    <input type="number" class="form-control" id="modalCostPrice" name="cost_price" placeholder="0.00" step="0.01" min="0" required>
                  </div>
                  <small class="form-text text-muted">Price you pay to supplier</small>
                </div>
              </div>
              <!-- Dynamic Pricing Fields Container -->
              <div id="modalPricingFieldsContainer" class="col-md-8">
                <!-- Fixed Pricing Fields -->
                <div id="modalFixedFields" class="pricing-fields row" style="display: flex;">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalSellingPrice" class="form-label required-field">Fixed Selling Price</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" class="form-control" id="modalSellingPrice" name="selling_price" placeholder="0.00" step="0.01" min="0">
                      </div>
                      <small class="form-text text-muted">Price you sell to customers</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalProfitMargin" class="form-label">Profit Margin</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="modalProfitMargin" name="profit_margin" placeholder="0%" readonly>
                        <span class="input-group-text">%</span>
                      </div>
                      <small class="form-text text-muted">Auto-calculated</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalPotentialProfit" class="form-label">Potential Profit</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="text" class="form-control" id="modalPotentialProfit" name="potential_profit" placeholder="0.00" readonly>
                      </div>
                      <small class="form-text text-muted">Per unit profit</small>
                    </div>
                  </div>
                </div>

                <!-- Manual Pricing Fields -->
                <div id="modalManualFields" class="pricing-fields row" style="display: none;">
                  <div class="col-md-12">
                    <div class="alert alert-info">
                      <i class="mdi mdi-information"></i>
                    </div>
                  </div>
                </div>

                <!-- Margin Pricing Fields -->
                <div id="modalMarginFields" class="pricing-fields row" style="display: none;">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalTargetMargin" class="form-label required-field">Target Profit Margin (%)</label>
                      <div class="input-group">
                        <input type="number" class="form-control" id="modalTargetMargin" name="target_margin" placeholder="0" step="0.01" min="0" max="1000">
                        <span class="input-group-text">%</span>
                      </div>
                      <small class="form-text text-muted">Desired profit margin percentage</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalCalculatedPrice" class="form-label">Calculated Selling Price</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" class="form-control" id="modalCalculatedPrice" name="calculated_price" placeholder="0.00" readonly>
                      </div>
                      <small class="form-text text-muted">Auto-calculated based on margin</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalMarginProfit" class="form-label">Potential Profit</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="text" class="form-control" id="modalMarginProfit" name="margin_profit" placeholder="0.00" readonly>
                      </div>
                      <small class="form-text text-muted">Per unit profit</small>
                    </div>
                  </div>
                </div>

                <!-- Range Pricing Fields -->
                <div id="modalRangeFields" class="pricing-fields row" style="display: none;">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalMinPrice" class="form-label required-field">Minimum Price</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" class="form-control" id="modalMinPrice" name="min_price" placeholder="0.00" step="0.01" min="0">
                      </div>
                      <small class="form-text text-muted">Lowest selling price</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalMaxPrice" class="form-label required-field">Maximum Price</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="number" class="form-control" id="modalMaxPrice" name="max_price" placeholder="0.00" step="0.01" min="0">
                      </div>
                      <small class="form-text text-muted">Highest selling price</small>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="modalRangePotentialProfit" class="form-label">Potential Profit Range</label>
                      <div class="input-group">
                        <span class="input-group-text">₦</span>
                        <input type="text" class="form-control" id="modalRangePotentialProfit" name="range_potential_profit" placeholder="0.00 to 0.00" readonly>
                      </div>
                      <small class="form-text text-muted">Profit range per unit</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closePricingModal()">
            <i class="mdi mdi-close"></i> Cancel
          </button>
          <button type="button" class="btn btn-primary" onclick="savePricing()">
            <i class="mdi mdi-content-save"></i> Save Pricing
          </button>
        </div>
      </div>
    </div>

      <!-- Edit Variant Modal (Embedded) -->
    <div class="edit-variant-modal-overlay" id="editVariantModalOverlay" style="display: none;">
      <div class="edit-variant-modal">
        <!-- Modal Header -->
        <div class="edit-variant-modal-header">
          <h4><i class="mdi mdi-pencil"></i> Edit Variant</h4>
          <button type="button" class="edit-variant-close-btn" onclick="closeEditVariantModal()">
            <i class="mdi mdi-close"></i>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="edit-variant-modal-body">
          <ul class="nav nav-tabs" id="editVariantTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="edit-item-details-tab" data-bs-toggle="tab" data-bs-target="#edit-item-details" type="button" role="tab">
                <i class="mdi mdi-tag-outline"></i> Item Details
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-pricing-tab" data-bs-toggle="tab" data-bs-target="#edit-pricing" type="button" role="tab">
                <i class="mdi mdi-currency-usd"></i> Pricing Details
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="edit-stock-tab" data-bs-toggle="tab" data-bs-target="#edit-stock" type="button" role="tab">
                <i class="mdi mdi-package-variant"></i> Stock Tracking
              </button>
            </li>
          </ul>

          <div class="tab-content" id="editVariantTabContent">
            <!-- Item Details Tab -->
            <div class="tab-pane fade show active" id="edit-item-details" role="tabpanel">
              <form id="editItemDetailsForm">
                <input type="hidden" id="editVariantIndex" value="">
                <div class="form-group">
                  <label for="editVariantDisplay" class="form-label">Variant</label>
                  <input type="text" class="form-control" id="editVariantDisplay" name="variant_display" readonly>
                  <small class="form-text text-muted">Variations separated by /</small>
                </div>
                <div class="form-group">
                  <label for="editVariantSku" class="form-label">SKU</label>
                  <input type="text" class="form-control" id="editVariantSku" name="sku" readonly>
                </div>
                <div class="form-group">
                  <label for="editVariantBarcode" class="form-label">Barcode</label>
                  <input type="text" class="form-control" id="editVariantBarcode" name="barcode" placeholder="Enter barcode">
                  <small class="form-text text-muted">Optional: Product barcode for scanning</small>
                </div>
              </form>
            </div>

            <!-- Pricing Details Tab -->
            <div class="tab-pane fade" id="edit-pricing" role="tabpanel">
              <form id="editPricingForm">
                <!-- Pricing Method Selection (Radio Buttons) -->
                <div class="form-group mb-3">
                  <label class="form-label required-field">Pricing Method</label>
                  <div class="pricing-methods-row">
                    <div class="pricing-method-option">
                      <input type="radio" class="form-check-input" id="editFixedPricing" name="edit_pricing_type" value="fixed" required checked>
                      <label for="editFixedPricing" class="pricing-method-label">
                        <i class="mdi mdi-lock"></i>
                        <span class="method-name">Fixed</span>
                      </label>
                    </div>
                    <div class="pricing-method-option">
                      <input type="radio" class="form-check-input" id="editManualPricing" name="edit_pricing_type" value="manual" required>
                      <label for="editManualPricing" class="pricing-method-label">
                        <i class="mdi mdi-pencil"></i>
                        <span class="method-name">Manual</span>
                      </label>
                    </div>
                    <div class="pricing-method-option">
                      <input type="radio" class="form-check-input" id="editMarginPricing" name="edit_pricing_type" value="margin" required>
                      <label for="editMarginPricing" class="pricing-method-label">
                        <i class="mdi mdi-percent"></i>
                        <span class="method-name">Margin</span>
                      </label>
                    </div>
                    <div class="pricing-method-option">
                      <input type="radio" class="form-check-input" id="editRangePricing" name="edit_pricing_type" value="range" required>
                      <label for="editRangePricing" class="pricing-method-label">
                        <i class="mdi mdi-chart-line"></i>
                        <span class="method-name">Range</span>
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Pricing Method Descriptions -->
                <div id="editPricingDescription" class="alert alert-light mb-3" style="display: none;">
                  <div id="editFixedDesc" class="pricing-desc" style="display: none;">
                    <strong><i class="mdi mdi-lock text-primary"></i> Fixed Pricing:</strong> Set a single, unchanging selling price for this item.
                  </div>
                  <div id="editManualDesc" class="pricing-desc" style="display: none;">
                    <strong><i class="mdi mdi-pencil text-warning"></i> Manual Pricing:</strong> Enter only the cost price. Selling prices will be set during sales.
                  </div>
                  <div id="editMarginDesc" class="pricing-desc" style="display: none;">
                    <strong><i class="mdi mdi-percent text-success"></i> Margin Pricing:</strong> Set a profit margin percentage, and selling price will be auto-calculated.
                  </div>
                  <div id="editRangeDesc" class="pricing-desc" style="display: none;">
                    <strong><i class="mdi mdi-chart-line text-info"></i> Range Pricing:</strong> Set minimum and maximum price boundaries for flexible pricing.
                  </div>
                </div>

                <!-- Fixed Pricing Fields -->
                <div id="editFixedFields" class="pricing-fields" style="display: block;">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editFixedCostPrice" class="form-label required-field">Cost Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editFixedCostPrice" name="fixed_cost_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Price you pay to supplier</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editSellingPrice" class="form-label required-field">Selling Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editSellingPrice" name="selling_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Price you sell to customers</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editProfitMargin" class="form-label">Profit Margin</label>
                        <div class="input-group">
                          <input type="text" class="form-control" id="editProfitMargin" name="profit_margin" placeholder="0%" readonly>
                          <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted">Auto-calculated margin percentage</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editPotentialProfit" class="form-label">Potential Profit</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="editPotentialProfit" name="potential_profit" placeholder="0.00" readonly>
                        </div>
                        <small class="form-text text-muted">Per unit profit</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editTaxRate" class="form-label">Tax Rate</label>
                        <select class="form-select" id="editTaxRate" name="tax_rate">
                          <option value="0">No Tax (0%)</option>
                          <option value="5">VAT 5%</option>
                          <option value="7.5">VAT 7.5%</option>
                          <option value="10">VAT 10%</option>
                          <option value="15">VAT 15%</option>
                        </select>
                        <small class="form-text text-muted">Applicable tax percentage</small>
                      </div>
                    </div>
                  {{--    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editDiscount" class="form-label">Discount</label>
                        <div class="input-group">
                          <input type="number" class="form-control" id="editDiscount" name="discount" placeholder="0" step="0.01" min="0" max="100">
                          <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted">Discount percentage (if any)</small>
                      </div>
                    </div>  --}}

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editFinalPrice" class="form-label">Final Price Review</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="editFinalPrice" name="final_price" placeholder="0.00" readonly>
                        </div>
                        <small class="form-text text-muted">After tax and discount</small>
                      </div>
                    </div>
                  </div>

                </div>

                <!-- Manual Pricing Fields -->
                <div id="editManualFields" class="pricing-fields" style="display: none;">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="editManualCostPrice" class="form-label required-field">Cost Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editManualCostPrice" name="manual_cost_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Price you pay to supplier</small>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Margin Pricing Fields -->
                <div id="editMarginFields" class="pricing-fields" style="display: none;">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editMarginCostPrice" class="form-label required-field">Cost Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editMarginCostPrice" name="margin_cost_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Price you pay to supplier</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editTargetMargin" class="form-label required-field">Target Profit Margin</label>
                        <div class="input-group">
                          <input type="number" class="form-control" id="editTargetMargin" name="target_margin" placeholder="0" step="0.01" min="0" max="1000">
                          <span class="input-group-text">%</span>
                        </div>
                        <small class="form-text text-muted">Desired profit margin percentage</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editCalculatedPrice" class="form-label">Calculated Selling Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editCalculatedPrice" name="calculated_price" placeholder="0.00" readonly>
                        </div>
                        <small class="form-text text-muted">Auto-calculated based on margin</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editMarginProfit" class="form-label">Potential Profit</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="editMarginProfit" name="margin_profit" placeholder="0.00" readonly>
                        </div>
                        <small class="form-text text-muted">Per unit profit</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="editMarginTaxRate" class="form-label">Tax Rate</label>
                        <select class="form-select" id="editMarginTaxRate" name="margin_tax_rate">
                          <option value="0">No Tax (0%)</option>
                          <option value="5">VAT 5%</option>
                          <option value="7.5">VAT 7.5%</option>
                          <option value="10">VAT 10%</option>
                          <option value="15">VAT 15%</option>
                        </select>
                        <small class="form-text text-muted">Applicable tax percentage</small>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Range Pricing Fields -->
                <div id="editRangeFields" class="pricing-fields" style="display: none;">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="editRangeCostPrice" class="form-label required-field">Cost Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editRangeCostPrice" name="range_cost_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Price you pay to supplier</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editMinPrice" class="form-label required-field">Minimum Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editMinPrice" name="min_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Lowest selling price</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editMaxPrice" class="form-label required-field">Maximum Price</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="number" class="form-control" id="editMaxPrice" name="max_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                        <small class="form-text text-muted">Highest selling price</small>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editRangeTaxRate" class="form-label">Tax Rate</label>
                        <select class="form-select" id="editRangeTaxRate" name="range_tax_rate">
                          <option value="0">No Tax (0%)</option>
                          <option value="5">VAT 5%</option>
                          <option value="7.5">VAT 7.5%</option>
                          <option value="10">VAT 10%</option>
                          <option value="15">VAT 15%</option>
                        </select>
                        <small class="form-text text-muted">Applicable tax percentage</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="editRangePotentialProfit" class="form-label">Potential Profit Range</label>
                        <div class="input-group">
                          <span class="input-group-text">₦</span>
                          <input type="text" class="form-control" id="editRangePotentialProfit" name="range_potential_profit" placeholder="0.00 to 0.00" readonly>
                        </div>
                        <small class="form-text text-muted">Profit range per unit</small>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <!-- Stock Tracking Tab -->
            <div class="tab-pane fade" id="edit-stock" role="tabpanel">
              <form id="editStockForm">
                <div class="form-group">
                  <label for="editStockQuantity" class="form-label">Stock Quantity</label>
                  <input type="number" class="form-control" id="editStockQuantity" name="stock_quantity" min="0" required>
                </div>
                <div class="form-group">
                  <label for="editLowStockThreshold" class="form-label">Low Stock Alert (Threshold)</label>
                  <input type="number" class="form-control" id="editLowStockThreshold" name="low_stock_threshold" min="0">
                </div>
                <div class="form-group">
                  <label for="editExpiryDate" class="form-label">Expiry Date</label>
                  <input type="date" class="form-control" id="editExpiryDate" name="expiry_date">
                </div>
                <div class="form-group">
                  <label for="editLocation" class="form-label">Storage Location</label>
                  <input type="text" class="form-control" id="editLocation" name="location" placeholder="e.g., Warehouse A, Shelf 3">
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="edit-variant-modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeEditVariantModal()">
            <i class="mdi mdi-close"></i> Cancel
          </button>
          <button type="button" class="btn btn-primary" onclick="saveVariantChanges()">
            <i class="mdi mdi-content-save"></i> Save Changes
          </button>
        </div>
      </div>


 <style>
      /* Edit Variant Modal Styles */
      .edit-variant-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease-out;
      }

      .edit-variant-modal {
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 700px;
        max-height: 85vh;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease-out;
        display: flex;
        flex-direction: column;
      }

      @keyframes slideUp {
        from {
          transform: translateY(50px);
          opacity: 0;
        }
        to {
          transform: translateY(0);
          opacity: 1;
        }
      }

      .edit-variant-modal-header {
        padding: 20px 25px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 16px 16px 0 0;
      }

      .edit-variant-modal-header h4 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
      }

      .edit-variant-close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
      }

      .edit-variant-close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
      }

      .edit-variant-modal-body {
        padding: 25px;
        overflow-y: auto;
        flex: 1;
      }

      .edit-variant-modal-body::-webkit-scrollbar {
        width: 8px;
      }

      .edit-variant-modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
      }

      .edit-variant-modal-body::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
      }

      .edit-variant-modal-footer {
        padding: 20px 25px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
      }

      .required-field::after {
        content: " *";
        color: #dc3545;
      }

      .pricing-desc {
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        background: rgba(102, 126, 234, 0.1);
      }
    </style>


     <script src="{{ asset('manager_asset/js/add_item_variant.js') }}"></script>

@endsection
