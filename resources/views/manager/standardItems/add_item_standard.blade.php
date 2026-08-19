@extends('manager.layouts.layout')
@section('manager_page_title')
Add Item Standard
@endsection
@section('manager_layout_content')

<link rel="stylesheet" href="{{ asset('manager_asset/css/add_item_standard.css') }}">

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



    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

<!-- Modal Body -->
      <div class="modal-body-custom">
        <div class="intro-text">
          <p>
            <i class="mdi mdi-information-outline"></i>
            Fill in the details below to add a new standard item to your inventory. All required fields are marked with an asterisk (*).
          </p>
        </div>

                    <form class="forms-sample" id="addItemForm" method="POST" action="{{ route('standard.create') }}" enctype="multipart/form-data">
                        @csrf
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
                                <input type="text" class="form-control" id="itemName" name="item_name" placeholder="Enter item name" required value="{{ old('item_name') }}">

                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="itemCode" class="form-label">Item Code/SKU</label>
                                <input type="text" class="form-control" id="itemCode" name="item_code" placeholder="Auto-generated or enter custom code" value="{{ old('item_code') }}">

                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                  <label for="category" class="form-label required-field mb-0">Category</label>
                                  <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 rounded-pill" style="font-size: 0.75rem;" onclick="suggestCategoryWithAI()">
                                    <i class="mdi mdi-robot"></i> ✨ Auto Suggest
                                  </button>
                                </div>
                                <select class="form-select" id="category" name="category" required>
                                  <option value="">Select Category</option>
                                   @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>
                                      {{ $category->category_name }}
                                    </option>
                                  @endforeach
                                  <option value="add_new_category" style="color: #007bff; font-weight: 600;">
                                    <i class="mdi mdi-plus"></i> + Add New Category
                                  </option>
                                </select>

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
                                  <option value="add_new_supplier" style="color: #007bff; font-weight: 600;">
                                    <i class="mdi mdi-plus"></i> + Add New Supplier
                                  </option>
                                </select>

                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="unit" class="form-label required-field">Unit of Measurement</label>
                                <div class="unit-input-container">
                                  <select class="form-select" id="unit" name="unit">
                                  <option value="">Select Unit</option>
                                  @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit') == $unit->id ? 'selected' : '' }}>
                                      {{ $unit->name }}
                                    </option>
                                  @endforeach
                                  <option value="add_new_unit" style="color: #007bff; font-weight: 600;">
                                    <i class="mdi mdi-plus"></i> + Add New Unit
                                  </option>
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
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                  <label for="description" class="form-label mb-0">Description</label>
                                  <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 rounded-pill" style="font-size: 0.75rem;" onclick="generateDescriptionWithAI()">
                                    <i class="mdi mdi-robot"></i> ✨ Write with AI
                                  </button>
                                </div>
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
                                    <input type="radio" class="form-check-input" id="fixedPricing" name="pricing_type" value="fixed" required {{ old('pricing_type', 'fixed') == 'fixed' ? 'checked' : '' }}>
                                    <label for="fixedPricing" class="pricing-method-label">
                                      <i class="mdi mdi-lock"></i>
                                      <span class="method-name">Fixed Pricing</span>
                                    </label>
                                  </div>
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="manualPricing" name="pricing_type" value="manual" required {{ old('pricing_type') == 'manual' ? 'checked' : '' }}>
                                    <label for="manualPricing" class="pricing-method-label">
                                      <i class="mdi mdi-pencil"></i>
                                      <span class="method-name">Manual Pricing</span>
                                    </label>
                                  </div>
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="marginPricing" name="pricing_type" value="margin" required {{ old('pricing_type') == 'margin' ? 'checked' : '' }}>
                                    <label for="marginPricing" class="pricing-method-label">
                                      <i class="mdi mdi-percent"></i>
                                      <span class="method-name">Margin Pricing</span>
                                      <small>Auto-calculated by margin</small>
                                    </label>
                                  </div>
                                  <div class="pricing-method-option">
                                    <input type="radio" class="form-check-input" id="rangePricing" name="pricing_type" value="range" required {{ old('pricing_type') == 'range' ? 'checked' : '' }}>
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

                          <!-- Dynamic Pricing Fields based on selected pricing type -->

                          <!-- Fixed Pricing: Cost Price, Selling Price, Profit Margin, Potential Profit, Tax Rate, Final Price -->
                          <div id="fixedFields" class="pricing-fields row" style="display: flex;">
                            <div class="col-md-3">
                              <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                  <label for="costPrice" class="form-label required-field mb-0">Cost Price</label>
                                  <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 rounded-pill" style="font-size: 0.75rem;" onclick="recommendPriceWithAI()">
                                    <i class="mdi mdi-robot"></i> ✨ AI Suggest
                                  </button>
                                </div>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="costPrice" name="cost_price" placeholder="0.00" step="0.01" min="0" required>
                                </div>
                                <small class="form-text text-muted">Price you pay to supplier</small>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                                <label for="sellingPrice" class="form-label required-field">Fixed Selling Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="sellingPrice" name="selling_price" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <small class="form-text text-muted">Price you sell to customers</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="profitMargin" class="form-label">Profit Margin</label>
                                <div class="input-group">
                                  <input type="text" class="form-control" id="profitMargin" name="profit_margin" placeholder="0%" readonly>
                                  <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Auto-calculated</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="potentialProfit" class="form-label">Potential Profit</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="text" class="form-control" id="potentialProfit" name="potential_profit" placeholder="0.00" readonly>
                                </div>
                                <small class="form-text text-muted">Per unit profit</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="fixedTaxRate" class="form-label">Tax Rate</label>
                                <select class="form-select" id="fixedTaxRate" name="tax_rate">
                                  <option value="0">No Tax (0%)</option>
                                  <option value="5">VAT 5%</option>
                                  <option value="7.5">VAT 7.5%</option>
                                  <option value="10">VAT 10%</option>
                                  <option value="15">VAT 15%</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div id="fixedFinalPrice" class="pricing-fields row" style="display: flex;">
                            <div class="col-md-3">
                              <div class="form-group">
                                <label class="form-label">Final Price Preview</label>
                                <div class="alert alert-info mb-0" role="alert" style="padding: 8px 12px;">
                                  <strong><span id="finalPrice">₦0.00</span></strong>
                                </div>
                                <input type="hidden" id="finalPriceInput" name="final_price" value="0">
                              </div>
                            </div>
                          </div>

                          <!-- Manual Pricing: Cost Price only -->
                          <div id="manualFields" class="pricing-fields row" style="display: none;">
                            <div class="col-md-3">
                              <div class="form-group">
                                <label for="manualCostPrice" class="form-label required-field">Cost Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="manualCostPrice" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <small class="form-text text-muted">Price you pay to supplier</small>
                              </div>
                            </div>
                          </div>

                          <!-- Margin Pricing: Cost Price, Target Margin, Calculated Price, Potential Profit, Tax Rate, Final Price -->
                          <div id="marginFields" class="pricing-fields row" style="display: none;">
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="marginCostPrice" class="form-label required-field">Cost Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="marginCostPrice" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <small class="form-text text-muted">Supplier price</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="targetMargin" class="form-label">Target Profit Margin (%)</label>
                                <div class="input-group">
                                  <input type="number" class="form-control" id="targetMargin" name="target_margin" placeholder="0" step="0.01" min="0" max="1000">
                                  <span class="input-group-text">%</span>
                                </div>
                                <small class="form-text text-muted">Desired margin</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="calculatedPrice" class="form-label">Calculated Selling Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="calculatedPrice" name="calculated_price" placeholder="0.00" readonly>
                                </div>
                                <small class="form-text text-muted">Auto-calculated</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="marginProfit" class="form-label">Potential Profit</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="text" class="form-control" id="marginProfit" name="margin_profit" placeholder="0.00" readonly>
                                </div>
                                <small class="form-text text-muted">Per unit</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="marginTaxRate" class="form-label">Tax Rate</label>
                                <select class="form-select" id="marginTaxRate">
                                  <option value="0">No Tax (0%)</option>
                                  <option value="5">VAT 5%</option>
                                  <option value="7.5">VAT 7.5%</option>
                                  <option value="10">VAT 10%</option>
                                  <option value="15">VAT 15%</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label class="form-label">Final Price</label>
                                <div class="alert alert-info mb-0" role="alert" style="padding: 8px 12px;">
                                  <strong><span id="marginFinalPrice">₦0.00</span></strong>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Range Pricing: Cost Price, Min Price, Max Price, Potential Profit Range, Tax Rate, Final Price -->
                          <div id="rangeFields" class="pricing-fields row" style="display: none;">
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="rangeCostPrice" class="form-label required-field">Cost Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="rangeCostPrice" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <small class="form-text text-muted">Supplier price</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="minPrice" class="form-label">Minimum Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="minPrice" name="min_price" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <small class="form-text text-muted">Lowest price</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="maxPrice" class="form-label">Maximum Price</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="number" class="form-control" id="maxPrice" name="max_price" placeholder="0.00" step="0.01" min="0">
                                </div>
                                <small class="form-text text-muted">Highest price</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="rangePotentialProfit" class="form-label">Potential Profit Range</label>
                                <div class="input-group">
                                  <span class="input-group-text">₦</span>
                                  <input type="text" class="form-control" id="rangePotentialProfit" name="range_potential_profit" placeholder="0.00 - 0.00" readonly>
                                </div>
                                <small class="form-text text-muted">Profit range</small>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label for="rangeTaxRate" class="form-label">Tax Rate</label>
                                <select class="form-select" id="rangeTaxRate">
                                  <option value="0">No Tax (0%)</option>
                                  <option value="5">VAT 5%</option>
                                  <option value="7.5">VAT 7.5%</option>
                                  <option value="10">VAT 10%</option>
                                  <option value="15">VAT 15%</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                <label class="form-label">Final Price Range</label>
                                <div class="alert alert-info mb-0" role="alert" style="padding: 8px 12px;">
                                  <strong><span id="rangeFinalPrice">₦0.00 - ₦0.00</span></strong>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Quantity-based Pricing Tiers (for Range Pricing) -->
                          <div id="pricingTiers" style="display: none;">
                      </div>

                      <!-- Section 3: Stock Details -->
                      <div class="card mb-4">
                        <div class="card-header">
                          <h5 class="mb-0 d-flex justify-content-between align-items-center">
                            <span>
                              <i class="mdi mdi-warehouse"></i> <strong>Stock Tracking Details</strong>
                            </span>
                            <label class="toggle-switch" title="Toggle Stock Details">
                              <input type="checkbox" id="stockToggleCheckbox" name="track_stock" value="1" checked onchange="toggleStockDetails()">
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
                    <button type="reset" class="btn btn-light" form="addItemForm">
                    <i class="mdi mdi-refresh"></i> Reset
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">
                    <i class="mdi mdi-close"></i> Cancel
                    </button>
                   <button type="submit" form="addItemForm" id="addItemBtn" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Add Item
                    </button>
                </div>


                    </form>
      </div> <!-- End Modal Body -->

    </div> <!-- End Modal Container -->

