@extends('manager.layouts.layout')
@section('manager_page_title')
Activity Logs
@endsection
@section('manager_layout_content')
<link rel="stylesheet" href="{{ asset('manager_asset/css/activity_logs.css') }}">


        <div class="content-wrapper">
            <!-- Page content starts here -->
            <div class="row">
                <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                        <div class="card-body">
                            <h4 class="card-title">Activity Logs</h4>
                            <p class="card-description">Track all system activities and user access logs.</p>

                            <!-- Search and Filter Options -->
                            <div class="row mb-3 filter-container">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search activities..." id="searchActivities">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                                    <!-- Access Type Filter -->
                                    <select class="form-select" id="accessTypeFilter" style="max-width: 140px;">
                                        <option value="">All Access Types</option>
                                        <option value="Login">Manager</option>
                                        <option value="Logout">Staff</option>
                                    </select>

                                    <!-- Staff Filter -->
                                    <select class="form-select" id="staffFilter" style="max-width: 140px;">
                                        <option value="">All Staff</option>
                                        <option value="John Smith">John Smith</option>
                                        <option value="Sarah Johnson">Sarah Johnson</option>
                                        <option value="Michael Brown">Michael Brown</option>
                                        <option value="Emily Davis">Emily Davis</option>
                                        <option value="David Wilson">David Wilson</option>
                                        <option value="Lisa Anderson">Lisa Anderson</option>
                                        <option value="Robert Taylor">Robert Taylor</option>
                                        <option value="Jennifer Garcia">Jennifer Garcia</option>
                                        <option value="System Admin">System Admin</option>
                                    </select>

                                    <!-- Date Range Filter -->
                                    <div class="date-filter-wrapper">
                                        <select class="form-select" id="dateFilter" style="max-width: 140px;">
                                            <option value="">All Dates</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last7days">Last 7 Days</option>
                                            <option value="last30days">Last 30 Days</option>
                                            <option value="custom">Custom Range</option>
                                        </select>

                                        <!-- Custom Date Inputs -->
                                        <div id="customDateInputs" class="custom-date-container">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="startDate" class="form-label text-muted">From Date</label>
                                                    <input type="date" class="form-control" id="startDate" onchange="performSearch()">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="endDate" class="form-label text-muted">To Date</label>
                                                    <input type="date" class="form-control" id="endDate" onchange="performSearch()">
                                                </div>
                                            </div>
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="hideCustomDateOverlay()">
                                                    <i class="fas fa-times"></i> Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>

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

                            <div class="table-responsive">
                                <table class="table table-striped" id="table">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Activity</th>
                                            <th>Staff Name</th>
                                            <th>Access Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $i => $log)
                                        <tr>
                                            <td>{{ ($logs->firstItem() ?? 0) + $i }}</td>
                                            <td>{{ $log->created_at->format('M d, Y h:i:s A') }}</td>
                                            <td>{{ $log->action }}{{ $log->details ? ': ' . $log->details : '' }}</td>
                                            <td>
                                                @if($log->staff)
                                                    {{ $log->staff->fullname ?? $log->staff->email ?? 'Staff' }}
                                                @elseif($log->user)
                                                    {{ $log->user->name ?? $log->user->email ?? 'User' }}
                                                @else
                                                    System
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->staff)
                                                    Staff
                                                @elseif($log->user)
                                                    {{ ucfirst($log->user->role ?? 'User') }}
                                                @else
                                                    System
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page content ends here -->
        </div>
        <!-- content-wrapper ends -->

        <!-- Activity Details Side Panel -->
        <div class="panel-overlay" id="panelOverlay"></div>
        <div class="activity-details-panel" id="activityDetailsPanel">
            <div class="panel-header d-flex justify-content-between align-items-center">
                <h5 class="panel-title">
                    <i class="bi bi-clipboard-data me-2"></i>Activity Details
                </h5>
                <button type="button" class="btn-close-panel" id="closePanelBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="panel-body">
                <!-- Basic Information -->
                <div class="detail-section">
                    <label class="detail-label">
                        <i class="bi bi-hash me-1"></i>Log ID
                    </label>
                    <div class="detail-value" id="detailLogId">#001</div>
                </div>

                <div class="detail-section">
                    <label class="detail-label">
                        <i class="bi bi-calendar-event me-1"></i>Date & Time
                    </label>
                    <div class="detail-value">
                        <div class="timestamp-info">
                            <i class="bi bi-clock"></i>
                            <span id="detailDateTime">Nov 7, 2025 9:15:00 AM</span>
                        </div>
                    </div>
                </div>

                <div class="detail-section">
                    <label class="detail-label">
                        <i class="bi bi-activity me-1"></i>Activity Description
                    </label>
                    <div class="detail-value activity-text" id="detailActivity">
                        User logged into system
                    </div>
                </div>

                <div class="detail-section">
                    <label class="detail-label">
                        <i class="bi bi-person me-1"></i>Staff Member
                    </label>
                    <div class="detail-value" id="detailStaffName">John Smith</div>
                </div>

                <div class="detail-section">
                    <label class="detail-label">
                        <i class="bi bi-shield-check me-1"></i>Access Level
                    </label>
                    <div class="detail-value">
                        <span class="access-badge manager" id="detailAccessType">Manager</span>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="detail-section">
                    <label class="detail-label">
                        <i class="bi bi-info-circle me-1"></i>Additional Information
                    </label>
                    <div class="detail-value">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Session ID:</small><br>
                                <span id="detailSessionId">SES_001234567</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">IP Address:</small><br>
                                <span id="detailIpAddress">192.168.1.101</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-muted">Browser:</small><br>
                                <span id="detailBrowser">Chrome 118.0</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Status:</small><br>
                                <span class="badge bg-success" id="detailStatus">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




<script src="{{ asset('manager_asset/js/activity_logs.js') }}"></script>
@endsection
