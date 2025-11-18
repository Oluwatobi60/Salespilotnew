@extends('manager.layouts.layout')
@section('manager_page_title')
Add Staff Member
@endsection
@section('manager_layout_content')

    <div class="content-wrapper d-flex" id="staffContentWrapper">
             <!-- Staff Management Content -->
            <div class="row">
              <div class="col-sm-12">
                <div class="home-tab">
                  <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Staff Management</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>


            <div class="row mt-4">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                      <div>
                        <h4 class="card-title mb-2">
                          <i class="bi bi-person-workspace me-2"></i>Staff Members
                        </h4>
                        <p class="card-description mb-0">Manage your staff members and their roles</p>
                      </div>
                      <button type="button" class="btn btn-primary" style="min-width: 150px;" data-bs-toggle="modal" data-bs-target="#addStaffModal"><strong>+ Add Staff</strong></button>
                </div>

 <!-- Search and Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <div class="input-group">
                          <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                          </span>
                          <input type="text" class="form-control border-start-0" placeholder="Search by name, email, or ID..." id="searchInput">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <select class="form-select" id="roleFilter">
                          <option value="">All Roles</option>
                          <option value="Manager">Manager</option>
                          <option value="Sales Staff">Sales Staff</option>
                        </select>
                      </div>
                    </div>

                     <div class="table-responsive mt-1">
                      <table class="table select-table" id="staffsTable">
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
                            <th>Staff Member</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!-- Sample Data -->
                           <tr>
                            <td>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" aria-checked="false">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="{{ asset('manager_asset/images/faces/face1.jpg') }}" alt="Profile" class="me-2" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                  <h6 class="mb-0">Alice Johnson</h6>
                                  <p class="text-muted mb-0">@ajohnson</p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="badge bg-primary">Manager</span>
                            </td>
                            <td>
                              <div>
                                <p class="mb-1">alice.johnson@salespilot.com</p>
                                <p class="text-muted mb-0">+234 800 123 4567</p>
                              </div>
                            </td>
                            <td>
                              <p class="mb-0">Oct 14, 2025</p>
                            </td>
                            <td>
                                 <a class="btn btn-sm btn-secondary text-white me-1 staff-settings-btn" title="Settings"
                                  href="staff_settings.php?name=Alice%20Johnson&username=@ajohnson&role=Manager&email=alice.johnson@salespilot.com&phone=%2B234%20800%20123%204567">
                                  <i class="mdi mdi-cog"></i>
                                </a>
                              <button class="btn btn-sm btn-info text-white me-1" title="View Details">
                                <i class="bi bi-eye"></i>
                              </button>
                              
                              <button class="btn btn-sm btn-danger text-white" title="Delete">
                                <i class="bi bi-trash"></i>
                              </button>
                            </td>
                          </tr>
                          
                          <tr>
                            <td>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" aria-checked="false">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="{{ asset('manager_asset/images/faces/face2.jpg') }}" alt="Profile" class="me-2" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                  <h6 class="mb-0">Bob Smith</h6>
                                  <p class="text-muted mb-0">@bsmith</p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="badge bg-secondary">Sales Staff</span>
                            </td>
                            <td>
                              <div>
                                <p class="mb-1">bob.smith@salespilot.com</p>
                                <p class="text-muted mb-0">+234 800 234 5678</p>
                              </div>
                            </td>
                            <td>
                              <p class="mb-0">Oct 18, 2025</p>
                            </td>
                            <td>
                              
                                <a class="btn btn-sm btn-secondary text-white me-1 staff-settings-btn" title="Settings"
                                  href="staff_settings.php?name=Bob%20Smith&username=@bsmith&role=Sales%20Staff&email=bob.smith@salespilot.com&phone=%2B234%20800%20234%205678">
                                  <i class="mdi mdi-cog"></i>
                                </a>
                              <button class="btn btn-sm btn-info text-white me-1" title="View Details">
                                <i class="bi bi-eye"></i>
                              </button>
                              
                              <button class="btn btn-sm btn-danger text-white" title="Delete">
                                <i class="bi bi-trash"></i>
                              </button>
                            </td>
                          </tr>

                           <tr>
                            <td>
                              <div class="form-check form-check-flat mt-0">
                                <label class="form-check-label">
                                  <input type="checkbox" class="form-check-input" aria-checked="false">
                                  <i class="input-helper"></i>
                                </label>
                              </div>
                            </td>
                            <td>
                              <div class="d-flex align-items-center">
                                <img src="{{ asset('manager_asset/images/faces/face3.jpg') }}" alt="Profile" class="me-2" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <div>
                                  <h6 class="mb-0">Carol Williams</h6>
                                  <p class="text-muted mb-0">@cwilliams</p>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="badge bg-secondary">Sales Staff</span>
                            </td>
                            <td>
                              <div>
                                <p class="mb-1">carol.williams@salespilot.com</p>
                                <p class="text-muted mb-0">+234 800 345 6789</p>
                              </div>
                            </td>
                            <td>
                              <p class="mb-0">Oct 19, 2025</p>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-secondary text-white me-1 staff-settings-btn" title="Settings"
                                  href="staff_settings.php?name=Carol%20Williams&username=@cwilliams&role=Sales%20Staff&email=carol.williams@salespilot.com&phone=%2B234%20800%20345%206789">
                                  <i class="mdi mdi-cog"></i>
                                </a>
                              <button class="btn btn-sm btn-info text-white me-1" title="View Details">
                                <i class="bi bi-eye"></i>
                              </button>
                             
                              <button class="btn btn-sm btn-danger text-white" title="Delete">
                                <i class="bi bi-trash"></i>
                              </button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                      <!-- Pagination and Info -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <div class="text-muted small">
                        Showing <strong>1-3</strong> of <strong>3</strong> entries
                      </div>
                      <nav aria-label="Table pagination">
                        <ul class="pagination pagination-sm mb-0">
                          <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                          </li>
                          <li class="page-item active"><a class="page-link" href="#">1</a></li>
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
@endsection
   <!-- Modal for Adding Staff -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addStaffModalLabel">
              <i class="bi bi-person-plus me-2"></i>Add New Staff Member
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <div class="modal-body">
            <form id="addStaffForm" action="" method="POST" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter full name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                  <select class="form-select" id="role" name="role" required>
                    <option value="">Select Role</option>
                  
                  </select>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-12 mb-3">
                  <label for="passport_photo" class="form-label">Profile Photo <span class="text-muted">(Optional)</span></label>
                  <input type="file" class="form-control" id="passport_photo" name="passport_photo" accept="image/*">
                  <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                  <div id="photoPreview" class="mt-2" style="display: none;">
                    <img id="previewImage" src="" alt="Preview">
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removePhoto">
                      <i class="bi bi-x-circle"></i> Remove
                    </button>
                  </div>
                </div>
                
              </div>
            </form>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addStaffForm" id="addStaffBtn" class="btn btn-primary">
              <i class="bi bi-person-plus me-1"></i>Add Staff Member
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- End Modal -->

 
