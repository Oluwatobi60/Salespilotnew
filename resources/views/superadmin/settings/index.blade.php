@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Application Settings')

@php
use App\Models\AppSetting;
@endphp

@section('superadmin_layout_content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="mb-4">
    <p class="text-muted mb-0">
        <i class="bi bi-info-circle me-1"></i>
        Control all application settings from this centralized dashboard. Changes take effect immediately.
    </p>
</div>

<form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Quick Actions Widget -->
    <div class="sa-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge text-warning me-2"></i>Quick Actions</h6>
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-danger w-100" onclick="toggleMaintenance()">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    @if(AppSetting::get('maintenance_mode', false))
                        Disable Maintenance
                    @else
                        Enable Maintenance
                    @endif
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-info w-100" onclick="clearCache()">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Clear All Cache
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-success w-100" onclick="runBackup()">
                    <i class="bi bi-shield-check me-1"></i>
                    Backup Database
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                    <i class="bi bi-envelope-check me-1"></i>
                    Test Email
                </button>
            </div>
        </div>
    </div>

    <!-- System Preferences Quick Link -->
    <div class="sa-card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-sliders text-white" style="font-size: 2rem;"></i>
                </div>
                <div class="ms-3 text-white">
                    <h5 class="mb-1 fw-bold">System Preferences</h5>
                    <p class="mb-0" style="opacity: 0.9;">Manage system-wide preferences, view all branches, staff, and BRMs</p>
                </div>
            </div>
            <a href="{{ route('superadmin.system-preferences') }}" class="btn btn-light btn-lg">
                <i class="bi bi-arrow-right-circle me-1"></i> Open System Preferences
            </a>
        </div>
    </div>

    <!-- Settings Widgets Grid -->
    <div class="row g-4">

        <!-- General Settings Widget -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="widget-icon me-3" style="background: linear-gradient(135deg, {{ primary_color() }} 0%, {{ secondary_color() }} 100%);">
                        <i class="bi bi-gear-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">General Settings</h6>
                        <small class="text-muted">Basic application configuration</small>
                    </div>
                </div>

                @foreach($groups['general'] as $setting)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input toggle-switch" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <label class="form-check-label toggle-label" for="{{ $setting->key }}">
                                    <span class="badge {{ $setting->value == '1' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $setting->value == '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </label>
                            </div>
                        @elseif($setting->type === 'textarea')
                            <textarea class="form-control" name="settings[{{ $setting->key }}]" rows="3">{{ $setting->value }}</textarea>
                        @else
                            <input type="{{ $setting->type }}" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Email Settings Widget -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="widget-icon me-3" style="background: linear-gradient(135deg, {{ primary_color() }} 0%, {{ secondary_color() }} 100%);">
                        <i class="bi bi-envelope-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Email Settings</h6>
                        <small class="text-muted">SMTP configuration</small>
                    </div>
                </div>

                @foreach($groups['email'] as $setting)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'password')
                            <input type="password" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="••••••••">
                        @elseif($setting->type === 'number')
                            <input type="number" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Settings Widget -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="widget-icon me-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="bi bi-credit-card-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Payment Settings</h6>
                        <small class="text-muted">Payment gateway configuration</small>
                    </div>
                </div>

                @foreach($groups['payment'] as $setting)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input toggle-switch" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <label class="form-check-label toggle-label" for="{{ $setting->key }}">
                                    <span class="badge {{ $setting->value == '1' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $setting->value == '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </label>
                            </div>
                        @elseif($setting->type === 'password')
                            <input type="password" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="••••••••">
                        @else
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Settings Widget -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="widget-icon me-3" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="bi bi-cpu-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">System Settings</h6>
                        <small class="text-muted">System behavior configuration</small>
                    </div>
                </div>

                @foreach($groups['system'] as $setting)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input toggle-switch" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <label class="form-check-label toggle-label" for="{{ $setting->key }}">
                                    <span class="badge {{ $setting->value == '1' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $setting->value == '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </label>
                            </div>
                        @elseif($setting->type === 'number')
                            <input type="number" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Appearance Settings Widget -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="widget-icon me-3" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
                        <i class="bi bi-palette-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Appearance Settings</h6>
                        <small class="text-muted">Branding and visual customization</small>
                    </div>
                </div>

                @foreach($groups['appearance'] as $setting)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'file')
                            @if($setting->value)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}"
                                        style="max-height: 60px; border: 1px solid #ddd; border-radius: 4px; padding: 4px;">
                                </div>
                            @endif
                            <input type="file" class="form-control" name="settings[{{ $setting->key }}]" accept="image/*">
                        @elseif($setting->type === 'color')
                            <input type="color" class="form-control form-control-color" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Security Settings Widget -->
        <div class="col-lg-6">
            <div class="sa-card h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="widget-icon me-3" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                        <i class="bi bi-shield-lock-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Security Settings</h6>
                        <small class="text-muted">Security and access control</small>
                    </div>
                </div>

                @foreach($groups['security'] as $setting)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>

                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input toggle-switch" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <label class="form-check-label toggle-label" for="{{ $setting->key }}">
                                    <span class="badge {{ $setting->value == '1' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $setting->value == '1' ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </label>
                            </div>
                        @elseif($setting->type === 'number')
                            <input type="number" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Save Button -->
    <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-check-circle me-2"></i>Save All Settings
        </button>
    </div>

