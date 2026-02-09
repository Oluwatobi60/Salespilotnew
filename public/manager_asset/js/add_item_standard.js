
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
        const profitMarginField = document.getElementById('profitMargin');

        if (costPrice > 0 && sellingPrice > 0) {
          const profit = sellingPrice - costPrice;
          const margin = (profit / costPrice) * 100;

          // Store numeric value in data attribute for submission
          profitMarginField.setAttribute('data-value', margin.toFixed(2));
          // Display with % for user
          profitMarginField.value = margin.toFixed(2) + '%';

          // Calculate potential profit for fixed pricing
          const potentialProfitField = document.getElementById('potentialProfit');
          if (potentialProfitField) {
            potentialProfitField.value = profit.toFixed(2);
          }
        } else {
          profitMarginField.setAttribute('data-value', '0');
          profitMarginField.value = '0%';
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
        const taxRateElement = document.getElementById('fixedTaxRate');
        const taxRate = taxRateElement ? parseFloat(taxRateElement.value) || 0 : 0;

        let finalPrice = sellingPrice;

        // Apply tax
        if (taxRate > 0) {
          finalPrice = finalPrice + (finalPrice * (taxRate / 100));
        }

        // Update display
        const finalPriceDisplay = document.getElementById('finalPrice');
        if (finalPriceDisplay) {
          finalPriceDisplay.textContent = '₦' + finalPrice.toFixed(2);
        }

        // Update hidden input field for form submission
        const finalPriceInput = document.getElementById('finalPriceInput');
        if (finalPriceInput) {
          finalPriceInput.value = finalPrice.toFixed(2);
        }
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

        // Check if required fields are filled
        if (!form.checkValidity()) {
          form.reportValidity();
          return false;
        }

        // Clean up profit margin field - remove % symbol
        const profitMarginField = document.getElementById('profitMargin');
        if (profitMarginField && profitMarginField.value) {
          const cleanValue = profitMarginField.value.replace('%', '').trim();
          profitMarginField.value = cleanValue || '0';
        }

        // Clean up other readonly calculated fields if they're empty strings
        const potentialProfitField = document.getElementById('potentialProfit');
        if (potentialProfitField && !potentialProfitField.value) {
          potentialProfitField.value = '0';
        }

        const marginProfitField = document.getElementById('marginProfit');
        if (marginProfitField && !marginProfitField.value) {
          marginProfitField.value = '0';
        }

        const calculatedPriceField = document.getElementById('calculatedPrice');
        if (calculatedPriceField && !calculatedPriceField.value) {
          calculatedPriceField.value = '0';
        }

        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Submitting...';

        // Submit the form
        form.submit();
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
        // ===== AUTO-GENERATE ITEM CODE =====
        const itemNameInput = document.getElementById('itemName');
        const itemCodeInput = document.getElementById('itemCode');

        if (itemNameInput && itemCodeInput) {
          let manuallyEditedCode = false;

          // Detect manual editing of item code
          itemCodeInput.addEventListener('focus', function() {
            manuallyEditedCode = true;
          });

          itemCodeInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
              manuallyEditedCode = false;
            }
          });

          // Auto-generate on item name input
          itemNameInput.addEventListener('input', function() {
            if (!manuallyEditedCode) {
              const name = this.value.trim();
              if (name) {
                const cleanName = name.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                const timestamp = Date.now().toString().slice(-4);
                const prefix = cleanName.slice(0, Math.min(3, cleanName.length));
                itemCodeInput.value = prefix + '-' + timestamp;
              } else {
                itemCodeInput.value = '';
              }
            }
          });
        }
        // ===== END AUTO-GENERATE =====

        // Price calculation events
        const costPriceField = document.getElementById('costPrice');
        const sellingPriceField = document.getElementById('sellingPrice');
        const fixedTaxRateField = document.getElementById('fixedTaxRate');

        if (costPriceField) {
          costPriceField.addEventListener('input', calculateProfitMargin);
        }
        if (sellingPriceField) {
          sellingPriceField.addEventListener('input', function() {
            calculateProfitMargin();
            calculateFinalPrice();
          });
        }
        if (fixedTaxRateField) {
          fixedTaxRateField.addEventListener('change', calculateFinalPrice);
        }

        // Add form submit event to clean data before submission
        document.getElementById('addItemForm').addEventListener('submit', function(e) {
          // Sync all cost price fields to the main cost_price field before submission
          const costPrice = document.getElementById('costPrice');
          const manualCostPrice = document.getElementById('manualCostPrice');
          const marginCostPrice = document.getElementById('marginCostPrice');
          const rangeCostPrice = document.getElementById('rangeCostPrice');

          // Get the active pricing type
          const selectedPricingType = document.querySelector('input[name="pricing_type"]:checked');
          if (selectedPricingType) {
            const pricingType = selectedPricingType.value;

            // Sync the appropriate cost price to the main field
            if (pricingType === 'manual' && manualCostPrice && manualCostPrice.value) {
              costPrice.value = manualCostPrice.value;
            } else if (pricingType === 'margin' && marginCostPrice && marginCostPrice.value) {
              costPrice.value = marginCostPrice.value;
            } else if (pricingType === 'range' && rangeCostPrice && rangeCostPrice.value) {
              costPrice.value = rangeCostPrice.value;
            }

            // Clear fields that are not relevant to the selected pricing type
            const minPriceField = document.getElementById('minPrice');
            const maxPriceField = document.getElementById('maxPrice');
            const targetMarginField = document.getElementById('targetMargin');
            const sellingPriceField = document.getElementById('sellingPrice');
            const calculatedPriceField = document.getElementById('calculatedPrice');
            const potentialProfitField = document.getElementById('potentialProfit');
            const marginProfitField = document.getElementById('marginProfit');
            const finalPriceInputField = document.getElementById('finalPriceInput');

            if (pricingType !== 'range') {
              // Clear min and max price for non-range pricing types
              if (minPriceField) minPriceField.value = '';
              if (maxPriceField) maxPriceField.value = '';
            }

            if (pricingType !== 'margin') {
              // Clear target margin for non-margin pricing types
              if (targetMarginField) targetMarginField.value = '';
              // Clear calculated price for non-margin pricing
              if (calculatedPriceField) calculatedPriceField.value = '';
            }

            if (pricingType === 'manual') {
              // Clear selling price for manual pricing
              if (sellingPriceField) sellingPriceField.value = '';
            }

            // For margin pricing, sync calculated price to selling price and margin profit to potential profit
            if (pricingType === 'margin') {
              if (calculatedPriceField && calculatedPriceField.value) {
                // Set selling_price from calculated price
                if (sellingPriceField) {
                  sellingPriceField.value = calculatedPriceField.value;
                }
              }

              if (marginProfitField && marginProfitField.value) {
                // Set potential_profit from margin profit
                if (potentialProfitField) {
                  potentialProfitField.value = marginProfitField.value;
                }
              }

              // Calculate and set final price from margin pricing
              const marginTaxRate = parseFloat(document.getElementById('marginTaxRate')?.value) || 0;
              const calculatedPrice = parseFloat(calculatedPriceField?.value) || 0;
              if (calculatedPrice > 0) {
                const finalPrice = calculatedPrice * (1 + (marginTaxRate / 100));
                if (finalPriceInputField) {
                  finalPriceInputField.value = finalPrice.toFixed(2);
                }
              }
            }

            // For range pricing, set selling price and final price based on average
            if (pricingType === 'range') {
              const minPrice = parseFloat(minPriceField?.value) || 0;
              const maxPrice = parseFloat(maxPriceField?.value) || 0;

              if (minPrice > 0 && maxPrice > 0) {
                // Use average of min and max for selling price
                const avgPrice = (minPrice + maxPrice) / 2;
                if (sellingPriceField) {
                  sellingPriceField.value = avgPrice.toFixed(2);
                }

                // Calculate final price with tax based on average price
                const rangeTaxRate = parseFloat(document.getElementById('rangeTaxRate')?.value) || 0;
                const finalPrice = avgPrice * (1 + (rangeTaxRate / 100));
                if (finalPriceInputField) {
                  finalPriceInputField.value = finalPrice.toFixed(2);
                }

                // Set potential profit based on average
                const costPrice = parseFloat(document.getElementById('rangeCostPrice')?.value) || parseFloat(document.getElementById('costPrice')?.value) || 0;
                const avgProfit = avgPrice - costPrice;
                if (potentialProfitField) {
                  potentialProfitField.value = avgProfit.toFixed(2);
                }
              }
            }
          }

          // Clean up profit margin field - use stored numeric value
          const profitMarginField = document.getElementById('profitMargin');
          if (profitMarginField) {
            // Get clean numeric value from data attribute or parse from displayed value
            let numericValue = profitMarginField.getAttribute('data-value') ||
                                profitMarginField.value.replace('%', '').replace(/\s/g, '').trim();
            // Ensure it's a valid number
            numericValue = parseFloat(numericValue) || 0;
            profitMarginField.value = numericValue.toString();
            console.log('Profit margin cleaned:', profitMarginField.value);
          }

          // Clean up other readonly calculated fields if they're empty strings
          const potentialProfitField = document.getElementById('potentialProfit');
          if (potentialProfitField && !potentialProfitField.value) {
            potentialProfitField.value = '0';
          }

          const marginProfitField = document.getElementById('marginProfit');
          if (marginProfitField && !marginProfitField.value) {
            marginProfitField.value = '0';
          }

          const calculatedPriceField = document.getElementById('calculatedPrice');
          if (calculatedPriceField && !calculatedPriceField.value) {
            calculatedPriceField.value = '0';
          }

          // Clean up range_potential_profit field - extract first number or set to 0
          const rangePotentialProfitField = document.getElementById('rangePotentialProfit');
          if (rangePotentialProfitField) {
            const value = rangePotentialProfitField.value;
            if (value && value.includes('-')) {
              // Extract first number from "0.00 - 0.00" format
              const firstNumber = value.split('-')[0].trim();
              rangePotentialProfitField.value = firstNumber || '0';
            } else if (!value) {
              rangePotentialProfitField.value = '0';
            }
          }

          console.log('Form data ready for submission');
          console.log('Cost Price:', costPrice.value);
          console.log('Pricing Type:', selectedPricingType ? selectedPricingType.value : 'none');

          // Show loading state on button
          const submitBtn = document.getElementById('addItemBtn');
          if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Submitting...';
          }
        });

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
        const manualCostPrice = document.getElementById('manualCostPrice');
        const marginCostPrice = document.getElementById('marginCostPrice');
        const rangeCostPrice = document.getElementById('rangeCostPrice');
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

        // Sync all cost price fields when any one changes
        [costPrice, manualCostPrice, marginCostPrice, rangeCostPrice].forEach(field => {
          if (field) {
            field.addEventListener('input', function() {
              const value = this.value;
              if (costPrice) costPrice.value = value;
              if (manualCostPrice) manualCostPrice.value = value;
              if (marginCostPrice) marginCostPrice.value = value;
              if (rangeCostPrice) rangeCostPrice.value = value;

              // Trigger calculations based on active pricing type
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
          }
        });

        // Handle margin pricing calculations
        if (targetMargin) {
          targetMargin.addEventListener('input', function() {
            calculateMarginPrice();
          });
        }

        // Handle tax rate changes for margin pricing
        const marginTaxRate = document.getElementById('marginTaxRate');
        if (marginTaxRate) {
          marginTaxRate.addEventListener('change', function() {
            calculateMarginPrice();
          });
        }

        // Tax rate for fixed pricing already handled above in DOMContentLoaded

        // Handle range pricing validation and profit calculation
        document.getElementById('minPrice')?.addEventListener('input', function() {
          validatePriceRange();
          calculateRangeProfits();
        });
        document.getElementById('maxPrice')?.addEventListener('input', function() {
          validatePriceRange();
          calculateRangeProfits();
        });

        // Handle tax rate changes for range pricing
        const rangeTaxRate = document.getElementById('rangeTaxRate');
        if (rangeTaxRate) {
          rangeTaxRate.addEventListener('change', function() {
            calculateRangeProfits();
          });
        }
      }

      // Show appropriate pricing fields based on selected type
      function showPricingFields(type) {
        console.log('showPricingFields called with type:', type);

        // Hide all pricing fields first
        document.querySelectorAll('.pricing-fields').forEach(field => {
          field.style.display = 'none';
        });

        // Hide pricing tiers
        const pricingTiersElement = document.getElementById('pricingTiers');
        if (pricingTiersElement) {
          pricingTiersElement.style.display = 'none';
        }

        // Remove required attribute from all pricing-specific fields first
        const sellingPriceInput = document.getElementById('sellingPrice');
        const targetMarginInput = document.getElementById('targetMargin');
        const minPriceInput = document.getElementById('minPrice');
        const maxPriceInput = document.getElementById('maxPrice');

        if (sellingPriceInput) sellingPriceInput.required = false;
        if (targetMarginInput) targetMarginInput.required = false;
        if (minPriceInput) minPriceInput.required = false;
        if (maxPriceInput) maxPriceInput.required = false;

        // Show relevant fields based on type
        switch(type) {
          case 'fixed':
            const fixedFields = document.getElementById('fixedFields');
            const fixedFinalPrice = document.getElementById('fixedFinalPrice');
            if (fixedFields) {
              fixedFields.style.display = '';
              fixedFields.classList.remove('d-none');
            }
            if (fixedFinalPrice) {
              fixedFinalPrice.style.display = '';
              fixedFinalPrice.classList.remove('d-none');
            }
            if (sellingPriceInput) {
              sellingPriceInput.required = true;
            }
            console.log('Fixed pricing fields shown');
            // Trigger calculation for fixed pricing
            calculateProfitMargin();
            calculateFinalPrice();
            break;

          case 'manual':
            const manualFields = document.getElementById('manualFields');
            if (manualFields) {
              manualFields.style.display = '';
              manualFields.classList.remove('d-none');
            }
            console.log('Manual pricing fields shown');
            break;

          case 'margin':
            const marginFields = document.getElementById('marginFields');
            if (marginFields) {
              marginFields.style.display = '';
              marginFields.classList.remove('d-none');
            }
            if (targetMarginInput) {
              targetMarginInput.required = true;
            }
            console.log('Margin pricing fields shown');
            // Trigger calculation for margin pricing
            calculateMarginPrice();
            break;

          case 'range':
            const rangeFields = document.getElementById('rangeFields');
            if (rangeFields) {
              rangeFields.style.display = '';
              rangeFields.classList.remove('d-none');
            }
            if (minPriceInput) {
              minPriceInput.required = true;
            }
            if (maxPriceInput) {
              maxPriceInput.required = true;
            }
            console.log('Range pricing fields shown');
            // Trigger calculation for range pricing
            calculateRangeProfits();
            break;
        }

        // Sync cost price fields
        syncCostPriceFields();
      }      // Show pricing description
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

      // Sync cost price across all pricing types
      function syncCostPriceFields() {
        const costPrice = document.getElementById('costPrice');
        const manualCostPrice = document.getElementById('manualCostPrice');
        const marginCostPrice = document.getElementById('marginCostPrice');
        const rangeCostPrice = document.getElementById('rangeCostPrice');

        if (costPrice && costPrice.value) {
          if (manualCostPrice) manualCostPrice.value = costPrice.value;
          if (marginCostPrice) marginCostPrice.value = costPrice.value;
          if (rangeCostPrice) rangeCostPrice.value = costPrice.value;
        }
      }

      // Calculate selling price and profit based on margin
      function calculateMarginPrice() {
        const costPrice = parseFloat(document.getElementById('marginCostPrice').value) || parseFloat(document.getElementById('costPrice').value) || 0;
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

          // Calculate final price with tax
          const taxRate = parseFloat(document.getElementById('marginTaxRate').value) || 0;
          const finalPrice = calculatedSellingPrice * (1 + (taxRate / 100));
          const marginFinalPrice = document.getElementById('marginFinalPrice');
          if (marginFinalPrice) {
            marginFinalPrice.textContent = '₦' + finalPrice.toFixed(2);
          }
        } else {
          document.getElementById('calculatedPrice').value = '';
          const marginProfitField = document.getElementById('marginProfit');
          if (marginProfitField) {
            marginProfitField.value = '0.00';
          }
          const marginFinalPrice = document.getElementById('marginFinalPrice');
          if (marginFinalPrice) {
            marginFinalPrice.textContent = '₦0.00';
          }
        }
      }

      // Validate price range
      function validatePriceRange() {
        const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
        const maxPrice = parseFloat(document.getElementById('maxPrice').value) || 0;
        const costPrice = parseFloat(document.getElementById('rangeCostPrice').value) || parseFloat(document.getElementById('costPrice').value) || 0;

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
        const costPrice = parseFloat(document.getElementById('rangeCostPrice').value) || parseFloat(document.getElementById('costPrice').value) || 0;
        const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
        const maxPrice = parseFloat(document.getElementById('maxPrice').value) || 0;

        const rangePotentialProfitField = document.getElementById('rangePotentialProfit');

        if (costPrice > 0 && minPrice > 0 && maxPrice > 0) {
          const minProfit = minPrice - costPrice;
          const maxProfit = maxPrice - costPrice;

          if (rangePotentialProfitField) {
            rangePotentialProfitField.value = `${minProfit.toFixed(2)} - ${maxProfit.toFixed(2)}`;
          }

          // Calculate final price range with tax
          const taxRate = parseFloat(document.getElementById('rangeTaxRate').value) || 0;
          const finalMinPrice = minPrice * (1 + (taxRate / 100));
          const finalMaxPrice = maxPrice * (1 + (taxRate / 100));
          const rangeFinalPrice = document.getElementById('rangeFinalPrice');
          if (rangeFinalPrice) {
            rangeFinalPrice.textContent = `₦${finalMinPrice.toFixed(2)} - ₦${finalMaxPrice.toFixed(2)}`;
          }
        } else {
          if (rangePotentialProfitField) {
            rangePotentialProfitField.value = '0.00 - 0.00';
          }
          const rangeFinalPrice = document.getElementById('rangeFinalPrice');
          if (rangeFinalPrice) {
            rangeFinalPrice.textContent = '₦0.00 - ₦0.00';
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

      // ============================
      // Category Panel is now handled by category-panel.js component
      // Initialize it here
      // ============================
      if (typeof CategoryPanel !== 'undefined' && document.getElementById('addCategoryPanel')) {
        CategoryPanel.init('category');
      }

      // ============================
      // Supplier Panel is now handled by supplier-panel.js component
      // Initialize it here
      // ============================
      if (typeof SupplierPanel !== 'undefined' && document.getElementById('addSupplierPanel')) {
        SupplierPanel.init('supplier');
      }

      // ============================
      // Supplier Panel is now handled by supplier-panel.js component
      // ============================

