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
            <label for="unit" class="form-label">Unit</label>
            <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $item->unit) }}">
            @error('unit')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
    @error('description')
        <div class="text-danger small">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="cost_price" class="form-label">Cost Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" value="{{ old('cost_price', $item->cost_price) }}" required>
            @error('cost_price')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="selling_price" class="form-label">Selling Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" value="{{ old('selling_price', $item->selling_price) }}" required>
            @error('selling_price')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="profit_margin" class="form-label">Profit Margin (%)</label>
            <input type="number" step="0.01" class="form-control" id="profit_margin" name="profit_margin" value="{{ old('profit_margin', $item->profit_margin) }}" readonly>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="current_stock" class="form-label">Current Stock</label>
            <input type="number" class="form-control" id="current_stock" name="current_stock" value="{{ old('current_stock', $item->current_stock) }}">
            @error('current_stock')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
            <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="{{ old('low_stock_threshold', $item->low_stock_threshold) }}">
            @error('low_stock_threshold')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label for="item_image" class="form-label">Item Image</label>
            <input type="file" class="form-control" id="item_image" name="item_image" accept="image/*">
            @if($item->item_image)
                <small class="text-muted d-block mt-1">Current: {{ basename($item->item_image) }}</small>
                <img src="{{ asset($item->item_image) }}" alt="Current Image" class="mt-2" style="max-width: 100px; max-height: 100px;">
            @endif
            @error('item_image')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
