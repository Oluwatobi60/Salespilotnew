@extends('superadmin.layouts.layout')
@section('superadmin_page_title') Plan Features @endsection
@section('superadmin_layout_content')
<link rel="stylesheet" href="{{ asset('superadmin_asset/css/subscription-feat.css') }}">

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="sf-page-header">
    <div class="sf-page-header-content">
        <div class="sf-page-header-left">
            <div class="sf-header-icon">
                <i class="bi bi-stars"></i>
            </div>
            <div>
                <h1 class="sf-page-title">Subscription Plans &amp; Features</h1>
                <p class="sf-page-subtitle">Control what each role can access in every subscription plan</p>
            </div>
        </div>
        <div class="sf-header-actions">
            <button type="button" class="sf-btn sf-btn-ghost"
                onclick="if(confirm('This will enable ALL features for ALL plans. Are you sure?')) { window.location.href='{{ route('superadmin.subscription-features.index') }}?reset_features=true'; }">
                <i class="bi bi-arrow-clockwise"></i>
                Reset All
            </button>
            <button type="button" class="sf-btn sf-btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
                <i class="bi bi-plus-circle"></i>
                Add Feature
            </button>
        </div>
    </div>
</div>

{{-- ── Alert Container ─────────────────────────────────────── --}}
<div id="alertContainer"></div>

{{-- ── Plan Tabs Navigation ────────────────────────────────── --}}
<div class="sf-tabs-wrapper">
    <div class="sf-tabs-nav" id="planTabs" role="tablist">
        @foreach($plans as $plan)
        @php
            $tabColors = [
                ['from' => '#10b981', 'to' => '#059669', 'icon' => 'gift'],
                ['from' => '#667eea', 'to' => '#764ba2', 'icon' => 'briefcase'],
                ['from' => '#f59e0b', 'to' => '#d97706', 'icon' => 'rocket-takeoff'],
                ['from' => '#ec4899', 'to' => '#8b5cf6', 'icon' => 'gem'],
                ['from' => '#06b6d4', 'to' => '#0891b2', 'icon' => 'lightning'],
            ];
            $tc = $tabColors[$loop->index % count($tabColors)];
        @endphp
        <button class="sf-tab-btn {{ $loop->first ? 'active' : '' }}"
            id="tab-{{ $plan->id }}"
            data-bs-toggle="tab"
            data-bs-target="#plan-{{ $plan->id }}"
            type="button"
            role="tab"
            style="--tab-from: {{ $tc['from'] }}; --tab-to: {{ $tc['to'] }};">
            <i class="bi bi-{{ $tc['icon'] }}"></i>
            <span class="sf-tab-name">{{ ucfirst($plan->name) }}</span>
            <span class="sf-tab-badge {{ !$plan->is_active ? 'inactive' : '' }}">
                {{ $plan->is_active ? 'Active' : 'Inactive' }}
            </span>
        </button>
        @endforeach
    </div>
</div>

