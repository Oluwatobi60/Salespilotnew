@extends('manager.layouts.layout')
@section('manager_page_title')
Discount Report
@endsection
@section('manager_layout_content')
      <div class="container-scroller">
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
          <div class="content-wrapper">
            <!-- Discount content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <h4 class="card-title">Discount Report</h4>
                    <p class="card-description">View and manage discount transactions.</p>
                    @if(session('success'))
                      <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                    @if(session('error'))
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    @endif
                    <div class="row mb-3">
                      <div class="col-sm-4">
                        <select class="form-select form-select-sm mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()" style="font-size:0.85rem;">
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
                            <span class="input-group-text" style="font-size:0.85rem;">From</span>
                            <input type="date" class="form-control form-control-sm" id="customStartDate" style="font-size:0.85rem;" value="{{ request('start_date') }}">
                          </div>
                          <div class="input-group input-group-sm">
                            <span class="input-group-text" style="font-size:0.85rem;">To</span>
                            <input type="date" class="form-control form-control-sm" id="customEndDate" style="font-size:0.85rem;" value="{{ request('end_date') }}">
                          </div>
                        </div>
                        <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter discounts.</small>
                      </div>
                      <div class="col-sm-4">
                        <select class="form-select form-select-sm" id="staffFilter" style="font-size:0.85rem;">
                          <option value="">All Staff</option>
                        </select>
                      </div>

                      {{--  <div class="col-sm-4">
                        <button type="button" class="btn btn-primary" style="min-width: 150px;" id="openAddDiscountBtn"><strong>+ Add Discount</strong></button>
                      </div>  --}}

                    </div>

                    <div class="table-responsive">
                      <table class="table table-striped" id="discountTable">
                        <thead>
                          <tr>
                            <th>Discount Name</th>
                            {{--  <th>Type</th>
                            <th>Customers Group</th>  --}}
                         {{--     <th>Discount Rate</th>  --}}
                            <th>Times Used</th>
                            <th>Amount Discounted</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($discountStats as $discount)
                            <tr>
                              <td>{{ $discount['discount_name'] }}</td>
                             {{--   <td>{{ $discount['type'] }}</td>
                              <td>{{ $discount['customers_group'] }}</td>  --}}
                            {{--    <td>{{ $discount['discount_rate'] }}</td>  --}}
                              <td>{{ $discount['times_used'] }}</td>
                              <td>{{ number_format($discount['amount_discounted'], 2) }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Discount content ends here -->
          </div>
          <!-- content-wrapper ends -->

        </div>
      <!-- page-body-wrapper ends -->
    </div>


<script src="{{ asset('manager_asset/js/discount.js') }}"></script>
@endsection
