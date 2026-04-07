@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Dashboard')

@section('superadmin_layout_content')

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#ede9fe;">
                    <i class="bi bi-building" style="color:#6f42c1;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Users</div>
                    <div class="fw-bold fs-4">{{ \App\Models\User::whereNull('addby')->count() }}</div>
                    <div class="text-muted" style="font-size:0.72rem;">Business Creators</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#dcfce7;">
                    <i class="bi bi-patch-check-fill" style="color:#16a34a;"></i>
                </div>
                <div>
                    <div class="text-muted small">Active Subscriptions</div>
                    <div class="fw-bold fs-4">{{ \App\Models\UserSubscription::where('status','active')->where('end_date','>=',now())->distinct('user_id')->count('user_id') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fee2e2;">
                    <i class="bi bi-x-circle-fill" style="color:#dc2626;"></i>
                </div>
                <div>
                    <div class="text-muted small">Expired Subscriptions</div>
                    @php
                        $activeUserIds = \App\Models\UserSubscription::where('status','active')
                            ->where('end_date','>=',now())
                            ->pluck('user_id');
                        $expiredCount = \App\Models\UserSubscription::where('status','expired')
                            ->whereNotIn('user_id', $activeUserIds)
                            ->distinct('user_id')->count('user_id');
                    @endphp
                    <div class="fw-bold fs-4">{{ $expiredCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fef9c3;">
                    <i class="bi bi-cash-coin" style="color:#ca8a04;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Revenue</div>
                    <div class="fw-bold fs-4" style="font-size:clamp(1rem,2.5vw,1.5rem)!important;">&#8358;{{ number_format(\App\Models\UserSubscription::sum('amount_paid'), 2) }}</div>
                    <div class="text-muted" style="font-size:0.72rem;">All subscriptions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#f0f9ff;">
                    <i class="bi bi-person-gear" style="color:#0284c7;"></i>
                </div>
                <div>
                    <div class="text-muted small">Total BRM</div>
                    <div class="fw-bold fs-4">{{ $totalBrms }}</div>
                    <div class="text-muted" style="font-size:0.72rem;">{{ $activeBrms }} active</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Card -->
<div class="sa-card mb-4">
    <h6 class="fw-bold text-secondary mb-1">Welcome back, {{ $superadmin->name }}</h6>
    <p class="text-muted mb-0">You are logged in as Superadmin. Use the sidebar to navigate.</p>
</div>

<!-- Recent Activity -->
<div class="row g-3">

    <!-- New Registrations -->
    <div class="col-md-6 col-lg-4">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">New Registrations</h6>
                <span class="badge" style="background:#ede9fe;color:#6f42c1;">Users</span>
            </div>
            @forelse($recentUsers as $u)
                <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="stat-icon flex-shrink-0" style="background:#ede9fe;width:36px;height:36px;font-size:1rem;">
                        <i class="bi bi-building" style="color:#6f42c1;"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:0.85rem;">
                            {{ trim(($u->first_name ?? '') . ' ' . ($u->surname ?? '')) ?: $u->email }}
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.75rem;">{{ $u->business_name ?? $u->email }}</div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ $u->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="text-muted small mb-0">No registrations yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Subscriptions -->
    <div class="col-md-6 col-lg-4">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Recent Subscriptions</h6>
                <span class="badge" style="background:#dcfce7;color:#16a34a;">Payments</span>
            </div>
            @forelse($recentSubscriptions as $sub)
                <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="stat-icon flex-shrink-0" style="background:{{ $sub->status === 'active' ? '#dcfce7' : '#fee2e2' }};width:36px;height:36px;font-size:1rem;">
                        <i class="bi bi-{{ $sub->status === 'active' ? 'patch-check-fill' : 'x-circle-fill' }}"
                           style="color:{{ $sub->status === 'active' ? '#16a34a' : '#dc2626' }};"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate" style="font-size:0.85rem;">
                            {{ $sub->user ? trim(($sub->user->first_name ?? '') . ' ' . ($sub->user->surname ?? '')) ?: $sub->user->email : 'Unknown' }}
                        </div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            &#8358;{{ number_format($sub->amount_paid, 2) }} &middot;
                            <span class="badge rounded-pill {{ $sub->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}" style="font-size:0.65rem;">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ $sub->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="text-muted small mb-0">No subscription activity yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Activity Log -->
    <div class="col-md-12 col-lg-4">
        <div class="sa-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Recent Activity</h6>
                <span class="badge" style="background:#f0f9ff;color:#0284c7;">Logs</span>
            </div>
            @php
                $actionMeta = [
                    'Checkout completed' => ['icon' => 'bi-bag-check-fill',   'color' => '#16a34a', 'bg' => '#dcfce7', 'label' => 'Order Completed'],
                    'login'              => ['icon' => 'bi-box-arrow-in-right','color' => '#0284c7', 'bg' => '#f0f9ff', 'label' => 'Login'],
                    'create_branch'      => ['icon' => 'bi-diagram-3-fill',    'color' => '#ca8a04', 'bg' => '#fef9c3', 'label' => 'Branch Created'],
                    'create_category'    => ['icon' => 'bi-tag-fill',          'color' => '#6f42c1', 'bg' => '#ede9fe', 'label' => 'Category Created'],
                ];
            @endphp
            @forelse($recentActivity as $log)
                @php $meta = $actionMeta[$log->action] ?? ['icon' => 'bi-activity', 'color' => '#6c757d', 'bg' => '#f0f2f5', 'label' => $log->action]; @endphp
                <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="stat-icon flex-shrink-0" style="background:{{ $meta['bg'] }};width:36px;height:36px;font-size:1rem;">
                        <i class="bi {{ $meta['icon'] }}" style="color:{{ $meta['color'] }};"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold" style="font-size:0.85rem;">{{ $meta['label'] }}</div>
                        <div class="text-muted text-truncate" style="font-size:0.75rem;">
                            {{ $log->user ? trim(($log->user->first_name ?? '') . ' ' . ($log->user->surname ?? '')) ?: $log->user->email : 'Staff/System' }}
                        </div>
                        <div class="text-muted" style="font-size:0.72rem;">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @empty
                <p class="text-muted small mb-0">No recent activity logged.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
