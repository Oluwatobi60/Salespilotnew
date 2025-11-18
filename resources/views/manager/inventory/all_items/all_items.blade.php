@extends('manager.layouts.layout')
@section('manager_page_title')
Discount Report
@endsection
@section('manager_layout_content')
<div class="container-scroller">

    <div class="container-fluid page-body-wrapper">
		
	    <div class="content-wrapper">
						<!-- All Items content starts here -->
			<div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                        <div class="card-body">
                            <div class="d-sm-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h4 class="card-title mb-0">All Items</h4>
                                    <p class="card-description">Manage your inventory items</p>
                                </div>
                                <div class="btn-wrapper">
                                    <button type="button" class="btn btn-primary text-white me-0" id="addItemQuickAction">
                                        <i class="bi bi-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>
                                                
        <!-- Search and Filter Options -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search items..." id="searchItems">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                <!-- Category Filter -->
                <select class="form-select" id="categoryFilter" style="max-width: 140px;">
                    <option value="">All Categories</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Furniture">Furniture</option>
                    <option value="Kitchen">Kitchen</option>
                    <option value="Clothing">Clothing</option>
                </select>
                
                <!-- Inventory Status Filter -->
                <select class="form-select" id="inventoryFilter" style="max-width: 140px;">
                    <option value="">All Stock</option>
                    <option value="in-stock">In Stock</option>
                    <option value="low-stock">Low Stock</option>
                    <option value="out-of-stock">Out of Stock</option>
                </select>
                
                <!-- Suppliers Filter -->
                <select class="form-select" id="supplierFilter" style="max-width: 140px;">
                    <option value="">All Suppliers</option>
                    <option value="supplier1">Tech Solutions Ltd</option>
                    <option value="supplier2">Global Electronics</option>
                    <option value="supplier3">Office Furniture Co</option>
                    <option value="supplier4">Kitchen Essentials</option>
                </select>
                
                <!-- Action Buttons -->
                <button class="btn btn-outline-primary" id="applyFilters">
                    <i class="bi bi-funnel"></i> Apply
                </button>
                <button class="btn btn-outline-secondary" id="clearFilters">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
                <button class="btn btn-outline-success">
                    <i class="bi bi-download"></i> Export
                </button>
            </div>
        </div>
        <br>

        <!-- Items Table -->
        <div class="table-responsive">
            <table class="table table-striped" id="itemsTable">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Select</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Stock</th>
                        <th>Selling Price</th>
                        <th>Cost Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Item Row 1 -->
                    <tr>
                        <td>1</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="1">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face1.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">Samsung Galaxy S23 Ultra</h6>
                                    <small class="text-muted">SKU: SAM-S23-001</small>
                                </div>
                            </div>
                        </td>
                        <td>Electronics</td>
                        <td>Piece</td>
                        <td>45</td>
                        <td>₦850,000</td>
                        <td>₦780,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Item Row 2 -->
                    <tr>
                        <td>2</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="2">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face2.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">MacBook Pro 14"</h6>
                                    <small class="text-muted">SKU: APL-MBP-014</small>
                                </div>
                            </div>
                        </td>
                        <td>Electronics</td>
                        <td>Piece</td>
                        <td>8</td>
                        <td>₦1,450,000</td>
                        <td>₦1,300,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Item Row 3 -->
                    <tr>
                        <td>3</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="3">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face3.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">Sony WH-1000XM5 Headphones</h6>
                                    <small class="text-muted">SKU: SNY-WH-005</small>
                                </div>
                            </div>
                        </td>
                        <td>Accessories</td>
                        <td>Piece</td>
                        <td>0</td>
                        <td>₦180,000</td>
                        <td>₦150,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Item Row 4 -->
                    <tr>
                        <td>4</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="4">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face4.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">Herman Miller Aeron Chair</h6>
                                    <small class="text-muted">SKU: HM-AER-001</small>
                                </div>
                            </div>
                        </td>
                        <td>Furniture</td>
                        <td>Piece</td>
                        <td>12</td>
                        <td>₦450,000</td>
                        <td>₦380,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>

                    <!-- Item Row 5 -->
                    <tr>
                        <td>5</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="5">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face5.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">KitchenAid Stand Mixer</h6>
                                    <small class="text-muted">SKU: KA-SM-001</small>
                                </div>
                            </div>
                        </td>
                        <td>Kitchen</td>
                        <td>Piece</td>
                        <td>25</td>
                        <td>₦280,000</td>
                        <td>₦220,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Item Row 6 -->
                    <tr>
                        <td>6</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="6">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face1.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">Nike Air Max 270</h6>
                                    <small class="text-muted">SKU: NK-AM-270</small>
                                </div>
                            </div>
                        </td>
                        <td>Clothing</td>
                        <td>Pair</td>
                        <td>65</td>
                        <td>₦85,000</td>
                        <td>₦65,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    
                   
                 
                   
                    
                   
                    <!-- Item Row 13 -->
                    <tr>
                        <td>13</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="13">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="../assets/images/faces/face3.jpg" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">Bose QuietComfort Earbuds</h6>
                                    <small class="text-muted">SKU: BS-QC-EAR</small>
                                </div>
                            </div>
                        </td>
                        <td>Accessories</td>
                        <td>Piece</td>
                        <td>38</td>
                        <td>₦145,000</td>
                        <td>₦120,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                    
                    <!-- Item Row 20 -->
                    <tr>
                        <td>20</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input item-checkbox" type="checkbox" value="20">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('manager_asset/images/faces/face5.jpg') }}" alt="Product" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">Breville Barista Express</h6>
                                    <small class="text-muted">SKU: BV-BE-EXP</small>
                                </div>
                            </div>
                        </td>
                        <td>Kitchen</td>
                        <td>Piece</td>
                        <td>12</td>
                        <td>₦380,000</td>
                        <td>₦320,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary me-1">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        
        <!-- Pagination and Stats -->
        <div class="row mt-3">
            <div class="col-md-6">
                <span class="text-muted">Showing 1 to 5 of 124 entries</span>
            </div>
            <div class="col-md-6">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!-- End of All Items content -->
</div>
				</div>
				<!-- main-panel ends -->
			</div>
		
@endsection