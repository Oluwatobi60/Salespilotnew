<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <strong>Parent Item:</strong> {{ $item->variantItem->item_name }}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="variant_name" class="form-label">Variant Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="variant_name" name="variant_name" value="{{ old('variant_name', $item->variant_name) }}" required>
            @error('variant_name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" class="form-control" id="sku" name="sku" value="{{ old('sku', $item->sku) }}">
            @error('sku')
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
            <label for="variant_options" class="form-label">Variant Options</label>
            <input type="text" class="form-control" id="variant_options" name="variant_options" value="{{ old('variant_options', is_array($item->variant_options) ? json_encode($item->variant_options) : $item->variant_options) }}" placeholder="e.g., Size: Large, Color: Red">
            @error('variant_options')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="cost_price" class="form-label">Cost Price</label>
            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="{{ old('cost_price', $item->cost_price ?? $item->manual_cost_price ?? $item->margin_cost_price ?? $item->range_cost_price) }}">
            @error('cost_price')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="selling_price" class="form-label">Selling Price</label>
            <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="{{ old('selling_price', $item->selling_price ?? $item->calculated_price ?? $item->final_price) }}">
            @error('selling_price')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="profit_margin" class="form-label">Profit Margin (%)</label>
            <input type="number" step="0.01" class="form-control" id="profit_margin" name="profit_margin" value="{{ old('profit_margin', $item->profit_margin ?? $item->target_margin) }}" readonly>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="stock_quantity" class="form-label">Stock Quantity</label>
            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $item->stock_quantity) }}">
            @error('stock_quantity')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
            <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $item->low_stock_threshold) }}">
            @error('low_stock_threshold')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Parent Item Information (Read-Only)</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Category:</strong> {{ $item->variantItem->category_name ?? ($item->variantItem->category ?? 'N/A') }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Supplier:</strong> {{ optional($item->variantItem->supplier)->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Unit:</strong> {{ is_object($item->variantItem->unit) ? $item->variantItem->unit->name : ($item->variantItem->unit ?? 'N/A') }}</p>
                    </div>
                </div>
                @if($item->variantItem->item_image)
                    <div class="mt-2">
                        <strong>Item Image:</strong><br>
                        <img src="{{ asset($item->variantItem->item_image) }}" alt="Item Image" class="mt-2" style="max-width: 150px; max-height: 150px;">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-calculate profit margin when cost or selling price changes
    document.addEventListener('DOMContentLoaded', function() {
        const costPriceInput = document.getElementById('cost_price');
        const sellingPriceInput = document.getElementById('selling_price');
        const profitMarginInput = document.getElementById('profit_margin');

        function calculateMargin() {
            const costPrice = parseFloat(costPriceInput.value) || 0;
            const sellingPrice = parseFloat(sellingPriceInput.value) || 0;

            if (costPrice > 0) {
                const margin = ((sellingPrice - costPrice) / costPrice) * 100;
                profitMarginInput.value = margin.toFixed(2);
            } else {
                profitMarginInput.value = '0.00';
            }
        }

        costPriceInput?.addEventListener('input', calculateMargin);
        sellingPriceInput?.addEventListener('input', calculateMargin);
    });
</script>
