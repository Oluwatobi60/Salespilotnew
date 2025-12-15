
      // Staff Settings Side Panel logic
      const staffPanel = document.getElementById('staffSettingsPanel');
      const staffContentWrapper = document.getElementById('staffContentWrapper');
      function openStaffPanel(data) {
        document.getElementById('settingsStaffName').value = data.name;
        document.getElementById('settingsStaffUsername').value = data.username;
        document.getElementById('settingsStaffRole').value = data.role;
        document.getElementById('settingsStaffEmail').value = data.email;
        document.getElementById('settingsStaffPhone').value = data.phone;
        document.getElementById('settingsStaffPassword').value = '';
        document.getElementById('settingsStaffStatus').value = 'active';
        staffPanel.classList.add('open');
        staffContentWrapper.classList.add('collapse-for-panel');
      }
      function closeStaffPanel() {
        staffPanel.classList.remove('open');
        staffContentWrapper.classList.remove('collapse-for-panel');
      }
      document.querySelectorAll('.staff-settings-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          openStaffPanel({
            name: btn.getAttribute('data-name'),
            username: btn.getAttribute('data-username'),
            role: btn.getAttribute('data-role'),
            email: btn.getAttribute('data-email'),
            phone: btn.getAttribute('data-phone')
          });
        });
      });
      document.getElementById('closeStaffSettingsPanel').addEventListener('click', closeStaffPanel);
      document.getElementById('cancelStaffSettingsBtn').addEventListener('click', closeStaffPanel);
      document.getElementById('saveStaffSettingsBtn').addEventListener('click', function() {
        // Example: collect data and show a toast (implement actual save logic as needed)
        const name = document.getElementById('settingsStaffName').value;
        showToast('Settings saved for ' + name, true);
        closeStaffPanel();
      });

      // Populate staff settings modal with data from button
      document.querySelectorAll('.staff-settings-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
          document.getElementById('settingsStaffName').value = btn.getAttribute('data-name');
          document.getElementById('settingsStaffUsername').value = btn.getAttribute('data-username');
          document.getElementById('settingsStaffRole').value = btn.getAttribute('data-role');
          document.getElementById('settingsStaffEmail').value = btn.getAttribute('data-email');
          document.getElementById('settingsStaffPhone').value = btn.getAttribute('data-phone');
          document.getElementById('settingsStaffPassword').value = '';
          document.getElementById('settingsStaffStatus').value = 'active';
        });
      });
      // Save button handler (AJAX or form submit can be added here)
      document.getElementById('saveStaffSettingsBtn').addEventListener('click', function() {
        // Example: collect data and show a toast (implement actual save logic as needed)
        const name = document.getElementById('settingsStaffName').value;
        const role = document.getElementById('settingsStaffRole').value;
        const email = document.getElementById('settingsStaffEmail').value;
        const phone = document.getElementById('settingsStaffPhone').value;
        const status = document.getElementById('settingsStaffStatus').value;
        // TODO: AJAX save logic here
        showToast('Settings saved for ' + name, true);
        var modal = bootstrap.Modal.getInstance(document.getElementById('staffSettingsModal'));
        modal.hide();
      });


      // AJAX submit for Add Staff modal - Wait for DOM to be ready
      document.addEventListener('DOMContentLoaded', function() {
        initializeAddStaffForm();
      });

      // Also initialize when modal is shown (in case DOM loads before modal)
      var addStaffModal = document.getElementById('addStaffModal');
      if (addStaffModal) {
        addStaffModal.addEventListener('shown.bs.modal', function() {
          initializeAddStaffForm();
        });
      }

      function initializeAddStaffForm() {
        var form = document.getElementById('addStaffForm');
        if (!form) {
          console.log('Form not found');
          return;
        }

        // Prevent multiple initializations
        if (form.dataset.initialized === 'true') {
          return;
        }
        form.dataset.initialized = 'true';

        var addBtn = document.getElementById('addStaffBtn');

        // Photo preview functionality
        var photoInput = document.getElementById('passport_photo');
        var photoPreview = document.getElementById('photoPreview');
        var previewImage = document.getElementById('previewImage');
        var removePhotoBtn = document.getElementById('removePhoto');
        var uploadPlaceholder = document.getElementById('uploadPlaceholder');

        console.log('Photo elements found:', {
          photoInput: !!photoInput,
          photoPreview: !!photoPreview,
          previewImage: !!previewImage,
          removePhotoBtn: !!removePhotoBtn,
          uploadPlaceholder: !!uploadPlaceholder
        });

        // Click on placeholder to trigger file input
        if (uploadPlaceholder && photoInput) {
          uploadPlaceholder.addEventListener('click', function() {
            console.log('Upload placeholder clicked');
            photoInput.click();
          });

          // Drag and drop functionality
          uploadPlaceholder.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadPlaceholder.style.borderColor = '#0d6efd';
            uploadPlaceholder.style.background = '#f8f9ff';
          });

          uploadPlaceholder.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadPlaceholder.style.borderColor = '#ced4da';
            uploadPlaceholder.style.background = '#fff';
          });

          uploadPlaceholder.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadPlaceholder.style.borderColor = '#ced4da';
            uploadPlaceholder.style.background = '#fff';

            var files = e.dataTransfer.files;
            if (files.length > 0) {
              photoInput.files = files;
              // Trigger change event
              var event = new Event('change', { bubbles: true });
              photoInput.dispatchEvent(event);
            }
          });
        } else {
          console.error('Upload placeholder or photo input not found');
        }

        if (photoInput) {
          photoInput.addEventListener('change', function(e) {
            console.log('Photo input changed');
            var file = e.target.files[0];
            if (file) {
              console.log('File selected:', file.name, file.size, file.type);

              // Validate file size (2MB max)
              if (file.size > 2 * 1024 * 1024) {
                showToast('File size must be less than 2MB', false);
                photoInput.value = '';
                return;
              }

              // Validate file type
              if (!file.type.match('image.*')) {
                showToast('Please select a valid image file', false);
                photoInput.value = '';
                return;
              }

              // Show preview
              var reader = new FileReader();
              reader.onload = function(e) {
                console.log('Image loaded, showing preview');
                previewImage.src = e.target.result;
                if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
                photoPreview.style.display = 'block';
              };
              reader.readAsDataURL(file);
            }
          });
        } else {
          console.error('Photo input element not found');
        }

        if (removePhotoBtn) {
          removePhotoBtn.addEventListener('click', function() {
            console.log('Remove photo clicked');
            photoInput.value = '';
            photoPreview.style.display = 'none';
            if (uploadPlaceholder) uploadPlaceholder.style.display = 'block';
            previewImage.src = '';
          });
        } else {
          console.error('Remove photo button not found');
        }

        form.addEventListener('submit', function(e){
          e.preventDefault();
          if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adding...';
          }

          var data = new FormData(form);
          var csrfVal = data.get('csrf_token');

          // Clear previous validation errors
          form.querySelectorAll('.is-invalid').forEach(function(el) {
            el.classList.remove('is-invalid');
          });
          form.querySelectorAll('.invalid-feedback').forEach(function(el) {
            el.remove();
          });

          fetch(form.action, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-Token': csrfVal,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: data
          })
          .then(function(resp){
            if (!resp.ok) {
              return resp.json().then(function(errorData) {
                throw errorData;
              });
            }
            return resp.json();
          })
          .then(function(json){
            showToast(json.message || (json.success ? 'Staff member added successfully!' : 'Error occurred'), json.success);

            // If server provided email status, show it as an informational toast
            if (json.email_message) {
              setTimeout(function(){
                showToast(json.email_message, !!json.email_sent);
              }, 250);
            }

            if (json.success) {
              // Close modal
              var modalEl = document.getElementById('addStaffModal');
              if (modalEl) {
                var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                bsModal.hide();
              }

              // Reset form and preview
              form.reset();
              photoPreview.style.display = 'none';
              if (uploadPlaceholder) uploadPlaceholder.style.display = 'block';
              previewImage.src = '';

              // Optionally reload page to show new staff
              setTimeout(function(){
                location.reload();
              }, 1500);
            }
          })
          .catch(function(err){
            console.error('Error:', err);

            // Handle validation errors
            if (err.errors) {
              showToast(err.message || 'Please fix the validation errors', false);

              // Display validation errors next to fields
              Object.keys(err.errors).forEach(function(fieldName) {
                var input = form.querySelector('[name="' + fieldName + '"]');
                if (input) {
                  input.classList.add('is-invalid');

                  // Create error message element
                  var errorDiv = document.createElement('div');
                  errorDiv.className = 'invalid-feedback d-block';
                  errorDiv.textContent = err.errors[fieldName][0];

                  // Insert error message after the input
                  input.parentNode.appendChild(errorDiv);
                }
              });
            } else {
              showToast(err.message || 'Server error. Please try again.', false);
            }
          })
          .finally(function(){
            if (addBtn) {
              addBtn.disabled = false;
              addBtn.innerHTML = '<i class="bi bi-person-plus me-1"></i>Add Staff Member';
            }
          });
        });

        // Toast notification function
        function showToast(message, success) {
          var container = document.getElementById('globalToast');
          if (!container) return;

          var toast = document.createElement('div');
          toast.className = 'toast';
          toast.role = 'alert';
          toast.ariaLive = 'assertive';
          toast.ariaAtomic = 'true';

          toast.innerHTML = `
            <div class="toast-header ${success ? 'text-success' : 'text-danger'}">
              <i class="bi ${success ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
              <strong class="me-auto">${success ? 'Success' : 'Error'}</strong>
              <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
          `;

          container.appendChild(toast);
          var bs = new bootstrap.Toast(toast, { delay: 5000 });
          bs.show();

          // Remove after hidden
          toast.addEventListener('hidden.bs.toast', function(){
            toast.remove();
          });
        }
      }

      // Table Search and Filter Functionality
      document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const table = document.getElementById('staffsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        const checkAll = document.getElementById('check-all');

        // Check All functionality
        if (checkAll) {
          checkAll.addEventListener('change', function() {
            const checkboxes = table.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
              checkbox.checked = checkAll.checked;
            });
          });
        }

        // Search functionality
        if (searchInput) {
          searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            filterTable();
          });
        }

        // Role filter functionality
        if (roleFilter) {
          roleFilter.addEventListener('change', function() {
            filterTable();
          });
        }

        function filterTable() {
          const searchFilter = searchInput.value.toLowerCase();
          const roleFilterValue = roleFilter.value.toLowerCase();

          for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let searchFound = true;
            let roleFound = true;

            // Search through Staff Member name, Role, and Contact columns
            if (searchFilter) {
              searchFound = false;
              for (let j = 1; j < Math.min(cells.length, 5); j++) {
                const cellText = cells[j].textContent || cells[j].innerText;
                if (cellText.toLowerCase().indexOf(searchFilter) > -1) {
                  searchFound = true;
                  break;
                }
              }
            }

            // Role filter
            if (roleFilterValue) {
              const roleCell = cells[2]; // Role column
              const roleText = roleCell.textContent || roleCell.innerText;
              roleFound = roleText.toLowerCase().indexOf(roleFilterValue) > -1;
            }

            row.style.display = (searchFound && roleFound) ? '' : 'none';
          }

          // Update showing entries count
          updateEntriesCount();
        }

        // Update entries count
        function updateEntriesCount() {
          const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
          const totalRows = rows.length;
          const showingText = document.querySelector('.text-muted.small');
          if (showingText) {
            showingText.innerHTML = `Showing <strong>1-${visibleRows.length}</strong> of <strong>${totalRows}</strong> entries`;
          }
        }
      });
