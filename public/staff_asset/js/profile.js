document.addEventListener('DOMContentLoaded', function() {
      // Change password form handler
      const changePasswordForm = document.getElementById('changePasswordForm');
      if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const currentPassword = document.getElementById('currentPassword').value;
          const newPassword = document.getElementById('newPassword').value;
          const confirmPassword = document.getElementById('confirmPassword').value;

          if (newPassword !== confirmPassword) {
            alert('New passwords do not match!');
            return;
          }

          if (newPassword.length < 6) {
            alert('Password must be at least 6 characters long!');
            return;
          }

          // Simulate password update
          alert('Password updated successfully!');

          // Close modal and reset form
          const modal = bootstrap.Modal.getInstance(document.getElementById('changePasswordModal'));
          modal.hide();
          changePasswordForm.reset();
        });
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
