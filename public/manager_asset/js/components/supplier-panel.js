/**
 * Supplier Panel Component
 * Reusable supplier creation panel for item forms
 */

const SupplierPanel = {
  elements: {},

  /**
   * Initialize the supplier panel
   * @param {string} supplierSelectId - ID of the supplier select dropdown
   */
  init: function(supplierSelectId = 'supplier') {
    console.log('SupplierPanel.init() - Starting initialization for select:', supplierSelectId);

    // Get all elements
    this.elements = {
      supplierSelect: document.getElementById(supplierSelectId),
      panel: document.getElementById('addSupplierPanel'),
      overlay: document.getElementById('supplierPanelOverlay'),
      closeBtn: document.getElementById('closeSupplierPanel'),
      cancelBtn: document.getElementById('cancelSupplierBtn'),
      saveBtn: document.getElementById('saveSupplierBtn'),
      form: document.getElementById('addSupplierForm'),
      nameInput: document.getElementById('newSupplierName'),
      emailInput: document.getElementById('newSupplierEmail'),
      contactInput: document.getElementById('newSupplierContact'),
      phoneInput: document.getElementById('newSupplierPhone'),
      addressInput: document.getElementById('newSupplierAddress'),
      nameError: document.getElementById('supplierNameError'),
      emailError: document.getElementById('supplierEmailError')
    };

    // Verify required elements exist
    if (!this.elements.panel) {
      console.error('SupplierPanel: Panel element not found!');
      return false;
    }

    if (!this.elements.supplierSelect) {
      console.error('SupplierPanel: Supplier select element not found!', supplierSelectId);
      return false;
    }

    console.log('SupplierPanel - Elements found, attaching event listeners');

    // Attach event listeners
    this.attachEventListeners();

    console.log('SupplierPanel - Initialization complete');
    return true;
  },

  /**
   * Attach all event listeners
   */
  attachEventListeners: function() {
    const self = this;

    // Supplier select change event
    if (this.elements.supplierSelect) {
      this.elements.supplierSelect.addEventListener('change', function() {
        if (this.value === 'add_new_supplier') {
          this.value = '';
          self.open();
        }
      });
    }

    // Close button events
    if (this.elements.closeBtn) {
      this.elements.closeBtn.addEventListener('click', () => this.close());
    }

    if (this.elements.cancelBtn) {
      this.elements.cancelBtn.addEventListener('click', () => this.close());
    }

    if (this.elements.overlay) {
      this.elements.overlay.addEventListener('click', () => this.close());
    }

    // Escape key to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.elements.panel?.classList.contains('active')) {
        this.close();
      }
    });

    // Form submission
    if (this.elements.form) {
      this.elements.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    // Clear validation on input
    if (this.elements.nameInput) {
      this.elements.nameInput.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        self.elements.nameError.textContent = '';
      });
    }

    if (this.elements.emailInput) {
      this.elements.emailInput.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        self.elements.emailError.textContent = '';
      });
    }
  },

  /**
   * Open the supplier panel
   */
  open: function() {
    console.log('SupplierPanel - Opening panel');

    if (this.elements.panel && this.elements.overlay) {
      this.elements.panel.classList.add('active');
      this.elements.overlay.classList.add('active');
      document.body.style.overflow = 'hidden';

      // Focus on name input after animation
      setTimeout(() => {
        this.elements.nameInput?.focus();
      }, 300);
    }
  },

  /**
   * Close the supplier panel
   */
  close: function() {
    console.log('SupplierPanel - Closing panel');

    if (this.elements.panel && this.elements.overlay) {
      this.elements.panel.classList.remove('active');
      this.elements.overlay.classList.remove('active');
      document.body.style.overflow = '';

      // Reset form
      if (this.elements.form) {
        this.elements.form.reset();

        // Clear validation errors
        document.querySelectorAll('#addSupplierForm .form-control').forEach(input => {
          input.classList.remove('is-invalid');
        });

        if (this.elements.nameError) this.elements.nameError.textContent = '';
        if (this.elements.emailError) this.elements.emailError.textContent = '';
      }
    }
  },

  /**
   * Handle form submission
   */
  handleSubmit: function(e) {
    e.preventDefault();

    // Check if button is already disabled
    if (this.elements.saveBtn.disabled) {
      return;
    }

    // Get values
    const supplierName = this.elements.nameInput.value.trim();
    const supplierEmail = this.elements.emailInput.value.trim();
    const supplierContact = this.elements.contactInput.value.trim();
    const supplierPhone = this.elements.phoneInput.value.trim();
    const supplierAddress = this.elements.addressInput.value.trim();

    // Validation
    let hasError = false;

    if (!supplierName) {
      this.elements.nameInput.classList.add('is-invalid');
      this.elements.nameError.textContent = 'Supplier name is required';
      hasError = true;
    }

    if (!supplierEmail) {
      this.elements.emailInput.classList.add('is-invalid');
      this.elements.emailError.textContent = 'Email address is required';
      hasError = true;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(supplierEmail)) {
      this.elements.emailInput.classList.add('is-invalid');
      this.elements.emailError.textContent = 'Please enter a valid email address';
      hasError = true;
    }

    if (hasError) {
      return;
    }

    // Show loading state
    this.elements.saveBtn.disabled = true;
    this.elements.saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                     || document.querySelector('input[name="_token"]')?.value;

    // Send AJAX request
    fetch('/manager/supplier/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: supplierName,
        email: supplierEmail,
        contact_person: supplierContact,
        phone: supplierPhone,
        address: supplierAddress
      })
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(data => {
          throw { isValidation: true, errors: data.errors || {}, message: data.message };
        });
      }
      return response.json();
    })
    .then(data => this.handleSuccess(data))
    .catch(error => this.handleError(error))
    .finally(() => {
      // Re-enable button
      this.elements.saveBtn.disabled = false;
      this.elements.saveBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save';
    });
  },

  /**
   * Handle successful supplier creation
   */
  handleSuccess: function(data) {
    console.log('SupplierPanel - Supplier created successfully:', data.supplier);

    // Add new supplier to dropdown
    const newOption = document.createElement('option');
    newOption.value = data.supplier.id;
    newOption.textContent = data.supplier.name;
    newOption.selected = true;

    // Insert before "Add New Supplier" option
    const addNewOption = this.elements.supplierSelect.querySelector('option[value="add_new_supplier"]');
    if (addNewOption) {
      this.elements.supplierSelect.insertBefore(newOption, addNewOption);
    } else {
      this.elements.supplierSelect.appendChild(newOption);
    }

    // Trigger change event for Select2
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $(this.elements.supplierSelect).trigger('change');
    }

    // Close panel
    this.close();

    // Show success message
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Supplier created successfully',
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      alert('Supplier created successfully');
    }
  },

  /**
   * Handle errors
   */
  handleError: function(error) {
    console.error('SupplierPanel - Error:', error);

    if (error.isValidation && error.errors) {
      // Handle validation errors
      if (error.errors.name) {
        this.elements.nameInput.classList.add('is-invalid');
        this.elements.nameError.textContent = error.errors.name[0];
      }
      if (error.errors.email) {
        this.elements.emailInput.classList.add('is-invalid');
        this.elements.emailError.textContent = error.errors.email[0];
      }
    } else {
      // Handle general errors
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || 'An error occurred while creating the supplier'
        });
      } else {
        alert('Error: ' + (error.message || 'An error occurred'));
      }
    }
  }
};

// Auto-initialize if panel exists when DOM is ready
if (typeof jQuery !== 'undefined') {
  jQuery(document).ready(function() {
    if (document.getElementById('addSupplierPanel')) {
      console.log('SupplierPanel - Auto-initializing');
      SupplierPanel.init('supplier');
    }
  });
}
