<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="{{ old('item_name', $item->item_name) }}" required>
            @error('item_name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="item_code" class="form-label">Item Code <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="item_code" name="item_code" value="{{ old('item_code', $item->item_code) }}" required>
            @error('item_code')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="barcode" class="form-label">Barcode</label>
            <input type="text" class="form-control" id="barcode" name="barcode" value="{{ old('barcode', $item->barcode) }}">
            @error('barcode')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $item->category) }}" required>
            @error('category')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select class="form-select" id="supplier_id" name="supplier_id">
                <option value="">Select Supplier</option>
                @foreach($suppliers ?? [] as $supplier)
                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
            @error('supplier_id')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="unit_id" class="form-label">Unit</label>
            <select class="form-select" id="unit_id" name="unit_id">
                <option value="">Select Unit</option>
                @foreach($units ?? [] as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }} ({{ $unit->abbreviation }})
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <label for="brand" class="form-label">Brand</label>
    <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand', $item->brand) }}">
    @error('brand')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
    @error('description')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="form-group mb-3">
    <label for="item_image" class="form-label">Item Image</label>
    <input type="file" class="form-control" id="item_image" name="item_image" accept="image/*">
    @if($item->item_image)
        <div class="mt-2">
            <small class="text-muted d-block">Current Image:</small>
            <img src="{{ asset($item->item_image) }}" alt="Current Image" class="mt-1 border rounded" style="max-width: 150px; max-height: 150px; object-fit: cover;">
        </div>
    @endif
    @error('item_image')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<hr class="my-4">

<!-- Variant Sets Display -->
<h5 class="mb-3">
    <i class="bi bi-collection"></i> Variant Sets
</h5>
@if($item->variant_sets && is_array($item->variant_sets))
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                @php $setNumber = 1; @endphp
                @foreach($item->variant_sets as $index => $variantSet)
                    <div class="col-md-4 mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-primary mb-2">{{ $variantSet['name'] ?? 'Set ' . $setNumber }}</h6>
                            @if(isset($variantSet['options']) && is_array($variantSet['options']))
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($variantSet['options'] as $option)
                                        <span class="badge bg-secondary">{{ $option }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @php $setNumber++; @endphp
                @endforeach
            </div>
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> To modify variant sets, please use the Add Variant Item page
            </small>
        </div>
    </div>
@else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> No variant sets defined for this item
    </div>
@endif

<!-- Product Variants List -->
<h5 class="mb-3">
    <i class="bi bi-grid-3x3-gap"></i> Product Variants
    <span class="badge bg-primary">{{ $item->variants->count() ?? 0 }}</span>
</h5>

@if($item->variants && $item->variants->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Variant Name</th>
                    <th>SKU</th>
                    <th>Options</th>
                    <th>Stock</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($item->variants as $variant)
                    <tr>
                        <td>
                            <strong>{{ $variant->variant_name }}</strong>
                        </td>
                        <td>
                            <code>{{ $variant->sku ?? 'N/A' }}</code>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @if($variant->primary_value)
                                    <span class="badge bg-info">{{ $variant->primary_value }}</span>
                                @endif
                                @if($variant->secondary_value)
                                    <span class="badge bg-info">{{ $variant->secondary_value }}</span>
                                @endif
                                @if($variant->tertiary_value)
                                    <span class="badge bg-info">{{ $variant->tertiary_value }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $stock = $variant->stock_quantity ?? 0;
                                $threshold = $variant->low_stock_threshold ?? 10;
                            @endphp
                            <span class="badge
                                @if($stock <= 0) bg-danger
                                @elseif($stock <= $threshold) bg-warning
                                @else bg-success
                                @endif">
                                {{ $stock }}
                            </span>
                        </td>
                        <td>
                            @php
                                $costPrice = 0;
                                if ($variant->pricing_type === 'fixed' && $variant->cost_price) {
                                    $costPrice = $variant->cost_price;
                                } elseif ($variant->pricing_type === 'manual' && $variant->manual_cost_price) {
                                    $costPrice = $variant->manual_cost_price;
                                } elseif ($variant->pricing_type === 'margin' && $variant->margin_cost_price) {
                                    $costPrice = $variant->margin_cost_price;
                                } elseif ($variant->pricing_type === 'range' && $variant->range_cost_price) {
                                    $costPrice = $variant->range_cost_price;
                                }
                            @endphp
                            ₦{{ number_format($costPrice, 2) }}
                        </td>
                        <td>
                            @php
                                $sellingPrice = 0;
                                if ($variant->pricing_type === 'fixed' && $variant->selling_price) {
                                    $sellingPrice = $variant->selling_price;
                                } elseif ($variant->pricing_type === 'margin' && $variant->calculated_price) {
                                    $sellingPrice = $variant->calculated_price;
                                } elseif ($variant->final_price) {
                                    $sellingPrice = $variant->final_price;
                                }
                            @endphp
                            ₦{{ number_format($sellingPrice, 2) }}
                        </td>
                        <td>
                            @if($variant->sell_item)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Active
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-x-circle"></i> Inactive
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i> To edit individual variant details (stock, pricing, etc.), please use the Edit Variant page or contact administrator.
    </div>
@else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> No product variants have been created for this item yet.
    </div>
@endif
