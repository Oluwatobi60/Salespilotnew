@extends('manager.layouts.layout')
@section('manager_page_title')
Sales Summary
@endsection
@section('manager_layout_content')
 <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">

          <div class="content-wrapper">
            <!-- Sales by Staff content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Sales by Staff Report</h4>
                    <p class="card-description">Track individual staff member sales performance.</p>

                    <!-- Date and Staff Filter Section -->
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <select class="form-select form-select-sm mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()">
                          <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                          <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                          <option value="last7" {{ request('date_range') == 'last7' ? 'selected' : '' }}>Last 7 Days</option>
                          <option value="last30" {{ request('date_range') == 'last30' ? 'selected' : '' }}>Last 30 Days</option>
                          <option value="thisMonth" {{ request('date_range') == 'thisMonth' ? 'selected' : '' }}>This Month</option>
                          <option value="lastMonth" {{ request('date_range') == 'lastMonth' ? 'selected' : '' }}>Last Month</option>
                          <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                        <div id="customRangeInputs" style="display:{{ request('date_range') == 'custom' ? 'block' : 'none' }};">
                          <div class="input-group input-group-sm mb-1">
                            <span class="input-group-text">From</span>
                            <input type="date" class="form-control form-control-sm" id="customStartDate" value="{{ request('start_date') }}">
                          </div>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text">To</span>
                            <input type="date" class="form-control form-control-sm" id="customEndDate" value="{{ request('end_date') }}">
                          </div>
                        </div>
                        <small class="form-text text-muted">Choose a date range to filter sales by staff.</small>
                      </div>
                      <div class="col-md-4">
                        <select class="form-select form-select-sm" id="staffFilter">
                          <option value="">All Staff</option>
                          @foreach($staffList as $staffMember)
                            <option value="{{ $staffMember->id }}" {{ request('staff_id') == $staffMember->id ? 'selected' : '' }}>
                              {{ $staffMember->fullname }} ({{ $staffMember->staffsid }})
                            </option>
                          @endforeach
                        </select>
                        <small class="form-text text-muted">Filter by specific staff member.</small>
                      </div>
                    </div>

                    <div class="table-responsive">
                      <table class="table table-striped" id="salesByStaffTable">
                        <thead>
                          <tr>
                            <th>S/N</th>
                            <th>Staff Name</th>
                           {{--   <th>Employee ID</th>  --}}
                            <th>Transactions</th>
                            <th>Items Sold</th>
                            <th>Total Sales</th>
                            {{--  <th>Customers Registered (Staff/User)</th>  --}}
                           <th>Transactions Date</th>
                          </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesbystaff as $index => $staff)
                            <tr>
                              <td>{{ $index + 1 }}</td>
                              <td>
                                {{ $staff->seller_name ?? $staff->staff_name ?? $staff->manager_name ?? 'N/A' }}
                                @if($staff->seller_role)
                                  <span class="badge badge-sm {{ $staff->seller_role == 'Manager' ? 'badge-info' : 'badge-secondary' }}">
                                    {{ $staff->seller_role }}
                                  </span>
                                @endif
                              </td>
                              <td>{{ $staff->transactions_count ?? 0 }}</td>
                              <td>{{ $staff->items_sold ?? 0 }}</td>
                              <td>₦{{ number_format($staff->total_sales ?? 0, 2) }}</td>
                              <td><span class="badge badge-opacity-success">{{ $staff->last_transaction_date}}</span></td>
                            </tr>
                            @empty
                            <tr>
                              <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                  <i class="bi bi-inbox"></i>
                                  <h5>No Completed Sales</h5>
                                  <p class="text-muted">No sales have been completed yet.</p>
                                </div>
                              </td>
                            </tr>
                            @endforelse
                            @if($totals)
                            <tr style="font-weight:bold; background:#f8f9fa;">
                              <td colspan="2">Grand Total</td>
                              <td>{{ $totals->transactions_count ?? 0 }}</td>
                              <td>{{ $totals->items_sold ?? 0 }}</td>
                              <td>₦{{ number_format($totals->total_sales ?? 0, 2) }}</td>
                              <td></td>
                            </tr>
                            @endif
                        </tbody>
                    {{--      <tfoot>
                          <tr>
                            <th colspan="2">Total</th>
                            <th>515</th>
                            <th>978</th>
                            <th>$62,690.00</th>
                            <th>17</th>

                          </tr>
                        </tfoot>  --}}
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Sales by Staff content ends here -->
          </div>
          <!-- content-wrapper ends -->
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <script src="{{ asset('manager_asset/js/sales_by_staff.js') }}"></script>
@endsection
