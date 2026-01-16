@extends('manager.layouts.layout')
@section('manager_page_title')
All Items
@endsection
@section('manager_layout_content')


 <div class="content-wrapper d-flex">
						<!-- All Items content starts here -->

<!-- Success and Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Error!</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
<div class="col-12 grid-margin stretch-card">
<div class="card card-rounded">
<div class="card-body">
<div class="d-sm-flex align-items-center justify-content-between mb-3">
    <div>
        <h4 class="card-title mb-0">All Items</h4>
        <p class="card-description">Manage your inventory items</p>
        <p style="color: red;">NOTE; Items tracked here are items with with TURNED ON stock tracking details.</p>
    </div>
    <div class="btn-wrapper">
        <button type="button" class="btn btn-primary text-white me-0" id="addItemQuickAction">
            <i class="bi bi-plus"></i> Add Item
        </button>

        <!-- Bulk Action Buttons (Hidden by default) -->
        <div class="bulk-actions ms-2" id="bulkActions" style="display: none;">
            <button type="button" class="btn btn-outline-secondary me-2" id="deselectAllBtn">
                <i class="bi bi-x-circle"></i> Deselect All
            </button>
            <button type="button" class="btn btn-outline-danger" id="deleteSelectedBtn">
                <i class="bi bi-trash"></i> Delete Selected
            </button>
        </div>
    </div>
</div>

<!-- Search and Filter Options -->
<div class="row mb-3 align-items-center g-2">
    <!-- Search Input -->
    <div class="col-lg-4 col-md-6">
        <div class="input-group">
            <span class="input-group-text bg-white">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" class="form-control" placeholder="Search by name, code, or category..." id="searchItems">
        </div>
    </div>

    <!-- Filters -->
    <div class="col-lg-8 col-md-6">
        <div class="row g-2">
            <!-- Category Filter -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Inventory Status Filter -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <select class="form-select" id="inventoryFilter">
                    <option value="">All Stock</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
            </div>

            <!-- Suppliers Filter -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <select class="form-select" id="supplierFilter">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-sm-6 col-md-12 col-lg-3">
                <div class="d-flex gap-1">
                    <button class="btn btn-outline-primary flex-fill" id="applyFilters" title="Apply Filters">
                        <i class="bi bi-funnel"></i>
                    </button>
                    <button class="btn btn-outline-secondary flex-fill" id="clearFilters" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button class="btn btn-outline-info flex-fill" id="importItems" title="Import Items">
                        <i class="bi bi-upload"></i>
                    </button>
                    <button class="btn btn-outline-success flex-fill" id="exportItems" title="Export Items">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Hidden file input for import -->
            <input type="file" id="importFile" accept=".csv,.xlsx,.xls" style="display: none;">
        </div>
    </div>
</div>
<br>

