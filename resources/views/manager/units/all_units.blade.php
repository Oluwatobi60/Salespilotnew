@extends('manager.layouts.layout')
@section('manager_page_title')
All Units
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/category_style.css') }}">
<link rel="stylesheet" href="{{ asset('welcome_asset/css/loading-button.css') }}">

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

   <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <!-- Header Section -->
                    <div class="d-sm-flex justify-content-between align-items-start mb-2">
                      <div>
                        <h4 class="card-title mb-0">Units of Measurement</h4>
                        <p class="card-description">Manage your units of measurement below.</p>
                      </div>
                    </div>

                    <!-- Search and Action Section -->
                    <div class="row mb-3 align-items-center g-2">
                      <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                          </span>
                          <input type="text" class="form-control" placeholder="Search units..." id="searchUnits">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="d-flex justify-content-end gap-2">
                          <button type="button" class="btn btn-primary" id="addUnitBtn">
                            <i class="bi bi-plus"></i> Add Unit
                          </button>
                          <button class="btn btn-outline-success" id="exportUnits" title="Export Units">
                            <i class="bi bi-download"></i> Export
                          </button>
                        </div>
                      </div>
                    </div>



                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="unitsTable">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Unit Name</th>
                        <th>Abbreviation</th>
                        <th>Type</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $index => $unit)
                        <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>{{ $unit->abbreviation }}</td>
                        <td>
                          @if($unit->is_custom)
                            <span class="badge bg-info">Custom</span>
                          @else
                            <span class="badge bg-secondary">Standard</span>
                          @endif
                        </td>
                        <td style="white-space: nowrap;">
                          <div class="d-flex gap-1 justify-content-start align-items-center">
                            <button class="btn btn-sm btn-outline-primary edit-btn"
                                    style=""
                                    data-id="{{ $unit->id }}"
                                    data-name="{{ $unit->name }}"
                                    data-abbreviation="{{ $unit->abbreviation }}"
                                    title="Edit Unit">
                              <i class="bi bi-pencil" style="font-size: 0.875rem;"></i>
                            </button>

                            @if($unit->is_custom)
                            <form action="{{ route('unit.delete', ['id' => $unit->id]) }}" method="POST" style="display: inline; margin: 0;" class="delete-unit-form">
                              @csrf
                              @method('DELETE')
                              <button type="button" class="btn btn-sm btn-outline-danger delete-unit-btn"
                                      style="padding: 0.25rem 0.5rem; line-height: 1; min-width: 32px; height: 28px;"
                                      data-unit-name="{{ $unit->name }}">
                                <i class="bi bi-trash" style="font-size: 0.875rem;"></i>
                              </button>
                            </form>
                            @endif
                          </div>
                        </td>
                      </tr>
                        @endforeach

                    </tbody>
                  </table>
                </div>

                   <!-- Empty State -->
                    @if($units->isEmpty())
                      <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="mt-3 text-muted">No units found. Click "Add Unit" to create one.</p>
                      </div>
                    @endif

                  </div>
                </div>
              </div>
  </div>


<!-- Side Panel Overlay -->
<div class="side-panel-overlay" id="sidePanelOverlay"></div>

<!-- Add Unit Side Panel -->
<div class="side-panel" id="addUnitPanel">
  <div class="panel-header">
    <h5>
      <i class="bi bi-plus-circle"></i> Add New Unit
    </h5>
    <button type="button" class="close-btn" id="closeAddPanel">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="panel-body">
    <form id="addUnitForm" action="{{ route('unit.create') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="unitName" class="form-label">Unit Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="unitName" name="name" placeholder="Enter unit name (e.g., Kilogram, Piece)" required autocomplete="off">
        <small class="form-text text-muted">Must be 2-50 characters</small>
      </div>

      <div class="mb-3">
        <label for="unitAbbreviation" class="form-label">Abbreviation <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="unitAbbreviation" name="abbreviation" placeholder="Enter abbreviation (e.g., kg, pcs)" required autocomplete="off">
        <small class="form-text text-muted">Must be 1-10 characters</small>
      </div>

      <div class="panel-footer">
        <button type="button" class="btn btn-secondary" id="cancelAddBtn">
          <i class="bi bi-x"></i> Cancel
        </button>
        <button class="btn btn-primary btn-loading" type="submit" data-loading-text="Saving...">
          <span class="btn-text">Save Unit</span>
          <span class="btn-spinner"></span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Unit Side Panel -->
