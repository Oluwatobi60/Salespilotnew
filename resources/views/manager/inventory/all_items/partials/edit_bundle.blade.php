<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="bundle_name" class="form-label">Bundle Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="bundle_name" name="bundle_name" value="{{ old('bundle_name', $item->bundle_name) }}" required>
            @error('bundle_name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="bundle_code" class="form-label">Bundle Code <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="bundle_code" name="bundle_code" value="{{ old('bundle_code', $item->bundle_code) }}" required>
            @error('bundle_code')
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
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="bundle_selling_price" class="form-label">Bundle Selling Price <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="bundle_selling_price" name="bundle_selling_price" value="{{ old('bundle_selling_price', $item->bundle_selling_price) }}" required>
            @error('bundle_selling_price')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="total_bundle_cost" class="form-label">Total Bundle Cost</label>
            <input type="number" step="0.01" class="form-control" id="total_bundle_cost" name="total_bundle_cost" value="{{ old('total_bundle_cost', $item->total_bundle_cost) }}" readonly>
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
            <label for="bundle_image" class="form-label">Bundle Image</label>
            <input type="file" class="form-control" id="bundle_image" name="bundle_image" accept="image/*">
            @if($item->bundle_image)
                <small class="text-muted d-block mt-1">Current: {{ basename($item->bundle_image) }}</small>
                <img src="{{ asset($item->bundle_image) }}" alt="Current Image" class="mt-2" style="max-width: 100px; max-height: 100px;">
            @endif
            @error('bundle_image')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<h5 class="mt-4 mb-3">Bundle Components</h5>
<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> Bundle components can be managed separately from the bundle items page.
</div>
