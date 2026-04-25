@extends('manager.layouts.layout')
@section('manager_page_title')
All Items
@endsection
@section('manager_layout_content')

@php
  $showInventoryColumns = true;
  if(isset($activeSubscription) && $activeSubscription && $activeSubscription->subscriptionPlan) {
    $planName = strtolower(trim($activeSubscription->subscriptionPlan->name ?? ''));
    if($planName === 'basic' || $planName === 'free') {
      $showInventoryColumns = false;
    }
  }
  $stockLabel = $showInventoryColumns ? 'General Stock' : 'In Stock';
@endphp


<div class="content-wrapper d-flex">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><strong>Validation Error!</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Summary Stat Cards ────────────────────────────────────────── --}}
    @php
        $totalItems    = $allItemsPaginated->total();
        $totalGenStock = $allItemsPaginated->sum('current_stock');
        $totalGenLeft  = $allItemsPaginated->sum('general_left');
        $lowStockCount = collect($allItemsPaginated->items())->filter(fn($i) =>
            isset($i['current_stock'], $i['low_stock_threshold']) &&
            $i['current_stock'] > 0 &&
            $i['current_stock'] <= $i['low_stock_threshold']
        )->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ai-stat card shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-boxes"></i></div>
                    <div>
                        <div class="stat-label">Total Items</div>
                        <div class="stat-value">{{ number_format($totalItems) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ai-stat card shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-archive"></i></div>
                    <div>
                        <div class="stat-label">{{ $stockLabel }}</div>
                        <div class="stat-value">{{ number_format($totalGenStock) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @if($showInventoryColumns)
        <div class="col-6 col-md-3">
            <div class="ai-stat card shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-box-seam"></i></div>
                    <div>
                        <div class="stat-label">General Left</div>
                        <div class="stat-value">{{ number_format($totalGenLeft) }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-6 col-md-3">
            <div class="ai-stat card shadow-sm">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <div class="stat-label">Low / Out</div>
                        <div class="stat-value">{{ number_format($lowStockCount) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Toolbar ───────────────────────────────────────────────────── --}}
    <div class="ai-toolbar mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <h5 class="mb-0 fw-bold">All Items</h5>
                <p class="mb-0 text-muted" style="font-size:.78rem;">Stock-tracked inventory items</p>
            </div>
            <div class="col-12 col-md-8">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
                    <div class="input-group" style="width:220px; min-width:160px; flex:1 1 160px; max-width:260px;">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0"
                               placeholder="Search items…" id="searchItems">
                    </div>
                    <select class="form-select" id="categoryFilter" style="width:auto; min-width:130px; flex:0 0 auto;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    <select class="form-select" id="inventoryFilter" style="width:auto; min-width:120px; flex:0 0 auto;">
                        <option value="">All Stock</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                    <select class="form-select col-hide-md" id="supplierFilter" style="width:auto; min-width:130px; flex:0 0 auto;">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                    <div class="d-flex gap-1 flex-shrink-0">
                        <button class="btn btn-primary" id="addItemQuickAction">
                            <i class="bi bi-plus-lg me-1"></i><span class="d-none d-sm-inline">Add Item</span>
                        </button>
                        <button class="btn btn-outline-primary" id="applyFilters" title="Apply Filters"><i class="bi bi-funnel"></i></button>
                        <button class="btn btn-outline-secondary" id="clearFilters" title="Clear"><i class="bi bi-x-circle"></i></button>
                        <button class="btn btn-outline-success" id="exportItems" title="Export"><i class="bi bi-download"></i></button>
                    </div>
                </div>
                <div id="bulkActions" class="mt-2" style="display:none;">
                    <div class="d-flex gap-2 align-items-center bg-light rounded p-2">
                        <span class="text-muted small me-auto" id="selectedCountText">0 selected</span>
                        <button class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                            <i class="bi bi-x-circle me-1"></i>Deselect All
                        </button>
                        <button class="btn btn-sm btn-danger" id="deleteSelectedBtn">
                            <i class="bi bi-trash me-1"></i>Delete Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <input type="file" id="importFile" accept=".csv,.xlsx,.xls" style="display:none;">
    </div>

    {{-- ── Items Table ───────────────────────────────────────────────── --}}
    <div class="ai-table-card mb-4">
        <div class="card-head d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-semibold" style="font-size:.875rem;">
                Showing {{ $allItemsPaginated->firstItem() ?? 0 }}–{{ $allItemsPaginated->lastItem() ?? 0 }}
                of {{ $allItemsPaginated->total() }} items
            </span>
            <span class="text-muted" style="font-size:.78rem;">
                Page {{ $allItemsPaginated->currentPage() }} / {{ $allItemsPaginated->lastPage() }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th style="width:36px;">
                            <input class="form-check-input" type="checkbox" id="selectAllItems" title="Select all">
                        </th>
                        <th>Item</th>
                        <th class="col-hide-sm">Category</th>
                        <th class="col-hide-sm">Unit</th>
                        <th>
                            <span data-bs-toggle="tooltip"
                                  title="Total stock added (constant) = warehouse left + all branch allocations">
                                {{ $stockLabel }}
                            </span>
                        </th>
                        @if($showInventoryColumns)
                        <th>
                            <span data-bs-toggle="tooltip"
                                  title="Warehouse stock remaining after allocations. Decreases each time you allocate to a branch.">
                                General Left
                            </span>
                        </th>
                        <th class="col-hide-md">Branch Inventory</th>
                        @endif
                        <th class="col-hide-sm">Selling Price</th>
                        <th class="col-hide-md">Cost Price</th>
                        <th class="col-hide-md">Supplier</th>
                        <th style="width:90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allItemsPaginated as $index => $item)
                    @php
                        $stock     = $item['current_stock'] ?? null;
                        $threshold = $item['low_stock_threshold'] ?? null;
                        $genLeft   = $item['general_left'] ?? 0;

                        if ($stock === null) {
                            $stockClass = 'sp-na'; $stockIcon = '';
                        } elseif ($stock <= 0) {
                            $stockClass = 'sp-empty'; $stockIcon = '<i class="bi bi-x-circle-fill"></i>';
                        } elseif ($threshold !== null && $stock <= $threshold) {
                            $stockClass = 'sp-low'; $stockIcon = '<i class="bi bi-exclamation-circle-fill"></i>';
                        } else {
                            $stockClass = 'sp-good'; $stockIcon = '<i class="bi bi-check-circle-fill"></i>';
                        }

                        $leftClass = $genLeft <= 0
                            ? 'sp-empty'
                            : ($threshold !== null && $genLeft <= $threshold ? 'sp-low' : 'sp-good');
                    @endphp
                    <tr data-supplier-id="{{ isset($item['supplier']) && is_object($item['supplier']) ? $item['supplier']->id : '' }}">
                        <td class="text-muted" style="font-size:.78rem;">
                            {{ $allItemsPaginated->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <input class="form-check-input item-checkbox" type="checkbox"
                                   value="{{ $item['id'] }}" data-type="{{ $item['type'] }}">
                        </td>
                        {{-- Item --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $imagePath = $item['image'] ?? null;
                                    $imageExists = false;
                                    if ($imagePath && file_exists(public_path($imagePath))) {
                                        $imageExists = true;
                                    }
                                @endphp
                                @if($imageExists)
                                    <img src="{{ asset($imagePath) }}" alt="{{ $item['name'] }}" class="item-thumb" onerror="this.onerror=null;this.src='{{ asset('manager_asset/images/faces/face1.jpg') }}';">
                                @else
                                    <img src="{{ asset('manager_asset/images/faces/face1.jpg') }}" alt="Default" class="item-thumb">
                                @endif
                                <div>
                                    <div class="item-name">{{ $item['name'] }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                        @if($item['type'] === 'standard')
                                            <span class="type-badge type-std">Standard</span>
                                        @elseif(in_array($item['type'], ['variant', 'product_variant']))
                                            <span class="type-badge type-var">Variant</span>
                                        @elseif($item['type'] === 'bundle')
                                            <span class="type-badge type-bnd">Bundle</span>
                                        @endif
                                        @if(!empty($item['code']))
                                            <span class="item-code">{{ $item['code'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        {{-- Category --}}
                        <td class="col-hide-sm text-muted">{{ $item['category'] ?? '—' }}</td>
                        {{-- Unit --}}
                        <td class="col-hide-sm text-muted">
                            @if(isset($item['unit']) && is_object($item['unit']))
                                {{ $item['unit']->name ?? '—' }}
                            @elseif(isset($item['unit']))
                                {{ $item['unit'] }}
                            @else
                                —
                            @endif
                        </td>
                        {{-- General Stock --}}
                        <td>
                            @if($stock !== null)
                                <span class="stock-pill {{ $stockClass }}">
                                    {!! $stockIcon !!} {{ number_format($stock) }}
                                </span>
                                @if($threshold !== null && $stock <= $threshold && $stock > 0)
                                    <div class="mt-1" style="font-size:.68rem; color:#d97706;">
                                        <i class="bi bi-arrow-down-circle"></i> Restock needed
                                    </div>
                                @endif
                            @else
                                <span class="stock-pill sp-na">N/A</span>
                            @endif
                        </td>
                        @if($showInventoryColumns)
                        {{-- General Left --}}
                        <td>
                            <span class="stock-pill {{ $leftClass }}">
                                <i class="bi bi-box-seam"></i> {{ number_format($genLeft) }}
                            </span>
                        </td>
                        {{-- Branch Inventory --}}
                        <td class="col-hide-md">
                            @if(isset($item['branch_inventory_list']) && count($item['branch_inventory_list']) > 0)
                                <div class="branch-chips">
                                    @foreach($item['branch_inventory_list'] as $branchInv)
                                        @php
                                            $parts   = explode(':', $branchInv, 2);
                                            $bname   = trim($parts[0] ?? '');
                                            $details = $parts[1] ?? '';
                                            preg_match('/Current\s+([\d.]+)/i', $details, $m);
                                            $curQty  = isset($m[1]) ? (float)$m[1] : null;
                                            $qtyClass = $curQty === null ? '' :
                                                        ($curQty <= 0 ? 'qty-empty' :
                                                        ($curQty <= 5 ? 'qty-warn' : ''));
                                        @endphp
                                        <span class="branch-chip"
                                              data-bs-toggle="tooltip"
                                              title="{{ trim($details) }}">
                                            <i class="bi bi-shop-window" style="font-size:.7rem;"></i>
                                            {{ Str::limit($bname, 14) }}
                                            @if($curQty !== null)
                                                &nbsp;<span class="qty {{ $qtyClass }}">{{ number_format($curQty) }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="no-branches"><i class="bi bi-dash"></i> No branches</span>
                            @endif
                        </td>
                        @endif
                        {{-- Selling Price --}}
                        <td class="col-hide-sm">
                            @if(isset($item['selling_price']))
                                <span class="price-cell">₦{{ number_format($item['selling_price'], 2) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        {{-- Cost Price --}}
                        <td class="col-hide-md">
                            @if(isset($item['cost_price']))
                                <span class="price-cost">₦{{ number_format($item['cost_price'], 2) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        {{-- Supplier --}}
                        <td class="col-hide-md text-muted">
                            {{ (isset($item['supplier']) && is_object($item['supplier'])) ? $item['supplier']->name : '—' }}
                        </td>
                        {{-- Actions --}}
                        <td>
                            <div class="ai-actions">
                                <button class="btn btn-sm btn-outline-primary edit-btn"
                                        data-id="{{ $item['id'] }}"
                                        data-type="{{ $item['type'] }}"
                                        title="View / Edit">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <form action="{{ route('all_items.delete', ['type' => $item['type'], 'id' => $item['id']]) }}"
                                      method="POST" style="display:inline;" class="delete-item-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger delete-item-btn"
                                            data-item-name="{{ $item['name'] }}"
                                            data-item-type="{{ $item['type'] }}"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size:2.5rem; color:#d1d5db;"></i>
                            <p class="text-muted mt-2 mb-0">No items found</p>
                            <p class="text-muted" style="font-size:.8rem;">Try adjusting your search or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allItemsPaginated->hasPages())
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-top">
            <span class="text-muted" style="font-size:.8rem;">
                {{ $allItemsPaginated->firstItem() }}–{{ $allItemsPaginated->lastItem() }}
                of {{ $allItemsPaginated->total() }}
            </span>
            {{ $allItemsPaginated->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>{{-- /.content-wrapper --}}

{{-- ── Item Details Side Panel ─────────────────────────────────────────── --}}
<div class="item-details-panel" id="itemDetailsPanel">
    <div class="panel-overlay" id="panelOverlay"></div>
    <div class="panel-content">
        <div class="panel-header">
            <h5 class="panel-title"><i class="bi bi-box-seam me-2"></i>Item Details</h5>
            <button type="button" class="btn-close-panel" id="closePanelBtn"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="panel-body">
            <div class="item-image-section">
                <img id="panelItemImage" src="{{ asset('manager_asset/images/faces/face1.jpg') }}"
                     alt="Item Image" class="item-image">
                <div class="image-overlay">
                    <button class="btn btn-light btn-sm"><i class="bi bi-camera"></i> Change Image</button>
                </div>
            </div>
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
document.addEventListener('DOMContentLoaded', function () {
    // Bootstrap tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // SweetAlert2 delete confirmation
    document.querySelectorAll('.delete-item-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var name = this.dataset.itemName;
            var type = this.dataset.itemType;
            var form = this.closest('form');
            Swal.fire({
                title: 'Delete item?',
                html: 'You are about to delete <strong>' + name + '</strong> (' + type + ').<br><span class="text-muted">This cannot be undone.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) form.submit();
            });
        });
    });
});
</script>

@endsection
