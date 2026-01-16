@extends('manager.layouts.layout')
@section('manager_page_title')
Manager Profile Settings
@endsection
@section('manager_layout_content')
      <div class="content-wrapper">
            <!-- profile content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-sm-flex justify-content-between align-items-start">

                    </div>

                    <div class="d-sm-flex justify-content-between align-items-start mb-3">
                      <div>
                        <h4 class="card-title mb-0"style="color:#007bff;font-weight:600;">Personal Profile</h4>
                        <p class="card-description">Update your personal information</p>
                      </div>
                    </div>

                    <form class="forms-sample">
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <label for="firstName" class="form-label">First Name</label>
                          <input type="text" class="form-control" id="firstName" placeholder="Enter first name">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="lastName" class="form-label">Last Name</label>
                          <input type="text" class="form-control" id="lastName" placeholder="Enter last name">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="otherName" class="form-label">Other Name</label>
                          <input type="text" class="form-control" id="otherName" placeholder="Enter other name(s)">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <label for="gender" class="form-label">Gender</label>
                          <select class="form-select" id="gender">
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                          </select>
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="phoneNumber" class="form-label">Phone Number</label>
                          <input type="tel" class="form-control" id="phoneNumber" placeholder="Enter phone number">
                        </div>
                        <div class="col-md-4 mb-3">
                          <label for="dateOfBirth" class="form-label">Date of Birth</label>
                          <input type="date" class="form-control" id="dateOfBirth">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <label for="profilePhoto" class="form-label">Profile Photo</label>
                          <input class="form-control" type="file" id="profilePhoto" accept="image/*">
                          <small class="text-muted d-block mt-1">Max size 2MB. JPG or PNG.</small>
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                          <div class="d-flex align-items-center gap-3" style="width:100%;">
                            <div>
                              <span class="d-block mb-1">Preview</span>
                              <img id="profilePhotoPreview" src="../assets/images/faces/face8.jpg" alt="Profile preview" style="width:80px; height:80px; border-radius:8px; object-fit:cover; border:1px solid #ddd;">
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                        <button type="reset" class="btn btn-light">Cancel</button>
                      </div>
                    </form><br>




                    </div>
           <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
							<span class="text-muted text-center text-sm-left d-block d-sm-inline-block">© 2025 SalesPilot. All rights reserved.</span>
            </div>
          </footer>
        </div>
        <!-- content-wrapper ends -->

        <!-- partial -->
      </div>
      <!-- main-panel ends -->


      </div>
    </div>

@endsection
