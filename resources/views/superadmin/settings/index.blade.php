@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'System Settings')

@php
use App\Models\AppSetting;
@endphp

@section('superadmin_layout_content')

<link href="{{ asset('superadmin_asset/css/index_setting.css') }}" rel="stylesheet" type="text/css">

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 16px;" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" style="border-radius: 16px;" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form id="settingsForm" action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Hero Header & Quick Link -->
    <div class="settings-header d-flex justify-content-between align-items-center flex-wrap gap-4">
        <div>
            <h2 class="fw-bold mb-2 text-white">System Settings</h2>
            <p class="mb-0 text-white-50 fs-6">Control your application's global configuration and behavior.</p>
        </div>
        <a href="{{ route('superadmin.system-preferences') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold shadow-sm" style="color: #4f46e5; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <i class="bi bi-sliders me-2"></i>System Preferences
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="mb-5">
        <h5 class="fw-bold mb-3 text-secondary ps-2">Quick Actions</h5>
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="quick-action-btn" onclick="toggleMaintenance()">
                    <i class="bi bi-shield-lock"></i>
                    <span>{{ AppSetting::get('maintenance_mode', false) ? 'Disable Maintenance' : 'Enable Maintenance' }}</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="quick-action-btn" onclick="clearCache()">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Clear Cache</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="quick-action-btn" onclick="runBackup()">
                    <i class="bi bi-cloud-arrow-down"></i>
                    <span>Backup Database</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="quick-action-btn" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                    <i class="bi bi-envelope-paper"></i>
                    <span>Test Email</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="row g-4 pb-5 mb-5">
        
        <!-- General Settings -->
        <div class="col-xl-6">
            <div class="settings-card">
                <div class="settings-icon-wrapper icon-gradient-1">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <h5 class="fw-bold mb-4">General Settings</h5>
                
                @foreach($groups['general'] as $setting)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>
                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <input type="checkbox" class="form-check-input ios-toggle ms-0 mt-0" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <span class="toggle-label fw-medium text-muted" style="font-size: 0.9rem;">
                                    {{ $setting->value == '1' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @elseif($setting->type === 'textarea')
                            <textarea class="form-control premium-input" name="settings[{{ $setting->key }}]" rows="3">{{ $setting->value }}</textarea>
                        @else
                            <input type="{{ $setting->type }}" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Email Settings -->
        <div class="col-xl-6">
            <div class="settings-card">
                <div class="settings-icon-wrapper icon-gradient-2">
                    <i class="bi bi-envelope-at-fill"></i>
                </div>
                <h5 class="fw-bold mb-4">Email Configuration</h5>
                
                @foreach($groups['email'] as $setting)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>
                        @if($setting->type === 'password')
                            <input type="password" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="••••••••">
                        @elseif($setting->type === 'number')
                            <input type="number" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="col-xl-6">
            <div class="settings-card">
                <div class="settings-icon-wrapper icon-gradient-3">
                    <i class="bi bi-credit-card-fill"></i>
                </div>
                <h5 class="fw-bold mb-4">Payment Gateways</h5>
                
                @foreach($groups['payment'] as $setting)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>
                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <input type="checkbox" class="form-check-input ios-toggle ms-0 mt-0" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <span class="toggle-label fw-medium text-muted" style="font-size: 0.9rem;">
                                    {{ $setting->value == '1' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @elseif($setting->type === 'password')
                            <input type="password" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="••••••••">
                        @else
                            <input type="text" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Settings -->
        <div class="col-xl-6">
            <div class="settings-card">
                <div class="settings-icon-wrapper icon-gradient-4">
                    <i class="bi bi-cpu-fill"></i>
                </div>
                <h5 class="fw-bold mb-4">System Behavior</h5>
                
                @foreach($groups['system'] as $setting)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>
                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <input type="checkbox" class="form-check-input ios-toggle ms-0 mt-0" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <span class="toggle-label fw-medium text-muted" style="font-size: 0.9rem;">
                                    {{ $setting->value == '1' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @else
                            <input type="text" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Appearance Settings -->
        <div class="col-xl-6">
            <div class="settings-card">
                <div class="settings-icon-wrapper icon-gradient-5">
                    <i class="bi bi-palette-fill"></i>
                </div>
                <h5 class="fw-bold mb-4">Appearance</h5>
                
                @foreach($groups['appearance'] as $setting)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>
                        @if($setting->type === 'file')
                            @if($setting->value)
                                <div class="mb-3">
                                    <div style="padding: 10px; background: #f3f4f6; border-radius: 12px; display: inline-block;">
                                        <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->label }}"
                                            style="max-height: 50px; border-radius: 6px;">
                                    </div>
                                </div>
                            @endif
                            <input type="file" class="form-control premium-input" name="settings[{{ $setting->key }}]" accept="image/*">
                        @elseif($setting->type === 'color')
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" class="form-control form-control-color border-0 p-1 shadow-sm rounded-circle" style="width: 40px; height: 40px;" name="settings[{{ $setting->key }}]"
                                    value="{{ $setting->value }}">
                                <span class="text-muted font-monospace">{{ $setting->value }}</span>
                            </div>
                        @else
                            <input type="text" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Security Settings -->
        <div class="col-xl-6">
            <div class="settings-card">
                <div class="settings-icon-wrapper bg-dark text-white shadow-lg">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5 class="fw-bold mb-4">Security Policies</h5>
                
                @foreach($groups['security'] as $setting)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            {{ $setting->label }}
                            @if($setting->description)
                                <i class="bi bi-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="{{ $setting->description }}"></i>
                            @endif
                        </label>
                        @if($setting->type === 'boolean')
                            <div class="form-check form-switch d-flex align-items-center gap-3 ps-0">
                                <input type="checkbox" class="form-check-input ios-toggle ms-0 mt-0" name="settings[{{ $setting->key }}]"
                                    id="{{ $setting->key }}" data-value="{{ $setting->value }}" {{ $setting->value == '1' ? 'checked' : '' }}
                                    onchange="updateToggleSetting(this)">
                                <span class="toggle-label fw-medium text-muted" style="font-size: 0.9rem;">
                                    {{ $setting->value == '1' ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @elseif($setting->type === 'number')
                            <input type="number" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}">
                        @else
                            <input type="text" class="form-control premium-input" name="settings[{{ $setting->key }}]"
                                value="{{ $setting->value }}" placeholder="{{ $setting->label }}">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Floating Action Bar -->
    <div class="floating-action-bar">
        <div class="text-muted fw-medium d-none d-md-block">
            <i class="bi bi-info-circle me-1"></i> Don't forget to save your changes
        </div>
        <button type="submit" class="btn btn-premium px-5" onclick="this.innerHTML='<i class=\'spinner-border spinner-border-sm me-2\'></i>Saving...'">
            <i class="bi bi-cloud-check-fill me-2"></i> Save All Settings
        </button>
    </div>

</form>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Test Email Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="testEmailForm" onsubmit="return sendTestEmail(event)">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-muted mb-4">Send a test email to verify your SMTP configuration is working correctly.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recipient Email</label>
                        <input type="email" class="form-control premium-input" id="test_email" name="test_email" required
                            placeholder="hello@example.com">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-premium px-4">
                        <i class="bi bi-send me-1"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    document.querySelectorAll('.ios-toggle').forEach(function(checkbox) {
        const actualValue = checkbox.getAttribute('data-value');
        const shouldBeChecked = actualValue === '1';

        if (checkbox.checked !== shouldBeChecked) {
            checkbox.checked = shouldBeChecked;
            updateToggleLabel(checkbox);
        }
    });
});

function updateToggleLabel(checkbox) {
    const label = checkbox.parentElement.querySelector('.toggle-label');
    if(label) {
        if (checkbox.checked) {
            label.textContent = 'Active';
            label.style.color = '#10b981';
        } else {
            label.textContent = 'Inactive';
            label.style.color = '#6b7280';
        }
    }
}

function updateToggleSetting(checkbox) {
    const settingKey = checkbox.id;
    const isChecked = checkbox.checked;

    updateToggleLabel(checkbox);
    checkbox.disabled = true;

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
            checkbox.setAttribute('data-value', isChecked ? '1' : '0');
            showToast('success', data.message || 'Setting updated!');
        } else {
            checkbox.checked = !isChecked;
            updateToggleLabel(checkbox);
            showToast('error', data.message || 'Failed to update setting');
        }
    })
    .catch(error => {
        checkbox.checked = !isChecked;
        updateToggleLabel(checkbox);
        showToast('error', 'Error: ' + error.message);
    })
    .finally(() => {
        checkbox.disabled = false;
    });
}