<!-- Category Panel Component -->
<x-category-panel />

<!-- Supplier Panel Component -->
<x-supplier-panel />

<!-- Unit Panel Component -->
<x-unit-panel />



       <script src="{{ asset('manager_asset/js/components/category-panel.js') }}"></script>
       <script src="{{ asset('manager_asset/js/components/supplier-panel.js') }}"></script>
       <script src="{{ asset('manager_asset/js/components/unit-panel.js') }}"></script>
       <script src="{{ asset('manager_asset/js/add_item_standard.js') }}"></script>
        <script>
        // AI Category Tagging
        function suggestCategoryWithAI() {
            const itemName = document.getElementById('itemName').value;
            const description = document.getElementById('description').value;

            if (!itemName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Item Name Required',
                    text: 'Please fill in the item name before asking for category suggestions.',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            const btn = event.currentTarget || document.activeElement;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Tagging...';

            fetch('{{ route("manager.ai.suggest-category") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_name: itemName,
                    description: description
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.category_id) {
                    document.getElementById('category').value = data.category_id;
                    document.getElementById('category').dispatchEvent(new Event('change'));
                    Swal.fire({
                        icon: 'success',
                        title: 'Category Suggested!',
                        text: 'Category successfully suggested and selected based on your item details.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Confidence Match',
                        text: data.message || 'AI could not confidently match this item to any existing category.',
                        confirmButtonColor: '#007bff'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'AI Request Failed',
                    text: err.message || 'Failed to communicate with AI service.',
                    confirmButtonColor: '#007bff'
                });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // AI Description Copywriter
        function generateDescriptionWithAI() {
            const itemName = document.getElementById('itemName').value;
            const categoryId = document.getElementById('category').value;

            if (!itemName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Item Name Required',
                    text: 'Please enter the item name so the AI copywriter knows what to describe.',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            const btn = event.currentTarget || document.activeElement;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Writing...';

            fetch('{{ route("manager.ai.generate-description") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_name: itemName,
                    category_id: categoryId ? parseInt(categoryId) : null
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.description) {
                    document.getElementById('description').value = data.description;
                    Swal.fire({
                        icon: 'success',
                        title: 'Description Generated!',
                        text: 'Engaging description has been copied to the textarea.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Generation Failed',
                        text: data.message || 'AI copywriter failed to write a description.',
                        confirmButtonColor: '#007bff'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'AI Request Failed',
                    text: err.message || 'Failed to communicate with AI copywriter service.',
                    confirmButtonColor: '#007bff'
                });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // AI Pricing Recommendation
        function recommendPriceWithAI() {
            const itemName = document.getElementById('itemName').value;
            const categoryId = document.getElementById('category').value;
            
            // Get cost price depending on which field is currently active
            let costPrice = null;
            const pricingType = document.querySelector('input[name="pricing_type"]:checked').value;
            
            if (pricingType === 'fixed') {
                costPrice = document.getElementById('costPrice').value;
            } else if (pricingType === 'margin') {
                costPrice = document.getElementById('marginCostPrice').value;
            } else if (pricingType === 'range') {
                costPrice = document.getElementById('rangeCostPrice').value;
            } else {
                costPrice = document.getElementById('costPrice').value; // fallback
            }

            if (!itemName) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Item Name Required',
                    text: 'Please enter the item name first.',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            if (!costPrice || isNaN(parseFloat(costPrice))) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cost Price Required',
                    text: 'Please fill in a valid cost price first so the AI can suggest retail pricing.',
                    confirmButtonColor: '#007bff'
                });
                return;
            }

            const btn = event.currentTarget || document.activeElement;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Analyzing...';

            fetch('{{ route("manager.ai.recommend-price") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    item_name: itemName,
                    cost_price: parseFloat(costPrice),
                    category_id: categoryId ? parseInt(categoryId) : null
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'AI Price Recommendations',
                        html: `<div class="text-start">
                                <p><strong>Suggested Retail Price:</strong> ₦${parseFloat(data.recommended_price).toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
                                <p><strong>Estimated Profit Margin:</strong> ${data.margin_percentage}%</p>
                                <hr>
                                <p class="text-muted" style="font-size: 0.9rem;">${data.justification}</p>
                               </div>`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Apply Recommendation',
                        cancelButtonText: 'Dismiss',
                        confirmButtonColor: '#007bff',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (pricingType === 'fixed') {
                                document.getElementById('sellingPrice').value = data.recommended_price;
                                document.getElementById('sellingPrice').dispatchEvent(new Event('input'));
                            } else if (pricingType === 'margin') {
                                document.getElementById('targetMargin').value = data.margin_percentage;
                                document.getElementById('targetMargin').dispatchEvent(new Event('input'));
                            } else if (pricingType === 'range') {
                                document.getElementById('minPrice').value = data.recommended_price;
                                document.getElementById('maxPrice').value = (data.recommended_price * 1.15).toFixed(2);
                                document.getElementById('minPrice').dispatchEvent(new Event('input'));
                            }
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Pricing Applied!',
                                text: 'Pricing details have been set based on the AI recommendation.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Recommendation Failed',
                        text: data.message || 'AI was unable to generate price recommendations.',
                        confirmButtonColor: '#007bff'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'AI Request Failed',
                    text: err.message || 'Failed to communicate with pricing assistant service.',
                    confirmButtonColor: '#007bff'
                });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
        </script>
@endsection
