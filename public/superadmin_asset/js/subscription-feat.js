
// Initialize modal when DOM is ready
let addFeatureModal = null;
document.addEventListener('DOMContentLoaded', function() {
    if (typeof bootstrap !== 'undefined' && document.getElementById('addFeatureModal')) {
        addFeatureModal = new bootstrap.Modal(document.getElementById('addFeatureModal'));
    }
});

// Toggle single feature for a plan
function toggleFeature(planId, featureSlug, isChecked) {
    // Find the specific checkbox for this plan and feature using data attributes
    const checkbox = document.querySelector(`input[data-plan-id="${planId}"][data-feature-slug="${featureSlug}"]`);

    console.log('Toggle feature:', { planId, featureSlug, isChecked, checkbox });

    if (!checkbox) {
        console.error('Checkbox not found for plan', planId, 'feature', featureSlug);
        showAlert('error', 'Checkbox not found');
        return;
    }

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
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.json().then(err => {
                console.error('Server error response:', err);
                throw new Error(err.message || `Server error: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        if (data.success) {
            showAlert('success', `Feature ${isChecked ? 'enabled' : 'disabled'} successfully`);
            updateRoleCounter(planId, featureSlug);
        } else {
            showAlert('error', 'Failed to update feature: ' + (data.message || 'Unknown error'));
            // Revert checkbox
            checkbox.checked = !isChecked;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showAlert('error', 'Error: ' + error.message);
        // Revert checkbox
        checkbox.checked = !isChecked;
    });
}

// Toggle all features for a specific role in a plan
function toggleAllRoleFeatures(planId, roleSlug, featureSlugs) {
    const currentCheckedCount = featureSlugs.filter(slug => {
        const checkbox = document.querySelector(`input[data-plan-id="${planId}"][data-feature-slug="${slug}"]`);
        return checkbox && checkbox.checked;
    }).length;

    const shouldEnable = currentCheckedCount < featureSlugs.length / 2;

    featureSlugs.forEach(slug => {
        const checkbox = document.querySelector(`input[data-plan-id="${planId}"][data-feature-slug="${slug}"]`);
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
            if (addFeatureModal) {
                addFeatureModal.hide();
            }
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
        const checkbox = document.querySelector(`input[data-plan-id="${planId}"][data-feature-slug="${featureSlug}"]`);
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
            if (typeof bootstrap !== 'undefined') {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.remove();
            }
        }
    }, 5000);
}
