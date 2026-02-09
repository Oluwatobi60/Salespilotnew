@extends('manager.layouts.layout')
@section('manager_page_title')
Manage Branches
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/staff_style.css') }}">
<link rel="stylesheet" href="{{ asset('manager_asset/css/staffs_style.css') }}">

    <div class="content-wrapper d-flex" id="staffContentWrapper">
             <!-- Branch Management Content -->
            <div class="row">
              <div class="col-sm-12">
                <div class="home-tab">
                  <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Branch Management</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between align-items-center" role="alert">
                    <div>
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                    @if(session('upgrade_url'))
                        <a href="{{ session('upgrade_url') }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-arrow-up-circle me-1"></i> Upgrade Plan
                        </a>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Validation Error!</strong> Please correct the following:
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mt-4">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <div>
                        <h4 class="card-title mb-2">
                          <i class="bi bi-building me-2"></i>Manage Branches
                        </h4>
                        <p class="card-description mb-0">
                          Total: <strong>{{ $branches->total() }}</strong> branches
                          @if($activeSubscription && $activeSubscription->subscriptionPlan->max_branches !== null)
                            ({{ $branches->total() }}/{{ $activeSubscription->subscriptionPlan->max_branches }} used)
                          @endif
                        </p>
                      </div>
                      @if($isBusinessCreator)
                        <button type="button" class="btn btn-primary" style="min-width: 150px;" id="openAddBranchBtn"><strong>+ Add Branch</strong></button>
                      @else
                        <div class="alert alert-info mb-0" style="padding: 0.5rem 1rem;">
                          <i class="bi bi-info-circle me-1"></i>Only the business owner can manage branches
                        </div>
                      @endif
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                          </span>
                          <input type="text" class="form-control border-start-0" placeholder="Search by branch name, state, or LGA..." id="searchInput">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                          <option value="">All Status</option>
                          <option value="1">Active</option>
                          <option value="0">Inactive</option>
                        </select>
                      </div>
                    </div>

                    <div class="table-responsive mt-1">
                      <table class="table select-table" id="branchesTable">
                        <thead>
                          <tr>
                            <th>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" aria-checked="false" id="check-all">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </th>
                            <th>Branch Name</th>
                            <th>Location</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th>Date Added</th>
                            <th class="text-center">Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse ($branches as $branch)
                          <tr>
                            <td>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input branch-checkbox" value="{{ $branch->id }}" aria-checked="false">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                  <i class="bi bi-building text-white"></i>
                                </div>
                                <div>
                                  <h6 class="mb-0">{{ $branch->branch_name }}</h6>
                                  <p class="text-muted mb-0 small">{{ Str::limit($branch->address, 30) }}</p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <div>
                                <p class="mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $branch->state }}</p>
                                <p class="text-muted mb-0 small">{{ $branch->local_govt }}</p>
                              </div>
                            </td>
                            <td>
                              @if($branch->manager)
                                <div>
                                  <p class="mb-0">{{ $branch->manager->first_name }} {{ $branch->manager->surname }}</p>
                                  <p class="text-muted mb-0 small">{{ $branch->manager->email }}</p>
                                </div>
                              @else
                                <span class="badge bg-secondary">Not Assigned</span>
                              @endif
                            </td>
                            <td>
                              <span class="badge {{ $branch->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                {{ $branch->status == 1 ? 'Active' : 'Inactive' }}
                              </span>
                            </td>
                            <td>
                              <p class="mb-0">{{ $branch->created_at->format('M d, Y') }}</p>
                              <p class="text-muted mb-0 small">{{ $branch->created_at->diffForHumans() }}</p>
                            </td>
                            <td>
                              @if($isBusinessCreator)
                              <div class="d-flex justify-content-center align-items-center flex-nowrap mx-auto" style="gap: 3rem;">
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-info text-white edit-branch-btn" data-branch-id="{{ $branch->id }}" title="Edit Branch">
                                  <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Enable/Disable Switch -->
                                <form action="{{ route('branch.toggle_status', $branch->id) }}" method="POST" class="d-flex align-items-center m-0 p-0">
                                  @csrf
                                  @method('PATCH')
                                  <div class="form-check form-switch d-flex align-items-center m-0">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch{{ $branch->id }}" name="status"
                                      onchange="this.form.submit()" {{ $branch->status == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1 mb-0 small" for="statusSwitch{{ $branch->id }}">
                                      {{ $branch->status == 1 ? 'Enabled' : 'Disabled' }}
                                    </label>
                                  </div>
                                </form>

                                <!-- Delete Button -->
                                <form action="{{ route('branch.delete', $branch->id) }}" method="POST" class="delete-branch-form d-flex align-items-center m-0 p-0">
                                  @csrf
                                  @method('DELETE')
                                  <button type="button" class="btn btn-sm btn-danger delete-branch-btn"
                                          data-branch-name="{{ $branch->branch_name }}">
                                      <i class="bi bi-trash"></i>
                                  </button>
                                </form>
                              </div>
                              @else
                                <div class="text-center">
                                  <span class="badge bg-secondary"><i class="bi bi-eye me-1"></i>View Only</span>
                                </div>
                              @endif
                            </td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="7" class="text-center py-5">
                              <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-building" style="font-size: 4rem; color: #ccc;"></i>
                                <h5 class="mt-3 text-muted">No Branches Found</h5>
                                <p class="text-muted">Click "Add Branch" to create your first branch</p>
                              </div>
                            </td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>{{ $branches->firstItem() ?? 0 }}-{{ $branches->lastItem() ?? 0 }}</strong> of <strong>{{ $branches->total() }}</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        {{ $branches->links('pagination::bootstrap-5') }}
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            </div>
    </div>

<!-- Add Branch Side Panel -->
<div class="side-panel-overlay" id="sidePanelOverlay"></div>
<div class="side-panel" id="addBranchPanel">
  <div class="side-panel-content">
    <div class="side-panel-header">
      <h5 class="side-panel-title" id="branchPanelTitle">
        <i class="bi bi-building me-2"></i>Add New Branch
      </h5>
      <button type="button" class="btn-close" id="closeSidePanel" aria-label="Close"></button>
    </div>
    <div class="side-panel-body">
      <form id="branchForm" action="{{ route('branch.create') }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" name="branch_id" id="branchId">

        <!-- Branch Information Section -->
        <div class="form-section mb-4">
          <h6 class="section-title mb-3">
            <i class="bi bi-info-circle me-2"></i>Branch Information
          </h6>

          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="branch_name" class="form-label">Branch Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="Enter branch name" required value="{{ old('branch_name') }}">
              @error('branch_name')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="state" class="form-label">State <span class="text-danger">*</span></label>
              <select id="stateSelect" name="state" required class="form-select">
                <option value="">Select State</option>
                <option value="Abia">Abia</option>
                <option value="Adamawa">Adamawa</option>
                <option value="Akwa Ibom">Akwa Ibom</option>
                <option value="Anambra">Anambra</option>
                <option value="Bauchi">Bauchi</option>
                <option value="Bayelsa">Bayelsa</option>
                <option value="Benue">Benue</option>
                <option value="Borno">Borno</option>
                <option value="Cross River">Cross River</option>
                <option value="Delta">Delta</option>
                <option value="Ebonyi">Ebonyi</option>
                <option value="Edo">Edo</option>
                <option value="Ekiti">Ekiti</option>
                <option value="Enugu">Enugu</option>
                <option value="FCT">FCT - Abuja</option>
                <option value="Gombe">Gombe</option>
                <option value="Imo">Imo</option>
                <option value="Jigawa">Jigawa</option>
                <option value="Kaduna">Kaduna</option>
                <option value="Kano">Kano</option>
                <option value="Katsina">Katsina</option>
                <option value="Kebbi">Kebbi</option>
                <option value="Kogi">Kogi</option>
                <option value="Kwara">Kwara</option>
                <option value="Lagos">Lagos</option>
                <option value="Nasarawa">Nasarawa</option>
                <option value="Niger">Niger</option>
                <option value="Ogun">Ogun</option>
                <option value="Ondo">Ondo</option>
                <option value="Osun">Osun</option>
                <option value="Oyo">Oyo</option>
                <option value="Plateau">Plateau</option>
                <option value="Rivers">Rivers</option>
                <option value="Sokoto">Sokoto</option>
                <option value="Taraba">Taraba</option>
                <option value="Yobe">Yobe</option>
                <option value="Zamfara">Zamfara</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label for="local_govt" class="form-label">Local Government Area <span class="text-danger">*</span></label>
              <select id="lgaSelect" name="local_govt" required class="form-select">
                <option value="">Select Local Government Area</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter branch address (optional)">{{ old('address') }}</textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="manager_id" class="form-label">Assign Manager</label>
              <select class="form-select" id="manager_id" name="manager_id">
                <option value="">No Manager</option>
                @foreach($managers as $manager)
                  <option value="{{ $manager->id }}">
                    {{ $manager->first_name }} {{ $manager->surname }} ({{ $manager->email }})
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
              <select class="form-select" id="status" name="status" required>
                <option value="1" selected>Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="side-panel-footer">
      <button type="button" class="btn btn-secondary" id="cancelAddBranch">
        <i class="bi bi-x-circle me-1"></i>Cancel
      </button>
      <button type="submit" form="branchForm" id="submitBranchBtn" class="btn btn-primary">
        <i class="bi bi-building me-1"></i><span id="submitBtnText">Add Branch</span>
      </button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('manager_asset/js/manager.js') }}"></script>
