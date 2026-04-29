@extends('brm.layouts.layout')

@section('brms_page_title')
My Customers
@endsection

@section('brms_page_content')

 <link rel="stylesheet" href="{{ asset('brm_asset/css/customers.css') }}">


          <!-- Page Header -->
          <div class="page-header">
            <h1><i class="bi bi-people-fill"></i> My Customers</h1>
            <p>Manage and track your customer relationships</p>
          </div>

          <!-- Customer Stats -->
          <div class="customer-stats">
            <div class="stat-card total">
              <div class="stat-icon">
                <i class="bi bi-people-fill"></i>
              </div>
              <h3>{{ $totalCustomers }}</h3>
              <p>Total Customers</p>
            </div>

            <div class="stat-card active">
              <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h3>{{ $activeSubscriptions }}</h3>
              <p>Active Subscriptions</p>
            </div>

            <div class="stat-card new">
              <div class="stat-icon">
                <i class="bi bi-star-fill"></i>
              </div>
              <h3>{{ $customersThisMonth }}</h3>
              <p>New This Month</p>
            </div>

            <div class="stat-card revenue">
              <div class="stat-icon">
                <i class="bi bi-person-x-fill"></i>
              </div>
              <h3>{{ $inactiveCustomers }}</h3>
              <p>Inactive Customers</p>
            </div>
          </div>

          <!-- Search and Filter -->
          <div class="search-filter-section">
            <div class="search-bar">
              <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Search customers by name, company, or email...">
              </div>

              <div class="filter-group">
                <select id="statusFilter">
                  <option value="all">All Status</option>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="trial">Trial</option>
                </select>

                <select id="planFilter">
                  <option value="all">All Plans</option>
                  <option value="enterprise">Enterprise</option>
                  <option value="professional">Professional</option>
                  <option value="business">Business</option>
                  <option value="basic">Basic</option>
                </select>

                <div class="view-toggle">
                  <button id="gridViewBtn" class="active">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                  </button>
                  <button id="tableViewBtn">
                    <i class="bi bi-table"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Customers Grid View -->
          <div class="customers-grid" id="customersGrid">
            @forelse($customers as $customer)
              @php
                // Generate initials from business name or name
                $name = $customer->business_name ?? ($customer->first_name . ' ' . $customer->surname);
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($name)))));

                // Generate consistent gradient colors based on customer ID
                $gradients = [
                  'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                  'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                  'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                  'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                  'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                  'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                ];
                $gradientIndex = ($customer->id - 1) % count($gradients);
                $gradient = $gradients[$gradientIndex];

                // Determine status
                $subscriptionStatus = $customer->currentSubscription ? 'active' : 'inactive';
                $planName = $customer->currentSubscription?->subscriptionPlan?->name ?? 'N/A';
              @endphp

              <div class="customer-card" data-status="{{ $subscriptionStatus }}" data-plan="{{ strtolower($planName) }}">
                <div class="customer-header">
                  <div class="customer-avatar" style="background: {{ $gradient }};">
                    {{ $initials }}
                  </div>
                  <div class="customer-info">
                    <h5>{{ $name }}</h5>
                    <p>{{ $customer->email }}</p>
                  </div>
                </div>
                <div class="customer-details">
                  <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="status-badge {{ $subscriptionStatus }}">
                      @if($customer->currentSubscription)
                        Active
                      @else
                        Inactive
                      @endif
                    </span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Plan:</span>
                    <span class="plan-badge">{{ $planName }}</span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $customer->phone_number ?? 'N/A' }}</span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Join Date:</span>
                    <span class="detail-value">{{ $customer->created_at->format('M d, Y') }}</span>
                  </div>
                </div>
                <div class="customer-actions">
                  <button class="action-btn primary">
                    <i class="bi bi-eye"></i> View Details
                  </button>
                  <button class="action-btn">
                    <i class="bi bi-telephone"></i>
                  </button>
                </div>
              </div>
            @empty
              <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #999;">
                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem;"></i>
                <p>No customers found. Start adding customers to see them here.</p>
              </div>
            @endforelse
          </div>

          <!-- Table View (Initially Hidden) -->
          <div class="customers-table-container" id="customersTable">
            <table class="customers-table">
              <thead>
                <tr>
                  <th>Customer</th>
                  <th>Contact</th>
                  <th>Plan</th>
                  <th>Status</th>
                  <th>Monthly Value</th>
                  <th>Join Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($customers as $customer)
                  @php
                    $name = $customer->business_name ?? ($customer->first_name . ' ' . $customer->surname);
                    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($name)))));

                    $gradients = [
                      'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                      'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                      'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                      'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                      'linear-gradient(135deg, #30cfd0 0%, #330867 100%)',
                      'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                    ];
                    $gradient = $gradients[($customer->id - 1) % count($gradients)];

                    $subscriptionStatus = $customer->currentSubscription ? 'active' : 'inactive';
                    $planName = $customer->currentSubscription?->subscriptionPlan?->name ?? 'N/A';
                  @endphp

                  <tr data-status="{{ strtolower($subscriptionStatus) }}" data-plan="{{ strtolower($planName) }}">
                    <td>
                      <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="table-avatar" style="background: {{ $gradient }};">{{ $initials }}</div>
                        <strong>{{ $name }}</strong>
                      </div>
                    </td>
                    <td>{{ $customer->email }}</td>
                    <td><span class="plan-badge">{{ $planName }}</span></td>
                    <td><span class="status-badge {{ strtolower($subscriptionStatus) }}">{{ ucfirst($subscriptionStatus) }}</span></td>
                    <td><strong>{{ $customer->revenue ?? '₦0' }}</strong></td>
                    <td>{{ $customer->created_at->format('M d, Y') }}</td>
                    <td>
                      <button class="action-btn" title="View Details"><i class="bi bi-eye"></i></button>
                      <button class="action-btn" title="Call"><i class="bi bi-telephone"></i></button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: #999;">
                      <i class="bi bi-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
                      No customers found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        <!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              © {{ date('Y') }} {{ app_name() }}. All rights reserved.
            </span>
          </div>
        </footer>



@endsection
