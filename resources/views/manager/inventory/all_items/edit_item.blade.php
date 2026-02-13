@extends('manager.layouts.layout')
@section('manager_page_title')
Edit Item
@endsection
@section('manager_layout_content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper">
        <div class="content-wrapper">
            <!-- Edit Item content starts here -->
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                        <div class="card-body">
                            <div class="d-sm-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h4 class="card-title mb-0">Edit Item</h4>
                                    <p class="card-description">Update item details</p>
                                </div>
                                <div class="btn-wrapper">
                                    <a href="{{ route('all_items') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Back to All Items
                                    </a>
                                </div>
                            </div>

                            <!-- Item Type Badge -->
                            <div class="mb-3">
                                @if($itemType == 'standard')
                                    <span class="badge bg-primary fs-6">Standard Item</span>
                                @elseif($itemType == 'variant')
                                    <span class="badge bg-info fs-6">Variant Item</span>
                                @elseif($itemType == 'product_variant')
                                    <span class="badge bg-warning fs-6">Product Variant</span>
                                @elseif($itemType == 'bundle')
                                    <span class="badge bg-success fs-6">Bundle Item</span>
                                @endif
                            </div>

                            <!-- Edit Form -->
                            <form action="{{ route('all_items.update_item', ['type' => $itemType, 'id' => $item->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Standard Item Fields -->
                                @if($itemType == 'standard')
                                    @include('manager.inventory.all_items.partials.edit_standard', ['item' => $item])
                                @endif

                                <!-- Variant Item Fields -->
                                @if($itemType == 'variant')
                                    @include('manager.inventory.all_items.partials.edit_variant', ['item' => $item])
                                @endif

                                <!-- Product Variant Fields -->
                                @if($itemType == 'product_variant')
                                    @include('manager.inventory.all_items.partials.edit_product_variant', ['item' => $item])
                                @endif

                                <!-- Bundle Item Fields -->
                                @if($itemType == 'bundle')
                                    @include('manager.inventory.all_items.partials.edit_bundle', ['item' => $item])
                                @endif

                                <!-- Form Actions -->
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bi bi-save"></i> Update Item
                                    </button>
                                    <a href="{{ route('all_items') }}" class="btn btn-light">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Edit Item content -->
        </div>
    </div>
</div>

@endsection
