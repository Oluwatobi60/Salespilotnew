document.addEventListener('DOMContentLoaded', function() {
      // Side Panel Controls
      const passwordPanel = document.getElementById('passwordPanel');
      const panelOverlay = document.getElementById('panelOverlay');
      const openPasswordPanelBtn = document.getElementById('openPasswordPanel');
      const closePanelBtn = document.getElementById('closePanelBtn');
      const closePanelFooterBtn = document.getElementById('closePanelFooterBtn');

      // Open panel
      if (openPasswordPanelBtn) {
        openPasswordPanelBtn.addEventListener('click', function() {
          passwordPanel.classList.add('active');
          panelOverlay.classList.add('active');
        });
      }

      // Close panel function
      function closePanel() {
        passwordPanel.classList.remove('active');
        panelOverlay.classList.remove('active');
      }

      // Close panel on button click
      if (closePanelBtn) {
        closePanelBtn.addEventListener('click', closePanel);
      }

      if (closePanelFooterBtn) {
        closePanelFooterBtn.addEventListener('click', closePanel);
      }

      // Close panel on overlay click
      if (panelOverlay) {
        panelOverlay.addEventListener('click', closePanel);
      }

      // Password visibility toggles
      const toggleButtons = [
        { btn: 'toggleCurrentPassword', input: 'currentPassword' },
        { btn: 'toggleNewPassword', input: 'newPassword' },
        { btn: 'toggleConfirmPassword', input: 'confirmPassword' }
      ];

      toggleButtons.forEach(({ btn, input }) => {
        const toggleBtn = document.getElementById(btn);
        const inputField = document.getElementById(input);
        if (toggleBtn && inputField) {
          toggleBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (inputField.type === 'password') {
              inputField.type = 'text';
              icon.classList.remove('bi-eye');
              icon.classList.add('bi-eye-slash');
            } else {
              inputField.type = 'password';
              icon.classList.remove('bi-eye-slash');
              icon.classList.add('bi-eye');
            }
          });
        }
      });

      // Change password form handler
      const changePasswordForm = document.getElementById('changePasswordForm');
      const passwordMessage = document.getElementById('passwordMessage');

      if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', async function(e) {
          e.preventDefault();

          const currentPassword = document.getElementById('currentPassword').value;
          const newPassword = document.getElementById('newPassword').value;
          const confirmPassword = document.getElementById('confirmPassword').value;

          // Client-side validation
          if (newPassword !== confirmPassword) {
            showMessage('New passwords do not match!', 'danger');
            return;
          }

          if (newPassword.length < 8) {
            showMessage('Password must be at least 8 characters long!', 'danger');
            return;
          }

          // Disable submit button
          const submitBtn = document.querySelector('button[form="changePasswordForm"]');
          if (!submitBtn) {
            console.error('Submit button not found');
            return;
          }

          const originalBtnText = submitBtn.innerHTML;
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Updating...';

          try {
            // Get CSRF token
            const csrfToken = document.querySelector('input[name="_token"]').value;

            // Send AJAX request
            const response = await fetch('/staff/update-password', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: confirmPassword
              })
            });

            const data = await response.json();

            if (data.success) {
              showMessage(data.message, 'success');

              // Reset form and close panel after delay
              setTimeout(() => {
                changePasswordForm.reset();
                closePanel();
                passwordMessage.style.display = 'none';
              }, 2000);
            } else {
              showMessage(data.message || 'An error occurred', 'danger');
            }
          } catch (error) {
            showMessage('An error occurred while updating password', 'danger');
            console.error('Error:', error);
          } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
          }
        });
      }

      // Function to show messages
      function showMessage(message, type) {
        if (passwordMessage) {
          passwordMessage.className = `alert alert-${type}`;
          passwordMessage.textContent = message;
          passwordMessage.style.display = 'block';

          // Auto-hide success messages
          if (type === 'success') {
            setTimeout(() => {
              passwordMessage.style.display = 'none';
            }, 3000);
          }
        }
      }

      // Two-factor authentication toggle
      const twoFactorSwitch = document.getElementById('twoFactorSwitch');
      if (twoFactorSwitch) {
        twoFactorSwitch.addEventListener('change', function() {
          const label = this.nextElementSibling;
          if (this.checked) {
            label.textContent = 'Enabled';
            alert('Two-factor authentication has been enabled.');
          } else {
            label.textContent = 'Disabled';
            alert('Two-factor authentication has been disabled.');
          }
        });
      }

      // Email notifications toggle
      const emailNotifications = document.getElementById('emailNotifications');
      if (emailNotifications) {
        emailNotifications.addEventListener('change', function() {
          const label = this.nextElementSibling;
          if (this.checked) {
            label.textContent = 'Enabled';
          } else {
            label.textContent = 'Disabled';
          }
        });
      }

      // Initialize Bootstrap dropdown for user avatar
      setTimeout(function() {
        var userDropdown = document.getElementById('UserDropdown');
        var dropdownMenu = document.querySelector('.dropdown-menu[aria-labelledby="UserDropdown"]');
        if (userDropdown && dropdownMenu && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
          try {
            new bootstrap.Dropdown(userDropdown, { autoClose: true, boundary: 'viewport' });
          } catch (error) {
            console.error('Dropdown initialization error:', error);
          }
        }

        // Initialize sidebar collapse behavior
        var sidebar = document.getElementById('sidebar');
        if (sidebar) {
          sidebar.querySelectorAll('a.nav-link[data-bs-toggle="collapse"]').forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
              e.preventDefault();
              var target = document.querySelector(this.getAttribute('href'));
              if (target && typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                sidebar.querySelectorAll('div.collapse.show').forEach(function (m) {
                  if (m !== target) bootstrap.Collapse.getOrCreateInstance(m).hide();
                });
                bootstrap.Collapse.getOrCreateInstance(target).toggle();
              }
            });
          });
        }
      }, 500);
    });