function showToast(type, message) {
    const toastHtml = `
        <div class="toast align-items-center text-white border-0 shadow-lg" style="background: ${type === 'success' ? '#10b981' : '#ef4444'}; border-radius: 12px; margin-bottom: 15px;" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-medium py-3 px-4">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2 fs-5" style="vertical-align: text-bottom;"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-4';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    toastContainer.appendChild(toastElement.firstElementChild);

    const toast = new bootstrap.Toast(toastContainer.lastElementChild, {
        autohide: true,
        delay: 3000
    });
    toast.show();

    toastContainer.lastElementChild.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

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
                showToast('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', data.message || 'Unknown error');
            }
        })
        .catch(error => showToast('error', error.message));
    }
}

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
                showToast('success', data.message);
            } else {
                showToast('error', data.message || 'Unknown error');
            }
        })
        .catch(error => showToast('error', error.message));
    }
}

function runBackup() {
    if (confirm('Start database backup? This may take a few moments.')) {
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
                showToast('success', data.message);
                if (data.download_url) {
                    // Trigger download immediately
                    window.location.href = data.download_url;
                }
            } else {
                showToast('error', data.message || 'Unknown error');
            }
        })
        .catch(error => showToast('error', error.message));
    }
}

function sendTestEmail(e) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending...';

    fetch('{{ route('superadmin.settings.test-email') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            test_email: document.getElementById('test_email').value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('testEmailModal')).hide();
            showToast('success', data.message);
        } else {
            showToast('error', data.message || 'Unknown error');
        }
    })
    .catch(error => showToast('error', error.message))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
    
    return false;
}
</script>
@endsection
