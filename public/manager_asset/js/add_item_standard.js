
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

      // Calculate profit margin and profit amounts automatically
      function calculateProfitMargin() {
        const costPrice = parseFloat(document.getElementById('costPrice').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('sellingPrice').value) || 0;
        
        if (costPrice > 0 && sellingPrice > 0) {
          const profit = sellingPrice - costPrice;
          const margin = (profit / costPrice) * 100;
          document.getElementById('profitMargin').value = margin.toFixed(2) + '%';
          
          // Calculate potential profit for fixed pricing
          const potentialProfitField = document.getElementById('potentialProfit');
          if (potentialProfitField) {
            potentialProfitField.value = profit.toFixed(2);
          }
        } else {
          document.getElementById('profitMargin').value = '0%';
          const potentialProfitField = document.getElementById('potentialProfit');
          if (potentialProfitField) {
            potentialProfitField.value = '0.00';
          }
        }
        
        calculateFinalPrice(); 
      }

      // Calculate final price with tax
      function calculateFinalPrice() {
        const sellingPrice = parseFloat(document.getElementById('sellingPrice').value) || 0;
        const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;
        
        let finalPrice = sellingPrice;
        
        // Apply discount
        if (discount > 0) {
          finalPrice = finalPrice - (finalPrice * (discount / 100));
        }
        
        // Apply tax
        if (taxRate > 0) {
          finalPrice = finalPrice + (finalPrice * (taxRate / 100));
        }
        
        document.getElementById('finalPrice').textContent = '₦' + finalPrice.toFixed(2);
      }

      // Reset form
      function resetForm() {
        document.getElementById('addItemForm').reset();
        document.getElementById('profitMargin').value = '0%';
        document.getElementById('finalPrice').textContent = '₦0.00';
        
        // Clear any potential profit field if it exists
        const potentialProfitField = document.getElementById('potentialProfit');
        if (potentialProfitField) {
          potentialProfitField.value = '0.00';
        }
      }

      // Submit form with validation and enhanced UX
      function submitForm() {
        const form = document.getElementById('addItemForm');
        const submitBtn = document.querySelector('.btn-primary');
        
        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // Simulate validation delay for better UX
        setTimeout(() => {
          // Validate that selling price is greater than cost price
          const costPrice = parseFloat(document.getElementById('costPrice').value) || 0;
          const sellingPrice = parseFloat(document.getElementById('sellingPrice').value) || 0;
          
          if (sellingPrice <= costPrice && costPrice > 0) {
            showNotification('Warning: Selling price should be greater than cost price for profit!', 'warning');
            removeLoading(submitBtn);
            return false;
          }
          
          // Validate reorder level is less than max stock
          const reorderLevel = parseFloat(document.getElementById('reorderLevel').value) || 0;
          const maxStock = parseFloat(document.getElementById('maxStock').value) || 0;
          
          if (maxStock > 0 && reorderLevel >= maxStock) {
            showNotification('Error: Reorder level must be less than maximum stock level!', 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          // Check if required fields are filled
          if (!form.checkValidity()) {
            form.reportValidity();
            removeLoading(submitBtn);
            return false;
          }
          
          // If all validations pass
          showNotification('Form validation passed! Item ready to be saved.', 'success');
          
          // Simulate save process
          setTimeout(() => {
            removeLoading(submitBtn);
            showNotification('Item successfully added to inventory!', 'success');
            
            // Remove beforeunload warning since item is saved
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

      // Event listeners and enhanced form initialization
      document.addEventListener('DOMContentLoaded', function() {
        // Price calculation events
        document.getElementById('costPrice').addEventListener('input', calculateProfitMargin);
        document.getElementById('sellingPrice').addEventListener('input', calculateProfitMargin);
        document.getElementById('taxRate').addEventListener('change', calculateFinalPrice);
        document.getElementById('discount').addEventListener('input', calculateFinalPrice);

        // Initialize Select2 for better dropdowns
        if (typeof $ !== 'undefined' && $.fn.select2) {
          $('#category, #supplier, #unit, #taxRate').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select an option'
          });
        }

        // Enhanced image upload with preview
        document.getElementById('itemImage').addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            if (file.size > 2 * 1024 * 1024) {
              showNotification('File size must be less than 2MB', 'error');
              this.value = '';
              return;
            }

            // Show image preview (optional enhancement)
            const reader = new FileReader();
            reader.onload = function(e) {
              // You can add image preview functionality here
              showNotification('Image selected successfully', 'success');
            };
            reader.readAsDataURL(file);
          }
        });

        // Add form validation visual feedback
        const formInputs = document.querySelectorAll('.form-control, .form-select');
        formInputs.forEach(input => {
          input.addEventListener('blur', function() {
            validateField(this);
          });

          input.addEventListener('input', function() {
            // Clear validation state on input
            this.parentElement.classList.remove('success', 'error');
          });
        });

        // Auto-generate item code if not provided
        document.getElementById('itemName').addEventListener('input', function() {
          const itemCode = document.getElementById('itemCode');
          if (!itemCode.value) {
            const name = this.value.replace(/\s+/g, '').toUpperCase();
            const timestamp = Date.now().toString().slice(-4);
            itemCode.value = name.slice(0, 4) + timestamp;
          }
        });

        // Handle custom unit creation
        setupCustomUnitHandlers();

        // Handle pricing methods
        setupPricingMethodHandlers();

        // Initialize with fixed pricing (default selection)
        showPricingFields('fixed');
        showPricingDescription('fixed');

        // Show welcome message
        showNotification('Ready to add a new item to your inventory!', 'info');
      });

      // Field validation function
      function validateField(field) {
        const formGroup = field.closest('.form-group');
        
        if (field.required && !field.value.trim()) {
          formGroup.classList.add('error');
          formGroup.classList.remove('success');
        } else if (field.value.trim()) {
          formGroup.classList.add('success');
          formGroup.classList.remove('error');
        }
      }

      // Setup custom unit handlers
      function setupCustomUnitHandlers() {
        const unitSelect = document.getElementById('unit');
        const customUnitContainer = document.getElementById('customUnitContainer');
        const addUnitBtn = document.getElementById('addUnitBtn');
        const customUnit = document.getElementById('customUnit');
        const customUnitAbbr = document.getElementById('customUnitAbbr');

        // Show/hide custom unit container
        unitSelect.addEventListener('change', function() {
          if (this.value === 'custom') {
            customUnitContainer.style.display = 'block';
            customUnit.focus();
          } else {
            customUnitContainer.style.display = 'none';
            customUnit.value = '';
            customUnitAbbr.value = '';
          }
        });

        // Add new unit functionality
        addUnitBtn.addEventListener('click', function() {
          const unitName = customUnit.value.trim();
          const unitAbbr = customUnitAbbr.value.trim();

          if (!unitName || !unitAbbr) {
            showNotification('Please enter both unit name and abbreviation', 'error');
            return;
          }

          // Check if unit already exists
          const existingOptions = Array.from(unitSelect.options);
          const exists = existingOptions.some(option => 
            option.value.toLowerCase() === unitAbbr.toLowerCase() ||
            option.text.toLowerCase().layouts(unitName.toLowerCase())
          );

          if (exists) {
            showNotification('This unit already exists in the list', 'warning');
            return;
          }

          // Create new option
          const newOption = document.createElement('option');
          newOption.value = unitAbbr;
          newOption.text = `${unitName} (${unitAbbr})`;
          
          // Insert before the "Add New Unit" option
          const customOption = unitSelect.querySelector('option[value="custom"]');
          unitSelect.insertBefore(newOption, customOption);

          // Select the new option
          unitSelect.value = unitAbbr;
          
          // Hide the container and clear inputs
          customUnitContainer.style.display = 'none';
          customUnit.value = '';
          customUnitAbbr.value = '';

          // Update Select2 if initialized
          if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#unit').trigger('change.select2');
          }

          showNotification(`Unit "${unitName} (${unitAbbr})" added successfully!`, 'success');
        });

        // Allow Enter key to add unit
        customUnit.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            customUnitAbbr.focus();
          }
        });

        customUnitAbbr.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            addUnitBtn.click();
          }
        });

        // Auto-generate abbreviation
        customUnit.addEventListener('input', function() {
          if (!customUnitAbbr.value) {
            const abbr = this.value
              .split(' ')
              .map(word => word.charAt(0))
              .join('')
              .toLowerCase()
              .slice(0, 4);
            customUnitAbbr.value = abbr;
          }
        });
      }

      // Setup pricing method handlers
      function setupPricingMethodHandlers() {
        const pricingTypeRadios = document.querySelectorAll('input[name="pricing_type"]');
        const pricingDescription = document.getElementById('pricingDescription');
        const costPrice = document.getElementById('costPrice');
        const targetMargin = document.getElementById('targetMargin');
        const calculatedPrice = document.getElementById('calculatedPrice');

        // Handle pricing type change
        pricingTypeRadios.forEach(radio => {
          radio.addEventListener('change', function() {
            if (this.checked) {
              const selectedType = this.value;
              showPricingFields(selectedType);
              showPricingDescription(selectedType);
            }
          });
        });

        // Handle margin pricing calculations
        if (targetMargin) {
          targetMargin.addEventListener('input', function() {
            calculateMarginPrice();
          });
        }

        costPrice.addEventListener('input', function() {
          const selectedPricingType = document.querySelector('input[name="pricing_type"]:checked');
          if (selectedPricingType) {
            if (selectedPricingType.value === 'margin') {
              calculateMarginPrice();
            } else if (selectedPricingType.value === 'range') {
              calculateRangeProfits();
            } else if (selectedPricingType.value === 'fixed') {
              calculateProfitMargin();
            }
          }
        });

        // Handle range pricing validation and profit calculation
        document.getElementById('minPrice')?.addEventListener('input', function() {
          validatePriceRange();
          calculateRangeProfits();
        });
        document.getElementById('maxPrice')?.addEventListener('input', function() {
          validatePriceRange();
          calculateRangeProfits();
        });
      }

      // Show appropriate pricing fields based on selected type
      function showPricingFields(type) {
        // Hide all pricing fields first
        document.querySelectorAll('.pricing-fields').forEach(field => {
          field.style.display = 'none';
        });
        
        // Always hide pricing tiers (no longer used)
        document.getElementById('pricingTiers').style.display = 'none';

        // Show/hide additional pricing options based on type
        const additionalPricingOptions = document.getElementById('additionalPricingOptions');
        const discountField = additionalPricingOptions.querySelector('.col-md-4:nth-child(2)');
        const finalPriceField = additionalPricingOptions.querySelector('.col-md-4:nth-child(3)');
        
        if (type === 'manual') {
          additionalPricingOptions.style.display = 'none';
        } else if (type === 'margin' || type === 'range') {
          additionalPricingOptions.style.display = 'flex';
          // Hide discount and final price preview for margin and range pricing
          discountField.style.display = 'none';
          finalPriceField.style.display = 'none';
        } else {
          additionalPricingOptions.style.display = 'flex';
          // Show all fields for other pricing methods (fixed)
          discountField.style.display = 'block';
          finalPriceField.style.display = 'block';
        }

        // Show relevant fields based on type
        switch(type) {
          case 'fixed':
            document.getElementById('fixedFields').style.display = 'flex';
            const sellingPriceInput = document.getElementById('sellingPrice');
            sellingPriceInput.required = true;
            break;
          case 'manual':
            document.getElementById('manualFields').style.display = 'flex';
            // For manual pricing, only cost price is required, no selling price field shown
            const sellingPriceInputManual = document.getElementById('sellingPrice');
            if (sellingPriceInputManual) {
              sellingPriceInputManual.required = false;
            }
            // Reset tax, discount and final price for manual pricing
            document.getElementById('taxRate').value = '0';
            document.getElementById('discount').value = '0';
            document.getElementById('finalPrice').textContent = '₦0.00';
            break;
          case 'margin':
            document.getElementById('marginFields').style.display = 'flex';
            document.getElementById('targetMargin').required = true;
            // Reset discount and final price for margin pricing
            document.getElementById('discount').value = '0';
            document.getElementById('finalPrice').textContent = '₦0.00';
            break;
          case 'range':
            document.getElementById('rangeFields').style.display = 'flex';
            document.getElementById('minPrice').required = true;
            document.getElementById('maxPrice').required = true;
            // Reset discount and final price for range pricing
            document.getElementById('discount').value = '0';
            document.getElementById('finalPrice').textContent = '₦0.00';
            break;
        }
      }

      // Show pricing description
      function showPricingDescription(type) {
        const descContainer = document.getElementById('pricingDescription');
        
        // Hide all descriptions
        document.querySelectorAll('.pricing-desc').forEach(desc => {
          desc.style.display = 'none';
        });

        if (type) {
          descContainer.style.display = 'block';
          const targetDesc = document.getElementById(type + 'Desc');
          if (targetDesc) {
            targetDesc.style.display = 'block';
          }
        } else {
          descContainer.style.display = 'none';
        }
      }

      // Calculate selling price and profit based on margin
      function calculateMarginPrice() {
        const costPrice = parseFloat(document.getElementById('costPrice').value) || 0;
        const targetMargin = parseFloat(document.getElementById('targetMargin').value) || 0;
        
        if (costPrice > 0 && targetMargin > 0) {
          const calculatedSellingPrice = costPrice * (1 + (targetMargin / 100));
          const profit = calculatedSellingPrice - costPrice;
          
          document.getElementById('calculatedPrice').value = calculatedSellingPrice.toFixed(2);
          
          // Calculate and display margin profit
          const marginProfitField = document.getElementById('marginProfit');
          if (marginProfitField) {
            marginProfitField.value = profit.toFixed(2);
          }
          
          // Update the selling price field for final calculations
          document.getElementById('sellingPrice').value = calculatedSellingPrice.toFixed(2);
        } else {
          document.getElementById('calculatedPrice').value = '';
          const marginProfitField = document.getElementById('marginProfit');
          if (marginProfitField) {
            marginProfitField.value = '0.00';
          }
        }
      }

      // Validate price range
      function validatePriceRange() {
        const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
        const maxPrice = parseFloat(document.getElementById('maxPrice').value) || 0;
        const costPrice = parseFloat(document.getElementById('costPrice').value) || 0;

        if (minPrice > 0 && maxPrice > 0) {
          if (minPrice >= maxPrice) {
            showNotification('Minimum price must be less than maximum price', 'error');
            return false;
          }
          
          if (minPrice <= costPrice) {
            showNotification('Warning: Minimum price is less than or equal to cost price', 'warning');
          }
        }
        return true;
      }

      // Add new pricing tier
      function addPricingTier() {
        const tbody = document.getElementById('tierTableBody');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
          <td><input type="number" class="form-control form-control-sm" placeholder="1" min="1"></td>
          <td><input type="number" class="form-control form-control-sm" placeholder="10" min="1"></td>
          <td><input type="number" class="form-control form-control-sm" placeholder="0.00" step="0.01" min="0"></td>
          <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTier(this)">Remove</button></td>
        `;
        tbody.appendChild(newRow);
        showNotification('New pricing tier added', 'success');
      }

      // Calculate range pricing profits
      function calculateRangeProfits() {
        const costPrice = parseFloat(document.getElementById('costPrice').value) || 0;
        const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
        const maxPrice = parseFloat(document.getElementById('maxPrice').value) || 0;
        
        const rangePotentialProfitField = document.getElementById('rangePotentialProfit');
        
        if (costPrice > 0 && minPrice > 0 && maxPrice > 0) {
          const minProfit = minPrice - costPrice;
          const maxProfit = maxPrice - costPrice;
          
          if (rangePotentialProfitField) {
            rangePotentialProfitField.value = `${minProfit.toFixed(2)} to ${maxProfit.toFixed(2)}`;
          }
        } else {
          if (rangePotentialProfitField) {
            rangePotentialProfitField.value = '0.00 to 0.00';
          }
        }
      }

      // Remove pricing tier
      function removeTier(button) {
        const row = button.closest('tr');
        const tbody = document.getElementById('tierTableBody');
        
        if (tbody.children.length > 1) {
          row.remove();
          showNotification('Pricing tier removed', 'info');
        } else {
          showNotification('At least one pricing tier is required', 'warning');
        }
      }

      // Toggle Stock Details Section
      function toggleStockDetails() {
        const stockContent = document.getElementById('stockDetailsContent');
        const toggleCheckbox = document.getElementById('stockToggleCheckbox');
        
        if (toggleCheckbox.checked) {
          stockContent.style.display = 'block';
          // Add smooth slide down animation
          stockContent.style.animation = 'slideDown 0.3s ease-out';
        } else {
          stockContent.style.display = 'none';
          stockContent.style.animation = 'slideUp 0.3s ease-out';
        }
      }

      // Add CSS animation for smooth toggle
      const stockToggleStyle = document.createElement('style');
      stockToggleStyle.textContent = `
        @keyframes slideDown {
          from {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
          }
          to {
            opacity: 1;
            max-height: 1000px;
            overflow: visible;
          }
        }
        
        @keyframes slideUp {
          from {
            opacity: 1;
            max-height: 1000px;
            overflow: visible;
          }
          to {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
          }
        }
        
        /* Toggle Switch Styles */
        .toggle-switch {
          position: relative;
          display: inline-block;
          width: 50px;
          height: 24px;
          margin: 0;
          cursor: pointer;
        }
        
        .toggle-switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }
        
        .toggle-slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          transition: .4s;
          border-radius: 24px;
        }
        
        .toggle-slider:before {
          position: absolute;
          content: "";
          height: 18px;
          width: 18px;
          left: 3px;
          bottom: 3px;
          background-color: white;
          transition: .4s;
          border-radius: 50%;
          box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .toggle-switch input:checked + .toggle-slider {
          background-color: #2196F3;
        }
        
        .toggle-switch input:focus + .toggle-slider {
          box-shadow: 0 0 1px #2196F3;
        }
        
        .toggle-switch input:checked + .toggle-slider:before {
          transform: translateX(26px);
        }
        
        .toggle-switch:hover .toggle-slider {
          box-shadow: 0 0 8px rgba(33, 150, 243, 0.3);
        }
        
        #stockDetailsContent {
          transition: all 0.3s ease;
        }
      `;
      document.head.appendChild(stockToggleStyle);

      // Sell Toggle Functionality
      document.getElementById('sellToggle').addEventListener('change', function() {
        const toggleText = document.getElementById('sellToggleText');
        if (this.checked) {
          toggleText.textContent = 'Enabled';
          toggleText.className = 'fw-bold text-success';
          toggleText.style.color = '#28a745';
        } else {
          toggleText.textContent = 'Disabled';
          toggleText.className = 'fw-bold text-danger';
          toggleText.style.color = '#dc3545';
        }
      });

      // Initialize sell toggle text color
      document.addEventListener('DOMContentLoaded', function() {
        const sellToggle = document.getElementById('sellToggle');
        const toggleText = document.getElementById('sellToggleText');
        if (sellToggle.checked) {
          toggleText.className = 'fw-bold text-success';
          toggleText.style.color = '#28a745';
        } else {
          toggleText.className = 'fw-bold text-danger';
          toggleText.style.color = '#dc3545';
        }
      });