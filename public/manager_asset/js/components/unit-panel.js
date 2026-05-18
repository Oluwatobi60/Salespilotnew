/**
 * Unit Panel Component
 * Reusable component for adding new units
 *
 * Usage:
 * 1. Include this script in your page
 * 2. Include the unit-panel Blade component
 * 3. Call UnitPanel.init() after DOM is ready
 * 4. Add "+ Add New Unit" option to your select with value="add_new_unit"
 */

const UnitPanel = {
  // Elements
  elements: {
    unitSelect: null,
    unitPanel: null,
    unitOverlay: null,
    closeUnitPanel: null,
    cancelUnitBtn: null,
    addUnitForm: null,
    saveUnitBtn: null,
    unitNameInput: null,
    unitAbbreviationInput: null,
    unitNameError: null,
    unitAbbreviationError: null
  },

  /**
   * Initialize the unit panel
   * @param {string} unitSelectId - ID of the unit select element (default: 'unit')
   */
  init: function(unitSelectId = 'unit') {
    this.elements.unitSelect = document.getElementById(unitSelectId);
    this.elements.unitPanel = document.getElementById('addUnitPanel');
    this.elements.unitOverlay = document.getElementById('unitPanelOverlay');
    this.elements.closeUnitPanel = document.getElementById('closeUnitPanel');
    this.elements.cancelUnitBtn = document.getElementById('cancelUnitBtn');
    this.elements.addUnitForm = document.getElementById('addUnitForm');
    this.elements.saveUnitBtn = document.getElementById('saveUnitBtn');
    this.elements.unitNameInput = document.getElementById('newUnitName');
    this.elements.unitAbbreviationInput = document.getElementById('newUnitAbbreviation');
    this.elements.unitNameError = document.getElementById('unitNameError');
    this.elements.unitAbbreviationError = document.getElementById('unitAbbreviationError');

    this.attachEventListeners();
  },

  /**
   * Open the unit panel
   */
  open: function() {
    if (this.elements.unitPanel && this.elements.unitOverlay) {
      this.elements.unitPanel.classList.add('active');
      this.elements.unitOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';

      setTimeout(() => {
        this.elements.unitNameInput?.focus();
      }, 300);
    }
  },

  /**
   * Close the unit panel
   */
  close: function() {
    if (this.elements.unitPanel && this.elements.unitOverlay) {
      this.elements.unitPanel.classList.remove('active');
      this.elements.unitOverlay.classList.remove('active');
      document.body.style.overflow = '';

      // Reset form
      if (this.elements.addUnitForm) {
        this.elements.addUnitForm.reset();
        this.elements.unitNameInput?.classList.remove('is-invalid');
        this.elements.unitAbbreviationInput?.classList.remove('is-invalid');
        if (this.elements.unitNameError) {
          this.elements.unitNameError.textContent = '';
        }
        if (this.elements.unitAbbreviationError) {
          this.elements.unitAbbreviationError.textContent = '';
        }
      }
    }
  },

  /**
   * Attach event listeners
   */
  attachEventListeners: function() {
    const self = this;

    // Unit select change handler
    if (this.elements.unitSelect) {
      this.elements.unitSelect.addEventListener('change', function() {
        if (this.value === 'add_new_unit') {
          this.value = '';
          self.open();
        }
      });
    }

    // Close panel event listeners
    if (this.elements.closeUnitPanel) {
      this.elements.closeUnitPanel.addEventListener('click', () => this.close());
    }

    if (this.elements.cancelUnitBtn) {
      this.elements.cancelUnitBtn.addEventListener('click', () => this.close());
    }

    if (this.elements.unitOverlay) {
      this.elements.unitOverlay.addEventListener('click', () => this.close());
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.elements.unitPanel?.classList.contains('active')) {
        this.close();
      }
    });

    // Form submission
    if (this.elements.addUnitForm) {
      this.elements.addUnitForm.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    // Clear validation on input
    if (this.elements.unitNameInput) {
      this.elements.unitNameInput.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        self.elements.unitNameError.textContent = '';
      });
    }

    if (this.elements.unitAbbreviationInput) {
      this.elements.unitAbbreviationInput.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        self.elements.unitAbbreviationError.textContent = '';
      });
    }
  },

  /**
   * Handle form submission
   */
  handleSubmit: function(e) {
    e.preventDefault();

    if (this.elements.saveUnitBtn.disabled) return;

    const unitName = this.elements.unitNameInput.value.trim();
    const abbreviation = this.elements.unitAbbreviationInput.value.trim();

    // Validation
    if (!unitName) {
      this.showError('unitName', 'Please enter a unit name');
      return;
    }

    if (unitName.length < 2) {
      this.showError('unitName', 'Unit name must be at least 2 characters');
      return;
    }

    if (unitName.length > 50) {
      this.showError('unitName', 'Unit name must not exceed 50 characters');
      return;
    }

    if (!abbreviation) {
      this.showError('abbreviation', 'Please enter an abbreviation');
      return;
    }

    if (abbreviation.length < 1) {
      this.showError('abbreviation', 'Abbreviation must be at least 1 character');
      return;
    }

    if (abbreviation.length > 10) {
      this.showError('abbreviation', 'Abbreviation must not exceed 10 characters');
      return;
    }

    // Disable button and show loading state
    this.elements.saveUnitBtn.disabled = true;
    this.elements.saveUnitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('input[name="_token"]')?.value;

    // Send AJAX request
    fetch('/manager/unit/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: unitName,
        abbreviation: abbreviation
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
      this.elements.saveUnitBtn.disabled = false;
      this.elements.saveUnitBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save';
    });
  },

  /**
   * Show validation error
   */
  showError: function(field, message) {
    if (field === 'unitName') {
      this.elements.unitNameInput.classList.add('is-invalid');
      this.elements.unitNameError.textContent = message;
    } else if (field === 'abbreviation') {
      this.elements.unitAbbreviationInput.classList.add('is-invalid');
      this.elements.unitAbbreviationError.textContent = message;
    }
  },

  /**
   * Handle successful unit creation
   */
  handleSuccess: function(data) {
    // Create new option element
    const newOption = document.createElement('option');
    newOption.value = data.unit.id;
    newOption.textContent = data.unit.name;
    newOption.selected = true;

    // Insert before the "Add New Unit" option
    const addNewOption = this.elements.unitSelect.querySelector('option[value="add_new_unit"]');
    if (addNewOption) {
      this.elements.unitSelect.insertBefore(newOption, addNewOption);
    } else {
      this.elements.unitSelect.appendChild(newOption);
    }

    // Update Select2 if available
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#' + this.elements.unitSelect.id).val(data.unit.id).trigger('change');
    }

    // Close panel
    this.close();

    // Show success message
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Unit created successfully',
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      alert('Unit created successfully');
    }
  },

  /**
   * Handle error
   */
  handleError: function(error) {
    console.error('Error:', error);

    if (error.isValidation && error.errors) {
      // Handle validation errors for specific fields
      if (error.errors.name) {
        this.showError('unitName', error.errors.name[0]);
      }
      if (error.errors.abbreviation) {
        this.showError('abbreviation', error.errors.abbreviation[0]);
      }

      // If no specific field errors, show general error
      if (!error.errors.name && !error.errors.abbreviation) {
        const errorMessages = Object.values(error.errors).flat();
        this.showError('unitName', errorMessages[0] || 'Validation error occurred');
      }
    } else {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || 'Failed to create unit. Please try again.',
        });
      } else {
        alert('Failed to create unit. Please try again.');
      }
    }
  }
};

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('unit')) {
    UnitPanel.init('unit');
  }
});