{{-- ── Plan Tab Contents ───────────────────────────────────── --}}
<div class="tab-content sf-tab-content-wrapper" id="planTabContent">
    @foreach($plans as $plan)
    @php
        $tabColors = [
            ['from' => '#10b981', 'to' => '#059669', 'icon' => 'gift'],
            ['from' => '#667eea', 'to' => '#764ba2', 'icon' => 'briefcase'],
            ['from' => '#f59e0b', 'to' => '#d97706', 'icon' => 'rocket-takeoff'],
            ['from' => '#ec4899', 'to' => '#8b5cf6', 'icon' => 'gem'],
            ['from' => '#06b6d4', 'to' => '#0891b2', 'icon' => 'lightning'],
        ];
        $tc = $tabColors[$loop->index % count($tabColors)];

        $planFeatureSlugs = is_array($plan->features)
            ? $plan->features
            : (is_string($plan->features) ? json_decode($plan->features, true) ?? [] : []);

        $totalAll = 0;
        $totalEnabled = 0;
        foreach ($features as $roleFeats) {
            $totalAll += $roleFeats->count();
            $totalEnabled += $roleFeats->filter(fn($f) => in_array($f->slug, $planFeatureSlugs))->count();
        }
        $overallPct = $totalAll > 0 ? round($totalEnabled / $totalAll * 100) : 0;

        $categoryIcons = [
            'general'   => 'grid',
            'inventory' => 'boxes',
            'sales'     => 'cart-check',
            'reports'   => 'bar-chart-line',
            'users'     => 'people',
            'support'   => 'headset',
            'branches'  => 'building',
            'settings'  => 'gear',
        ];
        $roleGradients = [
            'business_creator' => 'linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)',
            'manager'          => 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)',
            'staff'            => 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)',
            'branch'           => 'linear-gradient(135deg, #d97706 0%, #b45309 100%)',
        ];
        $navFeatures = ['advanced_user_roles', 'multi_branch', 'advanced_inventory', 'pos_system'];
    @endphp

    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
         id="plan-{{ $plan->id }}"
         role="tabpanel"
         aria-labelledby="tab-{{ $plan->id }}">

        {{-- Plan Overview Bar --}}
        <div class="sf-plan-overview" style="--plan-from: {{ $tc['from'] }}; --plan-to: {{ $tc['to'] }};">
            <div class="sf-plan-overview-main">
                <div class="sf-plan-overview-icon">
                    <i class="bi bi-{{ $tc['icon'] }}"></i>
                </div>
                <div>
                    <h2 class="sf-plan-overview-name">{{ ucfirst($plan->name) }} Plan</h2>
                    <div class="sf-plan-overview-meta">
                        <span><i class="bi bi-cash-stack"></i>
                            @if($plan->monthly_price == 0) Free
                            @else ₦{{ number_format($plan->monthly_price, 0) }}/mo
                            @endif
                        </span>
                        <span><i class="bi bi-person-badge"></i> {{ $plan->max_managers }} Manager(s)</span>
                        <span><i class="bi bi-people"></i> {{ $plan->max_staff ?? '∞' }} Staff</span>
                        <span><i class="bi bi-building"></i> {{ $plan->max_branches ?? '∞' }} Branch(es)</span>
                        @if($plan->trial_days)
                            <span><i class="bi bi-clock"></i> {{ $plan->trial_days }}-day trial</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="sf-plan-overview-stats">
                <div class="sf-plan-feat-progress">
                    <div class="sf-plan-feat-numbers">
                        <span class="sf-feat-enabled">{{ $totalEnabled }}</span>
                        <span class="sf-feat-total">/ {{ $totalAll }} features</span>
                    </div>
                    <div class="sf-progress-bar">
                        <div class="sf-progress-fill" style="width: {{ $overallPct }}%"></div>
                    </div>
                    <div class="sf-progress-label">{{ $overallPct }}% enabled</div>
                </div>

                <div class="sf-plan-toggle-wrap">
                    <label class="sf-plan-status-label">
                        <input type="checkbox" class="sf-plan-status-input"
                            id="planStatus{{ $plan->id }}"
                            {{ $plan->is_active ? 'checked' : '' }}
                            onchange="togglePlanStatus({{ $plan->id }}, this.checked)">
                        <span class="sf-plan-status-track"></span>
                        <span class="sf-plan-status-text">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Roles Feature Grid --}}
        <div class="sf-roles-grid">
            @foreach($roles as $role)
            @php
                $roleFeatures    = $features[$role['slug']] ?? collect([]);
                $byCategory      = $roleFeatures->groupBy('category');
                $enabledRoleCount = $roleFeatures->filter(fn($f) => in_array($f->slug, $planFeatureSlugs))->count();
                $totalRoleCount  = $roleFeatures->count();
                $rolePct         = $totalRoleCount > 0 ? round($enabledRoleCount / $totalRoleCount * 100) : 0;
                $roleGrad        = $roleGradients[$role['slug']] ?? 'linear-gradient(135deg,#667eea,#764ba2)';
                $pctClass        = $rolePct === 100 ? 'full' : ($rolePct > 60 ? 'high' : ($rolePct > 30 ? 'mid' : 'low'));
            @endphp

            <div class="sf-role-card">
                {{-- Header --}}
                <div class="sf-role-header" style="background: {{ $roleGrad }};">
                    <div class="sf-role-icon-wrap">
                        <i class="bi bi-{{ $role['icon'] }}"></i>
                    </div>
                    <div class="sf-role-info">
                        <h3 class="sf-role-name">{{ $role['label'] }}</h3>
                        <p class="sf-role-desc">{{ $role['description'] }}</p>
                    </div>
                    <div class="sf-role-count-badge">
                        <span class="sf-role-enabled">{{ $enabledRoleCount }}</span>
                        <span class="sf-role-sep">/</span>
                        <span class="sf-role-total">{{ $totalRoleCount }}</span>
                    </div>
                </div>

                {{-- Mini progress bar --}}
                <div class="sf-role-mini-progress">
                    <div class="sf-role-progress-fill {{ $pctClass }}" style="width: {{ $rolePct }}%"></div>
                </div>

                {{-- Features grouped by category --}}
                <div class="sf-features-list" id="features-{{ $plan->id }}-{{ $role['slug'] }}">
                    @forelse($byCategory as $category => $catFeatures)
                    <div class="sf-category-group">
                        <div class="sf-category-label">
                            <i class="bi bi-{{ $categoryIcons[$category] ?? 'tag' }}"></i>
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                            <span class="sf-category-count">{{ $catFeatures->count() }}</span>
                        </div>
                        @foreach($catFeatures as $feature)
                        @php $isEnabled = in_array($feature->slug, $planFeatureSlugs); @endphp
                        <div class="sf-feature-item {{ $isEnabled ? 'enabled' : '' }}"
                             data-plan="{{ $plan->id }}"
                             data-feature="{{ $feature->slug }}">
                            <label class="sf-feature-label" for="plan{{ $plan->id }}_feat{{ $feature->id }}">
                                <input class="sf-feature-checkbox"
                                    type="checkbox"
                                    id="plan{{ $plan->id }}_feat{{ $feature->id }}"
                                    value="{{ $feature->slug }}"
                                    data-plan-id="{{ $plan->id }}"
                                    data-feature-slug="{{ $feature->slug }}"
                                    {{ $isEnabled ? 'checked' : '' }}
                                    onchange="toggleFeature({{ $plan->id }}, '{{ $feature->slug }}', this.checked)">
                                <span class="sf-feature-check-ui"></span>
                                <span class="sf-feature-text">
                                    <span class="sf-feature-name">
                                        @if(in_array($feature->slug, $navFeatures))
                                            <i class="bi bi-menu-button-wide sf-nav-indicator" title="Controls navigation menu"></i>
                                        @endif
                                        {{ $feature->name }}
                                    </span>
                                    @if($feature->description)
                                        <span class="sf-feature-desc">{{ $feature->description }}</span>
                                    @endif
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @empty
                    <div class="sf-empty-role">
                        <i class="bi bi-inbox"></i>
                        <p>No features for this role</p>
                    </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="sf-role-footer">
                    <span class="sf-role-footer-count" id="counter-{{ $plan->id }}-{{ $role['slug'] }}">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ $enabledRoleCount }} / {{ $totalRoleCount }} enabled
                    </span>
                    <button type="button" class="sf-toggle-all-btn"
                        onclick="toggleAllRoleFeatures({{ $plan->id }}, '{{ $role['slug'] }}', {{ json_encode($roleFeatures->pluck('slug')->toArray()) }})">
                        <i class="bi bi-check-all"></i> Toggle All
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>

