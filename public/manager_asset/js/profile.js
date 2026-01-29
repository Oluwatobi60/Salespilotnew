
// Edit Profile Panel
const openEditPanelBtn = document.getElementById('openEditPanel');
const editPanel = document.getElementById('editPanel');
const editPanelOverlay = document.getElementById('editPanelOverlay');
const closeEditPanelBtn = document.getElementById('closeEditPanelBtn');
const cancelEditPanelBtn = document.getElementById('cancelEditPanelBtn');

if (openEditPanelBtn && editPanel) {
    openEditPanelBtn.addEventListener('click', () => {
        editPanel.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}
if (closeEditPanelBtn && editPanel) {
    closeEditPanelBtn.addEventListener('click', () => {
        editPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (editPanelOverlay && editPanel) {
    editPanelOverlay.addEventListener('click', () => {
        editPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (cancelEditPanelBtn && editPanel) {
    cancelEditPanelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        editPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}

// Change Password Panel
const openPasswordPanelBtn = document.getElementById('openPasswordPanel');
const passwordPanel = document.getElementById('passwordPanel');
const panelOverlay = document.getElementById('panelOverlay');
const closePanelBtn = document.getElementById('closePanelBtn');
const cancelPasswordPanelBtn = document.getElementById('cancelPasswordPanelBtn');

if (openPasswordPanelBtn && passwordPanel) {
    openPasswordPanelBtn.addEventListener('click', () => {
        passwordPanel.classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}
if (closePanelBtn && passwordPanel) {
    closePanelBtn.addEventListener('click', () => {
        passwordPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (panelOverlay && passwordPanel) {
    panelOverlay.addEventListener('click', () => {
        passwordPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
if (cancelPasswordPanelBtn && passwordPanel) {
    cancelPasswordPanelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        passwordPanel.classList.remove('active');
        document.body.style.overflow = '';
    });
}
// Toggle password visibility
['CurrentPassword', 'NewPassword', 'ConfirmPassword'].forEach(function(type) {
    const toggleBtn = document.getElementById('toggle' + type);
    const input = document.getElementById(type.charAt(0).toLowerCase() + type.slice(1));
    if (toggleBtn && input) {
        toggleBtn.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                toggleBtn.querySelector('i').classList.remove('bi-eye');
                toggleBtn.querySelector('i').classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                toggleBtn.querySelector('i').classList.remove('bi-eye-slash');
                toggleBtn.querySelector('i').classList.add('bi-eye');
            }
        });
    }
});
