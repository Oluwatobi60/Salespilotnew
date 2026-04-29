
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