{{-- ── Add New Feature Modal ───────────────────────────────── --}}
<div class="modal fade" id="addFeatureModal" tabindex="-1" aria-labelledby="addFeatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFeatureModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Feature
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFeatureForm" onsubmit="return addNewFeature(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Feature Name *</label>
                        <input type="text" class="form-control" name="name" required
                            placeholder="e.g., Advanced Analytics">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug (Identifier) *</label>
                        <input type="text" class="form-control" name="slug" required
                            placeholder="e.g., advanced_analytics"
                            pattern="[a-z0-9_]+"
                            title="Only lowercase letters, numbers, and underscores">
                        <small class="form-text text-muted">Auto-generated from name. Only lowercase, numbers, underscores.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"
                            placeholder="Describe what this feature does"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">User Role *</label>
                            <select class="form-select" name="role" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role['slug'] }}">{{ $role['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="general">General</option>
                                <option value="inventory">Inventory</option>
                                <option value="sales">Sales</option>
                                <option value="reports">Reports &amp; Analytics</option>
                                <option value="users">User Management</option>
                                <option value="support">Support</option>
                                <option value="branches">Branches</option>
                                <option value="settings">Settings</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addFeatureSubmitBtn">
                        <i class="bi bi-plus-lg me-1"></i>Create Feature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('superadmin_asset/js/subscription-feat.js') }}"></script>
@endsection
