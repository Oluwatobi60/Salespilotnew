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

              // Build variant display (only variations, separated by /)
              let variantDisplay = option1;
              if (option2) variantDisplay += ` / ${option2}`;
              if (option3) variantDisplay += ` / ${option3}`;

              // Build full variant name for backend (includes base name)
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
                         required
                         style="display: none;">
                  <div class="variant-display-text">${variantDisplay}</div>
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
                <td colspan="2">
                  <button type="button" onclick="openEditVariantModal(${variantCounter}, '${variantSku}')" class="btn btn-outline-info btn-sm w-100 pricing-group-btn" title="Click to edit pricing">
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="text-start">
                        <i class="mdi mdi-currency-usd"></i>
                        <span class="cost-price-display" id="costDisplay${variantCounter}">₦0.00</span>
                      </div>
                      <div class="text-end">
                        <i class="mdi mdi-tag"></i>
                        <span class="sell-price-display" id="sellDisplay${variantCounter}">₦0.00</span>
                      </div>
                    </div>
                  </button>
                  <input type="hidden" name="variants[${variantCounter}][cost_price]" id="costPrice${variantCounter}" value="0.00" required>
                  <input type="hidden" name="variants[${variantCounter}][selling_price]" id="sellPrice${variantCounter}" value="0.00" required>
                  <input type="hidden" name="variants[${variantCounter}][pricing_method]" id="pricingMethod${variantCounter}" value="fixed">
                  <input type="hidden" name="variants[${variantCounter}][profit_margin]" id="profitMargin${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][potential_profit]" id="potentialProfit${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][tax_rate]" id="taxRate${variantCounter}" value="0">
                  <input type="hidden" name="variants[${variantCounter}][discount]" id="discount${variantCounter}" value="0">
                  <input type="hidden" name="variants[${variantCounter}][final_price]" id="finalPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][manual_cost_price]" id="manualCostPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][margin_cost_price]" id="marginCostPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][target_margin]" id="targetMargin${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][calculated_price]" id="calculatedPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][margin_profit]" id="marginProfit${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][range_cost_price]" id="rangeCostPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][min_price]" id="minPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][max_price]" id="maxPrice${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][range_potential_profit]" id="rangePotentialProfit${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][location]" id="location${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][expiry_date]" id="expiryDate${variantCounter}" value="">
                  <input type="hidden" name="variants[${variantCounter}][barcode]" id="barcode${variantCounter}" value="">
                </td>
                <td>
                  <input type="number" class="form-control form-control-sm"
                         name="variants[${variantCounter}][stock_quantity]"
                         id="stockQty${variantCounter}"
                         value="0"
                         min="0"
                         step="1"
                         placeholder="0"
                         title="Stock quantity">
                </td>
                <td>
                  <input type="number" class="form-control form-control-sm"
                         name="variants[${variantCounter}][low_stock_threshold]"
                         id="lowStock${variantCounter}"
                         value="0"
                         min="0"
                         step="1"
                         placeholder="0"
                         title="Low stock alert threshold">
                </td>
                <td class="text-center">
                  <div class="btn-group" role="group">
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
            option.text.toLowerCase().includes(unitName.toLowerCase())
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
            e.preventDefault();
            customUnitAbbr.focus();
          }
        });

        customUnitAbbr.addEventListener('keypress', function(e) {
          if (e.key === 'Enter') {
            e.preventDefault();
            addUnitBtn.click();
          }
        });
      }

      // Close variant modal on overlay click
      document.addEventListener('DOMContentLoaded', function() {
        // Setup custom unit handlers
        setupCustomUnitHandlers();

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
        const variantTableBody = document.getElementById('variantTableBody');
        variantTableBody.innerHTML = `
          <tr>
            <td><input type="text" class="form-control variant-name" name="variant_names[]" placeholder="Enter variant name" required></td>
            <td><input type="text" class="form-control variant-sku" name="variant_skus[]" placeholder="Auto-generated" readonly></td>
            <td><input type="number" class="form-control variant-cost" name="variant_costs[]" value="0.00" step="0.01" min="0"></td>
            <td><input type="number" class="form-control variant-price" name="variant_prices[]" value="0.00" step="0.01" min="0"></td>
            <td><input type="number" class="form-control variant-stock" name="variant_stocks[]" value="0" min="0"></td>
            <td>
              <button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)" title="Remove Variant">
                <i class="mdi mdi-delete"></i>
              </button>
            </td>
          </tr>
        `;

        showNotification('Form has been reset', 'info');
      }

      // Sync pricing fields for form submission
      function syncPricingFieldsForSubmission(pricingType) {
        console.log('Syncing pricing fields for submission, type:', pricingType);

        // Get all pricing-related fields
        const costPrice = document.getElementById('costPrice');
        const manualCostPrice = document.getElementById('manualCostPrice');
        const marginCostPrice = document.getElementById('marginCostPrice');
        const rangeCostPrice = document.getElementById('rangeCostPrice');

        const sellingPrice = document.getElementById('sellingPrice');
        const calculatedPrice = document.getElementById('calculatedPrice');
        const targetMargin = document.getElementById('targetMargin');
        const marginProfit = document.getElementById('marginProfit');

        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const rangePotentialProfit = document.getElementById('rangePotentialProfit');

        // Create or update hidden pricing_type input
        let pricingTypeInput = document.querySelector('input[name="pricing_type"][type="hidden"]');
        if (!pricingTypeInput) {
          pricingTypeInput = document.createElement('input');
          pricingTypeInput.type = 'hidden';
          pricingTypeInput.name = 'pricing_type';
          document.getElementById('addVariantForm').appendChild(pricingTypeInput);
        }
        pricingTypeInput.value = pricingType;
        console.log('Pricing type set to:', pricingType);

        // Create hidden tax_rate input if it doesn't exist
        let taxRateInput = document.querySelector('input[name="tax_rate"]');
        if (!taxRateInput) {
          taxRateInput = document.createElement('input');
          taxRateInput.type = 'hidden';
          taxRateInput.name = 'tax_rate';
          document.getElementById('addVariantForm').appendChild(taxRateInput);
        }

        // Create hidden potential_profit input if it doesn't exist
        let potentialProfitInput = document.querySelector('input[name="potential_profit"]');
        if (!potentialProfitInput) {
          potentialProfitInput = document.createElement('input');
          potentialProfitInput.type = 'hidden';
          potentialProfitInput.name = 'potential_profit';
          document.getElementById('addVariantForm').appendChild(potentialProfitInput);
        }

        // Create hidden final_price input if it doesn't exist
        let finalPriceInput = document.querySelector('input[name="final_price"]');
        if (!finalPriceInput) {
          finalPriceInput = document.createElement('input');
          finalPriceInput.type = 'hidden';
          finalPriceInput.name = 'final_price';
          document.getElementById('addVariantForm').appendChild(finalPriceInput);
        }

        switch(pricingType) {
          case 'fixed':
            // Sync cost price from fixed field
            if (costPrice && costPrice.value) {
              if (manualCostPrice) manualCostPrice.value = costPrice.value;
              if (marginCostPrice) marginCostPrice.value = costPrice.value;
              if (rangeCostPrice) rangeCostPrice.value = costPrice.value;
            }

            // Use fixed tax rate
            const fixedTaxRate = document.getElementById('fixedTaxRate');
            if (fixedTaxRate) {
              taxRateInput.value = fixedTaxRate.value;
            }

            // Set potential profit from fixed pricing
            const potentialProfit = document.getElementById('potentialProfit');
            if (potentialProfit && potentialProfitInput) {
              potentialProfitInput.value = potentialProfit.value || '0';
            }

            // Calculate and set final price
            if (sellingPrice && fixedTaxRate) {
              const selling = parseFloat(sellingPrice.value) || 0;
              const tax = parseFloat(fixedTaxRate.value) || 0;
              const final = selling * (1 + tax / 100);
              finalPriceInput.value = final.toFixed(2);
            }

            // Clear fields not used in fixed pricing
            if (targetMargin) targetMargin.value = '';
            if (calculatedPrice) calculatedPrice.value = '';
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';
            break;

          case 'manual':
            // Sync cost price from manual field
            if (manualCostPrice && manualCostPrice.value) {
              if (costPrice) costPrice.value = manualCostPrice.value;
              if (marginCostPrice) marginCostPrice.value = manualCostPrice.value;
              if (rangeCostPrice) rangeCostPrice.value = manualCostPrice.value;
            }

            // Clear selling price and other fields for manual pricing
            if (sellingPrice) sellingPrice.value = '';
            if (targetMargin) targetMargin.value = '';
            if (calculatedPrice) calculatedPrice.value = '';
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';

            taxRateInput.value = '0';
            potentialProfitInput.value = '0';
            finalPriceInput.value = '0';
            break;

          case 'margin':
            // Sync cost price from margin field
            if (marginCostPrice && marginCostPrice.value) {
              if (costPrice) costPrice.value = marginCostPrice.value;
              if (manualCostPrice) manualCostPrice.value = marginCostPrice.value;
              if (rangeCostPrice) rangeCostPrice.value = marginCostPrice.value;
            }

            // Use margin tax rate
            const marginTaxRate = document.getElementById('marginTaxRate');
            if (marginTaxRate) {
              taxRateInput.value = marginTaxRate.value;
            }

            // Copy calculated price to selling price
            if (calculatedPrice && sellingPrice) {
              sellingPrice.value = calculatedPrice.value || '0';
            }

            // Set potential profit from margin pricing
            if (marginProfit && potentialProfitInput) {
              potentialProfitInput.value = marginProfit.value || '0';
            }

            // Calculate and set final price with tax
            if (calculatedPrice && marginTaxRate) {
              const calculated = parseFloat(calculatedPrice.value) || 0;
              const tax = parseFloat(marginTaxRate.value) || 0;
              const final = calculated * (1 + tax / 100);
              finalPriceInput.value = final.toFixed(2);
            }

            // Clear fields not used in margin pricing
            if (minPrice) minPrice.value = '';
            if (maxPrice) maxPrice.value = '';
            break;

          case 'range':
            // Sync cost price from range field
            if (rangeCostPrice && rangeCostPrice.value) {
              if (costPrice) costPrice.value = rangeCostPrice.value;
              if (manualCostPrice) manualCostPrice.value = rangeCostPrice.value;
              if (marginCostPrice) marginCostPrice.value = rangeCostPrice.value;
            }

            // Use range tax rate
            const rangeTaxRate = document.getElementById('rangeTaxRate');
            if (rangeTaxRate) {
              taxRateInput.value = rangeTaxRate.value;
            }

            // Calculate average of min and max for selling price
            if (minPrice && maxPrice && sellingPrice) {
              const min = parseFloat(minPrice.value) || 0;
              const max = parseFloat(maxPrice.value) || 0;
              const average = (min + max) / 2;
              sellingPrice.value = average.toFixed(2);
            }

            // Calculate average potential profit
            if (rangeCostPrice && minPrice && maxPrice && potentialProfitInput) {
              const cost = parseFloat(rangeCostPrice.value) || 0;
              const min = parseFloat(minPrice.value) || 0;
              const max = parseFloat(maxPrice.value) || 0;
              const avgPrice = (min + max) / 2;
              const avgProfit = avgPrice - cost;
              potentialProfitInput.value = avgProfit.toFixed(2);
            }

            // Calculate and set average final price with tax
            if (minPrice && maxPrice && rangeTaxRate) {
              const min = parseFloat(minPrice.value) || 0;
              const max = parseFloat(maxPrice.value) || 0;
              const tax = parseFloat(rangeTaxRate.value) || 0;
              const avgPrice = (min + max) / 2;
              const final = avgPrice * (1 + tax / 100);
              finalPriceInput.value = final.toFixed(2);
            }

            // Clear fields not used in range pricing
            if (targetMargin) targetMargin.value = '';
            if (calculatedPrice) calculatedPrice.value = '';
            break;
        }

        console.log('Tax rate set to:', taxRateInput.value);
        console.log('Final price set to:', finalPriceInput.value);
      }

      // Submit form with validation
      function submitForm() {
        const form = document.getElementById('addVariantForm');
        const submitBtn = document.querySelector('.btn-primary');

        // Add loading state
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;

        setTimeout(() => {
          // Sync pricing fields based on selected pricing type before validation
          const selectedPricingType = document.querySelector('input[name="pricing_type"]:checked');
          if (selectedPricingType) {
            syncPricingFieldsForSubmission(selectedPricingType.value);
          }

          // Check if required fields are filled
          if (!form.checkValidity()) {
            form.reportValidity();
            removeLoading(submitBtn);
            return false;
          }

          // Validate that at least one variant exists
          const variantRows = document.querySelectorAll('#variantTableBody tr');
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
          // Only close the main modal, not variant configuration modals
          const variantModal = document.getElementById('variantModalOverlay');
          const variantSettingsModal = document.getElementById('variantSettingsOverlay');
          const pricingModal = document.getElementById('pricingModalOverlay');

          // Check if any internal modals are open
          const isVariantModalOpen = variantModal && variantModal.style.display !== 'none';
          const isSettingsModalOpen = variantSettingsModal && variantSettingsModal.style.display === 'flex';
          const isPricingModalOpen = pricingModal && pricingModal.style.display !== 'none';

          // Only close main modal if no internal modals are open
          if (!isVariantModalOpen && !isSettingsModalOpen && !isPricingModalOpen) {
            closeModal();
          }
        }
      });



      // Close on overlay click (only if clicking the backdrop, not the modal content)
      // Note: This is for the main page overlay only, not for variant configuration modals
      const mainPageWrapper = document.querySelector('.modal-body-custom');
      if (mainPageWrapper) {
        // Don't attach click handler to modal-overlay as it interferes with variant modals
        // The main page is always visible, so we don't need an overlay click handler
      }

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

        // Save the variant changes
        saveVariantChanges();
      });

      // Submit stocking form
      document.getElementById('variantStockForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Save the variant changes
        saveVariantChanges();
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

        // Populate modal fields; default to 0.00 when values are empty
        if (costInput && costInput.value) {
          document.getElementById('modalCostPrice').value = costInput.value;
        } else {
          document.getElementById('modalCostPrice').value = '0.00';
        }
        if (sellInput && sellInput.value) {
          document.getElementById('modalSellingPrice').value = sellInput.value;
        } else {
          document.getElementById('modalSellingPrice').value = '0.00';
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

        // Auto-generate item code if not provided
        const itemNameInput = document.getElementById('itemName');
        if (itemNameInput) {
          itemNameInput.addEventListener('input', function() {
            const itemCode = document.getElementById('itemCode');
            if (itemCode && !itemCode.value) {
              const name = this.value.replace(/\s+/g, '').toUpperCase();
              const timestamp = Date.now().toString().slice(-4);
              itemCode.value = name.slice(0, 4) + timestamp;
            }
          });
        }

        // Initialize pricing calculations
        setupPricingCalculations();

        // Initialize edit pricing handlers once
        setupEditPricingHandlers();
      });

      // ============================
      // Add New Category Functionality
      // ============================

      // Category panel elements
      const categoryPanel = document.getElementById('addCategoryPanel');
      const categoryOverlay = document.getElementById('categoryPanelOverlay');
      const closeCategoryPanel = document.getElementById('closeCategoryPanel');
      const cancelCategoryBtn = document.getElementById('cancelCategoryBtn');

      // Function to open category panel
      function openCategoryPanel() {
        if (categoryPanel && categoryOverlay) {
          categoryPanel.classList.add('active');
          categoryOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';
          setTimeout(() => {
            document.getElementById('newCategoryName')?.focus();
          }, 300);
        }
      }

      // Function to close category panel
      function closeCategoryPanelFunc() {
        if (categoryPanel && categoryOverlay) {
          categoryPanel.classList.remove('active');
          categoryOverlay.classList.remove('active');
          document.body.style.overflow = '';
          const form = document.getElementById('addCategoryForm');
          if (form) {
            form.reset();
            document.getElementById('newCategoryName')?.classList.remove('is-invalid');
            const errorDiv = document.getElementById('categoryNameError');
            if (errorDiv) errorDiv.textContent = '';
          }
        }
      }

      // Close panel listeners
      if (closeCategoryPanel) closeCategoryPanel.addEventListener('click', closeCategoryPanelFunc);
      if (cancelCategoryBtn) cancelCategoryBtn.addEventListener('click', closeCategoryPanelFunc);
      if (categoryOverlay) categoryOverlay.addEventListener('click', closeCategoryPanelFunc);

      // Escape key listener
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && categoryPanel?.classList.contains('active')) {
          closeCategoryPanelFunc();
        }
      });

      // Handle form submission
      const addCategoryForm = document.getElementById('addCategoryForm');
      if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const saveCategoryBtn = document.getElementById('saveCategoryBtn');
          const categoryNameInput = document.getElementById('newCategoryName');
          const categoryNameError = document.getElementById('categoryNameError');

          if (saveCategoryBtn.disabled) return;

          const categoryName = categoryNameInput.value.trim();

          if (!categoryName || categoryName.length < 5 || categoryName.length > 100) {
            categoryNameInput.classList.add('is-invalid');
            categoryNameError.textContent = !categoryName ? 'Please enter a category name' :
              categoryName.length < 5 ? 'Category name must be at least 5 characters' :
              'Category name must not exceed 100 characters';
            return;
          }

          saveCategoryBtn.disabled = true;
          saveCategoryBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                           document.querySelector('input[name="_token"]')?.value;

          fetch('/manager/category/create', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify({ category_name: categoryName })
          })
          .then(response => response.ok ? response.json() : response.json().then(data => {
            throw { isValidation: true, errors: data.errors || {}, message: data.message };
          }))
          .then(data => {
            const newOption = document.createElement('option');
            newOption.value = data.category.id;
            newOption.textContent = data.category.category_name;
            newOption.selected = true;

            const addNewOption = categorySelect.querySelector('option[value="add_new_category"]');
            if (addNewOption) {
              categorySelect.insertBefore(newOption, addNewOption);
            } else {
              categorySelect.appendChild(newOption);
            }

            if (typeof $ !== 'undefined' && $.fn.select2) $('#category').trigger('change');

            closeCategoryPanelFunc();

            if (typeof Swal !== 'undefined') {
              Swal.fire({ icon: 'success', title: 'Success!', text: 'Category created successfully', timer: 2000, showConfirmButton: false });
            } else {
              alert('Category created successfully');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            if (error.isValidation && error.errors) {
              const errorMessages = Object.values(error.errors).flat();
              categoryNameInput.classList.add('is-invalid');
              categoryNameError.textContent = errorMessages[0] || 'Validation error occurred';
            } else {
              if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'An error occurred while creating the category' });
              } else {
                alert('Error: ' + (error.message || 'An error occurred'));
              }
            }
          })
          .finally(() => {
            saveCategoryBtn.disabled = false;
            saveCategoryBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save';
          });
        });
      }

      // Clear validation on input
      const newCategoryNameInput = document.getElementById('newCategoryName');
      if (newCategoryNameInput) {
        newCategoryNameInput.addEventListener('input', function() {
          this.classList.remove('is-invalid');
          document.getElementById('categoryNameError').textContent = '';
        });
      }

      // ============================
      // Add New Supplier Functionality
      // ============================

      // Supplier panel elements
      const supplierPanel = document.getElementById('addSupplierPanel');
      const supplierOverlay = document.getElementById('supplierPanelOverlay');
      const closeSupplierPanel = document.getElementById('closeSupplierPanel');
      const cancelSupplierBtn = document.getElementById('cancelSupplierBtn');

      // Function to open supplier panel
      function openSupplierPanel() {
        if (supplierPanel && supplierOverlay) {
          supplierPanel.classList.add('active');
          supplierOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';
          setTimeout(() => {
            document.getElementById('newSupplierName')?.focus();
          }, 300);
        }
      }

      // Function to close supplier panel
      function closeSupplierPanelFunc() {
        if (supplierPanel && supplierOverlay) {
          supplierPanel.classList.remove('active');
          supplierOverlay.classList.remove('active');
          document.body.style.overflow = '';
          const form = document.getElementById('addSupplierForm');
          if (form) {
            form.reset();
            document.querySelectorAll('#addSupplierForm .form-control').forEach(input => {
              input.classList.remove('is-invalid');
            });
            document.getElementById('supplierNameError').textContent = '';
            document.getElementById('supplierEmailError').textContent = '';
          }
        }
      }

      // Close panel listeners
      if (closeSupplierPanel) closeSupplierPanel.addEventListener('click', closeSupplierPanelFunc);
      if (cancelSupplierBtn) cancelSupplierBtn.addEventListener('click', closeSupplierPanelFunc);
      if (supplierOverlay) supplierOverlay.addEventListener('click', closeSupplierPanelFunc);

      // Handle supplier form submission
      const addSupplierForm = document.getElementById('addSupplierForm');
      if (addSupplierForm) {
        addSupplierForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const saveSupplierBtn = document.getElementById('saveSupplierBtn');
          const supplierNameInput = document.getElementById('newSupplierName');
          const supplierEmailInput = document.getElementById('newSupplierEmail');
          const supplierContactInput = document.getElementById('newSupplierContact');
          const supplierPhoneInput = document.getElementById('newSupplierPhone');
          const supplierAddressInput = document.getElementById('newSupplierAddress');
          const supplierNameError = document.getElementById('supplierNameError');
          const supplierEmailError = document.getElementById('supplierEmailError');

          if (saveSupplierBtn.disabled) return;

          const supplierName = supplierNameInput.value.trim();
          const supplierEmail = supplierEmailInput.value.trim();
          const supplierContact = supplierContactInput.value.trim();
          const supplierPhone = supplierPhoneInput.value.trim();
          const supplierAddress = supplierAddressInput.value.trim();

          // Basic validation
          let hasError = false;
          if (!supplierName) {
            supplierNameInput.classList.add('is-invalid');
            supplierNameError.textContent = 'Supplier name is required';
            hasError = true;
          }
          if (!supplierEmail) {
            supplierEmailInput.classList.add('is-invalid');
            supplierEmailError.textContent = 'Email address is required';
            hasError = true;
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(supplierEmail)) {
            supplierEmailInput.classList.add('is-invalid');
            supplierEmailError.textContent = 'Please enter a valid email address';
            hasError = true;
          }

          if (hasError) return;

          saveSupplierBtn.disabled = true;
          saveSupplierBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                           document.querySelector('input[name="_token"]')?.value;

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
          .then(response => response.ok ? response.json() : response.json().then(data => {
            throw { isValidation: true, errors: data.errors || {}, message: data.message };
          }))
          .then(data => {
            const supplierSelect = document.getElementById('supplier');
            const newOption = document.createElement('option');
            newOption.value = data.supplier.id;
            newOption.textContent = data.supplier.name;
            newOption.selected = true;

            const addNewOption = supplierSelect.querySelector('option[value="add_new_supplier"]');
            if (addNewOption) {
              supplierSelect.insertBefore(newOption, addNewOption);
            } else {
              supplierSelect.appendChild(newOption);
            }

            if (typeof $ !== 'undefined' && $.fn.select2) $('#supplier').trigger('change');

            closeSupplierPanelFunc();

            if (typeof Swal !== 'undefined') {
              Swal.fire({ icon: 'success', title: 'Success!', text: 'Supplier created successfully', timer: 2000, showConfirmButton: false });
            } else {
              alert('Supplier created successfully');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            if (error.isValidation && error.errors) {
              if (error.errors.name) {
                supplierNameInput.classList.add('is-invalid');
                supplierNameError.textContent = error.errors.name[0];
              }
              if (error.errors.email) {
                supplierEmailInput.classList.add('is-invalid');
                supplierEmailError.textContent = error.errors.email[0];
              }
            } else {
              if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'An error occurred while creating the supplier' });
              } else {
                alert('Error: ' + (error.message || 'An error occurred'));
              }
            }
          })
          .finally(() => {
            saveSupplierBtn.disabled = false;
            saveSupplierBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save';
          });
        });
      }

      // Clear validation on input
      const newSupplierNameInput = document.getElementById('newSupplierName');
      if (newSupplierNameInput) {
        newSupplierNameInput.addEventListener('input', function() {
          this.classList.remove('is-invalid');
          document.getElementById('supplierNameError').textContent = '';
        });
      }

      const newSupplierEmailInput = document.getElementById('newSupplierEmail');
      if (newSupplierEmailInput) {
        newSupplierEmailInput.addEventListener('input', function() {
          this.classList.remove('is-invalid');
          document.getElementById('supplierEmailError').textContent = '';
        });
      }

      // Initialize Select2 and category panel after DOM loads
      document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing select2 and category panel');

        // Initialize Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
          console.log('Select2 is available, initializing...');

          // Initialize unit with Select2 and tags support (allows creating new units)
          $('#unit').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select or type to create unit',
            tags: true,
            dropdownParent: $('body'),
            createTag: function (params) {
              var term = $.trim(params.term);
              if (term === '' || term.toLowerCase() === '+ add new unit') {
                return null;
              }
              return {
                id: term,
                text: term,
                newTag: true
              }
            }
          });

          console.log('Initializing category select2...');
          // Initialize category with tags support (allows creating new options)
          $('#category').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Select or type to create category',
            tags: true,
            dropdownParent: $('body'),
            createTag: function (params) {
              var term = $.trim(params.term);
              if (term === '') {
                return null;
              }
              return {
                id: term,
                text: term,
                newTag: true
              }
            }
          });

          console.log('Category select2 initialized, attaching event handler...');

          // Attach event handler to category select
          $('#category').on('select2:select', function(e) {
            console.log('Category selected EVENT FIRED:', e.params.data);
            const selectedValue = e.params.data.id;
            console.log('Selected value:', selectedValue);
            if (selectedValue === 'add_new_category') {
              console.log('Opening category panel...');
              // Reset the select to empty
              $(this).val('').trigger('change');
              // Show the panel
              openCategoryPanel();
            }
          });

        // Initialize supplier with tags support
        $('#supplier').select2({
          theme: 'bootstrap',
          width: '100%',
          placeholder: 'Select or type to create supplier',
          tags: true,
          dropdownParent: $('body'),
          createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
              return null;
            }
            return {
              id: term,
              text: term,
              newTag: true
            }
          }
        });

          // Attach event handler to supplier select (after Select2 initialization)
          $('#supplier').on('select2:select', function(e) {
            const selectedValue = e.params.data.id;
            if (selectedValue === 'add_new_supplier') {
              // Reset the select to empty
              $(this).val('').trigger('change');
              // Show the panel
              openSupplierPanel();
            }
          });

        // Initialize tax rate dropdowns in edit variant modal
        $('#editTaxRate').select2({
          theme: 'bootstrap',
          width: '100%',
          placeholder: 'Select tax rate',
          minimumResultsForSearch: -1, // Hide search box
          dropdownParent: $('#editVariantModalOverlay')
        });

        $('#editMarginTaxRate').select2({
          theme: 'bootstrap',
          width: '100%',
          placeholder: 'Select tax rate',
          minimumResultsForSearch: -1, // Hide search box
          dropdownParent: $('#editVariantModalOverlay')
        });

        $('#editRangeTaxRate').select2({
          theme: 'bootstrap',
          width: '100%',
          placeholder: 'Select tax rate',
          minimumResultsForSearch: -1, // Hide search box
          dropdownParent: $('#editVariantModalOverlay')
        });

        // ============================
        // Add New Category Functionality
        // ============================

        // Category panel elements
        const closeCategoryPanel = document.getElementById('closeCategoryPanel');
        const cancelCategoryBtn = document.getElementById('cancelCategoryBtn');
        const categoryPanel = document.getElementById('addCategoryPanel');
        const categoryOverlay = document.getElementById('categoryPanelOverlay');

        // Function to close category panel
        function closeCategoryPanelFunc() {
          if (categoryPanel && categoryOverlay) {
            categoryPanel.classList.remove('active');
            categoryOverlay.classList.remove('active');
            document.body.style.overflow = '';
            // Reset form
            const form = document.getElementById('addCategoryForm');
            if (form) {
              form.reset();
              // Clear validation errors
              document.querySelectorAll('#addCategoryForm .form-control').forEach(input => {
                input.classList.remove('is-invalid');
              });
              document.getElementById('categoryNameError').textContent = '';
            }
          }
        }

        // Close panel event listeners
        if (closeCategoryPanel) {
          closeCategoryPanel.addEventListener('click', closeCategoryPanelFunc);
        }
        if (cancelCategoryBtn) {
          cancelCategoryBtn.addEventListener('click', closeCategoryPanelFunc);
        }
        if (categoryOverlay) {
          categoryOverlay.addEventListener('click', closeCategoryPanelFunc);
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && categoryPanel?.classList.contains('active')) {
            closeCategoryPanelFunc();
          }
        });

        // Handle category form submission
        const addCategoryForm = document.getElementById('addCategoryForm');
        if (addCategoryForm) {
          addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const saveCategoryBtn = document.getElementById('saveCategoryBtn');
            const categoryNameInput = document.getElementById('newCategoryName');
            const categoryNameError = document.getElementById('categoryNameError');

            // Check if button is already disabled
            if (saveCategoryBtn.disabled) {
              return;
            }

            // Get category name
            const categoryName = categoryNameInput.value.trim();

            // Basic validation
            if (!categoryName) {
              categoryNameInput.classList.add('is-invalid');
              categoryNameError.textContent = 'Please enter a category name';
              return;
            }

            if (categoryName.length < 5) {
              categoryNameInput.classList.add('is-invalid');
              categoryNameError.textContent = 'Category name must be at least 5 characters';
              return;
            }

            if (categoryName.length > 100) {
              categoryNameInput.classList.add('is-invalid');
              categoryNameError.textContent = 'Category name must not exceed 100 characters';
              return;
            }

            // Disable button and show loading state
            saveCategoryBtn.disabled = true;
            saveCategoryBtn.innerHTML = '<span class=\"spinner-border spinner-border-sm me-2\"></span>Saving...';

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content')
                             || document.querySelector('input[name=\"_token\"]')?.value;

            // Send AJAX request
            fetch('/manager/category/create', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                category_name: categoryName
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
            .then(data => {
              // Success - add the new category to the dropdown
              const newOption = new Option(data.category.category_name, data.category.id, true, true);

              // Find the \"Add New Category\" option and insert before it
              const $select = $('#category');
              const $addNewOption = $select.find('option[value=\"add_new_category\"]');
              if ($addNewOption.length) {
                $addNewOption.before(newOption);
              } else {
                $select.append(newOption);
              }
              $select.trigger('change');

              // Close panel
              closeCategoryPanelFunc();

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
            })
            .catch(error => {
              console.error('Error:', error);

              if (error.isValidation && error.errors) {
                // Handle validation errors
                const errorMessages = Object.values(error.errors).flat();
                categoryNameInput.classList.add('is-invalid');
                categoryNameError.textContent = errorMessages[0] || 'Validation error occurred';
              } else {
                // Handle general errors
                if (typeof Swal !== 'undefined') {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while creating the category',
                  });
                } else {
                  alert('Error: ' + (error.message || 'An error occurred while creating the category'));
                }
              }
            })
            .finally(() => {
              // Re-enable button
              saveCategoryBtn.disabled = false;
              saveCategoryBtn.innerHTML = '<i class=\"mdi mdi-content-save\"></i> Save';
            });
          });
        }

        // Clear validation error when user types
        const newCategoryNameInput = document.getElementById('newCategoryName');
        if (newCategoryNameInput) {
          newCategoryNameInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            document.getElementById('categoryNameError').textContent = '';
          });
        }
        } else {
          console.warn('jQuery or Select2 not available. Dropdowns will use standard HTML select.');
        }
      });


 // Edit Variant Modal Functions
      function openEditVariantModal(variantIndex, variantSku) {
        const modal = document.getElementById('editVariantModalOverlay');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Store the variant index
        document.getElementById('editVariantIndex').value = variantIndex;

        // Find the variant row
        const variantRow = document.querySelector(`#variantTableBody tr:nth-child(${variantIndex})`);
        if (!variantRow) return;

        // Get current values from the row
        const variantDisplayDiv = variantRow.querySelector('.variant-display-text');
        const skuInput = variantRow.querySelector('input[name*="[sku]"]');
        const costPriceInput = variantRow.querySelector('input[name*="[cost_price]"]');
        const sellingPriceInput = variantRow.querySelector('input[name*="[selling_price]"]');
        const stockQtyInput = variantRow.querySelector('input[name*="[stock_quantity]"]');
        const lowStockInput = variantRow.querySelector('input[name*="[low_stock_threshold]"]');

        // Populate modal fields
        document.getElementById('editVariantDisplay').value = variantDisplayDiv ? variantDisplayDiv.textContent.trim() : '';
        document.getElementById('editVariantSku').value = skuInput ? skuInput.value : variantSku;
        document.getElementById('editVariantBarcode').value = ''; // TODO: Get from database when available

        // Populate all method-specific cost price fields with the same initial value
        const costPriceValue = costPriceInput ? costPriceInput.value : '0.00';
        document.getElementById('editFixedCostPrice').value = costPriceValue;
        document.getElementById('editManualCostPrice').value = costPriceValue;
        document.getElementById('editMarginCostPrice').value = costPriceValue;
        document.getElementById('editRangeCostPrice').value = costPriceValue;

        document.getElementById('editSellingPrice').value = sellingPriceInput ? sellingPriceInput.value : '0.00';
        document.getElementById('editStockQuantity').value = stockQtyInput ? stockQtyInput.value : '0';
        document.getElementById('editLowStockThreshold').value = lowStockInput ? lowStockInput.value : '0';

        // Reset tax rate dropdowns
        $('#editTaxRate').val('0').trigger('change');
        $('#editMarginTaxRate').val('0').trigger('change');
        $('#editRangeTaxRate').val('0').trigger('change');

        // Ensure Fixed pricing is selected by default
        const editFixedRadio = document.getElementById('editFixedPricing');
        if (editFixedRadio) {
          editFixedRadio.checked = true;
          editShowPricingFields('fixed');
          editShowPricingDescription('fixed');
        }

        // Calculate initial profit margin and final price
        editCalculateProfitMargin();

        // Reset to first tab and ensure tab functionality
        const firstTabButton = document.querySelector('#edit-item-details-tab');
        if (firstTabButton) {
          // Remove active class from all tabs
          document.querySelectorAll('#editVariantTab .nav-link').forEach(tab => {
            tab.classList.remove('active');
          });
          document.querySelectorAll('#editVariantTabContent .tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
          });

          // Activate first tab
          firstTabButton.classList.add('active');
          document.getElementById('edit-item-details').classList.add('show', 'active');

          // Initialize tab switching
          initializeEditVariantTabs();
        }
      }

      // Initialize tab switching for edit variant modal
      function initializeEditVariantTabs() {
        const tabButtons = document.querySelectorAll('#editVariantTab .nav-link');

        tabButtons.forEach(button => {
          button.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active from all tabs
            document.querySelectorAll('#editVariantTab .nav-link').forEach(tab => {
              tab.classList.remove('active');
            });
            document.querySelectorAll('#editVariantTabContent .tab-pane').forEach(pane => {
              pane.classList.remove('show', 'active');
            });

            // Activate clicked tab
            this.classList.add('active');
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
              targetPane.classList.add('show', 'active');
            }
          });
        });
      }

      function closeEditVariantModal() {
        const modal = document.getElementById('editVariantModalOverlay');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
      }

      function saveVariantChanges() {
        const variantIndex = document.getElementById('editVariantIndex').value;
        if (!variantIndex) return;

        const variantRow = document.querySelector(`#variantTableBody tr:nth-child(${variantIndex})`);
        if (!variantRow) return;

        // Get the active pricing method
        const selectedPricingType = document.querySelector('input[name="edit_pricing_type"]:checked');
        let costPrice = '0.00';

        // Get cost price from the appropriate method-specific field
        if (selectedPricingType) {
          switch(selectedPricingType.value) {
            case 'fixed':
              costPrice = document.getElementById('editFixedCostPrice').value;
              break;
            case 'manual':
              costPrice = document.getElementById('editManualCostPrice').value;
              break;
            case 'margin':
              costPrice = document.getElementById('editMarginCostPrice').value;
              break;
            case 'range':
              costPrice = document.getElementById('editRangeCostPrice').value;
              break;
            default:
              costPrice = document.getElementById('editFixedCostPrice').value;
          }
        }

        // Get values from modal
        const sellingPrice = document.getElementById('editSellingPrice').value;
        const stockQty = document.getElementById('editStockQuantity').value;
        const lowStock = document.getElementById('editLowStockThreshold').value;

        // Update hidden inputs in the table
        const costPriceInput = variantRow.querySelector('input[name*="[cost_price]"]');
        const sellingPriceInput = variantRow.querySelector('input[name*="[selling_price]"]');
        const stockQtyInput = variantRow.querySelector('input[name*="[stock_quantity]"]');
        const lowStockInput = variantRow.querySelector('input[name*="[low_stock_threshold]"]');

        if (costPriceInput) costPriceInput.value = costPrice;
        if (sellingPriceInput) sellingPriceInput.value = sellingPrice;
        if (stockQtyInput) stockQtyInput.value = stockQty;
        if (lowStockInput) lowStockInput.value = lowStock;

        // Update visible displays
        const costDisplay = variantRow.querySelector('.cost-price-display');
        const sellDisplay = variantRow.querySelector('.sell-price-display');
        if (costDisplay) costDisplay.textContent = '₦' + parseFloat(costPrice).toFixed(2);
        if (sellDisplay) sellDisplay.textContent = '₦' + parseFloat(sellingPrice).toFixed(2);

        // Close modal
        closeEditVariantModal();

        showNotification('Variant details updated successfully!', 'success');
      }

      // Edit Modal Pricing Handlers
      function setupEditPricingHandlers() {
        console.log('setupEditPricingHandlers called');
        const pricingTypeRadios = document.querySelectorAll('input[name="edit_pricing_type"]');
        console.log('Found edit pricing type radios:', pricingTypeRadios.length);

        pricingTypeRadios.forEach(radio => {
          console.log('Adding change listener to radio:', radio.value);
          radio.addEventListener('change', function() {
            console.log('Radio changed to:', this.value);
            if (this.checked) {
              editShowPricingFields(this.value);
              editShowPricingDescription(this.value);
            }
          });
        });

        // Fixed Pricing handlers
        const editFixedCostPrice = document.getElementById('editFixedCostPrice');
        const editSellingPrice = document.getElementById('editSellingPrice');
        const editTaxRate = document.getElementById('editTaxRate');

        if (editFixedCostPrice) editFixedCostPrice.addEventListener('input', editCalculateProfitMargin);
        if (editSellingPrice) editSellingPrice.addEventListener('input', editCalculateProfitMargin);
        if (editTaxRate) editTaxRate.addEventListener('change', editCalculateFinalPrice);

        // Margin Pricing handlers
        const editMarginCostPrice = document.getElementById('editMarginCostPrice');
        const editTargetMargin = document.getElementById('editTargetMargin');
        const editMarginTaxRate = document.getElementById('editMarginTaxRate');

        if (editMarginCostPrice) editMarginCostPrice.addEventListener('input', editCalculateMarginPrice);
        if (editTargetMargin) editTargetMargin.addEventListener('input', editCalculateMarginPrice);
        if (editMarginTaxRate) editMarginTaxRate.addEventListener('change', editCalculateMarginPrice);

        // Range Pricing handlers
        const editRangeCostPrice = document.getElementById('editRangeCostPrice');
        const editMinPrice = document.getElementById('editMinPrice');
        const editMaxPrice = document.getElementById('editMaxPrice');

        if (editRangeCostPrice) editRangeCostPrice.addEventListener('input', editCalculateRangeProfits);
        if (editMinPrice) {
          editMinPrice.addEventListener('input', function() {
            editValidatePriceRange();
            editCalculateRangeProfits();
          });
        }
        if (editMaxPrice) {
          editMaxPrice.addEventListener('input', function() {
            editValidatePriceRange();
            editCalculateRangeProfits();
          });
        }
      }

      function editShowPricingFields(type) {
        console.log('editShowPricingFields called with type:', type);

        // Hide all pricing fields within edit modal
        const editModal = document.getElementById('editVariantModalOverlay');
        if (editModal) {
          editModal.querySelectorAll('.pricing-fields').forEach(field => {
            field.style.display = 'none';
          });
        }

        // Remove all required attributes first
        const editSellingPrice = document.getElementById('editSellingPrice');
        const editTargetMargin = document.getElementById('editTargetMargin');
        const editMinPrice = document.getElementById('editMinPrice');
        const editMaxPrice = document.getElementById('editMaxPrice');
        const editManualCostPrice = document.getElementById('editManualCostPrice');
        const editFixedCostPrice = document.getElementById('editFixedCostPrice');
        const editMarginCostPrice = document.getElementById('editMarginCostPrice');
        const editRangeCostPrice = document.getElementById('editRangeCostPrice');

        if (editSellingPrice) editSellingPrice.required = false;
        if (editTargetMargin) editTargetMargin.required = false;
        if (editMinPrice) editMinPrice.required = false;
        if (editMaxPrice) editMaxPrice.required = false;
        if (editManualCostPrice) editManualCostPrice.required = false;
        if (editFixedCostPrice) editFixedCostPrice.required = false;
        if (editMarginCostPrice) editMarginCostPrice.required = false;
        if (editRangeCostPrice) editRangeCostPrice.required = false;

        switch(type) {
          case 'fixed':
            const editFixedFields = document.getElementById('editFixedFields');
            if (editFixedFields) {
              editFixedFields.style.display = 'block';
              if (editFixedCostPrice) editFixedCostPrice.required = true;
              if (editSellingPrice) editSellingPrice.required = true;
            }
            console.log('Edit Fixed pricing fields shown');
            editCalculateProfitMargin();
            break;
          case 'manual':
            const editManualFields = document.getElementById('editManualFields');
            if (editManualFields) {
              editManualFields.style.display = 'block';
              if (editManualCostPrice) editManualCostPrice.required = true;
            }
            console.log('Edit Manual pricing fields shown');
            break;
          case 'margin':
            const editMarginFields = document.getElementById('editMarginFields');
            if (editMarginFields) {
              editMarginFields.style.display = 'block';
              if (editMarginCostPrice) editMarginCostPrice.required = true;
              if (editTargetMargin) editTargetMargin.required = true;
            }
            console.log('Edit Margin pricing fields shown');
            editCalculateMarginPrice();
            break;
          case 'range':
            const editRangeFields = document.getElementById('editRangeFields');
            if (editRangeFields) {
              editRangeFields.style.display = 'block';
              if (editRangeCostPrice) editRangeCostPrice.required = true;
              if (editMinPrice) editMinPrice.required = true;
              if (editMaxPrice) editMaxPrice.required = true;
            }
            console.log('Edit Range pricing fields shown');
            editCalculateRangeProfits();
            break;
        }
      }

      function editShowPricingDescription(type) {
        const descContainer = document.getElementById('editPricingDescription');
        document.querySelectorAll('#editPricingDescription .pricing-desc').forEach(desc => {
          desc.style.display = 'none';
        });

        if (type) {
          descContainer.style.display = 'block';
          const targetDesc = document.getElementById('edit' + type.charAt(0).toUpperCase() + type.slice(1) + 'Desc');
          if (targetDesc) targetDesc.style.display = 'block';
        } else {
          descContainer.style.display = 'none';
        }
      }

      function editCalculateProfitMargin() {
        const editFixedCostPrice = document.getElementById('editFixedCostPrice');
        const editSellingPrice = document.getElementById('editSellingPrice');
        const editProfitMargin = document.getElementById('editProfitMargin');
        const editPotentialProfit = document.getElementById('editPotentialProfit');

        if (!editFixedCostPrice || !editSellingPrice || !editProfitMargin || !editPotentialProfit) return;

        const costPrice = parseFloat(editFixedCostPrice.value) || 0;
        const sellingPrice = parseFloat(editSellingPrice.value) || 0;

        if (costPrice > 0 && sellingPrice > 0) {
          const profit = sellingPrice - costPrice;
          const margin = ((profit / costPrice) * 100).toFixed(2);

          editProfitMargin.value = margin + '%';
          editPotentialProfit.value = profit.toFixed(2);
        } else {
          editProfitMargin.value = '0%';
          editPotentialProfit.value = '0.00';
        }

        // Also calculate final price
        editCalculateFinalPrice();
      }

      function editCalculateFinalPrice() {
        const editSellingPrice = document.getElementById('editSellingPrice');
        const editTaxRate = document.getElementById('editTaxRate');
        const editFinalPrice = document.getElementById('editFinalPrice');

        if (!editSellingPrice || !editTaxRate || !editFinalPrice) return;

        const sellingPrice = parseFloat(editSellingPrice.value) || 0;
        const taxRate = parseFloat(editTaxRate.value) || 0;

        if (sellingPrice > 0) {
          // Apply tax only
          let finalPrice = sellingPrice * (1 + (taxRate / 100));
          editFinalPrice.value = finalPrice.toFixed(2);
        } else {
          editFinalPrice.value = '0.00';
        }
      }

      function editCalculateMarginPrice() {
        const editMarginCostPrice = document.getElementById('editMarginCostPrice');
        const editTargetMargin = document.getElementById('editTargetMargin');
        const editMarginTaxRate = document.getElementById('editMarginTaxRate');
        const editCalculatedPrice = document.getElementById('editCalculatedPrice');
        const editMarginProfit = document.getElementById('editMarginProfit');

        if (!editMarginCostPrice || !editTargetMargin || !editCalculatedPrice || !editMarginProfit) return;

        const costPrice = parseFloat(editMarginCostPrice.value) || 0;
        const targetMargin = parseFloat(editTargetMargin.value) || 0;
        const taxRate = parseFloat(editMarginTaxRate?.value) || 0;

        if (costPrice > 0 && targetMargin > 0) {
          let calculatedSellingPrice = costPrice * (1 + (targetMargin / 100));
          const profit = calculatedSellingPrice - costPrice;

          // Apply tax if applicable
          let finalPrice = calculatedSellingPrice;
          if (taxRate > 0) {
            finalPrice = calculatedSellingPrice * (1 + (taxRate / 100));
          }

          editCalculatedPrice.value = calculatedSellingPrice.toFixed(2);
          editMarginProfit.value = profit.toFixed(2);

          // Update final price display if exists
          const editMarginFinalPrice = document.getElementById('editMarginFinalPrice');
          if (editMarginFinalPrice) {
            editMarginFinalPrice.textContent = '₦' + finalPrice.toFixed(2);
          }
        } else {
          editCalculatedPrice.value = '';
          editMarginProfit.value = '0.00';
        }
      }

      function editCalculateRangeProfits() {
        const editRangeCostPrice = document.getElementById('editRangeCostPrice');
        const editMinPrice = document.getElementById('editMinPrice');
        const editMaxPrice = document.getElementById('editMaxPrice');
        const editRangePotentialProfit = document.getElementById('editRangePotentialProfit');

        if (!editRangeCostPrice || !editMinPrice || !editMaxPrice || !editRangePotentialProfit) return;

        const costPrice = parseFloat(editRangeCostPrice.value) || 0;
        const minPrice = parseFloat(editMinPrice.value) || 0;
        const maxPrice = parseFloat(editMaxPrice.value) || 0;

        if (costPrice > 0 && minPrice > 0 && maxPrice > 0) {
          const minProfit = minPrice - costPrice;
          const maxProfit = maxPrice - costPrice;
          editRangePotentialProfit.value = `${minProfit.toFixed(2)} to ${maxProfit.toFixed(2)}`;
        } else {
          editRangePotentialProfit.value = '0.00 to 0.00';
        }
      }

      function editValidatePriceRange() {
        const minPrice = parseFloat(document.getElementById('editMinPrice').value) || 0;
        const maxPrice = parseFloat(document.getElementById('editMaxPrice').value) || 0;
        const costPrice = parseFloat(document.getElementById('editRangeCostPrice').value) || 0;

        if (minPrice > 0 && maxPrice > 0 && minPrice >= maxPrice) {
          showNotification('Minimum price must be less than maximum price', 'error');
          return false;
        }
        return true;
      }

      // Close modal on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          const modal = document.getElementById('editVariantModalOverlay');
          if (modal && modal.style.display === 'flex') {
            closeEditVariantModal();
          }
        }
      });

      // Close modal on overlay click
      document.addEventListener('click', function(e) {
        const modal = document.getElementById('editVariantModalOverlay');
        if (e.target === modal) {
          closeEditVariantModal();
        }
      });