<div class="side-panel" id="editUnitPanel">
  <div class="panel-header">
    <h5>
      <i class="bi bi-pencil"></i> Edit Unit
    </h5>
    <button type="button" class="close-btn" id="closeEditPanel">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="panel-body">
    <form id="editUnitForm" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" id="editUnitId" name="unit_id">

      <div class="mb-3">
        <label for="editUnitName" class="form-label">Unit Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="editUnitName" name="name" required autocomplete="off">
        <small class="form-text text-muted">Must be 2-50 characters</small>
      </div>

      <div class="mb-3">
        <label for="editUnitAbbreviation" class="form-label">Abbreviation <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="editUnitAbbreviation" name="abbreviation" required autocomplete="off">
        <small class="form-text text-muted">Must be 1-10 characters</small>
      </div>

      <div class="panel-footer">
        <button type="button" class="btn btn-secondary" id="cancelEditBtn">
          <i class="bi bi-x"></i> Cancel
        </button>
        <button class="btn btn-primary btn-loading" type="submit" id="editUnitSubmitBtn" data-loading-text="Updating...">
          <span class="btn-text">Update Unit</span>
          <span class="btn-spinner"></span>
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('welcome_asset/js/loading-button.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    var addUnitPanel = document.getElementById('addUnitPanel');
    var editUnitPanel = document.getElementById('editUnitPanel');
    var addUnitBtn = document.getElementById('addUnitBtn');
    var closeAddPanel = document.getElementById('closeAddPanel');
    var closeEditPanel = document.getElementById('closeEditPanel');
    var cancelAddBtn = document.getElementById('cancelAddBtn');
    var cancelEditBtn = document.getElementById('cancelEditBtn');
    var editButtons = document.querySelectorAll('.edit-btn');
    var deleteButtons = document.querySelectorAll('.delete-unit-btn');
    var searchInput = document.getElementById('searchUnits');
    var exportButton = document.getElementById('exportUnits');
    var sidePanelOverlay = document.getElementById('sidePanelOverlay');

    // Open Add Panel
    if(addUnitBtn) {
        addUnitBtn.addEventListener('click', function() {
            addUnitPanel.classList.add('active');
            if(sidePanelOverlay) {
                sidePanelOverlay.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        });
    }

    // Close Add Panel function
    function closeAddUnitPanel() {
        if(addUnitPanel) {
            addUnitPanel.classList.remove('active');
        }
        if(sidePanelOverlay) {
            sidePanelOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    // Close Edit Panel function
    function closeEditUnitPanel() {
        if(editUnitPanel) {
            editUnitPanel.classList.remove('active');
        }
        if(sidePanelOverlay) {
            sidePanelOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }

    // Close Add Panel
    if(closeAddPanel) {
        closeAddPanel.addEventListener('click', closeAddUnitPanel);
    }

    if(cancelAddBtn) {
        cancelAddBtn.addEventListener('click', closeAddUnitPanel);
    }

    // Close Edit Panel
    if(closeEditPanel) {
        closeEditPanel.addEventListener('click', closeEditUnitPanel);
    }

    if(cancelEditBtn) {
        cancelEditBtn.addEventListener('click', closeEditUnitPanel);
    }

    // Close panels on overlay click
    if(sidePanelOverlay) {
        sidePanelOverlay.addEventListener('click', function() {
            closeAddUnitPanel();
            closeEditUnitPanel();
        });
    }

    // Close panels on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (addUnitPanel && addUnitPanel.classList.contains('active')) {
                closeAddUnitPanel();
            }
            if (editUnitPanel && editUnitPanel.classList.contains('active')) {
                closeEditUnitPanel();
            }
        }
    });

    // Edit Unit
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var unitId = this.getAttribute('data-id');
            var unitName = this.getAttribute('data-name');
            var unitAbbreviation = this.getAttribute('data-abbreviation');

            document.getElementById('editUnitId').value = unitId;
            document.getElementById('editUnitName').value = unitName;
            document.getElementById('editUnitAbbreviation').value = unitAbbreviation;
            document.getElementById('editUnitForm').action = '/manager/unit/update/' + unitId;

            editUnitPanel.classList.add('active');
            if(sidePanelOverlay) {
                sidePanelOverlay.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        });
    });

    // Delete Unit
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var unitName = this.getAttribute('data-unit-name');
            var form = this.closest('.delete-unit-form');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete '" + unitName + "'?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Search Units
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var rows = document.querySelectorAll('#unitsTable tbody tr');

            rows.forEach(function(row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // Export Units
    if(exportButton) {
        exportButton.addEventListener('click', function() {
            var table = document.getElementById('unitsTable');
            var rows = table.querySelectorAll('tr');
            var csv = [];

            rows.forEach(function(row) {
                var cols = row.querySelectorAll('td, th');
                var csvRow = [];
                for(var i = 0; i < cols.length - 1; i++) { // Exclude last column (Actions)
                    csvRow.push(cols[i].textContent.trim());
                }
                csv.push(csvRow.join(','));
            });

            var csvFile = new Blob([csv.join('\n')], {type: 'text/csv'});
            var downloadLink = document.createElement('a');
            downloadLink.download = 'units.csv';
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        });
    }

    // Initialize loading button functionality for forms
    const addForm = document.getElementById('addUnitForm');
    const editForm = document.getElementById('editUnitForm');

    if (addForm && typeof LoadingButton !== 'undefined') {
        const addBtn = addForm.querySelector('.btn-loading');
        if (addBtn && !addBtn.dataset.loadingInitialized) {
            addBtn.dataset.loadingInitialized = 'true';
            addForm.addEventListener('submit', function(e) {
                if (addForm.checkValidity()) {
                    LoadingButton.start(addBtn);
                }
            });
        }
    }

    if (editForm && typeof LoadingButton !== 'undefined') {
        const editBtn = editForm.querySelector('.btn-loading');
        if (editBtn && !editBtn.dataset.loadingInitialized) {
            editBtn.dataset.loadingInitialized = 'true';
            editForm.addEventListener('submit', function(e) {
                if (editForm.checkValidity()) {
                    LoadingButton.start(editBtn);
                }
            });
        }
    }
});
</script>

@endsection
