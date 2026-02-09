/**
 * Category Panel Component
 * Reusable component for adding new categories
 *
 * Usage:
 * 1. Include this script in your page
 * 2. Include the category-panel Blade component
 * 3. Call CategoryPanel.init() after DOM is ready
 * 4. Add "+ Add New Category" option to your select with value="add_new_category"
 */

const CategoryPanel = {
  // Elements
  elements: {
    categorySelect: null,
    categoryPanel: null,
    categoryOverlay: null,
    closeCategoryPanel: null,
    cancelCategoryBtn: null,
    addCategoryForm: null,
    saveCategoryBtn: null,
    categoryNameInput: null,
    categoryNameError: null,
    newCategoryNameInput: null
  },

  /**
   * Initialize the category panel
   * @param {string} categorySelectId - ID of the category select element (default: 'category')
   */
  init: function(categorySelectId = 'category') {
    this.elements.categorySelect = document.getElementById(categorySelectId);
    this.elements.categoryPanel = document.getElementById('addCategoryPanel');
    this.elements.categoryOverlay = document.getElementById('categoryPanelOverlay');
    this.elements.closeCategoryPanel = document.getElementById('closeCategoryPanel');
    this.elements.cancelCategoryBtn = document.getElementById('cancelCategoryBtn');
    this.elements.addCategoryForm = document.getElementById('addCategoryForm');
    this.elements.saveCategoryBtn = document.getElementById('saveCategoryBtn');
    this.elements.categoryNameInput = document.getElementById('newCategoryName');
    this.elements.categoryNameError = document.getElementById('categoryNameError');
    this.elements.newCategoryNameInput = document.getElementById('newCategoryName');

    this.attachEventListeners();
  },

  /**
   * Open the category panel
   */
  open: function() {
    if (this.elements.categoryPanel && this.elements.categoryOverlay) {
      this.elements.categoryPanel.classList.add('active');
      this.elements.categoryOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';

      setTimeout(() => {
        this.elements.categoryNameInput?.focus();
      }, 300);
    }
  },

  /**
   * Close the category panel
   */
  close: function() {
    if (this.elements.categoryPanel && this.elements.categoryOverlay) {
      this.elements.categoryPanel.classList.remove('active');
      this.elements.categoryOverlay.classList.remove('active');
      document.body.style.overflow = '';

      // Reset form
      if (this.elements.addCategoryForm) {
        this.elements.addCategoryForm.reset();
        this.elements.categoryNameInput?.classList.remove('is-invalid');
        if (this.elements.categoryNameError) {
          this.elements.categoryNameError.textContent = '';
        }
      }
    }
  },

  /**
   * Attach event listeners
   */
  attachEventListeners: function() {
    const self = this;

    // Category select change handler
    if (this.elements.categorySelect) {
      this.elements.categorySelect.addEventListener('change', function() {
        if (this.value === 'add_new_category') {
          this.value = '';
          self.open();
        }
      });
    }

    // Close panel event listeners
    if (this.elements.closeCategoryPanel) {
      this.elements.closeCategoryPanel.addEventListener('click', () => this.close());
    }

    if (this.elements.cancelCategoryBtn) {
      this.elements.cancelCategoryBtn.addEventListener('click', () => this.close());
    }

    if (this.elements.categoryOverlay) {
      this.elements.categoryOverlay.addEventListener('click', () => this.close());
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.elements.categoryPanel?.classList.contains('active')) {
        this.close();
      }
    });

    // Form submission
    if (this.elements.addCategoryForm) {
      this.elements.addCategoryForm.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    // Clear validation on input
    if (this.elements.newCategoryNameInput) {
      this.elements.newCategoryNameInput.addEventListener('input', function() {
        this.classList.remove('is-invalid');
        self.elements.categoryNameError.textContent = '';
      });
    }
  },

  /**
   * Handle form submission
   */
  handleSubmit: function(e) {
    e.preventDefault();

    if (this.elements.saveCategoryBtn.disabled) return;

    const categoryName = this.elements.categoryNameInput.value.trim();

    // Validation
    if (!categoryName) {
      this.showError('Please enter a category name');
      return;
    }

    if (categoryName.length < 5) {
      this.showError('Category name must be at least 5 characters');
      return;
    }

    if (categoryName.length > 100) {
      this.showError('Category name must not exceed 100 characters');
      return;
    }

    // Disable button and show loading state
    this.elements.saveCategoryBtn.disabled = true;
    this.elements.saveCategoryBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     document.querySelector('input[name="_token"]')?.value;

    // Send AJAX request
    fetch('/manager/category/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ category_name: categoryName })
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
      this.elements.saveCategoryBtn.disabled = false;
      this.elements.saveCategoryBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save';
    });
  },

  /**
   * Show validation error
   */
  showError: function(message) {
    this.elements.categoryNameInput.classList.add('is-invalid');
    this.elements.categoryNameError.textContent = message;
  },

  /**
   * Handle successful category creation
   */
  handleSuccess: function(data) {
    // Create new option element
    const newOption = document.createElement('option');
    newOption.value = data.category.id;
    newOption.textContent = data.category.category_name;
    newOption.selected = true;

    // Insert before the "Add New Category" option
    const addNewOption = this.elements.categorySelect.querySelector('option[value="add_new_category"]');
    if (addNewOption) {
      this.elements.categorySelect.insertBefore(newOption, addNewOption);
    } else {
      this.elements.categorySelect.appendChild(newOption);
    }

    // Update Select2 if available
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#' + this.elements.categorySelect.id).val(data.category.id).trigger('change');
    }

    // Close panel
    this.close();

    // Show success message
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Category created successfully',
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      alert('Category created successfully');
    }
  },

  /**
   * Handle error
   */
  handleError: function(error) {
    console.error('Error:', error);

    if (error.isValidation && error.errors) {
      const errorMessages = Object.values(error.errors).flat();
      this.showError(errorMessages[0] || 'Validation error occurred');
    } else {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || 'An error occurred while creating the category'
        });
      } else {
        alert('Error: ' + (error.message || 'An error occurred'));
      }
    }
  }
};

// Auto-initialize if jQuery is available (for backwards compatibility)
if (typeof $ !== 'undefined') {
  $(document).ready(function() {
    if (document.getElementById('addCategoryPanel')) {
      CategoryPanel.init();
    }
  });
}
