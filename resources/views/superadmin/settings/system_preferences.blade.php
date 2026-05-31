@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'System Preferences')

@section('superadmin_layout_content')

<style>
.settings-heading {
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1.5rem;
}

.tab-section-fixed {
    background: #f8f9fa;
    border-right: 1px solid #dee2e6;
    padding: 1.5rem;
    min-height: 600px;
}

.nav-tabs-line .nav-link {
    border: none;
    border-left: 3px solid transparent;
    border-radius: 0;
    padding: 0.75rem 1rem;
    margin-bottom: 0.5rem;
    color: #495057;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-tabs-line .nav-link:hover {
    background-color: #e9ecef;
    color: #007bff;
}

.nav-tabs-line .nav-link.active {
    background-color: #fff;
    border-left-color: #007bff;
    color: #007bff;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.1);
}

.tab-pane-content {
    background: #fff;
    border-radius: 8px;
    padding: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}

.stat-card h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-card p {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.table-responsive {
    max-height: 500px;
    overflow-y: auto;
}
</style>

<div class="container py-5" style="padding-left: 50px; padding-right:0px; max-width:100%;">
    <h2 class="settings-heading">System Preferences</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Validation Errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-0 settings-row">
        <div class="col-md-3 d-flex flex-column align-items-stretch tab-section-fixed">
            <div class="nav flex-column nav-pills nav-tabs-line" id="settingsTabs" role="tablist" aria-orientation="vertical">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">System Overview</button>
                <button class="nav-link" id="stack-tab" data-bs-toggle="pill" data-bs-target="#stack" type="button" role="tab" aria-controls="stack" aria-selected="false">Stack Version</button>
                <button class="nav-link" id="preferences-tab" data-bs-toggle="pill" data-bs-target="#preferences" type="button" role="tab" aria-controls="preferences" aria-selected="false">Global Preferences</button>
                <button class="nav-link" id="branches-tab" data-bs-toggle="pill" data-bs-target="#branches" type="button" role="tab" aria-controls="branches" aria-selected="false">All Branches</button>
                <button class="nav-link" id="staffs-tab" data-bs-toggle="pill" data-bs-target="#staffs" type="button" role="tab" aria-controls="staffs" aria-selected="false">All Staff</button>
                <button class="nav-link" id="brms-tab" data-bs-toggle="pill" data-bs-target="#brms" type="button" role="tab" aria-controls="brms" aria-selected="false">BRMs</button>
            </div>
        </div>

        <div class="col-md-9 d-flex flex-column justify-content-start align-items-stretch" style="padding-left:15px;">
            <div class="tab-content w-100" id="settingsTabsContent" style="min-width:0;flex:1 1 0;display:block;">

                <!-- System Overview -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="tab-pane-content">
                        <h5 class="mb-4" style="color:#007bff;font-weight:600;">System Overview</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h3>{{ $totalBusinesses }}</h3>
                                    <p><i class="bi bi-building me-2"></i>Total Businesses</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <h3>{{ $totalBranches }}</h3>
                                    <p><i class="bi bi-shop me-2"></i>Total Branches</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <h3>{{ $totalStaff }}</h3>
                                    <p><i class="bi bi-people me-2"></i>Total Staff Members</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                    <h3>{{ $totalBrms }}</h3>
                                    <p><i class="bi bi-person-badge me-2"></i>Total BRMs</p>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <h3>{{ $activePlans }}</h3>
                                    <p><i class="bi bi-card-list me-2"></i>Active Subscription Plans</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stack Version -->
                <div class="tab-pane fade" id="stack" role="tabpanel" aria-labelledby="stack-tab">
                    <div class="tab-pane-content">
                        <h5 class="mb-4" style="color:#007bff;font-weight:600;">Technology Stack Version</h5>

                        <div class="card mb-4 p-4">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #667eea;">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-filetype-php text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <small class="text-muted d-block">PHP Version</small>
                                            <h6 class="mb-0 fw-bold">{{ $stackVersions['php'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #f5576c;">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-box text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <small class="text-muted d-block">Laravel Version</small>
                                            <h6 class="mb-0 fw-bold">{{ $stackVersions['laravel'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #00f2fe;">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-database text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <small class="text-muted d-block">MySQL Version</small>
                                            <h6 class="mb-0 fw-bold">{{ $stackVersions['mysql'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #38f9d7;">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-server text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <small class="text-muted d-block">Web Server</small>
                                            <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">{{ $stackVersions['server'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #fee140;">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-globe text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <small class="text-muted d-block">Default Timezone</small>
                                            <h6 class="mb-0 fw-bold">{{ $stackVersions['timezone'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="d-flex align-items-center p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #007bff;">
                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #0062cc 0%, #007bff 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-cpu text-white" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="ms-3">
                                            <small class="text-muted d-block">Environment</small>
                                            <h6 class="mb-0 fw-bold text-uppercase">{{ $stackVersions['environment'] }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Debug Mode:</strong> {{ $stackVersions['debug_mode'] }}
                                @if($stackVersions['debug_mode'] === 'Enabled')
                                    <span class="badge bg-warning text-dark ms-2">Warning: Disable in production!</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Global Preferences -->
                <div class="tab-pane fade" id="preferences" role="tabpanel" aria-labelledby="preferences-tab">
                    <div class="tab-pane-content">
                        <h5 class="mb-4" style="color:#007bff;font-weight:600;">Global System Preferences</h5>

                        <form action="{{ route('superadmin.system-preferences.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card mb-4 p-3">
                                <h6 class="mb-3" style="font-weight:600;">Regional Settings</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Default Currency</label>
                                            <select class="form-control" name="default_currency">
                                                <option value="NGN" {{ $systemPreferences['default_currency'] == 'NGN' ? 'selected' : '' }}>Nigerian Naira (NGN)</option>
                                                <option value="USD" {{ $systemPreferences['default_currency'] == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                                <option value="EUR" {{ $systemPreferences['default_currency'] == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                                <option value="GBP" {{ $systemPreferences['default_currency'] == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Default Timezone</label>
                                            <select class="form-control" name="default_timezone">
                                                <option value="Africa/Lagos" {{ $systemPreferences['default_timezone'] == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos</option>
                                                <option value="UTC" {{ $systemPreferences['default_timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="America/New_York" {{ $systemPreferences['default_timezone'] == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                                <option value="Europe/London" {{ $systemPreferences['default_timezone'] == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4 p-3">
                                <h6 class="mb-3" style="font-weight:600;">Date & Time Format</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Date Format</label>
                                            <select class="form-control" name="date_format">
                                                <option value="Y-m-d" {{ $systemPreferences['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                                <option value="d/m/Y" {{ $systemPreferences['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                                <option value="m/d/Y" {{ $systemPreferences['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Time Format</label>
                                            <select class="form-control" name="time_format">
                                                <option value="H:i:s" {{ $systemPreferences['time_format'] == 'H:i:s' ? 'selected' : '' }}>24 Hour (HH:MM:SS)</option>
                                                <option value="h:i A" {{ $systemPreferences['time_format'] == 'h:i A' ? 'selected' : '' }}>12 Hour (hh:mm AM/PM)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4 p-3">
                                <h6 class="mb-3" style="font-weight:600;">System Limits</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Items Per Page</label>
                                            <input type="number" class="form-control" name="items_per_page" value="{{ $systemPreferences['items_per_page'] }}" min="5" max="100">
                                            <small class="text-muted">Number of items to display per page (5-100)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Session Timeout (minutes)</label>
                                            <input type="number" class="form-control" name="session_timeout" value="{{ $systemPreferences['session_timeout'] }}" min="5" max="1440">
                                            <small class="text-muted">Session timeout in minutes (5-1440)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Max Upload Size (KB)</label>
                                            <input type="number" class="form-control" name="max_upload_size" value="{{ $systemPreferences['max_upload_size'] }}" min="1024" max="10240">
                                            <small class="text-muted">Maximum file upload size (1024-10240 KB)</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Allowed File Types</label>
                                            <input type="text" class="form-control" name="allowed_file_types" value="{{ $systemPreferences['allowed_file_types'] }}">
                                            <small class="text-muted">Comma-separated list of allowed file extensions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-check-circle me-1"></i>Save Changes</button>
                                <button type="reset" class="btn btn-light"><i class="bi bi-x-circle me-1"></i>Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- All Branches -->
                <div class="tab-pane fade" id="branches" role="tabpanel" aria-labelledby="branches-tab">
                    <div class="tab-pane-content">
                        <h5 class="mb-4" style="color:#007bff;font-weight:600;">All Branches Across System</h5>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Branch Name</th>
                                        <th>Business</th>
                                        <th>Manager</th>
                                        <th>Staff Count</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($branches as $branch)
                                        <tr>
                                            <td><strong>{{ $branch->branch_name }}</strong></td>
                                            <td>{{ $branch->manager->business_name ?? 'N/A' }}</td>
                                            <td>{{ $branch->manager->full_name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info">{{ $branch->staffMembers->count() }}</span></td>
                                            <td>
                                                @if($branch->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $branch->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No branches found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $branches->links() }}
                        </div>
                    </div>
                </div>

                <!-- All Staff -->
                <div class="tab-pane fade" id="staffs" role="tabpanel" aria-labelledby="staffs-tab">
                    <div class="tab-pane-content">
                        <h5 class="mb-4" style="color:#007bff;font-weight:600;">All Staff Members Across System</h5>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Business</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($staffs as $staff)
                                        <tr>
                                            <td><strong>{{ $staff->full_name }}</strong></td>
                                            <td>{{ $staff->email }}</td>
                                            <td>{{ $staff->business_name }}</td>
                                            <td>{{ $staff->branch_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($staff->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $staff->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No staff members found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $staffs->links() }}
                        </div>
                    </div>
                </div>

                <!-- BRMs -->
                <div class="tab-pane fade" id="brms" role="tabpanel" aria-labelledby="brms-tab">
                    <div class="tab-pane-content">
                        <h5 class="mb-4" style="color:#007bff;font-weight:600;">Business Relationship Managers</h5>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Clients</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($brms as $brm)
                                        <tr>
                                            <td><strong>{{ $brm->name }}</strong></td>
                                            <td>{{ $brm->email }}</td>
                                            <td>{{ $brm->phone ?? 'N/A' }}</td>
                                            <td><span class="badge bg-primary">{{ $brm->customers_count }}</span></td>
                                            <td>
                                                @if($brm->status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $brm->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No BRMs found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $brms->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
