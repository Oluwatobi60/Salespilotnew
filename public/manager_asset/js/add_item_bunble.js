
      // Close modal and return to previous page
      function closeModal() {
        const modalContainer = document.querySelector('.modal-container');
        const modalOverlay = document.querySelector('.modal-overlay');
        
        // Add slide out animation
        modalContainer.style.animation = 'slideOut 0.3s cubic-bezier(0.55, 0.085, 0.68, 0.53) forwards';
        modalOverlay.style.animation = 'fadeOut 0.3s ease-out forwards';
        
        // Redirect after animation
        setTimeout(function() {
          // Check if there's a previous page in history
          if (document.referrer && document.referrer !== window.location.href) {
            // Go back to the previous page
            window.history.back();
          } else {
            // Fallback to dashboard if no referrer
            window.location.href = '../index.php';
          }
        }, 300);
      }

      // Add slide out animation keyframe
      const style = document.createElement('style');
      style.textContent = `
        @keyframes slideOut {
          to {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 0;
          }
        }
        @keyframes fadeOut {
          to {
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);

      // Close on escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeModal();
        }
      });



      // Close on overlay click
      document.querySelector('.modal-overlay').addEventListener('click', closeModal);

      // Update item information when product is selected
      function updateItemInfo(selectElement) {
        const row = selectElement.closest('tr');
        const option = selectElement.options[selectElement.selectedIndex];
        const stock = option.getAttribute('data-stock') || '0';
        const cost = option.getAttribute('data-cost') || '0';
        
        // Update stock and cost fields
        row.querySelector('.available-stock').value = stock;
        row.querySelector('.unit-cost').value = parseFloat(cost).toFixed(2);
        
        // Calculate subtotal
        calculateSubtotal(row.querySelector('.bundle-quantity'));
        
        // Update maximum possible bundles
        updateMaxPossibleBundles();
        
        // Calculate individual items total for pricing comparison
        calculateIndividualTotal();
      }

      // Calculate subtotal for a bundle item row
      function calculateSubtotal(quantityInput) {
        const row = quantityInput.closest('tr');
        const quantity = parseInt(quantityInput.value) || 0;
        const unitCost = parseFloat(row.querySelector('.unit-cost').value) || 0;
        const subtotal = quantity * unitCost;
        
        row.querySelector('.subtotal').value = subtotal.toFixed(2);
        
        // Update total bundle cost
        calculateBundlePricing();
        
        // Update maximum possible bundles
        updateMaxPossibleBundles();
      }

      // Add new bundle item row
      function addBundleItem() {
        const tbody = document.getElementById('bundleItemsBody');
        const newRow = document.createElement('tr');
        newRow.className = 'bundle-item-row';
        newRow.innerHTML = `
          <td>
            <select class="form-select product-select" name="bundle_items[]" onchange="updateItemInfo(this)" required>
              <option value="">Select Product</option>
              <option value="1" data-cost="500" data-stock="50">Wireless Mouse</option>
              <option value="2" data-cost="1200" data-stock="30">Keyboard</option>
              <option value="3" data-cost="800" data-stock="25">Mouse Pad</option>
              <option value="4" data-cost="2500" data-stock="15">Headset</option>
            </select>
          </td>
          <td>
            <input type="text" class="form-control available-stock" placeholder="0" readonly>
          </td>
          <td>
            <input type="number" class="form-control bundle-quantity" name="bundle_quantities[]" placeholder="1" min="1" value="1" onchange="calculateSubtotal(this)" required>
          </td>
          <td>
            <div class="input-group">
              <span class="input-group-text">₦</span>
              <input type="text" class="form-control unit-cost" placeholder="0.00" readonly>
            </div>
          </td>
          <td>
            <div class="input-group">
              <span class="input-group-text">₦</span>
              <input type="text" class="form-control subtotal" placeholder="0.00" readonly>
            </div>
          </td>
          <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeBundleItem(this)" title="Remove Item">
              <i class="mdi mdi-delete"></i>
            </button>
          </td>
        `;
        tbody.appendChild(newRow);
        
        // Initialize Select2 for new row
        if (typeof $ !== 'undefined' && $.fn.select2) {
          $(newRow).find('.product-select').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Product'
          });
        }
        
        showNotification('New item row added to bundle', 'success');
      }

      // Remove bundle item row
      function removeBundleItem(button) {
        const tbody = document.getElementById('bundleItemsBody');
        const row = button.closest('tr');
        
        if (tbody.children.length > 1) {
          row.classList.add('removing');
          setTimeout(() => {
            row.remove();
            calculateBundlePricing();
            updateMaxPossibleBundles();
            calculateIndividualTotal();
            showNotification('Item removed from bundle', 'info');
          }, 300);
        } else {
          showNotification('At least one item is required for a bundle', 'warning');
        }
      }

      // Calculate bundle pricing and margins
      function calculateBundlePricing() {
        const subtotalInputs = document.querySelectorAll('.subtotal');
        const assemblyFee = parseFloat(document.getElementById('assemblyFee').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('bundleSellingPrice').value) || 0;
        
        let totalItemCost = 0;
        subtotalInputs.forEach(input => {
          totalItemCost += parseFloat(input.value) || 0;
        });
        
        const totalBundleCost = totalItemCost + assemblyFee;
        const profit = sellingPrice - totalBundleCost;
        const margin = totalBundleCost > 0 ? (profit / totalBundleCost) * 100 : 0;
        
        // Update display
        document.getElementById('totalItemCost').textContent = '₦' + totalItemCost.toFixed(2);
        document.getElementById('totalBundleCost').textContent = '₦' + totalBundleCost.toFixed(2);
        document.getElementById('bundleMargin').value = margin.toFixed(2) + '%';
        document.getElementById('bundleProfit').value = profit.toFixed(2);
        
        // Calculate customer savings
        calculateCustomerSavings();
      }

      // Calculate individual items total for comparison
      function calculateIndividualTotal() {
        // This would typically fetch individual selling prices from database
        // For now, assuming 30% markup on cost as example
        const subtotalInputs = document.querySelectorAll('.subtotal');
        let individualTotal = 0;
        
        subtotalInputs.forEach(input => {
          const cost = parseFloat(input.value) || 0;
          individualTotal += cost * 1.3; // 30% markup example
        });
        
        document.getElementById('individualTotal').value = individualTotal.toFixed(2);
        calculateCustomerSavings();
      }

      // Calculate customer savings
      function calculateCustomerSavings() {
        const individualTotal = parseFloat(document.getElementById('individualTotal').value) || 0;
        const bundlePrice = parseFloat(document.getElementById('bundleSellingPrice').value) || 0;
        const savings = individualTotal - bundlePrice;
        
        document.getElementById('bundleSavings').value = savings.toFixed(2);
        
        // Update savings display color
        const savingsInput = document.getElementById('bundleSavings');
        if (savings > 0) {
          savingsInput.style.color = '#28a745';
          savingsInput.style.fontWeight = 'bold';
        } else if (savings < 0) {
          savingsInput.style.color = '#dc3545';
          savingsInput.style.fontWeight = 'bold';
        } else {
          savingsInput.style.color = '#6c757d';
          savingsInput.style.fontWeight = 'normal';
        }
      }

      // Update maximum possible bundles based on stock levels
      function updateMaxPossibleBundles() {
        const rows = document.querySelectorAll('.bundle-item-row');
        let maxBundles = Infinity;
        
        rows.forEach(row => {
          const productSelect = row.querySelector('.product-select');
          const quantityInput = row.querySelector('.bundle-quantity');
          const stockInput = row.querySelector('.available-stock');
          
          if (productSelect.value && quantityInput.value) {
            const availableStock = parseInt(stockInput.value) || 0;
            const quantityNeeded = parseInt(quantityInput.value) || 1;
            const possibleBundles = Math.floor(availableStock / quantityNeeded);
            maxBundles = Math.min(maxBundles, possibleBundles);
          }
        });
        
        const maxBundlesDisplay = maxBundles === Infinity ? '0' : maxBundles.toString();
        document.getElementById('maxPossibleBundles').value = maxBundlesDisplay;
        
        // Validate initial stock against maximum possible
        const initialStock = document.getElementById('initialBundleStock');
        if (parseInt(initialStock.value) > maxBundles && maxBundles !== Infinity) {
          initialStock.style.borderColor = '#dc3545';
          showNotification(`Initial stock cannot exceed ${maxBundles} bundles based on available inventory`, 'warning');
        } else {
          initialStock.style.borderColor = '#28a745';
        }
      }

      // Reset form
      function resetForm() {
        document.getElementById('addBundleForm').reset();
        
        // Reset calculated fields
        document.getElementById('totalItemCost').textContent = '₦0.00';
        document.getElementById('totalBundleCost').textContent = '₦0.00';
        document.getElementById('bundleMargin').value = '0%';
        document.getElementById('bundleProfit').value = '0.00';
        document.getElementById('individualTotal').value = '0.00';
        document.getElementById('bundleSavings').value = '0.00';
        document.getElementById('maxPossibleBundles').value = '0';
        
        // Reset bundle items table to single row
        const tbody = document.getElementById('bundleItemsBody');
        while (tbody.children.length > 1) {
          tbody.removeChild(tbody.lastChild);
        }
        
        // Clear first row
        const firstRow = tbody.querySelector('.bundle-item-row');
        firstRow.querySelector('.product-select').value = '';
        firstRow.querySelector('.available-stock').value = '';
        firstRow.querySelector('.bundle-quantity').value = '1';
        firstRow.querySelector('.unit-cost').value = '';
        firstRow.querySelector('.subtotal').value = '';
        
        showNotification('Form has been reset', 'info');
      }

      // Submit form with validation
      function submitForm() {
        const form = document.getElementById('addBundleForm');
        const submitBtn = document.querySelector('.btn-primary');
        
        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        setTimeout(() => {
          // Validate bundle items
          const productSelects = document.querySelectorAll('.product-select');
          const validItems = Array.from(productSelects).filter(select => select.value !== '');
          
          if (validItems.length < 2) {
            showNotification('A bundle must contain at least 2 different items', 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          // Check for duplicate items
          const selectedProducts = validItems.map(select => select.value);
          const uniqueProducts = [...new Set(selectedProducts)];
          if (selectedProducts.length !== uniqueProducts.length) {
            showNotification('Cannot have duplicate items in a bundle. Use quantity instead.', 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          // Validate stock levels
          const initialStock = parseInt(document.getElementById('initialBundleStock').value) || 0;
          const maxPossible = parseInt(document.getElementById('maxPossibleBundles').value) || 0;
          
          if (initialStock > maxPossible) {
            showNotification(`Initial stock (${initialStock}) cannot exceed maximum possible bundles (${maxPossible})`, 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          // Validate selling price
          const sellingPrice = parseFloat(document.getElementById('bundleSellingPrice').value) || 0;
          const totalCost = parseFloat(document.getElementById('totalBundleCost').textContent.replace('₦', '')) || 0;
          
          if (sellingPrice <= 0) {
            showNotification('Bundle selling price must be greater than zero', 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          if (sellingPrice <= totalCost) {
            showNotification('Warning: Selling price should be greater than total cost for profit!', 'warning');
            // Don't return false, just warn
          }
          
          // Check if required fields are filled
          if (!form.checkValidity()) {
            form.reportValidity();
            removeLoading(submitBtn);
            return false;
          }
          
          // If all validations pass
          showNotification('Bundle validation passed! Ready to be saved.', 'success');
          
          // Simulate save process
          setTimeout(() => {
            removeLoading(submitBtn);
            showNotification('Bundle successfully created!', 'success');
            
            // Remove beforeunload warning since bundle is saved
            window.removeEventListener('beforeunload', arguments.callee);
            
            // After successful save, return to previous page
            setTimeout(() => {
              // Check if there's a previous page in history
              if (document.referrer && document.referrer !== window.location.href) {
                // Go back to the previous page
                window.history.back();
              } else {
                // Fallback to dashboard if no referrer
                window.location.href = '../index.php';
              }
            }, 1000); // Give time to see the success message
            
            // Uncomment the line below when you have the backend ready
            // form.submit();
          }, 1500);
        }, 800);
      }

      // Helper function to remove loading state
      function removeLoading(button) {
        button.classList.remove('loading');
        button.disabled = false;
      }

      // Smart notification system
      function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotification = document.querySelector('.smart-notification');
        if (existingNotification) {
          existingNotification.remove();
        }

        const notification = document.createElement('div');
        notification.className = `smart-notification alert alert-${type === 'error' ? 'danger' : type}`;
        notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          z-index: 10000;
          min-width: 300px;
          max-width: 500px;
          padding: 15px 20px;
          border-radius: 10px;
          box-shadow: 0 8px 25px rgba(0,0,0,0.2);
          transform: translateX(100%);
          transition: all 0.3s ease;
        `;

        const icon = type === 'success' ? 'mdi-check-circle' : 
                    type === 'warning' ? 'mdi-alert-circle' : 
                    type === 'error' ? 'mdi-close-circle' : 'mdi-information';

        notification.innerHTML = `
          <i class="mdi ${icon} me-2"></i>
          ${message}
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
          notification.style.transform = 'translateX(0)';
        }, 100);

        // Auto remove after 4 seconds
        setTimeout(() => {
          notification.style.transform = 'translateX(100%)';
          setTimeout(() => notification.remove(), 300);
        }, 4000);
      }

      // Event listeners and form initialization
      document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate bundle code
        document.getElementById('bundleName').addEventListener('input', function() {
          const bundleCode = document.getElementById('bundleCode');
          if (!bundleCode.value) {
            const name = this.value.replace(/\s+/g, '').toUpperCase();
            const timestamp = Date.now().toString().slice(-4);
            bundleCode.value = 'BDL-' + name.slice(0, 6) + timestamp;
          }
        });

        // Initialize Select2 for better dropdowns
        if (typeof $ !== 'undefined' && $.fn.select2) {
          $('#category, #supplier, #unit, #taxRate').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select an option'
          });
          
          $('.product-select').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select Product'
          });
        }

        // Bundle pricing event listeners
        document.getElementById('assemblyFee').addEventListener('input', calculateBundlePricing);
        document.getElementById('bundleSellingPrice').addEventListener('input', calculateBundlePricing);
        document.getElementById('initialBundleStock').addEventListener('input', updateMaxPossibleBundles);

        // Enhanced image upload with preview
        document.getElementById('bundleImage').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            if (file.size > 2 * 1024 * 1024) {
              showNotification('File size must be less than 2MB', 'error');
              this.value = '';
              return;
            }
            showNotification('Bundle image selected successfully', 'success');
          }
        });

        // Form validation visual feedback
        const formInputs = document.querySelectorAll('.form-control, .form-select');
        formInputs.forEach(input => {
          input.addEventListener('blur', function() {
            if (this.required && !this.value.trim()) {
              this.closest('.form-group').classList.add('error');
              this.closest('.form-group').classList.remove('success');
            } else if (this.value.trim()) {
              this.closest('.form-group').classList.add('success');
              this.closest('.form-group').classList.remove('error');
            }
          });
        });

        // Show welcome message
        showNotification('Ready to create a new bundled item for your inventory!', 'info');
      });
   