<script src="{{ asset('welcome_asset/js/register_lg.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBranchBtn = document.getElementById('openAddBranchBtn');
    const addBranchPanel = document.getElementById('addBranchPanel');
    const sidePanelOverlay = document.getElementById('sidePanelOverlay');
    const closeSidePanel = document.getElementById('closeSidePanel');
    const cancelAddBranch = document.getElementById('cancelAddBranch');
    const branchForm = document.getElementById('branchForm');
    const editBtns = document.querySelectorAll('.edit-branch-btn');
    const deleteBtns = document.querySelectorAll('.delete-branch-btn');

    // Open add branch panel
    if (addBranchBtn) {
        addBranchBtn.addEventListener('click', function() {
            branchForm.reset();
            branchForm.action = "{{ route('branch.create') }}";
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('branchPanelTitle').innerHTML = '<i class="bi bi-building me-2"></i>Add New Branch';
            document.getElementById('submitBtnText').textContent = 'Add Branch';

            addBranchPanel.classList.add('active');
            sidePanelOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    // Close panel
    function closePanel() {
        addBranchPanel.classList.remove('active');
        sidePanelOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (closeSidePanel) closeSidePanel.addEventListener('click', closePanel);
    if (cancelAddBranch) cancelAddBranch.addEventListener('click', closePanel);
    if (sidePanelOverlay) sidePanelOverlay.addEventListener('click', closePanel);

    // Edit branch
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const branchId = this.getAttribute('data-branch-id');

            fetch(`/manager/branches/${branchId}/edit`)
                .then(response => response.json())
                .then(data => {
                    const branch = data.branch;

                    branchForm.action = `/manager/branches/${branchId}`;
                    document.getElementById('formMethod').value = 'PUT';
                    document.getElementById('branchId').value = branchId;
                    document.getElementById('branchPanelTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Edit Branch';
                    document.getElementById('submitBtnText').textContent = 'Update Branch';

                    document.getElementById('branch_name').value = branch.branch_name;
                    document.getElementById('address').value = branch.address || '';
                    document.getElementById('stateSelect').value = branch.state;

                    const stateEvent = new Event('change');
                    document.getElementById('stateSelect').dispatchEvent(stateEvent);

                    setTimeout(() => {
                        document.getElementById('lgaSelect').value = branch.local_govt;
                    }, 100);

                    document.getElementById('manager_id').value = branch.manager_id || '';
                    document.getElementById('status').value = branch.status;

                    addBranchPanel.classList.add('active');
                    sidePanelOverlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to load branch data', 'error');
                });
        });
    });

    // Delete branch
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const branchName = this.getAttribute('data-branch-name');
            const form = this.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: `Delete "${branchName}"? This cannot be undone!`,
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

    // Search & filter
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('branchesTable');

    if (searchInput) searchInput.addEventListener('keyup', filterTable);
    if (statusFilter) statusFilter.addEventListener('change', filterTable);

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            if (row.cells.length < 2) continue;

            const branchName = row.cells[1].textContent.toLowerCase();
            const location = row.cells[2].textContent.toLowerCase();
            const status = row.cells[4].textContent.trim();

            const matchesSearch = branchName.includes(searchTerm) || location.includes(searchTerm);
            const matchesStatus = !statusValue ||
                (statusValue === '1' && status === 'Active') ||
                (statusValue === '0' && status === 'Inactive');

            row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        }
    }

    // Check all
    const checkAll = document.getElementById('check-all');
    const checkboxes = document.querySelectorAll('.branch-checkbox');

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});
</script>

@endsection
