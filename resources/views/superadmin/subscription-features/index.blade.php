@extends('superadmin.layouts.layout')
@section('superadmin_page_title') Subscription Plans & Features @endsection
@section('superadmin_layout_content')

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

<style>
.plan-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
}

.plan-section-header {
    padding: 2rem;
    border-bottom: 2px solid #f1f5f9;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
}

.plan-icon-lg {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.plan-section-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    text-transform: capitalize;
}

.plan-section-subtitle {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
}

.plan-roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 0;
}

.role-card {
    border-right: 1px solid #e2e8f0;
    border-bottom: 1px solid #e2e8f0;
    background: white;
    display: flex;
    flex-direction: column;
}

.role-card:last-child {
    border-right: none;
}

.role-card-header {
    padding: 1.5rem;
    border-bottom: 2px solid #f1f5f9;
    background: #fafbfc;
    display: flex;
    align-items: start;
    gap: 1rem;
}

.role-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.role-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 0.25rem 0;
}

.role-description {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0 0 0.5rem 0;
}

.role-features-list {
    padding: 1rem;
    flex: 1;
    max-height: 450px;
    overflow-y: auto;
}

.feature-checkbox-wrapper {
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.feature-checkbox-wrapper:hover {
    background: #f8fafc;
    border-color: #e2e8f0;
}

.feature-checkbox-wrapper .form-check {
    margin: 0;
}

.feature-checkbox-wrapper .form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    margin-top: 0.125rem;
    cursor: pointer;
}

.feature-checkbox-wrapper .form-check-label {
    cursor: pointer;
    width: 100%;
    margin-left: 0.5rem;
}

.feature-checkbox-wrapper .form-check-label strong {
    color: #1e293b;
    font-size: 0.95rem;
}

.feature-checkbox-wrapper .form-check-label small {
    line-height: 1.4;
}

.role-card-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.plan-status-toggle {
    width: 3rem;
    height: 1.5rem;
    cursor: pointer;
}

.plan-status-toggle:checked {
    background-color: #10b981;
    border-color: #10b981;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .plan-roles-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .plan-roles-grid {
        grid-template-columns: 1fr;
    }
    
    .role-card {
        border-right: none;
    }
}
</style>

<script>
const addFeatureModal = new bootstrap.Modal(document.getElementById('addFeatureModal'));

// Toggle single feature for a plan
function toggleFeature(planId, featureSlug, isChecked) {
    fetch(`/superadmin/subscription-features/plans/${planId}/toggle-feature`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ 
            feature_slug: featureSlug,
            enabled: isChecked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `Feature ${isChecked ? 'enabled' : 'disabled'} successfully`);
            updateRoleCounter(planId, featureSlug);
        } else {
            showAlert('error', 'Failed to update feature: ' + (data.message || 'Unknown error'));
            // Revert checkbox
            document.getElementById(`plan${planId}_feature${featureSlug}`).checked = !isChecked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating feature');
        // Revert checkbox
        const checkbox = document.querySelector(`input[value="${featureSlug}"]`);
        if (checkbox) checkbox.checked = !isChecked;
    });
}

// Toggle all features for a specific role in a plan
function toggleAllRoleFeatures(planId, roleSlug, featureSlugs) {
    const currentCheckedCount = featureSlugs.filter(slug => {
        const checkbox = document.querySelector(`#plan${planId}_feature_${slug}`);
        return checkbox && checkbox.checked;
    }).length;
    
    const shouldEnable = currentCheckedCount < featureSlugs.length / 2;
    
    featureSlugs.forEach(slug => {
        const checkbox = document.querySelector(`input[value="${slug}"]`);
        if (checkbox && checkbox.checked !== shouldEnable) {
            checkbox.checked = shouldEnable;
            toggleFeature(planId, slug, shouldEnable);
        }
    });
}

// Toggle plan status
function togglePlanStatus(planId, isActive) {
    fetch(`/superadmin/subscription-features/plans/${planId}/info`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ is_active: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `Plan ${isActive ? 'activated' : 'deactivated'} successfully`);
        } else {
            showAlert('error', 'Failed to update plan status');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while updating plan status');
        location.reload();
    });
}

// Add new feature
function addNewFeature(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/superadmin/subscription-features/features', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addFeatureModal.hide();
            event.target.reset();
            showAlert('success', 'Feature created successfully');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', 'Failed to create feature: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while creating feature');
    });
    
    return false;
}

// Update role feature counter
function updateRoleCounter(planId, featureSlug) {
    // Live counter update without reload
    setTimeout(() => {
        const checkbox = document.querySelector(`input[value="${featureSlug}"]`);
        if (checkbox) {
            const roleCard = checkbox.closest('.role-card');
            if (roleCard) {
                const allCheckboxes = roleCard.querySelectorAll('input[type="checkbox"]');
                const checkedCount = Array.from(allCheckboxes).filter(cb => cb.checked).length;
                const totalCount = allCheckboxes.length;
                const counterEl = roleCard.querySelector('.role-card-footer small');
                if (counterEl) {
                    counterEl.innerHTML = `<i class="bi bi-check-circle-fill text-success"></i> ${checkedCount} / ${totalCount} enabled`;
                }
            }
        }
    }, 100);
}

// Show alert message
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    const container = document.getElementById('alertContainer');
    container.innerHTML = alertHtml;
    
    // Scroll to top to see alert
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}
</script>

@endsection
