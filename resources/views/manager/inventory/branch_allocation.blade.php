@extends('manager.layouts.layout')
@section('manager_page_title')
Branch Inventory Allocation | {{ config('app.name') }}
@endsection
@section('manager_layout_content')
<div class="container-fluid p-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Branch Inventory</h2>
            <p class="text-muted mb-0">Allocate stock to branches or transfer between branches</p>
        </div>
    </div>

    @if($branches->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i> No active branches found.
            <a href="{{ route('manager.branches') }}" class="alert-link">Create a branch</a> first.
        </div>
    @else

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="allocationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="allocate-tab" data-bs-toggle="tab"
                    data-bs-target="#allocatePane" type="button" role="tab">
                <i class="bi bi-box-arrow-in-right me-1"></i> Allocate to Branch
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="transfer-tab" data-bs-toggle="tab"
                    data-bs-target="#transferPane" type="button" role="tab">
                <i class="bi bi-arrow-left-right me-1"></i> Transfer Between Branches
            </button>
        </li>
    </ul>

    <div class="tab-content" id="allocationTabContent">

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TAB 1 — ALLOCATE                                          --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade show active" id="allocatePane" role="tabpanel">
            <div class="row">
                <!-- Left: Branch picker + current inventory -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Select Branch</h5>
                        </div>
                        <div class="card-body">
                            <select id="branchSelector" class="form-select mb-3">
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        data-name="{{ $branch->branch_name }}"
                                        data-address="{{ $branch->address }}">
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="branchInfo" class="d-none mb-3">
                                <div class="alert alert-light border">
                                    <strong id="selectedBranchName"></strong>
                                    <p class="mb-0 small text-muted" id="selectedBranchAddress"></p>
                                </div>
                            </div>

                            <div id="branchInventoryContainer" class="d-none">
                                <h6 class="mb-2 fw-semibold">Current Inventory</h6>
                                <div id="branchInventoryList" class="list-group list-group-flush">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Allocation form -->
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Allocate Items to Branch</h5>
                        </div>
                        <div class="card-body">
                            <div id="allocationFormContainer" class="d-none">
                                <form id="allocationForm">
                                    @csrf
                                    <input type="hidden" id="selectedBranchId" name="branch_id">

                                    <div class="mb-3">
                                        <label class="form-label">Item Type</label>
                                        <select id="itemTypeSelector" name="item_type" class="form-select" required>
                                            <option value="">-- Select Item Type --</option>
                                            <option value="standard">Standard Items</option>
                                            <option value="variant">Variant Items</option>
                                        </select>
                                    </div>

                                    <div id="standardItemsContainer" class="mb-3 d-none">
                                        <label class="form-label">Select Standard Item</label>
                                        <select id="standardItemSelector" class="form-select">
                                            <option value="">-- Select Item --</option>
                                            @foreach($standardItems as $item)
                                                <option value="{{ $item->id }}"
                                                    data-name="{{ $item->item_name }}"
                                                    data-stock="{{ $item->current_stock }}"
                                                    data-unit="{{ $item->unit ?? '' }}">
                                                    {{ $item->item_name }}
                                                    (Stock: {{ $item->current_stock }} {{ $item->unit ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="variantItemsContainer" class="mb-3 d-none">
                                        <label class="form-label">Select Variant Product</label>
                                        <select id="variantParentSelector" class="form-select mb-2">
                                            <option value="">-- Select Product --</option>
                                            @foreach($variantItems as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                        <select id="variantItemSelector" class="form-select d-none">
                                            <option value="">-- Select Variant --</option>
                                        </select>
                                    </div>

                                    <div id="stockInfo" class="alert alert-info d-none mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Available Stock:</strong> <span id="availableStock"></span>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Quantity to Allocate</label>
                                            <input type="number" id="quantityInput" name="quantity"
                                                class="form-control" step="1" min="1" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Low Stock Threshold <span class="text-muted">(optional)</span></label>
                                            <input type="number" name="low_stock_threshold"
                                                class="form-control" step="0.01" min="0"
                                                placeholder="Alert below this level">
                                        </div>
                                    </div>

                                    <div class="mb-3 mt-3">
                                        <label class="form-label">Notes <span class="text-muted">(optional)</span></label>
                                        <textarea name="notes" class="form-control" rows="2"
                                            placeholder="Any notes about this allocation..."></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Allocate to Branch
                                    </button>
                                </form>
                            </div>

                            <div id="selectBranchPrompt" class="text-center text-muted py-5">
                                <i class="bi bi-hand-index-thumb fs-1 mb-3 d-block"></i>
                                <p>Select a branch on the left to begin allocation</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /allocatePane --}}

        {{-- ══════════════════════════════════════════════════════════ --}}
        {{-- TAB 2 — TRANSFER                                          --}}
        {{-- ══════════════════════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="transferPane" role="tabpanel">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header" style="background:#7c3aed; color:#fff;">
                            <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Transfer Between Branches</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-4">
                                Move stock from one branch to another. Main warehouse stock is not affected.
                            </p>

                            <form id="transferForm">
                                @csrf

                                <!-- From / To branches -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">From Branch</label>
                                        <select id="transferFromBranch" name="from_branch_id"
                                                class="form-select" required>
                                            <option value="">-- Select Source --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end justify-content-center pb-1">
                                        <i class="bi bi-arrow-right fs-4 text-muted"></i>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold">To Branch</label>
                                        <select id="transferToBranch" name="to_branch_id"
                                                class="form-select" required>
                                            <option value="">-- Select Destination --</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Item Type -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Item Type</label>
                                    <select id="transferItemType" name="item_type" class="form-select" required>
                                        <option value="">-- Select Item Type --</option>
                                        <option value="standard">Standard Item</option>
                                        <option value="variant">Variant Item</option>
                                    </select>
                                </div>

                                <!-- Standard item picker -->
                                <div id="transferStandardContainer" class="mb-3 d-none">
                                    <label class="form-label fw-semibold">Select Item</label>
                                    <select id="transferStandardItem" class="form-select">
                                        <option value="">-- Select Item --</option>
                                        @foreach($standardItems as $item)
                                            <option value="{{ $item->id }}"
                                                    data-name="{{ $item->item_name }}">
                                                {{ $item->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Variant item picker -->
                                <div id="transferVariantContainer" class="mb-3 d-none">
                                    <label class="form-label fw-semibold">Select Variant Product</label>
                                    <select id="transferVariantParent" class="form-select mb-2">
                                        <option value="">-- Select Product --</option>
                                        @foreach($variantItems as $item)
                                            <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                    <select id="transferVariantItem" class="form-select d-none">
                                        <option value="">-- Select Variant --</option>
                                    </select>
                                </div>

                                <!-- Source branch stock info -->
                                <div id="transferStockInfo" class="alert alert-secondary d-none mb-3">
                                    <i class="bi bi-shop me-2"></i>
                                    Available in <strong id="transferFromBranchName">source branch</strong>:
                                    <strong><span id="transferAvailableStock">—</span></strong>
                                </div>

                                <!-- Quantity -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Quantity to Transfer</label>
                                    <input type="number" id="transferQty" name="quantity"
                                           class="form-control" step="1" min="1" required>
                                </div>

                                <!-- Notes -->
                                <div class="mb-4">
                                    <label class="form-label">Notes <span class="text-muted">(optional)</span></label>
                                    <textarea name="notes" class="form-control" rows="2"
                                              placeholder="Reason for transfer..."></textarea>
                                </div>

                                <button type="submit" class="btn w-100 text-white"
                                        style="background:#7c3aed;">
                                    <i class="bi bi-arrow-left-right me-1"></i> Transfer Stock
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- /transferPane --}}

    </div>{{-- /tab-content --}}
    @endif

</div>{{-- /container --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    let variantData = @json($variantItems);

    /* ════════════════════════════════════════════════════════════════ */
    /*  TAB 1 — ALLOCATE                                               */
    /* ════════════════════════════════════════════════════════════════ */

    const branchSelector = document.getElementById('branchSelector');
    if (branchSelector) {
        branchSelector.addEventListener('change', function () {
            const branchId = this.value;
            if (branchId) {
                const opt = this.options[this.selectedIndex];
                document.getElementById('selectedBranchName').textContent = opt.dataset.name;
                document.getElementById('selectedBranchAddress').textContent = opt.dataset.address;
                document.getElementById('branchInfo').classList.remove('d-none');
                document.getElementById('selectedBranchId').value = branchId;
                document.getElementById('allocationFormContainer').classList.remove('d-none');
                document.getElementById('selectBranchPrompt').classList.add('d-none');
                loadBranchInventory(branchId, 'branchInventoryList', 'branchInventoryContainer');
            } else {
                document.getElementById('branchInfo').classList.add('d-none');
                document.getElementById('allocationFormContainer').classList.add('d-none');
                document.getElementById('selectBranchPrompt').classList.remove('d-none');
                document.getElementById('branchInventoryContainer').classList.add('d-none');
            }
        });
    }

    $('#itemTypeSelector').on('change', function () {
        const t = $(this).val();
        $('#standardItemsContainer').addClass('d-none');
        $('#variantItemsContainer').addClass('d-none');
        $('#stockInfo').addClass('d-none');
        $('#standardItemSelector').removeAttr('name required');
        $('#variantItemSelector').removeAttr('name required');

        if (t === 'standard') {
            $('#standardItemsContainer').removeClass('d-none');
            $('#standardItemSelector').attr({ name: 'item_id', required: true });
        } else if (t === 'variant') {
            $('#variantItemsContainer').removeClass('d-none');
            $('#variantItemSelector').attr({ name: 'item_id', required: true });
        }
    });

    $('#standardItemSelector').on('change', function () {
        const opt = $(this).find(':selected');
        const stock = opt.data('stock');
        const unit  = opt.data('unit') || '';
        if (stock !== undefined && $(this).val()) {
            $('#availableStock').text(stock + ' ' + unit);
            $('#stockInfo').removeClass('d-none');
            $('#quantityInput').attr('max', stock);
        } else {
            $('#stockInfo').addClass('d-none');
        }
    });

    $('#variantParentSelector').on('change', function () {
        const parentId = $(this).val();
        $('#variantItemSelector').html('<option value="">-- Select Variant --</option>');
        if (parentId) {
            const found = variantData.find(v => v.id == parentId);
            if (found && found.variants) {
                found.variants.forEach(v => {
                    $('#variantItemSelector').append(
                        `<option value="${v.id}" data-stock="${v.stock_quantity}">
                            ${v.variant_name} (Stock: ${v.stock_quantity})
                        </option>`
                    );
                });
                $('#variantItemSelector').removeClass('d-none');
            }
        } else {
            $('#variantItemSelector').addClass('d-none');
        }
    });

    $('#variantItemSelector').on('change', function () {
        const stock = $(this).find(':selected').data('stock');
        if (stock !== undefined && $(this).val()) {
            $('#availableStock').text(stock);
            $('#stockInfo').removeClass('d-none');
            $('#quantityInput').attr('max', stock);
        } else {
            $('#stockInfo').addClass('d-none');
        }
    });

    $('#allocationForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Allocating…');

        $.ajax({
            url: '{{ route("manager.inventory.allocate") }}',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire('Allocated!', res.message, 'success');
                    $('#allocationForm')[0].reset();
                    $('#itemTypeSelector').trigger('change');
                    $('#stockInfo').addClass('d-none');
                    const bid = $('#selectedBranchId').val();
                    if (bid) loadBranchInventory(bid, 'branchInventoryList', 'branchInventoryContainer');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Allocation failed', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-box-arrow-in-right me-1"></i> Allocate to Branch');
            }
        });
    });

    /* ════════════════════════════════════════════════════════════════ */
    /*  TAB 2 — TRANSFER                                               */
    /* ════════════════════════════════════════════════════════════════ */

    // When item type changes in transfer tab
    $('#transferItemType').on('change', function () {
        const t = $(this).val();
        $('#transferStandardContainer').addClass('d-none');
        $('#transferVariantContainer').addClass('d-none');
        $('#transferStockInfo').addClass('d-none');
        $('#transferStandardItem').removeAttr('name required');
        $('#transferVariantItem').removeAttr('name required');

        if (t === 'standard') {
            $('#transferStandardContainer').removeClass('d-none');
            $('#transferStandardItem').attr({ name: 'item_id', required: true });
        } else if (t === 'variant') {
            $('#transferVariantContainer').removeClass('d-none');
            $('#transferVariantItem').attr({ name: 'item_id', required: true });
        }
        refreshTransferStock();
    });

    $('#transferVariantParent').on('change', function () {
        const parentId = $(this).val();
        $('#transferVariantItem').html('<option value="">-- Select Variant --</option>');
        if (parentId) {
            const found = variantData.find(v => v.id == parentId);
            if (found && found.variants) {
                found.variants.forEach(v => {
                    $('#transferVariantItem').append(
                        `<option value="${v.id}" data-name="${v.variant_name}">${v.variant_name}</option>`
                    );
                });
                $('#transferVariantItem').removeClass('d-none');
            }
        } else {
            $('#transferVariantItem').addClass('d-none');
        }
        refreshTransferStock();
    });

    $('#transferFromBranch, #transferStandardItem, #transferVariantItem, #transferVariantParent').on('change', function () {
        refreshTransferStock();
    });

    // Prevent selecting same branch for from and to
    $('#transferToBranch').on('change', function () {
        const fromVal = $('#transferFromBranch').val();
        if ($(this).val() && $(this).val() === fromVal) {
            Swal.fire('Invalid', 'Source and destination branch cannot be the same.', 'warning');
            $(this).val('');
        }
    });
    $('#transferFromBranch').on('change', function () {
        const toVal = $('#transferToBranch').val();
        if (toVal && $(this).val() === toVal) {
            Swal.fire('Invalid', 'Source and destination branch cannot be the same.', 'warning');
            $(this).val('');
        }
        refreshTransferStock();
    });

    function refreshTransferStock() {
        const fromBranchId = $('#transferFromBranch').val();
        const itemType     = $('#transferItemType').val();
        const itemId       = itemType === 'standard'
            ? $('#transferStandardItem').val()
            : $('#transferVariantItem').val();

        if (!fromBranchId || !itemType || !itemId) {
            $('#transferStockInfo').addClass('d-none');
            return;
        }

        // Fetch source branch inventory and find this item
        $.getJSON(`/manager/inventory/branch/${fromBranchId}`, function (res) {
            if (res.success) {
                const record = res.inventory.find(i =>
                    String(i.item_id ?? '') === String(itemId) &&
                    i.item_type === (itemType === 'variant' ? 'variant' : 'standard')
                );
                const qty  = record ? record.current_quantity : 0;
                const name = $('#transferFromBranch option:selected').text();
                $('#transferFromBranchName').text(name);
                $('#transferAvailableStock').text(qty);
                $('#transferQty').attr('max', qty);
                $('#transferStockInfo').removeClass('d-none');
            }
        });
    }

    $('#transferForm').on('submit', function (e) {
        e.preventDefault();

        const fromId  = $('#transferFromBranch').val();
        const toId    = $('#transferToBranch').val();
        if (!fromId || !toId) {
            Swal.fire('Missing fields', 'Please select both source and destination branches.', 'warning');
            return;
        }
        if (fromId === toId) {
            Swal.fire('Invalid', 'Source and destination branch cannot be the same.', 'warning');
            return;
        }

        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Transferring…');

        $.ajax({
            url: '{{ route("manager.inventory.transfer") }}',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire('Transferred!', res.message, 'success');
                    $('#transferForm')[0].reset();
                    $('#transferItemType').trigger('change');
                    $('#transferStockInfo').addClass('d-none');
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function (xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Transfer failed', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="bi bi-arrow-left-right me-1"></i> Transfer Stock');
            }
        });
    });

    /* ════════════════════════════════════════════════════════════════ */
    /*  SHARED — load branch inventory list                            */
    /* ════════════════════════════════════════════════════════════════ */
    function loadBranchInventory(branchId, listId, containerId) {
        $.getJSON(`/manager/inventory/branch/${branchId}`, function (res) {
            const list = document.getElementById(listId);
            if (res.success && res.inventory.length > 0) {
                list.innerHTML = res.inventory.map(item => {
                    const warn = item.low_stock
                        ? '<span class="badge bg-warning text-dark ms-1">Low</span>'
                        : '';
                    return `<div class="list-group-item py-2 px-0 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong class="d-block">${item.item_name}</strong>
                                <span class="badge bg-light text-secondary border">${item.item_type}</span>
                            </div>
                            <div class="text-end">
                                <div class="small">
                                    <span class="text-muted">Allocated:</span> ${item.allocated_quantity}
                                </div>
                                <div class="small">
                                    <span class="text-muted">Remaining:</span>
                                    <strong>${item.current_quantity}</strong>${warn}
                                </div>
                                <div class="small text-muted">Sold: ${item.sold_quantity}</div>
                            </div>
                        </div>
                    </div>`;
                }).join('');
            } else {
                list.innerHTML = '<div class="text-muted small py-2">No inventory allocated yet.</div>';
            }
            document.getElementById(containerId).classList.remove('d-none');
        });
    }

});
</script>
@endsection
