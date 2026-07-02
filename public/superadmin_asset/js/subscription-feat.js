/* ===================================================
   Subscription Features — Enhanced JS
   =================================================== */

let addFeatureModal = null;

document.addEventListener('DOMContentLoaded', function () {

    // Init Bootstrap modal
    if (typeof bootstrap !== 'undefined' && document.getElementById('addFeatureModal')) {
        addFeatureModal = new bootstrap.Modal(document.getElementById('addFeatureModal'));
    }

    // Sync plan status toggle text on change
    document.querySelectorAll('.sf-plan-status-input').forEach(input => {
        input.addEventListener('change', function () {
            const label = this.closest('.sf-plan-status-label');
            const text = label && label.querySelector('.sf-plan-status-text');
            if (text) text.textContent = this.checked ? 'Active' : 'Inactive';
        });
    });

    // Auto-generate slug from feature name in Add Feature modal
    const nameInput = document.querySelector('#addFeatureForm input[name="name"]');
    const slugInput = document.querySelector('#addFeatureForm input[name="slug"]');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function () {
            if (!slugInput.dataset.manuallyEdited) {
                slugInput.value = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
            }
        });
        slugInput.addEventListener('input', function () {
            this.dataset.manuallyEdited = this.value ? 'true' : '';
        });
    }

    // Reset modal state on close
    const addModal = document.getElementById('addFeatureModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('addFeatureForm');
            if (form) form.reset();
            if (slugInput) delete slugInput.dataset.manuallyEdited;
            const submitBtn = document.getElementById('addFeatureSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Create Feature';
            }
        });
    }
});

/**
 * Toggle a single feature for a plan
 */
function toggleFeature(planId, featureSlug, isChecked) {
    const checkbox = document.querySelector(
        `input[data-plan-id="${planId}"][data-feature-slug="${featureSlug}"]`
    );
    if (!checkbox) return;

    const featureItem = checkbox.closest('.sf-feature-item');
    if (featureItem) featureItem.classList.add('loading');
    checkbox.disabled = true;

    fetch(`/superadmin/subscription-features/plans/${planId}/toggle-feature`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ feature_slug: featureSlug, enabled: isChecked })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || `Server error: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (featureItem) {
                featureItem.classList.toggle('enabled', isChecked);
            }
            updateRoleCounter(planId, featureSlug);
            updatePlanProgressBar(planId);
            showAlert('success', `Feature ${isChecked ? 'enabled' : 'disabled'}`);
        } else {
            showAlert('error', 'Failed: ' + (data.message || 'Unknown error'));
            checkbox.checked = !isChecked;
            if (featureItem) featureItem.classList.toggle('enabled', !isChecked);
        }
    })
    .catch(error => {
        showAlert('error', 'Error: ' + error.message);
        checkbox.checked = !isChecked;
        if (featureItem) featureItem.classList.toggle('enabled', !isChecked);
    })
    .finally(() => {
        if (featureItem) featureItem.classList.remove('loading');
        checkbox.disabled = false;
    });
}

/**
 * Toggle all features for a role in a plan
 */
function toggleAllRoleFeatures(planId, roleSlug, featureSlugs) {
    const checkedCount = featureSlugs.filter(slug => {
        const cb = document.querySelector(
            `input[data-plan-id="${planId}"][data-feature-slug="${slug}"]`
        );
        return cb && cb.checked;
    }).length;

    const shouldEnable = checkedCount < featureSlugs.length / 2;

    featureSlugs.forEach(slug => {
        const cb = document.querySelector(
            `input[data-plan-id="${planId}"][data-feature-slug="${slug}"]`
        );
        if (cb && cb.checked !== shouldEnable) {
            cb.checked = shouldEnable;
            toggleFeature(planId, slug, shouldEnable);
        }
    });
}

/**
 * Toggle plan active/inactive status
 */
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
            // Update the tab badge
            const tabBtn = document.getElementById(`tab-${planId}`);
            if (tabBtn) {
                const badge = tabBtn.querySelector('.sf-tab-badge');
                if (badge) {
                    badge.textContent = isActive ? 'Active' : 'Inactive';
                    badge.classList.toggle('inactive', !isActive);
                }
            }
            // Update the toggle label text
            const statusText = document.querySelector(
                `#planStatus${planId}`)?.closest('.sf-plan-status-label')?.querySelector('.sf-plan-status-text');
            if (statusText) statusText.textContent = isActive ? 'Active' : 'Inactive';

            showAlert('success', `Plan ${isActive ? 'activated' : 'deactivated'} successfully`);
        } else {
            showAlert('error', 'Failed to update plan status');
            setTimeout(() => location.reload(), 1200);
        }
    })
    .catch(() => {
        showAlert('error', 'An error occurred. Refreshing...');
        setTimeout(() => location.reload(), 1500);
    });
}

