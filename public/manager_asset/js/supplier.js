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

      // Fill edit modal form with fetched data
      document.getElementById('editSupplierId').value = supplierId;
      document.getElementById('edit_supplier_name').value = supplierName;
      document.getElementById('edit_email').value = supplierEmail;
      document.getElementById('edit_contact_person').value = supplierContact || '';
      document.getElementById('edit_phone').value = supplierPhone || '';
      document.getElementById('edit_address').value = supplierAddress || '';

      // Set form action with supplier ID
      const form = document.getElementById('editSupplierForm');
      form.action = '/manager/update_supplier/' + supplierId;

      // Show edit modal
      const modal = new bootstrap.Modal(document.getElementById('editSupplierModal'));
      modal.show();
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
