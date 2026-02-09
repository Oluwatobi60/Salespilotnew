@extends('manager.layouts.layout')
@section('manager_page_title')
Branch Inventory Allocation | {{ config('app.name') }}
@endsection
@section('manager_layout_content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Branch Inventory Allocation</h2>
            <p class="text-muted">Distribute inventory to your branches</p>
        </div>
    </div>

    <div class="row">
        <!-- Left: Branch Selection & Inventory List -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select Branch</h5>
                </div>
                <div class="card-body">
                    @if($branches->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No active branches found.
                            <a href="  {{ route('manager.branches') }}  " class="alert-link">Create a branch</a> first.
                        </div>
                    @else
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
                            <div class="alert alert-light">
                                <strong id="selectedBranchName"></strong>
                                <p class="mb-0 small text-muted" id="selectedBranchAddress"></p>
                            </div>
                        </div>

                        <!-- Branch Inventory List -->
                        <div id="branchInventoryContainer" class="d-none">
                            <h6 class="mb-3">Current Inventory</h6>
                            <div id="branchInventoryList" class="list-group">
                                <!-- Populated via AJAX -->
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Item Selection & Allocation Form -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Allocate Items to Branch</h5>
                </div>
                <div class="card-body">
                    <div id="allocationFormContainer" class="d-none">
                        <form id="allocationForm">
                            @csrf
                            <input type="hidden" id="selectedBranchId" name="branch_id">

                            <!-- Item Type Selection -->
                            <div class="mb-3">
                                <label class="form-label">Item Type</label>
                                <select id="itemTypeSelector" name="item_type" class="form-select" required>
                                    <option value="">-- Select Item Type --</option>
                                    <option value="standard">Standard Items</option>
                                    <option value="variant">Variant Items</option>
                                </select>
                            </div>

                            <!-- Standard Items Dropdown -->
                            <div id="standardItemsContainer" class="mb-3 d-none">
                                <label class="form-label">Select Standard Item</label>
                                <select id="standardItemSelector" class="form-select">
                                    <option value="">-- Select Item --</option>
                                    @foreach($standardItems as $item)
                                        <option value="{{ $item->id }}"
                                            data-name="{{ $item->item_name }}"
                                            data-stock="{{ $item->current_stock }}"
                                            data-unit="{{ $item->unit ?? '' }}">
                                            {{ $item->item_name }} (Stock: {{ $item->current_stock }} {{ $item->unit ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Variant Items Dropdown -->
                            <div id="variantItemsContainer" class="mb-3 d-none">
                                <label class="form-label">Select Variant Item</label>
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

                            <!-- Available Stock Info -->
                            <div id="stockInfo" class="alert alert-info d-none mb-3">
                                <strong>Available Stock:</strong> <span id="availableStock"></span>
                            </div>

                            <!-- Quantity -->
                            <div class="mb-3">
                                <label class="form-label">Quantity to Allocate</label>
                                <input type="number" id="quantityInput" name="quantity"
                                    class="form-control" step="0.01" min="0.01" required>
                            </div>

                            <!-- Low Stock Threshold -->
                            <div class="mb-3">
                                <label class="form-label">Low Stock Threshold (Optional)</label>
                                <input type="number" name="low_stock_threshold"
                                    class="form-control" step="0.01" min="0"
                                    placeholder="Alert when stock falls below this level">
                            </div>

                            <!-- Notes -->
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="2"
                                    placeholder="Any notes about this allocation..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-share-alt"></i> Allocate to Branch
                            </button>
                        </form>
                    </div>

                    <div id="selectBranchPrompt" class="text-center text-muted py-5">
                        <i class="fas fa-hand-pointer fa-3x mb-3"></i>
                        <p>Please select a branch to begin allocation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let variantData = @json($variantItems);

    // Branch selection handler
    document.getElementById('branchSelector').addEventListener('change', function() {
        const branchId = this.value;

        if (branchId) {
            const selectedOption = this.options[this.selectedIndex];
            document.getElementById('selectedBranchName').textContent = selectedOption.dataset.name;
            document.getElementById('selectedBranchAddress').textContent = selectedOption.dataset.address;
            document.getElementById('branchInfo').classList.remove('d-none');
            document.getElementById('selectedBranchId').value = branchId;
            document.getElementById('allocationFormContainer').classList.remove('d-none');
            document.getElementById('selectBranchPrompt').classList.add('d-none');

            // Load branch inventory
            loadBranchInventory(branchId);
        } else {
            document.getElementById('branchInfo').classList.add('d-none');
            document.getElementById('allocationFormContainer').classList.add('d-none');
            document.getElementById('selectBranchPrompt').classList.remove('d-none');
            document.getElementById('branchInventoryContainer').classList.add('d-none');
        }
    });

    // Item type selection handler
    $('#itemTypeSelector').on('change', function() {
        const itemType = $(this).val();

        $('#standardItemsContainer').addClass('d-none');
        $('#variantItemsContainer').addClass('d-none');
        $('#stockInfo').addClass('d-none');

        // Remove name attribute from both to prevent submission
        $('#standardItemSelector').removeAttr('name');
        $('#variantItemSelector').removeAttr('name');

        if (itemType === 'standard') {
            $('#standardItemsContainer').removeClass('d-none');
            $('#standardItemSelector').attr('required', true).attr('name', 'item_id');
            $('#variantItemSelector').attr('required', false);
        } else if (itemType === 'variant') {
            $('#variantItemsContainer').removeClass('d-none');
            $('#variantItemSelector').attr('required', true).attr('name', 'item_id');
            $('#standardItemSelector').attr('required', false);
        }
    });

    // Standard item selection handler
    $('#standardItemSelector').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const stock = selectedOption.data('stock');
        const unit = selectedOption.data('unit');

        if (stock !== undefined) {
            $('#availableStock').text(`${stock} ${unit}`);
            $('#stockInfo').removeClass('d-none');
            $('#quantityInput').attr('max', stock);
        } else {
            $('#stockInfo').addClass('d-none');
        }
    });

    // Variant parent selection handler
    $('#variantParentSelector').on('change', function() {
        const parentId = $(this).val();
        $('#variantItemSelector').html('<option value="">-- Select Variant --</option>');

        if (parentId) {
            const item = variantData.find(v => v.id == parentId);
            if (item && item.variants) {
                item.variants.forEach(variant => {
                    $('#variantItemSelector').append(
                        `<option value="${variant.id}" data-stock="${variant.stock_quantity}">
                            ${variant.variant_name} (Stock: ${variant.stock_quantity})
                        </option>`
                    );
                });
                $('#variantItemSelector').removeClass('d-none');
            }
        } else {
            $('#variantItemSelector').addClass('d-none');
        }
    });

    // Variant item selection handler
    $('#variantItemSelector').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const stock = selectedOption.data('stock');

        if (stock !== undefined) {
            $('#availableStock').text(stock);
            $('#stockInfo').removeClass('d-none');
            $('#quantityInput').attr('max', stock);
        } else {
            $('#stockInfo').addClass('d-none');
        }
    });

    // Form submission
    $('#allocationForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Allocating...');

        $.ajax({
            url: '{{ route("manager.inventory.allocate") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Success!', response.message, 'success');
                    $('#allocationForm')[0].reset();
                    $('#itemTypeSelector').trigger('change');
                    $('#stockInfo').addClass('d-none');

                    // Reload branch inventory
                    const branchId = $('#selectedBranchId').val();
                    if (branchId) {
                        loadBranchInventory(branchId);
                    }
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to allocate inventory';
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-share-alt"></i> Allocate to Branch');
            }
        });
    });

    // Load branch inventory
    function loadBranchInventory(branchId) {
        $.ajax({
            url: `/manager/inventory/branch/${branchId}`,
            method: 'GET',
            success: function(response) {
                if (response.success && response.inventory.length > 0) {
                    let html = '';
                    response.inventory.forEach(item => {
                        const lowStockClass = item.low_stock ? 'text-danger' : '';
                        const lowStockIcon = item.low_stock ? '<i class="fas fa-exclamation-triangle text-warning"></i>' : '';

                        html += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>${item.item_name}</strong>
                                        <span class="badge bg-secondary">${item.item_type}</span>
                                        <div class="small text-muted">
                                            Allocated: ${item.allocated_quantity} |
                                            Current: <span class="${lowStockClass}">${item.current_quantity}</span> ${lowStockIcon} |
                                            Sold: ${item.sold_quantity}
                                        </div>
                                        <div class="small text-muted">Date: ${item.allocated_at}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#branchInventoryList').html(html);
                    $('#branchInventoryContainer').removeClass('d-none');
                } else {
                    $('#branchInventoryList').html('<div class="alert alert-info">No inventory allocated yet</div>');
                    $('#branchInventoryContainer').removeClass('d-none');
                }
            }
        });
    }
});
</script>
@endsection