/**
 * Create a new feature via modal form
 */
function addNewFeature(event) {
    event.preventDefault();

    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    const submitBtn = document.getElementById('addFeatureSubmitBtn');

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Creating...';
    }

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
    .then(result => {
        if (result.success) {
            if (addFeatureModal) addFeatureModal.hide();
            showAlert('success', 'Feature created! Reloading page...');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', 'Failed: ' + (result.message || 'Unknown error'));
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Create Feature';
            }
        }
    })
    .catch(() => {
        showAlert('error', 'An error occurred while creating the feature');
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-plus-lg me-1"></i>Create Feature';
        }
    });

    return false;
}

/**
 * Update the role card's counter and progress bar (live, no reload)
 */
function updateRoleCounter(planId, featureSlug) {
    setTimeout(() => {
        const checkbox = document.querySelector(
            `input[data-plan-id="${planId}"][data-feature-slug="${featureSlug}"]`
        );
        if (!checkbox) return;

        const roleCard = checkbox.closest('.sf-role-card');
        if (!roleCard) return;

        const allCbs = roleCard.querySelectorAll('input.sf-feature-checkbox');
        const checkedCount = Array.from(allCbs).filter(cb => cb.checked).length;
        const totalCount = allCbs.length;

        // Footer text
        const counterEl = roleCard.querySelector('.sf-role-footer-count');
        if (counterEl) {
            counterEl.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${checkedCount} / ${totalCount} enabled`;
        }

        // Header badge
        const enabledEl = roleCard.querySelector('.sf-role-enabled');
        if (enabledEl) enabledEl.textContent = checkedCount;

        // Mini progress bar
        const pct = totalCount > 0 ? Math.round(checkedCount / totalCount * 100) : 0;
        const progressFill = roleCard.querySelector('.sf-role-progress-fill');
        if (progressFill) {
            progressFill.style.width = pct + '%';
            const cls = pct === 100 ? 'full' : pct > 60 ? 'high' : pct > 30 ? 'mid' : 'low';
            progressFill.className = `sf-role-progress-fill ${cls}`;
        }
    }, 80);
}

/**
 * Update the plan-level progress bar and enabled count
 */
function updatePlanProgressBar(planId) {
    setTimeout(() => {
        const tabPane = document.getElementById(`plan-${planId}`);
        if (!tabPane) return;

        const allCbs = tabPane.querySelectorAll('input.sf-feature-checkbox');
        const checkedCount = Array.from(allCbs).filter(cb => cb.checked).length;
        const totalCount = allCbs.length;

        const enabledEl = tabPane.querySelector('.sf-feat-enabled');
        if (enabledEl) enabledEl.textContent = checkedCount;

        const pct = totalCount > 0 ? Math.round(checkedCount / totalCount * 100) : 0;
        const fill = tabPane.querySelector('.sf-progress-fill');
        if (fill) fill.style.width = pct + '%';

        const label = tabPane.querySelector('.sf-progress-label');
        if (label) label.textContent = pct + '% enabled';
    }, 180);
}

/**
 * Show a dismissible alert at the top of the page
 */
function showAlert(type, message) {
    const isSuccess = type === 'success';
    const alertHtml = `
        <div class="alert alert-${isSuccess ? 'success' : 'danger'} alert-dismissible fade show mb-3"
             role="alert"
             style="border-radius: 10px; border-left: 4px solid ${isSuccess ? '#10b981' : '#ef4444'};">
            <i class="bi bi-${isSuccess ? 'check-circle-fill' : 'exclamation-triangle-fill'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    const container = document.getElementById('alertContainer');
    if (container) {
        container.innerHTML = alertHtml;
        window.scrollTo({ top: 0, behavior: 'smooth' });

        setTimeout(() => {
            const alertEl = container.querySelector('.alert');
            if (alertEl) {
                if (typeof bootstrap !== 'undefined') {
                    new bootstrap.Alert(alertEl).close();
                } else {
                    alertEl.remove();
                }
            }
        }, 4000);
    }
}