</form>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Email Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testEmailForm" onsubmit="return sendTestEmail(event)">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">Send a test email to verify your SMTP configuration is working correctly.</p>
                    <div class="mb-3">
                        <label class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" id="test_email" name="test_email" required
                            placeholder="Enter email address">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Send Test Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.widget-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.sa-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.sa-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.form-check-input:checked {
    background-color: {{ primary_color() }};
    border-color: {{ primary_color() }};
}

.btn-primary {
    background: linear-gradient(135deg, {{ primary_color() }} 0%, {{ secondary_color() }} 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px {{ primary_color() }}66;
}
</style>

<script>
// Initialize tooltips and ensure correct checkbox states
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Force correct checkbox states on page load (prevent browser autocomplete)
    document.querySelectorAll('.toggle-switch').forEach(function(checkbox) {
        const actualValue = checkbox.getAttribute('data-value');
        const shouldBeChecked = actualValue === '1';

        if (checkbox.checked !== shouldBeChecked) {
            checkbox.checked = shouldBeChecked;
            updateToggleLabel(checkbox);
        }
    });
});

// Update toggle label when switch is changed
function updateToggleLabel(checkbox) {
    const label = checkbox.parentElement.querySelector('.toggle-label span');
    if (checkbox.checked) {
        label.textContent = 'Enabled';
        label.classList.remove('bg-secondary');
        label.classList.add('bg-success');
    } else {
        label.textContent = 'Disabled';
        label.classList.remove('bg-success');
        label.classList.add('bg-secondary');
    }
}

// Update individual toggle setting via AJAX
function updateToggleSetting(checkbox) {
    const settingKey = checkbox.id;
    const isChecked = checkbox.checked;

    // Update label immediately for better UX
    updateToggleLabel(checkbox);

    // Show loading state
    checkbox.disabled = true;

    // Send AJAX request
    fetch('{{ route('superadmin.settings.update-toggle') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            key: settingKey,
            value: isChecked ? '1' : '0'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update data-value attribute to match new state
            checkbox.setAttribute('data-value', isChecked ? '1' : '0');
            // Show success message
            showToast('success', data.message || 'Setting updated successfully!');
        } else {
            // Revert the toggle on error
            checkbox.checked = !isChecked;
            updateToggleLabel(checkbox);
            showToast('error', data.message || 'Failed to update setting');
        }
    })
    .catch(error => {
        // Revert the toggle on error
        checkbox.checked = !isChecked;
        updateToggleLabel(checkbox);
        showToast('error', 'Error: ' + error.message);
    })
    .finally(() => {
        // Re-enable the checkbox
        checkbox.disabled = false;
    });
}

// Show toast notification
function showToast(type, message) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    toastContainer.appendChild(toastElement.firstElementChild);

    const toast = new bootstrap.Toast(toastElement.firstElementChild, {
        autohide: true,
        delay: 3000
    });
    toast.show();

    // Remove toast element after it's hidden
    toastElement.firstElementChild.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Toggle maintenance mode
function toggleMaintenance() {
    if (confirm('Are you sure you want to toggle maintenance mode?')) {
        fetch('{{ route('superadmin.settings.toggle-maintenance') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// Clear cache
function clearCache() {
    if (confirm('Clear all application caches?')) {
        fetch('{{ route('superadmin.settings.clear-cache') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// Run backup
function runBackup() {
    if (confirm('Run database backup now?')) {
        fetch('{{ route('superadmin.settings.run-backup') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

// Send test email
function sendTestEmail(event) {
    event.preventDefault();

    const email = document.getElementById('test_email').value;

    fetch('{{ route('superadmin.settings.test-email') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            test_email: email
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('testEmailModal'));
            if (modal) modal.hide();
            // Reset form
            document.getElementById('testEmailForm').reset();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });

    return false;
}
</script>

@endsection
