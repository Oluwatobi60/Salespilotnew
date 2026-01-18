document.addEventListener('DOMContentLoaded', function() {

  // Side panel controls for adding supplier
  var openAddSupplierBtn = document.getElementById('openAddSupplierBtn');
  var addSupplierPanel = document.getElementById('addSupplierPanel');
  var closeSidePanel = document.getElementById('closeSidePanel');
  var cancelAddSupplier = document.getElementById('cancelAddSupplier');
  var sidePanelOverlay = document.getElementById('sidePanelOverlay');

  // Open side panel
  if (openAddSupplierBtn && addSupplierPanel && sidePanelOverlay) {
    openAddSupplierBtn.addEventListener('click', function() {
      addSupplierPanel.classList.add('active');
      sidePanelOverlay.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
  }

  // Close side panel function
  function closePanel() {
    if (addSupplierPanel) {
      addSupplierPanel.classList.remove('active');
      document.body.style.overflow = ''; // Restore scrolling
    }
    if (sidePanelOverlay) {
      sidePanelOverlay.classList.remove('active');
    }
  }

  // Close side panel on close button click
  if (closeSidePanel) {
    closeSidePanel.addEventListener('click', closePanel);
  }

  // Close side panel on cancel button click
  if (cancelAddSupplier) {
    cancelAddSupplier.addEventListener('click', closePanel);
  }

  // Close side panel on overlay click
  if (sidePanelOverlay) {
    sidePanelOverlay.addEventListener('click', closePanel);
  }

  // Close side panel on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && addSupplierPanel && addSupplierPanel.classList.contains('active')) {
      closePanel();
    }
  });

  // Edit button functionality - fetch data by ID from data attributes
  document.querySelectorAll('.edit-btn').forEach(function(button) {
    button.addEventListener('click', function(e) {
      e.stopPropagation();

      // Fetch supplier data from data attributes
      const supplierId = this.getAttribute('data-id');
      const supplierName = this.getAttribute('data-name');
      const supplierEmail = this.getAttribute('data-email');
      const supplierContact = this.getAttribute('data-contact');
      const supplierPhone = this.getAttribute('data-phone');
      const supplierAddress = this.getAttribute('data-address');

      // Fill edit panel form with fetched data
      document.getElementById('editSupplierId').value = supplierId;
      document.getElementById('edit_supplier_name').value = supplierName;
      document.getElementById('edit_email').value = supplierEmail;
      document.getElementById('edit_contact_person').value = supplierContact || '';
      document.getElementById('edit_phone').value = supplierPhone || '';
      document.getElementById('edit_address').value = supplierAddress || '';

      // Set form action with supplier ID
      const form = document.getElementById('editSupplierForm');
      form.action = '/manager/update_supplier/' + supplierId;

      // Show edit side panel
      const editPanel = document.getElementById('editSupplierPanel');
      const editOverlay = document.getElementById('editSidePanelOverlay');
      if (editPanel && editOverlay) {
        editPanel.classList.add('active');
        editOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  });

  // Close edit panel function
  function closeEditPanel() {
    const editPanel = document.getElementById('editSupplierPanel');
    const editOverlay = document.getElementById('editSidePanelOverlay');
    if (editPanel) {
      editPanel.classList.remove('active');
      document.body.style.overflow = '';
    }
    if (editOverlay) {
      editOverlay.classList.remove('active');
    }
  }

  // Close edit panel on close button click
  const closeEditSidePanel = document.getElementById('closeEditSidePanel');
  if (closeEditSidePanel) {
    closeEditSidePanel.addEventListener('click', closeEditPanel);
  }

  // Close edit panel on cancel button click
  const cancelEditSupplier = document.getElementById('cancelEditSupplier');
  if (cancelEditSupplier) {
    cancelEditSupplier.addEventListener('click', closeEditPanel);
  }

  // Close edit panel on overlay click
  const editSidePanelOverlay = document.getElementById('editSidePanelOverlay');
  if (editSidePanelOverlay) {
    editSidePanelOverlay.addEventListener('click', closeEditPanel);
  }

  // Close edit panel on Escape key
  document.addEventListener('keydown', function(e) {
    const editPanel = document.getElementById('editSupplierPanel');
    if (e.key === 'Escape' && editPanel && editPanel.classList.contains('active')) {
      closeEditPanel();
    }
  });

  // Handle edit form submission with AJAX
  const editSupplierForm = document.getElementById('editSupplierForm');
  if (editSupplierForm) {
    editSupplierForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const supplierId = document.getElementById('editSupplierId').value;
      // Find the submit button by form attribute since it's outside the form
      const submitButton = document.querySelector('button[form="editSupplierForm"][type="submit"]');
      const originalButtonText = submitButton ? submitButton.innerHTML : '';

      // Disable submit button
      if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
      }

      fetch('/manager/update_supplier/' + supplierId, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close edit panel
          closeEditPanel();

          // Show success message with SweetAlert
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: data.message || 'Supplier updated successfully',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            // Reload page to show updated data
            window.location.reload();
          });
        } else {
          throw new Error(data.message || 'Failed to update supplier');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: error.message || 'Failed to update supplier. Please try again.',
          confirmButtonText: 'OK'
        });
      })
      .finally(() => {
        // Re-enable submit button
        if (submitButton) {
          submitButton.disabled = false;
          submitButton.innerHTML = originalButtonText;
        }
      });
    });
  }

  // Delete button functionality with SweetAlert confirmation
  document.querySelectorAll('.delete-btn').forEach(function(button) {
    button.addEventListener('click', function(e) {
      e.stopPropagation();

      const supplierId = this.getAttribute('data-id');
      const supplierName = this.getAttribute('data-name');

      // Show confirmation dialog
      Swal.fire({
        title: 'Are you sure?',
        html: `Do you want to delete supplier <strong>${supplierName}</strong>?<br><small class="text-muted">This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Yes, delete it!',
        cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Cancel',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          // Show loading state
          Swal.fire({
            title: 'Deleting...',
            html: 'Please wait while we delete the supplier.',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          // Create form data with CSRF token
          const formData = new FormData();
          formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
          formData.append('_method', 'DELETE');

          // Send delete request
          fetch('/manager/delete_supplier/' + supplierId, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message || 'Supplier has been deleted successfully.',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                // Reload page to show updated table
                window.location.reload();
              });
            } else {
              throw new Error(data.message || 'Failed to delete supplier');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: error.message || 'Failed to delete supplier. Please try again.',
              confirmButtonText: 'OK'
            });
          });
        }
      });
    });
  });

  // Search filter functionality
  const searchInput = document.getElementById('searchSuppliers');
  const suppliersTable = document.getElementById('suppliersTable');
  if (searchInput && suppliersTable) {
    searchInput.addEventListener('input', function() {
      const term = searchInput.value.toLowerCase();
      const rows = suppliersTable.querySelectorAll('tbody tr');
      rows.forEach(row => {
        // Skip empty state row
        if (row.cells.length < 7) return;

        // Search by supplier name, email, contact person, phone
        const name = row.cells[2].textContent.toLowerCase();
        const email = row.cells[3].textContent.toLowerCase();
        const contact = row.cells[4].textContent.toLowerCase();
        const phone = row.cells[5].textContent.toLowerCase();

        if (
          name.includes(term) ||
          email.includes(term) ||
          contact.includes(term) ||
          phone.includes(term)
        ) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
});
