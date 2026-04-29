@extends('superadmin.layouts.layout')
@section('superadmin_page_title') Subscription Plans & Features @endsection
@section('superadmin_layout_content')
<link rel="stylesheet" href="{{ asset('superadmin_asset/css/subscription-feat.css') }}">
<div class="sa-header">
    <div>
        <h1><i class="bi bi-shield-check me-2"></i>Subscription Plans & Role Features</h1>
        <p class="sa-header-subtitle">Control what each role can do in every subscription plan</p>
    </div>
    <div class="sa-header-actions">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeatureModal">
            <i class="bi bi-plus-circle me-1"></i>Add New Feature
        </button>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="alertContainer"></div>

<!-- Quick Reference Guide -->
<div class="alert alert-info mb-4" style="background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); border: 1px solid #3b82f6; border-radius: 12px;">
    <div class="d-flex align-items-start gap-3">
        <i class="bi bi-info-circle-fill" style="font-size: 24px; color: #3b82f6; margin-top: 2px;"></i>
        <div style="flex: 1;">
            <h5 class="mb-2" style="color: #1e40af; font-weight: 600;">
                <i class="bi bi-lightbulb me-1"></i>User Hierarchy & Feature Control
            </h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <strong style="color: #1e40af;">Feature Organization:</strong>
                    <ul class="mb-0 mt-1" style="font-size: 0.9rem;">
                        <li><span class="badge bg-danger">Business Creator</span> - Owner/Full Access (addby = null)</li>
                        <li><span class="badge bg-primary">Manager</span> - Added by owner (addby = owner email)</li>
                        <li><span class="badge bg-success">Staff</span> - Added by manager/owner</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <strong style="color: #1e40af;">Key Business Creator Features:</strong>
                    <ul class="mb-0 mt-1" style="font-size: 0.9rem;">
                        <li><code>manage_managers</code> → Add/manage managers</li>
                        <li><code>multi_branch</code> → Create branches</li>
                        <li><code>advanced_inventory</code> → Full inventory control</li>
                        <li><code>pos_system</code> → POS access</li>
                    </ul>
                </div>
            </div>
            <div class="mt-2 pt-2" style="border-top: 1px solid #3b82f6;">
                <small style="color: #1e40af;">
                    <i class="bi bi-check-circle me-1"></i>
                    <strong>Tip:</strong> Features are role-specific. Manager features have "manager_" prefix, staff features have "staff_" prefix. Check the boxes below to enable!
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Plans with Role-Based Features -->
@foreach($plans as $plan)
<div class="plan-section mb-5">
    <div class="plan-section-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="plan-icon-lg" style="background: linear-gradient(135deg, 
                    @if($loop->index == 0) #10b981 0%, #059669 100%
                    @elseif($loop->index == 1) #3b82f6 0%, #2563eb 100%
                    @elseif($loop->index == 2) #8b5cf6 0%, #7c3aed 100%
                    @else #ef4444 0%, #dc2626 100%
                    @endif
                );">
                    <i class="bi bi-{{ 
                        $loop->index == 0 ? 'box' : 
                        ($loop->index == 1 ? 'briefcase' : 
                        ($loop->index == 2 ? 'rocket-takeoff' : 'gem')) 
                    }}"></i>
                </div>
                <div>
                    <h2 class="plan-section-title">{{ ucfirst($plan->name) }} Plan</h2>
                    <p class="plan-section-subtitle">
                        ₦{{ number_format($plan->monthly_price, 2) }}/month  •  
                        {{ $plan->max_managers }} Manager(s)  •  
                        {{ $plan->max_staff ?? '∞' }} Staff  •  
                        {{ $plan->max_branches ?? '∞' }} Branch(es)
                    </p>
                </div>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input plan-status-toggle" type="checkbox" 
                    id="planStatus{{ $plan->id }}" 
                    {{ $plan->is_active ? 'checked' : '' }}
                    onchange="togglePlanStatus({{ $plan->id }}, this.checked)">
                <label class="form-check-label fw-semibold" for="planStatus{{ $plan->id }}">
                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                </label>
            </div>
        </div>
    </div>

    <div class="plan-roles-grid">
        @foreach($roles as $role)
            @php
                $roleFeatures = $features[$role['slug']] ?? collect([]);
                $planFeatureSlugs = $plan->features ?? [];
            @endphp
            <div class="role-card">
                <div class="role-card-header">
                    <div class="role-icon bg-{{ $role['color'] }}">
                        <i class="bi bi-{{ $role['icon'] }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="role-title">{{ $role['label'] }}</h4>
                        <p class="role-description">{{ $role['description'] }}</p>
                        <small class="text-muted">{{ $roleFeatures->count() }} available features</small>
                    </div>
                </div>

                <div class="role-features-list">
                    @forelse($roleFeatures as $feature)
                        @php
                            // Highlight key navigation features
                            $navFeatures = ['advanced_user_roles', 'multi_branch', 'advanced_inventory', 'pos_system'];
                            $isNavFeature = in_array($feature->slug, $navFeatures);
                        @endphp
                        <div class="feature-checkbox-wrapper">
                            <div class="form-check">
                                <input class="form-check-input" 
                                    type="checkbox" 
                                    id="plan{{ $plan->id }}_feature{{ $feature->id }}" 
                                    value="{{ $feature->slug }}"
                                    {{ in_array($feature->slug, $planFeatureSlugs) ? 'checked' : '' }}
                                    onchange="toggleFeature({{ $plan->id }}, '{{ $feature->slug }}', this.checked)">
                                <label class="form-check-label" for="plan{{ $plan->id }}_feature{{ $feature->id }}">
                                    <strong>
                                        @if($isNavFeature)
                                            <i class="bi bi-menu-button-wide text-primary me-1" title="Controls navigation menu"></i>
                                        @endif
                                        {{ $feature->name }}
                                    </strong>
                                    <small class="d-block text-muted">{{ $feature->description }}</small>
                                </label>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small px-3">No features available for this role</p>
                    @endforelse
                </div>

                <div class="role-card-footer">
                    <small class="text-muted">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        {{ $roleFeatures->filter(fn($f) => in_array($f->slug, $planFeatureSlugs))->count() }} / 
                        {{ $roleFeatures->count() }} enabled
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                        onclick="toggleAllRoleFeatures({{ $plan->id }}, '{{ $role['slug'] }}', {{ json_encode($roleFeatures->pluck('slug')->toArray()) }})">
                        <i class="bi bi-check-all"></i> Toggle All
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endforeach

<!-- Add New Feature Modal -->
<div class="modal fade" id="addFeatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add New Feature
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <small class="form-text text-muted">Used in code. Only lowercase, numbers, underscores.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" 
                            placeholder="Describe what this feature does"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User Role *</label>
                        <select class="form-select" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role['slug'] }}">{{ $role['label'] }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Which user role does this feature belong to?</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category (Optional)</label>
                        <select class="form-select" name="category">
                            <option value="general">General</option>
                            <option value="inventory">Inventory</option>
                            <option value="sales">Sales</option>
                            <option value="reports">Reports & Analytics</option>
                            <option value="users">User Management</option>
                            <option value="support">Support</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Create Feature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script src="{{ asset('superadmin_asset/js/subscription-feat.js') }}"></script>

@endsection