<!-- Items Table -->
<div class="table-responsive">
    <table class="table table-striped" id="itemsTable">
        <thead>
            <tr>
        <th>S/N</th>
        <th>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllItems" title="Select All Items">
            </div>
        </th>
        <th>Item</th>
        <th>Category</th>
        <th>Unit</th>
        <th>Stock</th>
        <th>Selling Price</th>
        <th>Cost Price</th>
        <th>Supplier</th>
        <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allItemsPaginated as $index => $item)
            <tr data-supplier-id="{{ isset($item['supplier']) && is_object($item['supplier']) ? $item['supplier']->id : '' }}">
                <td>{{ $allItemsPaginated->firstItem() + $index }}</td>
                <td>
                    <div class="form-check">
                        <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item['id'] }}" data-type="{{ $item['type'] }}">
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        @if($item['image'])
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <img src="{{ asset('manager_asset/images/faces/face1.jpg') }}" alt="Default" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                        @endif
                        <div>
                            <h6 class="mb-0">{{ $item['name'] }}</h6>
                            <small class="text-muted">
                                @if($item['type'] == 'standard')
                                    <span class="badge bg-primary">Standard</span>
                                @elseif($item['type'] == 'variant')
                                    <span class="badge bg-info">Variant</span>
                                @elseif($item['type'] == 'bundle')
                                    <span class="badge bg-success">Bundle</span>
                                @endif
                                {{ $item['code'] ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                </td>
                <td>{{ $item['category'] ?? 'N/A' }}</td>
                <td>
                    @if($item['type'] == 'standard')
                        {{ $item['unit'] ?? 'N/A' }}
                    @elseif(isset($item['unit']) && is_object($item['unit']))
                        {{ $item['unit']->name ?? 'N/A' }}
                    @elseif(isset($item['unit']))
                        {{ $item['unit'] }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if(isset($item['current_stock']))
                        <span class="badge
                            @if($item['current_stock'] <= 0) bg-danger
                            @elseif(isset($item['low_stock_threshold']) && $item['current_stock'] == $item['low_stock_threshold']) bg-danger
                            @elseif(isset($item['low_stock_threshold']) && $item['current_stock'] < $item['low_stock_threshold']) bg-warning
                            @else bg-success
                            @endif">
                            {{ $item['current_stock'] }}
                        </span>
                        @if(isset($item['low_stock_threshold']) && $item['current_stock'] == $item['low_stock_threshold'])
                            <span class="badge bg-danger ms-1">Needs Restock</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">N/A</span>
                    @endif
                </td>
                <td>
                    @if(isset($item['selling_price']))
                        ₦{{ number_format($item['selling_price'], 2) }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if(isset($item['cost_price']))
                        ₦{{ number_format($item['cost_price'], 2) }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if(isset($item['supplier']) && is_object($item['supplier']))
                        {{ $item['supplier']->name ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary edit-btn"
                            data-id="{{ $item['id'] }}"
                            data-type="{{ $item['type'] }}"
                            title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>

                    <form action="{{ route('all_items.delete', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" style="display: inline;" class="delete-item-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-item-btn"
                                data-item-name="{{ $item['name'] }}"
                                data-item-type="{{ $item['type'] }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form></tr>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">
                    <div class="py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-2">No items found</p>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <br>

                    <!-- Pagination and Stats -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <span class="text-muted">
                                Showing {{ $allItemsPaginated->firstItem() ?? 0 }} to {{ $allItemsPaginated->lastItem() ?? 0 }} of {{ $allItemsPaginated->total() }} entries
                            </span>
                        </div>
                        <div class="col-md-6">
                            {{ $allItemsPaginated->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
						<!-- End of All Items content -->


	<!-- Item Details Side Panel -->
        <div class="item-details-panel" id="itemDetailsPanel">
            <div class="panel-overlay" id="panelOverlay"></div>
            <div class="panel-content">
                <div class="panel-header">
                    <h5 class="panel-title">
                        <i class="bi bi-box-seam me-2"></i>Item Details
                    </h5>
                    <button type="button" class="btn-close-panel" id="closePanelBtn">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

        <div class="panel-body">
            <!-- Item Image Section -->
            <div class="item-image-section">
                <img id="panelItemImage" src="../assets/images/faces/face1.jpg" alt="Item Image" class="item-image">
                <div class="image-overlay">
                    <button class="btn btn-light btn-sm">
                        <i class="bi bi-camera"></i> Change Image
                    </button>
                </div>
            </div>

            <!-- Item Information Form -->
            <div class="item-form-section">
                <div class="form-group">
                    <label class="form-label">Item Name</label>
                    <input type="text" class="form-control" id="panelItemName" readonly>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">SKU</label>
                            <input type="text" class="form-control" id="panelItemSku" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" id="panelItemCategory" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control" id="panelItemUnit" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Stock Quantity</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="panelItemStock" readonly>
                                <span class="input-group-text stock-status" id="panelStockStatus">In Stock</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Selling Price</label>
                            <input type="text" class="form-control" id="panelItemSellingPrice" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Cost Price</label>
                            <input type="text" class="form-control" id="panelItemCostPrice" readonly>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="panelItemSupplier" readonly>
                </div>

                <!-- Calculated Fields -->
                <div class="calculated-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Profit Margin</div>
                                <div class="info-value" id="panelItemProfit">0%</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-label">Total Value</div>
                                <div class="info-value" id="panelItemTotalValue">₦0</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Last Updated</label>
                    <input type="text" class="form-control" id="panelItemLastUpdated" readonly>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="button" class="btn btn-secondary me-2" id="closePanelFooterBtn">Close</button>
            <button type="button" class="btn btn-primary" id="editItemPanelBtn">
                <i class="bi bi-pencil me-1"></i>Edit Item
            </button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('manager_asset/js/all_items.js') }}"></script>

<script>
// SweetAlert2 for delete confirmation
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtns = document.querySelectorAll('.delete-item-btn');

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemName = this.getAttribute('data-item-name');
            const itemType = this.getAttribute('data-item-type');
            const form = this.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete "${itemName}" (${itemType})? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection
