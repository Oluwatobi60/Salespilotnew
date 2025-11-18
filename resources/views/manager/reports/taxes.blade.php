@extends('manager.layouts.layout')
@section('manager_page_title')
Taxes Report
@endsection
@section('manager_layout_content')
 <div class="container-scroller">
      <div class="container-fluid page-body-wrapper">
          <div class="content-wrapper">
            <!-- Taxes content starts here -->
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                <div class="card card-rounded">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h4 class="card-title mb-0">Tax Report</h4>
                      <button type="button" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add Tax Rate
                      </button>
                    </div>
                    <p class="card-description">Summary of taxes collected and tax rates applied to transactions.</p>
                    <div class="table-responsive">
                      <div class="row mb-3">
                        <div class="col-sm-4">
                          <select class="form-select form-select-sm mb-2" id="dateRangeFilter" onchange="toggleCustomRangeInputs()" style="font-size:0.85rem;">
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="last7">Last 7 Days</option>
                            <option value="last30">Last 30 Days</option>
                            <option value="thisMonth">This Month</option>
                            <option value="lastMonth">Last Month</option>
                            <option value="custom">Custom Range</option>
                          </select>
                          <div id="customRangeInputs" style="display:none;">
                            <div class="input-group input-group-sm mb-1">
                              <span class="input-group-text" style="font-size:0.85rem;">From</span>
                              <input type="date" class="form-control form-control-sm" id="customStartDate" style="font-size:0.85rem;">
                            </div>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text" style="font-size:0.85rem;">To</span>
                              <input type="date" class="form-control form-control-sm" id="customEndDate" style="font-size:0.85rem;">
                            </div>
                          </div>
                          <small class="form-text text-muted" style="font-size:0.8rem;">Choose a date range to filter taxes.</small>
                        </div>
                      </div>
                     
                      <table class="table table-striped" id="taxesTable">
                        <thead>
                          <tr>
                            <th>Tax Name</th>
                            <th>Tax Rate (%)</th>
                            <th>Total Sales</th>
                            <th>Tax Collected</th>
                            <th>Transactions</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Sales Tax (Standard)</td>
                            <td>7.5%</td>
                            <td>$25,420.00</td>
                            <td>$1,906.50</td>
                            <td>142</td>
                            <td><span class="badge badge-opacity-success">Active</span></td>
                            <td>
                              <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                  <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="View Details">
                                  <i class="bi bi-eye"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>VAT (Value Added Tax)</td>
                            <td>15.0%</td>
                            <td>$18,750.00</td>
                            <td>$2,812.50</td>
                            <td>87</td>
                            <td><span class="badge badge-opacity-success">Active</span></td>
                            <td>
                              <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                  <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="View Details">
                                  <i class="bi bi-eye"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>Luxury Goods Tax</td>
                            <td>20.0%</td>
                            <td>$8,900.00</td>
                            <td>$1,780.00</td>
                            <td>23</td>
                            <td><span class="badge badge-opacity-success">Active</span></td>
                            <td>
                              <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                  <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="View Details">
                                  <i class="bi bi-eye"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td>Service Tax</td>
                            <td>5.0%</td>
                            <td>$12,300.00</td>
                            <td>$615.00</td>
                            <td>64</td>
                            <td><span class="badge badge-opacity-warning">Pending Review</span></td>
                            <td>
                              <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" title="Edit">
                                  <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="View Details">
                                  <i class="bi bi-eye"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td colspan="3" class="text-end fw-bold">Total Tax Collected:</td>
                            <td class="fw-bold text-success">$7,114.00</td>
                            <td class="fw-bold">316</td>
                            <td colspan="2"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Taxes content ends here -->
          </div>
          <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
@endsection