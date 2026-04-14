@extends('manager.layouts.layout')
@section('manager_page_title')
System Preferences
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/system_preferences.css') }}">,

<div class="container py-5"style="padding-left: 50px; padding-right:0px; max-width:100%;">
			<h2 class="settings-heading">System Preferences</h2>
      <div class="row g-0 settings-row">
        <div class="col-md-3 d-flex flex-column align-items-stretch tab-section-fixed">
          <div class="nav flex-column nav-pills nav-tabs-line" id="settingsTabs" role="tablist" aria-orientation="vertical">
            <button class="nav-link active" id="business-tab" data-bs-toggle="pill" data-bs-target="#business" type="button" role="tab" aria-controls="business" aria-selected="true">Business Information</button>
            <button class="nav-link" id="subscriptions-tab" data-bs-toggle="pill" data-bs-target="#subscriptions" type="button" role="tab" aria-controls="subscriptions" aria-selected="false">Subscriptions</button>
            @php
              $canViewBranches = true;
              if($currentSubscription && $currentSubscription->subscriptionPlan) {
                $planName = strtolower(trim($currentSubscription->subscriptionPlan->name));
                if($planName === 'basic') {
                  $canViewBranches = false;
                }
              }
            @endphp
            @if($canViewBranches)
              <button class="nav-link" id="branches-tab" data-bs-toggle="pill" data-bs-target="#branches" type="button" role="tab" aria-controls="branches" aria-selected="false">Branches</button>
            @endif
            <button class="nav-link" id="staffs-tab" data-bs-toggle="pill" data-bs-target="#staffs" type="button" role="tab" aria-controls="staffs" aria-selected="false">Staffs</button>
            <button class="nav-link" id="receipt-tab" data-bs-toggle="pill" data-bs-target="#receipt" type="button" role="tab" aria-controls="receipt" aria-selected="false">Receipt Settings</button>
           {{--   <button class="nav-link" id="measurement-tab" data-bs-toggle="pill" data-bs-target="#measurement" type="button" role="tab" aria-controls="measurement" aria-selected="false">Measurement Units</button>  --}}
          </div>
        </div>


        <div class="col-md-9 d-flex flex-column justify-content-start align-items-stretch" style="padding-left:15px;">
          <div class="tab-content w-100 business-info-section" id="settingsTabsContent" style="min-width:0;flex:1 1 0;display:block;">
      <!-- Personal & Business Information -->
          <div class="tab-pane fade show active" id="business" role="tabpanel" aria-labelledby="business-tab">

            <div class="card mb-4 p-3">
                  <h5 class="mb-3" style="color:#007bff;font-weight:600;">Business Information</h5>
                <div class="row">

                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group mb-3">
                          <label class="form-label">Business Name <span class="text-danger">*</span></label>
                          <input type="text" class="form-control" placeholder="Enter business name" value="SalesPilot">
                        </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group mb-3 pe-md-3">
                            <label class="form-label">Business Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" placeholder="Enter business email" value="info@salespilot.com">
                          </div>
                      </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group mb-3 ps-md-3">
                            <label class="form-label">Business Phone</label>
                            <input type="tel" class="form-control" placeholder="Enter phone number" value="+1 234 567 8900">
                          </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group mb-3">
                          <label class="form-label">Business Address</label>
                          <textarea class="form-control" rows="3" placeholder="Enter business address">123 Business Street, City, State 12345</textarea>
                        </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group mb-3">
                            <label class="form-label">Business Registration Number (CAC)</label>
                            <input type="text" class="form-control" placeholder="Enter CAC number">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group mb-3">
                            <label class="form-label">Tax Identification Number (TIN)</label>
                            <input type="text" class="form-control" placeholder="Enter TIN number">
                          </div>
                        </div>
                    </div>


                </div>
            </div>

              <div class="mt-3">
                <button type="button" class="btn btn-primary me-2">Save Changes</button>
                <button type="button" class="btn btn-light">Reset</button>
              </div>
          </div>

            <!-- Subscriptions -->
            <div class="tab-pane fade" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
              <div class="tab-pane-content card mb-4 p-4">
                <h4 class="mb-4" style="color:#007bff;font-weight:600;">
                  <i class="bi bi-credit-card-2-front me-2"></i>Subscription Plans
                </h4>

                <!-- Current Plan Card -->
                @if($currentSubscription && $currentSubscription->status === 'active')
                <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                  <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div>
                        <h5 class="text-white mb-1">
                          <i class="bi bi-star-fill me-2"></i>Current Plan
                        </h5>
                        <p class="mb-0 opacity-75">Active Subscription</p>
                      </div>
                      <span class="badge bg-white text-primary px-3 py-2">
                        <i class="bi bi-check-circle-fill me-1"></i>Active
                      </span>
                    </div>
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <h3 class="text-white mb-2">
                          {{ $currentSubscription->subscriptionPlan->name ?? 'Premium' }} Plan
                        </h3>
                        <div class="d-flex align-items-center mb-2">
                          <i class="bi bi-calendar-check me-2"></i>
                          <span>Valid until
                            <strong>
                              {{ $currentSubscription->end_date->format('F d, Y') }}
                            </strong>
                          </span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <i class="bi bi-cash-stack me-2"></i>
                          <span>Amount Paid:
                            <strong>₦{{ number_format($currentSubscription->amount_paid, 2) }}</strong>
                            @if($currentSubscription->duration_months == 1)
                              (Monthly)
                            @elseif($currentSubscription->duration_months == 3)
                              (3 Months)
                            @elseif($currentSubscription->duration_months == 6)
                              (6 Months)
                            @elseif($currentSubscription->duration_months == 12)
                              (Annual)
                            @else
                              ({{ $currentSubscription->duration_months }} months)
                            @endif
                          </span>
                        </div>
                        @php
                          $daysRemaining = now()->diffInDays($currentSubscription->end_date, false);
                        @endphp
                        @if($daysRemaining > 0)
                          <div class="mt-2">
                            <small class="opacity-75">
                              <i class="bi bi-hourglass-split me-1"></i>
                              {{ $daysRemaining }} days remaining
                            </small>
                          </div>
                        @elseif($daysRemaining <= 0 && $daysRemaining > -30)
                          <div class="mt-2">
                            <small class="bg-warning text-dark px-2 py-1 rounded">
                              <i class="bi bi-exclamation-triangle me-1"></i>
                              Expired {{ abs($daysRemaining) }} days ago
                            </small>
                          </div>
                        @endif
                      </div>
                      <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('plan_pricing') }}" class="btn btn-light btn-sm px-4">
                          <i class="bi bi-arrow-repeat me-1"></i>Renew Plan
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                @else
                <div class="alert alert-warning d-flex align-items-center mb-4">
                  <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                  <div>
                    <strong class="d-block mb-1">No Active Subscription</strong>
                    <p class="mb-0">You currently don't have an active subscription. Please choose a plan below to continue using all features.</p>
                  </div>
                </div>
                @endif

                <!-- Pricing Plans -->
                <h5 class="mb-3 mt-4" style="color:#333;font-weight:600;">
                  <i class="bi bi-tag-fill me-2"></i>Upgrade or Change Plan
                </h5>
                <p class="text-muted mb-4">Choose the plan that best fits your business needs. Click on any plan to see details.</p>

                <div class="table-responsive">
                  <table class="table table-hover align-middle subscription-table">
                    <thead class="table-light">
                      <tr>
                        <th style="width: 20%; font-weight: 600; color: #495057;">
                          <i class="bi bi-box-seam me-2"></i>Plan
                        </th>
                        <th class="text-center" style="width: 20%; font-weight: 600; color: #495057;">
                          <i class="bi bi-calendar me-1"></i>Monthly
                        </th>
                        <th class="text-center" style="width: 20%; font-weight: 600; color: #495057;">
                          <i class="bi bi-calendar3 me-1"></i>3 Months
                        </th>
                        <th class="text-center" style="width: 20%; font-weight: 600; color: #495057;">
                          <i class="bi bi-calendar2-range me-1"></i>6 Months
                        </th>
                        <th class="text-center" style="width: 20%; font-weight: 600; color: #495057;">
                          <i class="bi bi-calendar-check me-1"></i>Annual
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="plan-details" style="display:none"><td colspan="5"></td></tr>
                      <tr class="plan-row" data-plan="basic">
                        <td>
                          <button class="btn btn-link plan-toggle p-0 text-start text-decoration-none" type="button">
                            <div class="d-flex align-items-center">
                              <div class="plan-icon me-3">
                                <i class="bi bi-layers-fill text-primary" style="font-size: 1.5rem;"></i>
                              </div>
                              <div>
                                <strong class="d-block text-dark" style="font-size: 1.1rem;">Basic</strong>
                                <small class="text-muted">For small businesses</small>
                              </div>
                            </div>
                          </button>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦5,000</strong>
                            <small class="text-muted">/month</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦14,250</strong>
                            <small class="text-success fw-bold">Save 5%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦27,000</strong>
                            <small class="text-success fw-bold">Save 10%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦51,000</strong>
                            <small class="text-success fw-bold">Save 15%</small>
                          </div>
                        </td>
                      </tr>
                      <tr class="plan-details" style="display:none"><td colspan="5"></td></tr>
                      <tr class="plan-row" data-plan="standard">
                        <td>
                          <button class="btn btn-link plan-toggle p-0 text-start text-decoration-none" type="button">
                            <div class="d-flex align-items-center">
                              <div class="plan-icon me-3">
                                <i class="bi bi-stars text-warning" style="font-size: 1.5rem;"></i>
                              </div>
                              <div>
                                <strong class="d-block text-dark" style="font-size: 1.1rem;">Standard</strong>
                                <small class="text-muted">For growing businesses</small>
                              </div>
                            </div>
                          </button>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦10,000</strong>
                            <small class="text-muted">/month</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦28,500</strong>
                            <small class="text-success fw-bold">Save 5%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦54,000</strong>
                            <small class="text-success fw-bold">Save 10%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦102,000</strong>
                            <small class="text-success fw-bold">Save 15%</small>
                          </div>
                        </td>
                      </tr>
                      <tr class="plan-details" style="display:none"><td colspan="5"></td></tr>
                      <tr class="plan-row" data-plan="premium">
                        <td>
                          <button class="btn btn-link plan-toggle p-0 text-start text-decoration-none" type="button">
                            <div class="d-flex align-items-center">
                              <div class="plan-icon me-3">
                                <i class="bi bi-gem text-danger" style="font-size: 1.5rem;"></i>
                              </div>
                              <div>
                                <strong class="d-block text-dark" style="font-size: 1.1rem;">Premium</strong>
                                <small class="text-muted">For large enterprises</small>
                              </div>
                            </div>
                          </button>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦20,000</strong>
                            <small class="text-muted">/month</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦57,000</strong>
                            <small class="text-success fw-bold">Save 10%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦108,000</strong>
                            <small class="text-success fw-bold">Save 15%</small>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="price-cell">
                            <strong class="d-block text-dark" style="font-size: 1.1rem;">₦204,000</strong>
                            <small class="text-success fw-bold">Save 20%</small>
                          </div>
                        </td>
                      </tr>
                      <tr class="plan-details" style="display:none"><td colspan="5"></td></tr>
                    </tbody>
                  </table>
                </div>

                <div class="alert alert-light border mt-4 d-flex align-items-start">
                  <i class="bi bi-info-circle text-primary me-3" style="font-size: 1.5rem;"></i>
                  <div>
                    <strong class="d-block mb-1">Need help choosing?</strong>
                    <p class="mb-0 text-muted">Contact our support team at <a href="mailto:support@salespilot.com">support@salespilot.com</a> or call +234 800 000 0000 for personalized assistance.</p>
                  </div>
                </div>

              </div>

            </div>

  <!-- Branches -->
  @if($canViewBranches)
  <div class="tab-pane fade" id="branches" role="tabpanel" aria-labelledby="branches-tab">
    <div class="card mb-4 p-3">
      <h4 class="mb-3" style="color:#007bff;font-weight:600;">
        <i class="bi bi-building me-2"></i>Branches
      </h4>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="mb-0">Branch Locations</h5>
          <p class="text-muted mb-0 small">Total: <strong>{{ $branches->count() }}</strong> branch{{ $branches->count() != 1 ? 'es' : '' }}</p>
        </div>
        @if($isBusinessCreator)
          <div>
            @if($currentSubscription && $currentSubscription->subscriptionPlan)
              @php
                $planName = strtolower(trim($currentSubscription->subscriptionPlan->name));
                $canAddBranch = false;

                if($planName === 'standard' && $branches->count() < 2) {
                  $canAddBranch = true;
                } elseif($planName === 'premium') {
                  $canAddBranch = true;
                }
              @endphp

              @if($canAddBranch)
                <a href="{{ route('manager.branches') }}" class="btn btn-primary">
                  <i class="bi bi-plus"></i> Add Branch
                </a>
              @else
                <button class="btn btn-secondary" disabled>
                  <i class="bi bi-plus"></i> Add Branch
                </button>
                <small class="d-block text-muted mt-1">Upgrade to add more branches</small>
              @endif
            @else
              <a href="{{ route('plan_pricing') }}" class="btn btn-warning">
                <i class="bi bi-cart-plus"></i> Subscribe to Add Branches
              </a>
            @endif
          </div>
        @else
          <div class="alert alert-info mb-0 py-2 px-3">
            <i class="bi bi-info-circle me-1"></i>Only business owner can add branches
          </div>
        @endif
      </div>

      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Branch Name</th>
              <th>Location</th>
              <th>Manager</th>
              @if($isBusinessCreator)
                <th>Staff</th>
              @endif
              <th>Status</th>
              <th>Date Added</th>
              @if($isBusinessCreator)
                <th class="text-center">Actions</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($branches as $branch)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="me-2" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                      <i class="bi bi-building text-white"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ $branch->branch_name }}</h6>
                      @if(!$isBusinessCreator && $branch->branch_name == Auth::user()->branch_name)
                        <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">YOUR BRANCH</span>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  <div>
                    <p class="mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $branch->state }}</p>
                    <p class="text-muted mb-0 small">{{ $branch->local_govt }}</p>
                    <p class="text-muted mb-0 small">{{ Str::limit($branch->address, 30) }}</p>
                  </div>
                </td>
                <td>
                  @if($branch->manager)
                    <div>
                      <p class="mb-0"><strong>{{ $branch->manager->first_name }} {{ $branch->manager->surname }}</strong></p>
                      <p class="text-muted mb-0 small">{{ $branch->manager->email }}</p>
                    </div>
                  @else
                    <span class="badge bg-secondary">Not Assigned</span>
                  @endif
                </td>
                @if($isBusinessCreator)
                  <td>
                    @if($branch->staffMembers && $branch->staffMembers->count() > 0)
                      <div>
                        @foreach($branch->staffMembers->take(2) as $index => $staffMember)
                          <div class="{{ $index > 0 ? 'mt-1' : '' }}">
                            <p class="mb-0 small"><strong>{{ $staffMember->fullname }}</strong></p>
                          </div>
                        @endforeach
                        @if($branch->staffMembers->count() > 2)
                          <small class="text-muted">+{{ $branch->staffMembers->count() - 2 }} more</small>
                        @endif
                        <small class="text-muted d-block">{{ $branch->staffMembers->count() }} total</small>
                      </div>
                    @else
                      <span class="badge bg-secondary">No Staff</span>
                    @endif
                  </td>
                @endif
                <td>
                  <span class="badge {{ $branch->status == 1 ? 'bg-success' : 'bg-danger' }}">
                    {{ $branch->status == 1 ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <p class="mb-0">{{ $branch->created_at->format('M d, Y') }}</p>
                  <p class="text-muted mb-0 small">{{ $branch->created_at->diffForHumans() }}</p>
                </td>
                @if($isBusinessCreator)
                  <td class="text-center">
                    <a href="{{ route('manager.branches') }}" class="btn btn-sm btn-outline-primary" title="Manage in Branches">
                      <i class="bi bi-gear"></i>
                    </a>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ $isBusinessCreator ? '7' : '5' }}" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-building" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0">No branches found</p>
                    @if($isBusinessCreator)
                      <a href="{{ route('manager.branches') }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-plus"></i> Add Your First Branch
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

      <!-- Edit Unit Modal -->
      <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:520px;">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editUnitModalLabel">
                <i class="bi bi-pencil-square"></i> Edit Measurement Unit
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUnitForm">
              <div class="modal-body">
                <div class="mb-3">
                  <label for="editUnitName" class="form-label required-field">Unit Name</label>
                  <input type="text" class="form-control" id="editUnitName" name="edit_unit_name" required>
                </div>
                <div class="mb-3">
                  <label for="editUnitAbbr" class="form-label required-field">Abbreviation</label>
                  <input type="text" class="form-control" id="editUnitAbbr" name="edit_unit_abbr" maxlength="10" required>
                </div>
                <div class="mb-3">
                  <label for="editUnitPrecision" class="form-label required-field">Precision</label>
                  <select class="form-select" id="editUnitPrecision" name="edit_unit_precision" required>
                    <option value="1">1 (Whole numbers)</option>
                    <option value="0.1">0.1 (One decimal place)</option>
                    <option value="0.01" selected>0.01 (Two decimal places)</option>
                    <option value="0.001">0.001 (Three decimal places)</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>


  <!-- Staffs -->
  <div class="tab-pane fade" id="staffs" role="tabpanel" aria-labelledby="staffs-tab">
    <div class="card mb-4 p-3">
      <h4 class="mb-3" style="color:#007bff;font-weight:600;">
        <i class="bi bi-people me-2"></i>Staff Management
      </h4>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="mb-0">{{ $isBusinessCreator ? 'All Staff Members' : 'My Branch Staff' }}</h5>
          <p class="text-muted mb-0 small">Total: <strong>{{ $staffs->count() }}</strong> staff member{{ $staffs->count() != 1 ? 's' : '' }}</p>
        </div>
        @if($isBusinessCreator)
          <a href="{{ route('manager.staff') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add Staff
          </a>
        @else
          <div class="alert alert-info mb-0 py-2 px-3">
            <i class="bi bi-info-circle me-1"></i>Contact business owner to add staff
          </div>
        @endif
      </div>

      <!-- Staff Statistics -->
      @php
        $activeStaff = $staffs->where('status', 1)->count();
        $inactiveStaff = $staffs->where('status', 0)->count();
      @endphp
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <h4 class="text-primary" id="totalStaffCount">{{ $staffs->count() }}</h4>
              <p class="mb-0">Total Staff</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <h4 class="text-success" id="activeStaffCount">{{ $activeStaff }}</h4>
              <p class="mb-0">Active</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <h4 class="text-danger" id="inactiveStaffCount">{{ $inactiveStaff }}</h4>
              <p class="mb-0">Inactive</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Staff List Table -->
      <div class="table-responsive">
        <table class="table table-hover" id="staffListTable">
          <thead>
            <tr>
              <th>Staff Member</th>

              @if($isBusinessCreator)
                <th>Branch</th>
              @endif
              <th>Email</th>
              <th>Phone</th>
              <th>Status</th>
              <th>Date Added</th>
              @if($isBusinessCreator)
                <th>Actions</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @forelse($staffs as $staff)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="me-2" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); display: flex; align-items: center; justify-content: center;">
                      <i class="bi bi-person text-white"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">{{ $staff->fullname }}</h6>
                    </div>
                  </div>
                </td>

                @if($isBusinessCreator)
                  <td>
                    @if($staff->branches && $staff->branches->count() > 0)
                      @foreach($staff->branches->take(2) as $index => $branch)
                        <span class="badge bg-info me-1">{{ $branch->branch_name }}</span>
                      @endforeach
                      @if($staff->branches->count() > 2)
                        <span class="badge bg-secondary">+{{ $staff->branches->count() - 2 }}</span>
                      @endif
                    @else
                      <span class="badge bg-secondary">Not Assigned</span>
                    @endif
                  </td>
                @endif
                <td>{{ $staff->email }}</td>
                <td>{{ $staff->phone ?? 'N/A' }}</td>
                <td>
                  <span class="badge {{ $staff->status == 1 ? 'bg-success' : 'bg-danger' }}">
                    {{ $staff->status == 1 ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <p class="mb-0">{{ $staff->created_at->format('M d, Y') }}</p>
                  <p class="text-muted mb-0 small">{{ $staff->created_at->diffForHumans() }}</p>
                </td>
                @if($isBusinessCreator)
                  <td>
                    <a href="{{ route('manager.staff') }}" class="btn btn-sm btn-outline-primary" title="Manage in Staffs">
                      <i class="bi bi-gear"></i>
                    </a>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                <td colspan="{{ $isBusinessCreator ? '8' : '6' }}" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0">No staff members found</p>
                    @if($isBusinessCreator)
                      <a href="{{ route('manager.staff') }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-person-plus"></i> Add Your First Staff
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>




  <!-- Receipt Settings -->
  <div class="tab-pane fade" id="receipt" role="tabpanel" aria-labelledby="receipt-tab">
    <div class="card mb-4 p-3">
      <h4 class="mb-3" style="color:#007bff;font-weight:600;">Receipt Settings</h4>
      <div class="row">
        <!-- Left: Settings Form Container -->
        <div class="col-md-6" id="receiptSettingsContainer">
          <h5 class="mb-3">Receipt Header</h5>
          <div class="form-group mb-3">
            <label class="form-label">Receipt Title</label>
            <input type="text" class="form-control" id="receiptTitleInput" value="SALES RECEIPT" placeholder="Enter receipt title">
          </div>
          <div class="form-group mb-3">
            <label class="form-label">Header Text</label>
            <textarea class="form-control" id="headerTextInput" rows="3" placeholder="Additional header information">Thank you for shopping with us!</textarea>
          </div>
          <div class="form-group mb-3">
            <label class="form-label">Footer Text</label>
            <textarea class="form-control" id="footerTextInput" rows="3" placeholder="Footer message">Visit us again soon!</textarea>
          </div>

          <h5 class="mb-3 mt-4">Receipt Format</h5>
          <div class="form-group mb-3">
            <label class="form-label">Paper Size</label>
            <select class="form-control" id="paperSizeSelect">
              <option value="80mm Thermal" selected>80mm Thermal</option>
              <option value="58mm Thermal">58mm Thermal</option>
              <option value="A4 Paper">A4 Paper</option>
              <option value="Letter Size">Letter Size</option>
            </select>
          </div>
          <div class="form-group mb-3">
            <label class="form-label">Font Size</label>
            <select class="form-control" id="fontSizeSelect">
              <option>Small</option>
              <option selected>Medium</option>
              <option>Large</option>
            </select>
          </div>

          <h5 class="mb-3 mt-4">Receipt Information</h5>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="showInvoiceNumber" data-preview="receiptInvoiceNumberPreview" data-display="flex" checked>
            <label class="form-check-label" for="showInvoiceNumber">Show invoice number</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="showDate" data-preview="receiptDatePreview" data-display="flex" checked>
            <label class="form-check-label" for="showDate">Show date</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showCashier" data-preview="receiptCashierPreview" data-display="flex" checked>
            <label class="form-check-label" for="showCashier">Show cashier name</label>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="showLogo" data-preview="receiptLogoPreview" data-display="block" checked>
            <label class="form-check-label" for="showLogo">Show business logo on receipt</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showBarcode" data-preview="receiptBarcodePreview,receiptBarcodeSeparator" data-display="block" checked>
            <label class="form-check-label" for="showBarcode">Include receipt barcode</label>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="showTaxDetails" data-preview="receiptTaxPreview" data-display="flex" checked>
            <label class="form-check-label" for="showTaxDetails">Show tax breakdown</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="showItemCodes" data-preview="receiptSkuPreview" data-display="block" checked>
            <label class="form-check-label" for="showItemCodes">Show item codes/SKUs</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="showDiscounts" data-preview="receiptDiscountPreview" data-display="flex" checked>
            <label class="form-check-label" for="showDiscounts">Show discount details</label>
          </div>
          <button type="button" class="btn btn-primary mt-2">Save Receipt Settings</button>
          <span id="receiptSaveStatus" class="ms-3 text-success" style="display:none;font-weight:600;">Saved</span>
        </div>

        <!-- Right: Live Preview Container -->
        <div class="col-md-6" id="receiptPreviewContainer">
          <h5 class="mb-3 text-center">Live Receipt Preview</h5>
          <div id="receiptPreview" style="max-height: 550px; overflow:auto; display:flex; justify-content:center; align-items:flex-start; padding:12px;">
            <div id="receiptPreviewInner" class="border rounded p-3 bg-light" style="width:300px; max-width:100%; box-sizing:border-box;">
              <div class="text-center mb-2" id="receiptLogoPreview" style="display:block;">
              <small class="text-muted">[Business Logo]</small>
            </div>
            <h6 class="text-center mb-1" id="receiptTitlePreview">SALES RECEIPT</h6>
            <p class="text-center mb-2" id="headerTextPreview" style="white-space:pre-line;">Thank you for shopping with us!</p>
            <hr class="my-2">

            <!-- Invoice Information -->
            <div class="mb-2 small">
              <div class="d-flex justify-content-between" id="receiptInvoiceNumberPreview" style="display: flex;">
                <span class="fw-bold">Invoice #:</span>
                <span>INV-2025-001</span>
              </div>
              <div class="d-flex justify-content-between" id="receiptDatePreview" style="display: flex;">
                <span class="fw-bold">Date:</span>
                <span>Nov 22, 2025 10:30 AM</span>
              </div>
              <div class="d-flex justify-content-between" id="receiptCashierPreview" style="display: flex;">
                <span class="fw-bold">Cashier:</span>
                <span>John Doe</span>
              </div>
            </div>
            <hr class="my-2">

            <!-- Items -->
            <div class="mb-2">
              <div class="d-flex justify-content-between">
                <span>Item A</span>
                <span>₦5,000</span>
              </div>
              <div class="d-flex justify-content-between">
                <span>Item B</span>
                <span>₦3,000</span>
              </div>
              <div class="d-flex justify-content-between" id="receiptDiscountPreview" style="display: flex;">
                <span class="text-muted">Discount</span>
                <span class="text-muted">-₦500</span>
              </div>
              <div class="d-flex justify-content-between" id="receiptTaxPreview" style="display: flex;">
                <span class="text-muted">Tax (7.5%)</span>
                <span class="text-muted">₦562.50</span>
              </div>
              <hr class="my-2">
              <div class="d-flex justify-content-between fw-bold">
                <span>Total</span>
                <span>₦7,562.50</span>
              </div>
              <div id="receiptSkuPreview" class="mt-2 small text-muted" style="display: block;">
                <div>SKU-001, SKU-002</div>
              </div>
            </div>
            <hr class="my-2" id="receiptBarcodeSeparator">
            <div class="text-center" id="receiptBarcodePreview" style="display:block;">
              <small class="text-muted">[Barcode]</small>
            </div>
            <p class="text-center mt-3 mb-0" id="footerTextPreview" style="white-space:pre-line;">Visit us again soon!</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Measurement Units -->
 {{--   <div class="tab-pane fade" id="measurement" role="tabpanel" aria-labelledby="measurement-tab">
    <div class="card mb-4 p-3">
      <h4 class="mb-3" style="color:#007bff;font-weight:600;">Measurement Units</h4>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">Manage Units of Measurement</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
          <i class="bi bi-plus"></i> Add Unit
        </button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead class="table-light">
            <tr>
              <th style="width: 30%;">Unit Name</th>
              <th style="width: 15%;">Abbreviation</th>
              <th style="width: 15%;">Precision</th>
              <th style="width: 25%;">Type</th>
              <th style="width: 15%;" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr data-unit-type="Default">
              <td>Piece</td>
              <td><span class="badge bg-secondary">pcs</span></td>
              <td>1</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Carton</td>
              <td><span class="badge bg-secondary">ct</span></td>
              <td>1</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Per item</td>
              <td><span class="badge bg-secondary">pi</span></td>
              <td>1</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Kilogram</td>
              <td><span class="badge bg-secondary">kg</span></td>
              <td>0.01</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Gram</td>
              <td><span class="badge bg-secondary">g</span></td>
              <td>0.1</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Litre</td>
              <td><span class="badge bg-secondary">L</span></td>
              <td>0.01</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Metre</td>
              <td><span class="badge bg-secondary">m</span></td>
              <td>0.01</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Centimeter</td>
              <td><span class="badge bg-secondary">cm</span></td>
              <td>0.1</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Millimetre</td>
              <td><span class="badge bg-secondary">mm</span></td>
              <td>1</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr data-unit-type="Default">
              <td>Yard</td>
              <td><span class="badge bg-secondary">yd</span></td>
              <td>0.01</td>
              <td><span class="badge bg-primary">Default</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be edited" disabled><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary" title="Default units cannot be deleted" disabled><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i> <strong>Note:</strong> Precision indicates the decimal places for quantity measurements. <strong>Default</strong> units are system-defined, while <strong>Custom</strong> units are user-created and can be modified or deleted.
      </div>
    </div>
  </div>  --}}

    </div> <!-- /.tab-content -->
    </div> <!-- /.col-md-9 -->
  </div> <!-- /.settings-row -->
</div> <!-- /.container -->


   <!-- Add Unit Modal -->
    <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:520px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addUnitModalLabel">
              <i class="bi bi-plus-circle"></i> Add Measurement Unit
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="addUnitForm">
            <div class="modal-body">
              <div class="mb-3">
                <label for="unitName" class="form-label required-field">Unit Name</label>
                <input type="text" class="form-control" id="unitName" name="unit_name" placeholder="e.g., Ton, Box, Dozen" required>
                <small class="form-text text-muted">Full name of the measurement unit</small>
              </div>

              <div class="mb-3">
                <label for="unitAbbreviation" class="form-label required-field">Abbreviation</label>
                <input type="text" class="form-control" id="unitAbbreviation" name="unit_abbreviation" placeholder="e.g., t, bx, dz" maxlength="10" required>
                <small class="form-text text-muted">Short code for the unit (max 10 characters)</small>
              </div>

              <div class="mb-3">
                <label for="unitPrecision" class="form-label required-field">Precision</label>
                <select class="form-select" id="unitPrecision" name="unit_precision" required>
                  <option value="">Select precision</option>
                  <option value="1">1 (Whole numbers)</option>
                  <option value="0.1">0.1 (One decimal place)</option>
                  <option value="0.01" selected>0.01 (Two decimal places)</option>
                  <option value="0.001">0.001 (Three decimal places)</option>
                </select>
                <small class="form-text text-muted">Decimal places for quantity measurements</small>
              </div>

              <div class="mb-3">
                <label for="unitType" class="form-label">Type</label>
                <input type="text" class="form-control" id="unitType" name="unit_type" value="Custom" readonly>
                <small class="form-text text-muted">User-created units are marked as Custom</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle"></i> Cancel
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Add Unit
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>


        <!-- Add Branch Modal - placed at end for proper z-index -->
    <div class="modal fade" id="addBranchModal" tabindex="-1" aria-labelledby="addBranchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addBranchModalLabel">Add Branch</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="addBranchForm">
            <div class="modal-body">
              <div class="mb-3">
                <label for="branchName" class="form-label">Branch Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="branchName" name="branchName" placeholder="e.g. Main Branch" required>
              </div>
              <div class="mb-3">
                <label for="branchAddress" class="form-label">Address <span class="text-danger">*</span></label>
                <textarea class="form-control" id="branchAddress" name="branchAddress" rows="2" placeholder="Enter branch address" required></textarea>
              </div>
              <div class="mb-3">
                <label for="branchState" class="form-label">State <span class="text-danger">*</span></label>
                <select class="form-select" id="branchState" name="branchState" required>
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
                  <option value="FCT">Federal Capital Territory</option>
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
              <div class="mb-3">
                <label for="branchLGA" class="form-label">Local Government <span class="text-danger">*</span></label>
                <select class="form-select" id="branchLGA" name="branchLGA" required>
                  <option value="">Select State First</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Branch</button>
            </div>
          </form>
        </div>
      </div>
    </div>


   <script src="{{ asset('manager_asset/js/system_preferences.js') }}"></script>
@endsection
