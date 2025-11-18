
      // Ensure DOM is ready before defining functions
      window.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, pricing modal functions available');
        
        // Test if modal exists
        const modal = document.getElementById('pricingModalOverlay');
        console.log('Pricing modal found:', modal !== null);
      });
      
      // Variant Modal Functions
      function showVariantModal() {
        const modalOverlay = document.getElementById('variantModalOverlay');
        modalOverlay.classList.remove('closing');
        modalOverlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Add a slight delay to ensure smooth animation
        setTimeout(() => {
          modalOverlay.style.opacity = '1';
        }, 10);
      }

      function closeVariantModal() {
        const modalOverlay = document.getElementById('variantModalOverlay');
        modalOverlay.classList.add('closing');
        
        // Wait for animation to complete before hiding
        setTimeout(() => {
          modalOverlay.style.display = 'none';
          modalOverlay.classList.remove('closing');
          document.body.style.overflow = 'auto';
          
          // Reset modal form
          document.getElementById('variantConfigForm').reset();
          // Reset variant sets to initial state
          disableVariantSet(2);
          disableVariantSet(3);
          // Reset all option containers to single input
          for (let i = 1; i <= 3; i++) {
            resetOptionsContainer(i);
            updateOptionCounter(i);
          }
          // Hide combination preview
          document.getElementById('combinationPreview').style.display = 'none';
        }, 400);
      }

      // Handle Enter key press in option inputs
      function handleOptionInputKeydown(event, setNumber) {
        if (event.key === 'Enter') {
          event.preventDefault(); // Prevent form submission
          
          const currentInput = event.target;
          const currentValue = currentInput.value.trim();
          
          // Only add new input if current input has a value
          if (currentValue !== '') {
            // Check if this is the last input in the container
            const container = document.getElementById(`optionsContainer${setNumber}`);
            const allInputs = container.querySelectorAll('.option-input');
            const currentIndex = Array.from(allInputs).indexOf(currentInput);
            
            // If this is the last input, add a new one
            if (currentIndex === allInputs.length - 1) {
              // Add visual feedback for Enter key usage
              currentInput.style.borderColor = '#28a745';
              setTimeout(() => {
                currentInput.style.borderColor = '';
              }, 800);
              
              addOptionInput(setNumber);
              showNotification('Option added! Press Enter in the new field to add another', 'success');
            } else {
              // If not the last input, focus on the next one
              const nextInput = allInputs[currentIndex + 1];
              if (nextInput) {
                nextInput.focus();
                nextInput.select(); // Select all text for easy editing
              }
            }
          } else {
            // If current input is empty, show a subtle reminder
            currentInput.style.borderColor = '#ffc107';
            currentInput.style.backgroundColor = '#fff3cd';
            setTimeout(() => {
              currentInput.style.borderColor = '';
              currentInput.style.backgroundColor = '';
            }, 1500);
            showNotification('Please enter an option value before adding a new one', 'warning');
          }
        }
        
        // Handle Tab key for better navigation
        if (event.key === 'Tab' && !event.shiftKey) {
          const container = document.getElementById(`optionsContainer${setNumber}`);
          const allInputs = container.querySelectorAll('.option-input');
          const currentIndex = Array.from(allInputs).indexOf(event.target);
          
          // If this is the last input and has content, add a new input before tabbing
          if (currentIndex === allInputs.length - 1 && event.target.value.trim() !== '') {
            event.preventDefault();
            addOptionInput(setNumber);
          }
        }
      }

      function enableVariantSet(setNumber) {
        const content = document.getElementById(`variantSet${setNumber}Content`);
        const addBtn = document.getElementById(`addSet${setNumber}Btn`);
        const removeBtn = document.getElementById(`removeSet${setNumber}Btn`);
        
        content.style.display = 'block';
        addBtn.style.display = 'none';
        removeBtn.style.display = 'inline-block';
        
        showNotification(`Variant Set ${setNumber} enabled`, 'success');
        updateCombinationPreview();
      }

      function disableVariantSet(setNumber) {
        const content = document.getElementById(`variantSet${setNumber}Content`);
        const addBtn = document.getElementById(`addSet${setNumber}Btn`);
        const removeBtn = document.getElementById(`removeSet${setNumber}Btn`);
        const nameInput = document.getElementById(`variantSetName${setNumber}`);
        const optionsContainer = document.getElementById(`optionsContainer${setNumber}`);
        
        content.style.display = 'none';
        addBtn.style.display = 'inline-block';
        removeBtn.style.display = 'none';
        
        // Clear inputs
        nameInput.value = '';
        
        // Reset options to single input
        resetOptionsContainer(setNumber);
        
        updateOptionCounter(setNumber);
        updateCombinationPreview();
      }

      function addOptionInput(setNumber) {
        const container = document.getElementById(`optionsContainer${setNumber}`);
        const currentCount = container.querySelectorAll('.option-input').length;
        
        if (currentCount >= 30) {
          showNotification('Maximum 30 options allowed per set', 'warning');
          return;
        }
        
        const newInputGroup = document.createElement('div');
        newInputGroup.className = 'input-group mb-2 option-fade-in';
        
        newInputGroup.innerHTML = `
          <input type="text" class="form-control option-input" placeholder="Enter option" onchange="updateCombinationPreview()" onkeydown="handleOptionInputKeydown(event, ${setNumber})">
          <button type="button" class="btn btn-outline-danger remove-option-btn" onclick="removeOptionInput(this)" title="Remove this option">
            <i class="mdi mdi-minus"></i>
          </button>
        `;
        
        container.appendChild(newInputGroup);
        
        // Focus on the new input
        const newInput = newInputGroup.querySelector('.option-input');
        newInput.focus();
        
        updateOptionCounter(setNumber);
        updateCombinationPreview();
        
        showNotification('New option field added', 'success');
      }

      function removeOptionInput(button) {
        const inputGroup = button.closest('.input-group');
        const container = inputGroup.closest('.options-container');
        const setNumber = container.id.replace('optionsContainer', '');
        
        // Don't allow removing if it's the last input
        const remainingInputs = container.querySelectorAll('.option-input').length;
        if (remainingInputs <= 1) {
          showNotification('At least one option field is required', 'warning');
          return;
        }
        
        inputGroup.remove();
        updateOptionCounter(setNumber);
        updateCombinationPreview();
        
        showNotification('Option field removed', 'info');
      }

      function resetOptionsContainer(setNumber) {
        const container = document.getElementById(`optionsContainer${setNumber}`);
        container.innerHTML = `
          <div class="option-input-group">
            <div class="input-group mb-2">
              <input type="text" class="form-control option-input" placeholder="Enter option" onchange="updateCombinationPreview()" onkeydown="handleOptionInputKeydown(event, ${setNumber})">
              <button type="button" class="btn btn-outline-success add-option-btn" onclick="addOptionInput(${setNumber})" title="Add another option">
                <i class="mdi mdi-plus"></i>
              </button>
            </div>
          </div>
        `;
      }

      function updateOptionCounter(setNumber) {
        const container = document.getElementById(`optionsContainer${setNumber}`);
        const counter = document.getElementById(`optionCount${setNumber}`);
        
        if (!container || !counter) return;
        
        const optionInputs = container.querySelectorAll('.option-input');
        const filledOptions = Array.from(optionInputs).filter(input => input.value.trim() !== '');
        const totalInputs = optionInputs.length;
        
        counter.textContent = totalInputs;
        
        // Update counter color based on count
        counter.className = '';
        if (totalInputs === 0) {
          counter.classList.add('text-muted');
        } else if (totalInputs <= 20) {
          counter.classList.add('text-success');
        } else if (totalInputs <= 25) {
          counter.classList.add('text-warning');
        } else if (totalInputs <= 30) {
          counter.classList.add('text-danger');
        } else {
          counter.classList.add('text-danger');
          counter.textContent = '30+';
        }
        
        // Show add button only if under 30 options
        const addButtons = container.querySelectorAll('.add-option-btn');
        addButtons.forEach(btn => {
          btn.style.display = totalInputs >= 30 ? 'none' : 'block';
        });
      }

      function updateCombinationPreview() {
        const options1 = getValidOptions(1);
        const options2 = getValidOptions(2);
        const options3 = getValidOptions(3);
        
        let totalCombinations = options1.length;
        if (options2.length > 0) totalCombinations *= options2.length;
        if (options3.length > 0) totalCombinations *= options3.length;
        
        const preview = document.getElementById('combinationPreview');
        const countSpan = document.getElementById('combinationCount');
        
        if (totalCombinations > 0) {
          countSpan.textContent = totalCombinations;
          preview.style.display = 'block';
          
          // Warn if too many combinations
          const alert = preview.querySelector('.alert');
          if (totalCombinations > 100) {
            alert.className = 'alert alert-warning';
            alert.innerHTML = `<i class="mdi mdi-alert"></i> <strong>Warning:</strong> ${totalCombinations} variants will be generated. This might be too many to manage effectively.`;
          } else if (totalCombinations > 50) {
            alert.className = 'alert alert-info';
            alert.innerHTML = `<i class="mdi mdi-information"></i> <strong>Notice:</strong> ${totalCombinations} variants will be generated.`;
          } else {
            alert.className = 'alert alert-success';
            alert.innerHTML = `<i class="mdi mdi-check"></i> <strong>Perfect:</strong> ${totalCombinations} variants will be generated.`;
          }
        } else {
          preview.style.display = 'none';
        }
      }

      function getValidOptions(setNumber) {
        const container = document.getElementById(`optionsContainer${setNumber}`);
        if (!container) return [];
        
        const optionInputs = container.querySelectorAll('.option-input');
        const options = Array.from(optionInputs)
          .map(input => input.value.trim())
          .filter(value => value !== '');
        
        return options.slice(0, 30); // Limit to 30 options
      }

      function reconfigureVariants() {
        // Hide variant section and show add variant button
        document.getElementById('variantSection').style.display = 'none';
        document.getElementById('addVariantCard').style.display = 'block';
        // Clear existing variants
        document.getElementById('variantTableBody').innerHTML = '';
        variantCounter = 0;
        showNotification('Variant configuration cleared. You can set up new variants.', 'info');
      }

      function configureVariants() {
        // Get all variant sets
        const setName1 = document.getElementById('variantSetName1').value.trim();
        const setName2 = document.getElementById('variantSetName2').value.trim();
        const setName3 = document.getElementById('variantSetName3').value.trim();

        // Get options from input fields
        const optionsArray1 = getValidOptions(1);
        const optionsArray2 = getValidOptions(2);
        const optionsArray3 = getValidOptions(3);

        // Validate required fields
        if (!setName1 || optionsArray1.length === 0) {
          showNotification('Please fill in the required fields (Primary Set Name and at least one option)', 'error');
          return;
        }

        // Validate option counts
        if (optionsArray1.length > 30) {
          showNotification('Primary set cannot have more than 30 options', 'error');
          return;
        }

        // Validate secondary set if provided
        if (setName2 && optionsArray2.length === 0) {
          showNotification('Please provide at least one option for the secondary variant set', 'error');
          return;
        }

        if (optionsArray2.length > 30) {
          showNotification('Secondary set cannot have more than 30 options', 'error');
          return;
        }

        // Validate tertiary set if provided
        if (setName3 && optionsArray3.length === 0) {
          showNotification('Please provide at least one option for the tertiary variant set', 'error');
          return;
        }

        if (optionsArray3.length > 30) {
          showNotification('Tertiary set cannot have more than 30 options', 'error');
          return;
        }

        // Check for duplicate options within each set
        const duplicates1 = findDuplicates(optionsArray1);
        const duplicates2 = findDuplicates(optionsArray2);
        const duplicates3 = findDuplicates(optionsArray3);

        if (duplicates1.length > 0) {
          showNotification(`Primary set has duplicate options: ${duplicates1.join(', ')}`, 'error');
          return;
        }

        if (duplicates2.length > 0) {
          showNotification(`Secondary set has duplicate options: ${duplicates2.join(', ')}`, 'error');
          return;
        }

        if (duplicates3.length > 0) {
          showNotification(`Tertiary set has duplicate options: ${duplicates3.join(', ')}`, 'error');
          return;
        }

        // Calculate total combinations
        let totalCombinations = optionsArray1.length;
        if (optionsArray2.length > 0) totalCombinations *= optionsArray2.length;
        if (optionsArray3.length > 0) totalCombinations *= optionsArray3.length;

        // Warn about too many combinations
        if (totalCombinations > 200) {
          if (!confirm(`This will generate ${totalCombinations} variants. This might be difficult to manage. Do you want to continue?`)) {
            return;
          }
        }

        // Show the variant section and hide the add button
        document.getElementById('variantSection').style.display = 'block';
        document.getElementById('addVariantCard').style.display = 'none';

        // Generate variant combinations
        generateVariantCombinations(optionsArray1, optionsArray2, optionsArray3, setName1, setName2, setName3);

        // Close modal
        closeVariantModal();
        
        showNotification(`Variant configuration applied! Generated ${totalCombinations} variant combinations.`, 'success');
      }

      function findDuplicates(array) {
        const seen = new Set();
        const duplicates = new Set();
        
        array.forEach(item => {
          const lowerItem = item.toLowerCase();
          if (seen.has(lowerItem)) {
            duplicates.add(item);
          } else {
            seen.add(lowerItem);
          }
        });
        
        return Array.from(duplicates);
      }

      function generateVariantCombinations(optionsArray1, optionsArray2, optionsArray3, setName1, setName2, setName3) {
        const tableBody = document.getElementById('variantTableBody');
        tableBody.innerHTML = ''; // Clear existing rows
        
        const baseName = document.getElementById('itemName').value || 'Item';
        const baseCode = document.getElementById('itemCode').value || 'ITM';
        
        // Ensure we have arrays to work with
        const options1 = optionsArray1 || [];
        const options2 = optionsArray2.length > 0 ? optionsArray2 : [''];
        const options3 = optionsArray3.length > 0 ? optionsArray3 : [''];

        let variantCounter = 0;
        let totalCombinations = 0;

        // Generate all combinations of the three sets
        options1.forEach(option1 => {
          options2.forEach(option2 => {
            options3.forEach(option3 => {
              // Skip if we have empty secondary/tertiary options when there are actual options
              if ((option2 === '' && options2.length > 1) || (option3 === '' && options3.length > 1)) {
                return;
              }
              
              variantCounter++;
              totalCombinations++;
              
              // Build variant name
              let variantName = `${baseName} - ${option1}`;
              if (option2) variantName += ` ${option2}`;
              if (option3) variantName += ` ${option3}`;
              
              // Build SKU
              let variantSku = baseCode;
              if (option1) variantSku += `-${option1.substring(0,2).toUpperCase()}`;
              if (option2) variantSku += `${option2.substring(0,2).toUpperCase()}`;
              if (option3) variantSku += `${option3.substring(0,2).toUpperCase()}`;
              
              const newRow = document.createElement('tr');
              newRow.innerHTML = `
                <td>
                  <input type="text" class="form-control form-control-sm" 
                         name="variants[${variantCounter}][name]" 
                         value="${variantName}" 
                         readonly
                         required>
                  <input type="hidden" name="variants[${variantCounter}][sku]" value="${variantSku}">
                  <input type="hidden" name="variants[${variantCounter}][primary_value]" value="${option1}">
                  <input type="hidden" name="variants[${variantCounter}][secondary_value]" value="${option2}${option3 ? (option2 ? ' / ' + option3 : option3) : ''}">
                  ${option3 ? `<input type="hidden" name="variants[${variantCounter}][tertiary_value]" value="${option3}">` : ''}
                </td>
                <td class="text-center align-middle">
                  <div class="d-flex flex-column justify-content-center align-items-center">
                    <div class="form-check form-switch mb-1">
                      <input class="form-check-input variant-sell-toggle" type="checkbox" id="sellToggle${variantCounter}" 
                             name="variants[${variantCounter}][is_sellable]" value="1" checked>
                      <label class="form-check-label" for="sellToggle${variantCounter}">
                        <span class="sell-status-text">Sell</span>
                      </label>
                    </div>
                    <small class="text-muted">${variantSku}</small>
                  </div>
                </td>
                <td>
                  <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                          onclick="openPricingModal(${variantCounter}, '${variantName.replace(/'/g, "\\'")}')">
                    <i class="mdi mdi-currency-usd"></i> 
                    <span class="cost-price-display" id="costDisplay${variantCounter}">Set Cost</span>
                  </button>
                  <input type="hidden" name="variants[${variantCounter}][cost_price]" id="costPrice${variantCounter}" value="" required>
                </td>
                <td>
                  <button type="button" class="btn btn-outline-success btn-sm w-100" 
                          onclick="openPricingModal(${variantCounter}, '${variantName.replace(/'/g, "\\'")}')">
                    <i class="mdi mdi-tag"></i> 
                    <span class="sell-price-display" id="sellDisplay${variantCounter}">Set Price</span>
                  </button>
                  <input type="hidden" name="variants[${variantCounter}][selling_price]" id="sellPrice${variantCounter}" value="" required>
                  <input type="hidden" name="variants[${variantCounter}][pricing_method]" id="pricingMethod${variantCounter}" value="fixed">
                  <input type="hidden" name="variants[${variantCounter}][stock_quantity]" value="0">
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
                    <a href="views/edit_variant.php?sku=${encodeURIComponent(variantSku)}" class="btn btn-sm btn-outline-secondary settings-variant-btn" title="Settings for this variant">
                      <i class="mdi mdi-cog"></i>
                    </a>
                    <button type="button" class="btn btn-sm remove-variant-btn" 
                            onclick="removeVariantRow(this)" title="Remove this variant">
                      <i class="mdi mdi-delete"></i>
                    </button>
                  </div>
                </td>
              `;
              tableBody.appendChild(newRow);
              
              // Add event listener for the sell toggle
              const sellToggle = newRow.querySelector(`#sellToggle${variantCounter}`);
              const sellingPriceInput = newRow.querySelector('input[name*="[selling_price]"]');
              
              sellToggle.addEventListener('change', function() {
                updateSellToggleState(this, sellingPriceInput);
              });
              
              // Initialize the toggle state
              updateSellToggleState(sellToggle, sellingPriceInput);
            });
          });
               });

        // Update global variant counter
        window.variantCounter = variantCounter;
        
        // Add master toggle functionality after generating all variants
        setupMasterToggle();
        
        return totalCombinations;
      }

      // Setup master toggle functionality
      function setupMasterToggle() {
        const masterToggle = document.getElementById('masterSellToggle');
        const variantToggles = document.querySelectorAll('.variant-sell-toggle');
        
        if (!masterToggle) return;
        
        // Master toggle controls all variant toggles
        masterToggle.addEventListener('change', function() {
          const isChecked = this.checked;
          
          variantToggles.forEach(toggle => {
            if (toggle.checked !== isChecked) {
              toggle.checked = isChecked;
              const sellingPriceInput = toggle.closest('tr').querySelector('input[name*="[selling_price]"]');
              updateSellToggleState(toggle, sellingPriceInput);
            }
          });
          
          showNotification(
            isChecked ? 'All variants enabled for sale' : 'All variants disabled for sale', 
            'info'
          );
        });
        
        // Update master toggle based on individual toggles
        function updateMasterToggleState() {
          const allToggles = document.querySelectorAll('.variant-sell-toggle');
          const checkedToggles = document.querySelectorAll('.variant-sell-toggle:checked');
          
          // Only turn off master toggle when ALL variants are off
          if (checkedToggles.length === 0) {
            masterToggle.checked = false;
            masterToggle.indeterminate = false;
          } else {
            // Keep master toggle on if any variant is on
            masterToggle.checked = true;
            // Show indeterminate state only when some (but not all) are checked
            masterToggle.indeterminate = checkedToggles.length < allToggles.length;
          }
        }
        
        // Add event listeners to existing variant toggles
        variantToggles.forEach(toggle => {
          toggle.addEventListener('change', function() {
            const sellingPriceInput = this.closest('tr').querySelector('input[name*="[selling_price]"]');
            updateSellToggleState(this, sellingPriceInput);
            updateMasterToggleState();
          });
        });
        
        // Initial state update
        updateMasterToggleState();
      }

      // Function to update sell toggle state and related fields
      function updateSellToggleState(toggleElement, sellingPriceInput) {
        if (toggleElement.checked) {
          // Item is sellable
          sellingPriceInput.required = true;
          sellingPriceInput.disabled = false;
          sellingPriceInput.style.backgroundColor = '';
          sellingPriceInput.placeholder = '0.00';
        } else {
          // Item is not sellable
          sellingPriceInput.required = false;
          sellingPriceInput.disabled = true;
          sellingPriceInput.style.backgroundColor = '#f8f9fa';
          sellingPriceInput.value = '';
          sellingPriceInput.placeholder = 'N/A (Not for sale)';
        }
      }

      // Close variant modal on overlay click
      document.addEventListener('DOMContentLoaded', function() {
        const variantModalOverlay = document.getElementById('variantModalOverlay');
        if (variantModalOverlay) {
          variantModalOverlay.addEventListener('click', function(e) {
            if (e.target === variantModalOverlay) {
              closeVariantModal();
            }
          });
        }

        // Add event listeners for option counting and validation
        for (let i = 1; i <= 3; i++) {
          // Add event delegation for dynamic option inputs
          const container = document.getElementById(`optionsContainer${i}`);
          if (container) {
            container.addEventListener('input', function(e) {
              if (e.target.classList.contains('option-input')) {
                updateOptionCounter(i);
                updateCombinationPreview();
              }
            });
          }
          
          const nameInput = document.getElementById(`variantSetName${i}`);
          if (nameInput) {
            nameInput.addEventListener('input', function() {
              updateCombinationPreview();
            });
          }
        }

        // Initialize option counters
        for (let i = 1; i <= 3; i++) {
          updateOptionCounter(i);
        }
      });

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

      // Reset form
      function resetForm() {
        document.getElementById('addVariantForm').reset();
        
        // Reset variant table to initial state
        const variantsTableBody = document.getElementById('variantsTableBody');
        variantsTableBody.innerHTML = `
          <tr>
            <td><input type="text" class="form-control variant-name" name="variant_names[]" placeholder="Enter variant name" required></td>
            <td><input type="text" class="form-control variant-sku" name="variant_skus[]" placeholder="Auto-generated" readonly></td>
            <td><input type="number" class="form-control variant-cost" name="variant_costs[]" placeholder="0.00" step="0.01" min="0"></td>
            <td><input type="number" class="form-control variant-price" name="variant_prices[]" placeholder="0.00" step="0.01" min="0"></td>
            <td><input type="number" class="form-control variant-stock" name="variant_stocks[]" placeholder="0" min="0"></td>
            <td>
              <button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)" title="Remove Variant">
                <i class="mdi mdi-delete"></i>
              </button>
            </td>
          </tr>
        `;
        
        showNotification('Form has been reset', 'info');
      }

      // Submit form with validation
      function submitForm() {
        const form = document.getElementById('addVariantForm');
        const submitBtn = document.querySelector('.btn-primary');
        
        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        setTimeout(() => {
          // Check if required fields are filled
          if (!form.checkValidity()) {
            form.reportValidity();
            removeLoading(submitBtn);
            return false;
          }
          
          // Validate that at least one variant exists
          const variantRows = document.querySelectorAll('#variantsTableBody tr');
          if (variantRows.length === 0) {
            showNotification('At least one variant is required', 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          // Validate that variant names are filled
          const variantNames = document.querySelectorAll('.variant-name');
          let hasValidVariant = false;
          variantNames.forEach(input => {
            if (input.value.trim() !== '') {
              hasValidVariant = true;
            }
          });
          
          if (!hasValidVariant) {
            showNotification('At least one variant must have a name', 'error');
            removeLoading(submitBtn);
            return false;
          }
          
          // If all validations pass
          showNotification('Variant item validation passed! Ready to be saved.', 'success');
          
          // Simulate save process
          setTimeout(() => {
            removeLoading(submitBtn);
            showNotification('Variant item successfully created!', 'success');
            
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

      // Remove variant row
      function removeVariantRow(button) {
        const row = button.closest('tr');
        const tableBody = document.getElementById('variantTableBody');
        
        if (tableBody.children.length > 1) {
          row.remove();
          showNotification('Variant row removed', 'info');
        } else {
          showNotification('At least one variant is required', 'warning');
        }
      }

      // Settings variant row
      function settingsVariantRow(button) {
        const row = button.closest('tr');
        // Example: get data from row inputs (customize as needed)
        const variantData = {
          name: row.querySelector('input[name*="[name]"]').value,
          sku: row.querySelector('input[name*="[sku]"]').value,
          cost_price: row.querySelector('input[name*="[cost_price]"]').value,
          selling_price: row.querySelector('input[name*="[selling_price]"]').value,
          stock_quantity: row.querySelector('input[name*="[stock_quantity]"]') ? row.querySelector('input[name*="[stock_quantity]"]').value : '',
          low_stock_threshold: '',
          expiry_date: '',
          location: ''
        };
        openVariantSettingsOverlay(variantData);
      }

      // Overlay functions
      function openVariantSettingsOverlay(variantData) {
        // Fill overlay form fields with variantData
        document.getElementById('variantName').value = variantData.name || '';
        document.getElementById('sku').value = variantData.sku || '';
        document.getElementById('costPrice').value = variantData.cost_price || '';
        document.getElementById('sellingPrice').value = variantData.selling_price || '';
        document.getElementById('stockQuantity').value = variantData.stock_quantity || '';
        document.getElementById('lowStockThreshold').value = variantData.low_stock_threshold || '';
        document.getElementById('expiryDate').value = variantData.expiry_date || '';
        document.getElementById('location').value = variantData.location || '';
        document.getElementById('variantSettingsOverlay').style.display = 'flex';
      }

      function closeVariantSettingsOverlay() {
        document.getElementById('variantSettingsOverlay').style.display = 'none';
      }

      // Submit pricing form
      document.getElementById('variantPricingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // TODO: Add AJAX request to save pricing data
        console.log('Pricing data:', Object.fromEntries(formData));
        
        closeVariantSettingsOverlay();
        showNotification('Pricing updated successfully', 'success');
      });

      // Submit stocking form
      document.getElementById('variantStockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        
        // TODO: Add AJAX request to save stocking data
        console.log('Stocking data:', Object.fromEntries(formData));
        
        closeVariantSettingsOverlay();
        showNotification('Stocking information updated successfully', 'success');
      });

      // Pricing Modal Functions
      window.openPricingModal = function(variantIndex, variantName) {
        console.log('Opening pricing modal for variant:', variantIndex, variantName);
        
        // Set current variant info
        document.getElementById('currentVariantIndex').value = variantIndex;
        document.getElementById('modalVariantName').textContent = variantName;
        
        // Load existing pricing data if available
        const costInput = document.getElementById(`costPrice${variantIndex}`);
        const sellInput = document.getElementById(`sellPrice${variantIndex}`);
        const methodInput = document.getElementById(`pricingMethod${variantIndex}`);
        
        if (costInput && costInput.value) {
          document.getElementById('modalCostPrice').value = costInput.value;
        }
        if (sellInput && sellInput.value) {
          document.getElementById('modalSellingPrice').value = sellInput.value;
        }
        if (methodInput && methodInput.value) {
          document.querySelector(`input[name="pricing_method"][value="${methodInput.value}"]`).checked = true;
          showPricingFields(methodInput.value);
        }
        
        // Show modal
        const modalOverlay = document.getElementById('pricingModalOverlay');
        console.log('Modal element found:', modalOverlay);
        
        if (!modalOverlay) {
          alert('Pricing modal not found! Please check the HTML structure.');
          return;
        }
        
        modalOverlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Initialize pricing calculations
        setupPricingCalculations();
      }

      window.closePricingModal = function() {
        const modalOverlay = document.getElementById('pricingModalOverlay');
        modalOverlay.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Reset form
        document.getElementById('variantPricingForm').reset();
        document.querySelector('input[name="pricing_method"][value="fixed"]').checked = true;
        showPricingFields('fixed');
      }

      window.savePricing = function() {
        const form = document.getElementById('variantPricingForm');
        const variantIndex = document.getElementById('currentVariantIndex').value;
        
        // Validate form
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }
        
        // Get pricing method and values
        const pricingMethod = document.querySelector('input[name="pricing_method"]:checked').value;
        const costPrice = parseFloat(document.getElementById('modalCostPrice').value) || 0;
        let sellingPrice = 0;
        
        // Calculate selling price based on method
        switch (pricingMethod) {
          case 'fixed':
            sellingPrice = parseFloat(document.getElementById('modalSellingPrice').value) || 0;
            break;
          case 'manual':
            sellingPrice = 0; // Will be set during sales
            break;
          case 'margin':
            sellingPrice = parseFloat(document.getElementById('modalCalculatedPrice').value) || 0;
            break;
          case 'range':
            sellingPrice = parseFloat(document.getElementById('modalMinPrice').value) || 0; // Use min price as base
            break;
        }
        
        // Validate pricing
        if (costPrice <= 0) {
          showNotification('Cost price must be greater than 0', 'warning');
          return;
        }
        
        if (pricingMethod === 'fixed' && sellingPrice <= 0) {
          showNotification('Selling price must be greater than 0 for fixed pricing', 'warning');
          return;
        }
        
        if (pricingMethod !== 'manual' && sellingPrice <= costPrice) {
          showNotification('Selling price must be greater than cost price', 'warning');
          return;
        }
        
        // Update hidden inputs
        document.getElementById(`costPrice${variantIndex}`).value = costPrice.toFixed(2);
        document.getElementById(`sellPrice${variantIndex}`).value = sellingPrice.toFixed(2);
        document.getElementById(`pricingMethod${variantIndex}`).value = pricingMethod;
        
        // Update button displays
        document.getElementById(`costDisplay${variantIndex}`).textContent = `₦${costPrice.toFixed(2)}`;
        
        if (pricingMethod === 'manual') {
          document.getElementById(`sellDisplay${variantIndex}`).textContent = 'Manual';
        } else if (pricingMethod === 'range') {
          const maxPrice = parseFloat(document.getElementById('modalMaxPrice').value) || 0;
          document.getElementById(`sellDisplay${variantIndex}`).textContent = `₦${sellingPrice.toFixed(2)} - ₦${maxPrice.toFixed(2)}`;
        } else {
          document.getElementById(`sellDisplay${variantIndex}`).textContent = `₦${sellingPrice.toFixed(2)}`;
        }
        
        // Close modal
        closePricingModal();
        showNotification('Pricing updated successfully', 'success');
      }

      function showPricingFields(method) {
        // Hide all pricing fields
        document.querySelectorAll('.pricing-fields').forEach(field => {
          field.style.display = 'none';
        });
        
        // Show selected pricing fields
        switch (method) {
          case 'fixed':
            document.getElementById('modalFixedFields').style.display = 'flex';
            break;
          case 'manual':
            document.getElementById('modalManualFields').style.display = 'flex';
            break;
          case 'margin':
            document.getElementById('modalMarginFields').style.display = 'flex';
            break;
          case 'range':
            document.getElementById('modalRangeFields').style.display = 'flex';
            break;
        }
      }

      function setupPricingCalculations() {
        // Add event listeners for pricing method changes
        document.querySelectorAll('input[name="pricing_method"]').forEach(radio => {
          radio.addEventListener('change', function() {
            showPricingFields(this.value);
          });
        });
        
        // Fixed pricing calculations
        const modalCostPrice = document.getElementById('modalCostPrice');
        const modalSellingPrice = document.getElementById('modalSellingPrice');
        const modalProfitMargin = document.getElementById('modalProfitMargin');
        const modalPotentialProfit = document.getElementById('modalPotentialProfit');
        
        function calculateFixedProfit() {
          const cost = parseFloat(modalCostPrice.value) || 0;
          const selling = parseFloat(modalSellingPrice.value) || 0;
          
          if (cost > 0 && selling > 0) {
            const profit = selling - cost;
            const margin = (profit / cost) * 100;
            
            modalProfitMargin.value = margin.toFixed(2) + '%';
            modalPotentialProfit.value = profit.toFixed(2);
          } else {
            modalProfitMargin.value = '';
            modalPotentialProfit.value = '';
          }
        }
        
        modalCostPrice.addEventListener('input', calculateFixedProfit);
        modalSellingPrice.addEventListener('input', calculateFixedProfit);
        
        // Margin pricing calculations
        const modalTargetMargin = document.getElementById('modalTargetMargin');
        const modalCalculatedPrice = document.getElementById('modalCalculatedPrice');
        const modalMarginProfit = document.getElementById('modalMarginProfit');
        
        function calculateMarginPrice() {
          const cost = parseFloat(modalCostPrice.value) || 0;
          const margin = parseFloat(modalTargetMargin.value) || 0;
          
          if (cost > 0 && margin > 0) {
            const calculatedPrice = cost * (1 + margin / 100);
            const profit = calculatedPrice - cost;
            
            modalCalculatedPrice.value = calculatedPrice.toFixed(2);
            modalMarginProfit.value = profit.toFixed(2);
          } else {
            modalCalculatedPrice.value = '';
            modalMarginProfit.value = '';
          }
        }
        
        modalTargetMargin.addEventListener('input', calculateMarginPrice);
        
        // Range pricing calculations
        const modalMinPrice = document.getElementById('modalMinPrice');
        const modalMaxPrice = document.getElementById('modalMaxPrice');
        const modalRangePotentialProfit = document.getElementById('modalRangePotentialProfit');
        
        function calculateRangeProfit() {
          const cost = parseFloat(modalCostPrice.value) || 0;
          const minPrice = parseFloat(modalMinPrice.value) || 0;
          const maxPrice = parseFloat(modalMaxPrice.value) || 0;
          
          if (cost > 0 && minPrice > 0 && maxPrice > 0) {
            const minProfit = minPrice - cost;
            const maxProfit = maxPrice - cost;
            
            modalRangePotentialProfit.value = `${minProfit.toFixed(2)} to ${maxProfit.toFixed(2)}`;
          } else {
            modalRangePotentialProfit.value = '';
          }
        }
        
        modalMinPrice.addEventListener('input', calculateRangeProfit);
        modalMaxPrice.addEventListener('input', calculateRangeProfit);
        modalCostPrice.addEventListener('input', () => {
          calculateFixedProfit();
          calculateMarginPrice();
          calculateRangeProfit();
        });
      }

      // Close pricing modal on overlay click
      document.addEventListener('DOMContentLoaded', function() {
        const pricingModalOverlay = document.getElementById('pricingModalOverlay');
        if (pricingModalOverlay) {
          pricingModalOverlay.addEventListener('click', function(e) {
            if (e.target === pricingModalOverlay) {
              closePricingModal();
            }
          });
        }
      });
    