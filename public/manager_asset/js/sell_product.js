
    // --- Discount Side Panel Logic ---
    let availableDiscounts = [];
    let selectedDiscount = null;

    const addDiscountBtn = document.getElementById('addDiscountBtn');
    const discountPanel = document.getElementById('discountSidePanel');
    const discountPanelOverlay = document.getElementById('discountPanelOverlay');
    const discountSelect = document.getElementById('discountSelect');
    const applyDiscountBtn = document.getElementById('applyDiscountBtn');
    const closeDiscountPanel = document.getElementById('closeDiscountPanel');
    const cancelDiscountBtn = document.getElementById('cancelDiscountBtn');

    // Helper to format currency
    function formatCurrency(amount) {
      return '₦' + parseFloat(amount).toLocaleString();
    }

    // Open side panel and fetch discounts
    if (addDiscountBtn) {
      addDiscountBtn.addEventListener('click', function() {
        if (discountPanel) discountPanel.classList.add('open');
        if (discountPanelOverlay) discountPanelOverlay.style.display = 'block';
        if (discountSelect) {
          discountSelect.innerHTML = '<option value="" selected disabled>Loading discounts...</option>';
          applyDiscountBtn.disabled = true;
          selectedDiscount = null;
          fetch('/manager/get_discounts', {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success && data.discounts.length > 0) {
              availableDiscounts = data.discounts;
              discountSelect.innerHTML = '<option value="" selected disabled>Select a discount</option>';
              data.discounts.forEach(function(discount, idx) {
                const option = document.createElement('option');
                option.value = discount.id;
                option.textContent = `${discount.discount_name} (₦${parseFloat(discount.discount_rate).toLocaleString()})`;
                option.dataset.rate = discount.discount_rate;
                discountSelect.appendChild(option);
              });
            } else {
              discountSelect.innerHTML = '<option value="" disabled>No discounts available</option>';
            }
          })
          .catch(() => {
            discountSelect.innerHTML = '<option value="" disabled>Failed to load discounts</option>';
          });
        }
      });
    }

    // Close side panel function
    function closeDiscountSidePanel() {
      if (discountPanel) discountPanel.classList.remove('open');
      if (discountPanelOverlay) discountPanelOverlay.style.display = 'none';
    }
    if (closeDiscountPanel) closeDiscountPanel.addEventListener('click', closeDiscountSidePanel);
    if (cancelDiscountBtn) cancelDiscountBtn.addEventListener('click', closeDiscountSidePanel);
    if (discountPanelOverlay) discountPanelOverlay.addEventListener('click', closeDiscountSidePanel);


    // Handle discount selection
    if (discountSelect) {
      discountSelect.addEventListener('change', function() {
        const selectedId = this.value;
        selectedDiscount = availableDiscounts.find(d => d.id == selectedId);
        applyDiscountBtn.disabled = !selectedDiscount;
      });
    }

    // Apply discount to cart
    if (applyDiscountBtn) {
      applyDiscountBtn.addEventListener('click', function() {
        if (!selectedDiscount) return;
        window.selectedDiscount = selectedDiscount;
        updateCartUI();
        closeDiscountSidePanel();
      });
    }

    // Patch checkout and save to include discount
    function getDiscountAmount() {
      return window.selectedDiscount ? parseFloat(window.selectedDiscount.discount_rate) : 0;
    }

    // Patch checkout and save to include discount
    function getDiscountAmount() {
      return window.selectedDiscount ? parseFloat(window.selectedDiscount.discount_rate) : 0;
    }
document.addEventListener('DOMContentLoaded', function() {
  // --- Discount Side Panel Logic ---
  let availableDiscounts = [];
  let selectedDiscount = null;

  const addDiscountBtn = document.getElementById('addDiscountBtn');
  const discountPanel = document.getElementById('discountSidePanel');
  const discountPanelOverlay = document.getElementById('discountPanelOverlay');
  const discountSelect = document.getElementById('discountSelect');
  const applyDiscountBtn = document.getElementById('applyDiscountBtn');
  const closeDiscountPanel = document.getElementById('closeDiscountPanel');
  const cancelDiscountBtn = document.getElementById('cancelDiscountBtn');

  // Helper to format currency
  function formatCurrency(amount) {
    return '₦' + parseFloat(amount).toLocaleString();
  }

  // Open side panel and fetch discounts
  if (addDiscountBtn) {
    addDiscountBtn.addEventListener('click', function() {
      if (discountPanel) discountPanel.classList.add('open');
      if (discountPanelOverlay) discountPanelOverlay.style.display = 'block';
      if (discountSelect) {
        discountSelect.innerHTML = '<option value="" selected disabled>Loading discounts...</option>';
        if (applyDiscountBtn) applyDiscountBtn.disabled = true;
        selectedDiscount = null;
        fetch('/manager/get_discounts', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success && data.discounts.length > 0) {
            availableDiscounts = data.discounts;
            discountSelect.innerHTML = '<option value="" selected disabled>Select a discount</option>';
            data.discounts.forEach(function(discount, idx) {
              const option = document.createElement('option');
              option.value = discount.id;
              option.textContent = `${discount.discount_name} (₦${parseFloat(discount.discount_rate).toLocaleString()})`;
              option.dataset.rate = discount.discount_rate;
              discountSelect.appendChild(option);
            });
          } else {
            discountSelect.innerHTML = '<option value="" disabled>No discounts available</option>';
          }
        })
        .catch(() => {
          discountSelect.innerHTML = '<option value="" disabled>Failed to load discounts</option>';
        });
      }
    });
  }

  // Close side panel function
  function closeDiscountSidePanel() {
    if (discountPanel) discountPanel.classList.remove('open');
    if (discountPanelOverlay) discountPanelOverlay.style.display = 'none';
  }
  if (closeDiscountPanel) closeDiscountPanel.addEventListener('click', closeDiscountSidePanel);
  if (cancelDiscountBtn) cancelDiscountBtn.addEventListener('click', closeDiscountSidePanel);
  if (discountPanelOverlay) discountPanelOverlay.addEventListener('click', closeDiscountSidePanel);

  // Handle discount selection
  if (discountSelect) {
    discountSelect.addEventListener('change', function() {
      const selectedId = this.value;
      selectedDiscount = availableDiscounts.find(d => d.id == selectedId);
      if (applyDiscountBtn) applyDiscountBtn.disabled = !selectedDiscount;
    });
  }

  // Apply discount to cart
  if (applyDiscountBtn) {
    applyDiscountBtn.addEventListener('click', function() {
      if (!selectedDiscount) return;
      window.selectedDiscount = selectedDiscount;
      updateCartUI();
      closeDiscountSidePanel();
    });
  }

  // Cart state
  let cartItems = [];
  let selectedCustomer = {
    id: null,
    name: 'Walk-in Customer'
  };

      // Customers array - will be populated from database
      let allCustomers = [
        { id: 'walk-in', name: 'Walk-in Customer', phone: '', type: 'default' }
      ];

      // Fetch customers from database
      function fetchCustomers() {
        fetch('/manager/get_all_customers', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Add walk-in customer at the beginning
            allCustomers = [
              { id: 'walk-in', name: 'Walk-in Customer', phone: '', type: 'default' }
            ];

            // Add database customers
            data.customers.forEach(function(customer) {
              allCustomers.push({
                id: customer.id,
                name: customer.customer_name,
                phone: customer.phone_number || '',
                email: customer.email || '',
                type: 'regular'
              });
            });
          }
        })
        .catch(error => {
          console.error('Error fetching customers:', error);
        });
      }

      // Fetch customers on page load
      fetchCustomers();

      // Check if restoring a saved cart
      function restoreSavedCart() {
        const restoreCartData = sessionStorage.getItem('restoreCartData');
        const restoreSessionId = sessionStorage.getItem('restoreCartSessionId');

        console.log('Checking for restore cart...', {restoreCartData, restoreSessionId});

        if (restoreCartData && restoreSessionId) {
          try {
            const cartData = JSON.parse(restoreCartData);
            console.log('Parsed cart data:', cartData);

            // Set customer if available
            if (cartData.customer_id && cartData.customer_name) {
              selectedCustomer = {
                id: cartData.customer_id,
                name: cartData.customer_name
              };
              document.getElementById('customerName').textContent = cartData.customer_name;
            }

            // Load cart items
            if (cartData.items && cartData.items.length > 0) {
              console.log('Loading items:', cartData.items);

              cartData.items.forEach(function(item) {
                console.log('Processing item:', item);
                // Determine correct type and id
                let itemType = 'standard';
                let itemId = item.item_id;
                // If item_type is explicitly 'variant', or if product_variant_id exists, treat as variant
                if ((item.item_type && item.item_type === 'variant') || item.product_variant_id) {
                  itemType = 'variant';
                  itemId = item.product_variant_id || item.item_id;
                }
                const cartItem = {
                  id: itemId,
                  name: item.item_name,
                  price: parseFloat(item.item_price),
                  quantity: parseInt(item.quantity),
                  note: item.note || '',
                  img: item.item_image || '/manager_asset/images/salespilot logo1.png',
                  type: itemType
                };
                cartItems.push(cartItem);
              });

              console.log('Cart items after loading:', cartItems);

              // Update cart display
              updateCartUI();

              // Clear sessionStorage
              sessionStorage.removeItem('restoreCartData');
              sessionStorage.removeItem('restoreCartSessionId');

              // Show success message
              Swal.fire({
                icon: 'success',
                title: 'Cart Restored!',
                text: cartItems.length + ' items loaded successfully',
                showConfirmButton: false,
                timer: 1500
              });
            } else {
              console.error('No items found in cart data');
              Swal.fire({
                icon: 'warning',
                title: 'No Items Found',
                text: 'No items found in the saved cart.',
                confirmButtonColor: '#3085d6'
              });
            }
          } catch (error) {
            console.error('Error restoring cart:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: 'Error restoring cart: ' + error.message,
              confirmButtonColor: '#d33'
            });
            sessionStorage.removeItem('restoreCartData');
            sessionStorage.removeItem('restoreCartSessionId');
          }
        } else {
          console.log('No restore cart data found');
        }
      }

      // Restore saved cart on page load (after a small delay to ensure DOM is ready)
      setTimeout(restoreSavedCart, 100);

      // Elements
      const itemCards = document.querySelectorAll('.item-card');
      const cartItemsContainer = document.getElementById('cartItems');
      const cartTotalElement = document.getElementById('cartTotal');
      const searchInput = document.getElementById('searchInput');
      const clearSearch = document.getElementById('clearSearch');
      const categoryFilter = document.getElementById('categoryFilter');
      const checkoutBtn = document.getElementById('checkoutBtn');
      const itemCount = document.getElementById('itemCount');

      // Modal elements
      const modal = document.getElementById('itemModal');
      const closeModal = document.getElementById('closeModal');
      const decreaseQty = document.getElementById('decreaseQty');
      const increaseQty = document.getElementById('increaseQty');
      const itemQuantity = document.getElementById('itemQuantity');
      const addToCartBtn = document.getElementById('addToCartBtn');
      const itemNote = document.getElementById('itemNote');
      const sellingPriceGroup = document.getElementById('sellingPriceGroup');
      const modalSellingPrice = document.getElementById('modalSellingPrice');

      // Customer dropdown elements
      const addCustomerBtn = document.getElementById('addCustomerBtn');
      const customerDropdown = document.getElementById('customerDropdown');
      const customerSearchInput = document.getElementById('customerSearchInput');
      const customerDropdownList = document.getElementById('customerDropdownList');

      // Customer modal elements
      const customerModal = document.getElementById('customerModal');
      const closeCustomerModal = document.getElementById('closeCustomerModal');
      const newCustomerForm = document.getElementById('newCustomerForm');
      const saveNewCustomerBtn = document.getElementById('saveNewCustomerBtn');
      const selectCustomerBtn = document.getElementById('selectCustomerBtn');

      // Save cart elements
      const saveCartBtn = document.getElementById('saveCartBtn');
      const saveCartModal = document.getElementById('saveCartModal');
      const closeSaveCartModal = document.getElementById('closeSaveCartModal');
      const confirmSaveCartBtn = document.getElementById('confirmSaveCartBtn');

      // Receipt elements
      const receiptModal = document.getElementById('receiptModal');
      const closeReceiptBtn = document.getElementById('closeReceiptBtn');
      const printReceiptBtn = document.getElementById('printReceiptBtn');

      let currentItem = null;

      // Helper: Check if item has only cost_price (no final/selling price)
      function needsManualPrice(card) {
        // If price is 0 or null, but card has data-cost-price
        const price = parseFloat(card.dataset.price);
        const cost = parseFloat(card.dataset.costPrice || 0);
        // Show input if price is 0 or missing, but cost is present
        return (!price || price === 0) && cost > 0;
      }

      // Customer Dropdown functionality
      addCustomerBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        customerDropdown.classList.toggle('active');
        if (customerDropdown.classList.contains('active')) {
          customerSearchInput.value = '';
          renderCustomerDropdown(allCustomers);
          customerSearchInput.focus();
        }
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!document.getElementById('customerSection').contains(e.target)) {
          customerDropdown.classList.remove('active');
        }
      });

      // Search customers
      customerSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const filtered = allCustomers.filter(function(customer) {
          return customer.name.toLowerCase().includes(searchTerm) ||
                 customer.phone.includes(searchTerm);
        });
        renderCustomerDropdown(filtered);
      });

      // Render customer dropdown list
      function renderCustomerDropdown(customers) {
        customerDropdownList.innerHTML = '';

        // Add "New Customer" option at the top
        const newCustomerItem = document.createElement('div');
        newCustomerItem.className = 'customer-dropdown-item new-customer';
        newCustomerItem.innerHTML = `
          <div class="customer-dropdown-icon"><i class="bi bi-plus"></i></div>
          <div class="customer-dropdown-info">
            <div class="customer-dropdown-name">Add New Customer</div>
            <div class="customer-dropdown-phone">Create a new customer</div>
          </div>
        `;
        newCustomerItem.addEventListener('click', function() {
          customerDropdown.classList.remove('active');
          customerModal.classList.add('active');
          newCustomerForm.classList.add('active');
          document.getElementById('customerList').style.display = 'none';
          selectCustomerBtn.style.display = 'none';
          document.getElementById('newCustomerName').focus();
        });
        customerDropdownList.appendChild(newCustomerItem);

        // Add customers
        customers.forEach(function(customer) {
          const isSelected = selectedCustomer.id === customer.id ||
                           (selectedCustomer.id === null && customer.id === 'walk-in');

          const item = document.createElement('div');
          item.className = 'customer-dropdown-item' + (isSelected ? ' selected' : '');

          const initials = customer.name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase().substring(0, 2);

          item.innerHTML = `
            <div class="customer-dropdown-icon">${initials}</div>
            <div class="customer-dropdown-info">
              <div class="customer-dropdown-name">${customer.name}</div>
              ${customer.phone ? '<div class="customer-dropdown-phone">' + customer.phone + '</div>' : '<div class="customer-dropdown-phone">No phone</div>'}
            </div>
            ${isSelected ? '<i class="bi bi-check-circle customer-dropdown-check"></i>' : ''}
          `;

          item.addEventListener('click', function() {
            selectCustomer(customer);
          });

          customerDropdownList.appendChild(item);
        });
      }

      // Select customer from dropdown
      function selectCustomer(customer) {
        selectedCustomer = {
          id: customer.id === 'walk-in' ? null : customer.id,
          name: customer.name,
          phone: customer.phone
        };
        document.getElementById('customerName').textContent = selectedCustomer.name;
        customerDropdown.classList.remove('active');
      }

      // Customer Modal close handlers
      closeCustomerModal.addEventListener('click', function() {
        customerModal.classList.remove('active');
        newCustomerForm.classList.remove('active');
        document.getElementById('customerList').style.display = 'block';
        selectCustomerBtn.style.display = 'block';
      });

      customerModal.addEventListener('click', function(e) {
        if (e.target === customerModal) {
          customerModal.classList.remove('active');
          newCustomerForm.classList.remove('active');
          document.getElementById('customerList').style.display = 'block';
          selectCustomerBtn.style.display = 'block';
        }
      });

      // Save New Customer Button
      saveNewCustomerBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any default behavior

        // Prevent double submission
        if (this.disabled) return;

        const name = document.getElementById('newCustomerName').value.trim();
        const phone = document.getElementById('newCustomerPhone').value.trim();
        const email = document.getElementById('newCustomerEmail').value.trim();
        const address = document.getElementById('newCustomerAddress').value.trim();

        if (!name) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'warning',
              title: 'Missing Information',
              text: 'Please enter customer name',
              confirmButtonColor: '#3085d6'
            });
          }
          return;
        }

        // Disable button to prevent double submission
        this.disabled = true;
        const originalText = this.innerHTML;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

        // Send AJAX request to save customer to database
        fetch('/manager/add_customer', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            customer_name: name,
            phone_number: phone,
            email: email,
            address: address
          })
        })
        .then(response => {
          console.log('Response status:', response.status);
          return response.json().then(data => {
            console.log('Response data:', data);
            if (!response.ok) {
              // Handle validation errors (422) or other errors
              if (response.status === 422 && data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('<br>');
                return Promise.reject({ isValidation: true, message: errorMessages });
              }
              return Promise.reject({ isValidation: false, message: data.message || 'Network response was not ok' });
            }
            return data;
          });
        })
        .then(data => {
          console.log('Success data:', data);
          if (data.success) {
            // Add to dropdown list
            allCustomers.push({
              id: data.customer.id,
              name: data.customer.customer_name,
              phone: data.customer.phone_number || '',
              email: data.customer.email || '',
              type: 'regular'
            });

            // Set as selected customer
            selectedCustomer = {
              id: data.customer.id,
              name: data.customer.customer_name,
              phone: data.customer.phone_number
            };
            document.getElementById('customerName').textContent = selectedCustomer.name;

            // Reset form and close modal
            document.getElementById('newCustomerName').value = '';
            document.getElementById('newCustomerPhone').value = '';
            document.getElementById('newCustomerEmail').value = '';
            document.getElementById('newCustomerAddress').value = '';
            customerModal.classList.remove('active');
            newCustomerForm.classList.remove('active');
            document.getElementById('customerList').style.display = 'block';
            selectCustomerBtn.style.display = 'block';

            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Customer added successfully!',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
              });
            }
          } else {
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: 'Failed to add customer: ' + (data.message || 'Unknown error'),
                confirmButtonColor: '#d33'
              });
            }
          }
        })
        .catch(error => {
          console.error('Caught error:', error);

          // Handle validation errors differently
          if (error.isValidation) {
            // Check if SweetAlert is available, fallback to alert
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'warning',
                title: 'Validation Error',
                html: error.message,
                confirmButtonColor: '#f39c12'
              });
            }
          } else {
            // Check if SweetAlert is available, fallback to alert
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'An error occurred while adding the customer. Please try again.',
                confirmButtonColor: '#d33'
              });
            }
          }
        })
        .finally(() => {
          // Re-enable button
          this.disabled = false;
          this.innerHTML = originalText;
        });
      });

      // Handle customer list item clicks in modal
      document.addEventListener('click', function(e) {
        const customerListItem = e.target.closest('.customer-list-item');
        if (customerListItem && customerListItem.dataset.customerId) {
          const customerId = customerListItem.dataset.customerId;

          // Check if it's the new customer option
          if (customerId === 'new') {
            newCustomerForm.classList.add('active');
            document.getElementById('customerList').style.display = 'none';
            selectCustomerBtn.style.display = 'none';
            document.getElementById('newCustomerName').focus();
            return;
          }

          // Find customer and select
          const customerName = customerListItem.dataset.customerName;
          const customerPhone = customerListItem.dataset.customerPhone || '';

          selectedCustomer = {
            id: customerId === 'walk-in' ? null : customerId,
            name: customerName,
            phone: customerPhone
          };

          document.getElementById('customerName').textContent = selectedCustomer.name;
          customerModal.classList.remove('active');
        }
      });

      // Save Cart functionality
      saveCartBtn.addEventListener('click', function() {
        if (cartItems.length === 0) {
          Swal.fire({
            icon: 'warning',
            title: 'Cart Empty',
            text: 'Add items before saving the cart.',
            confirmButtonColor: '#3085d6'
          });
          return;
        }
        // Prevent double open
        if (saveCartBtn.disabled) return;
        saveCartBtn.disabled = true;
        saveCartModal.classList.add('active');
        document.getElementById('savedCartName').value = '';
        document.getElementById('savedCartNote').value = '';
        loadSavedCartsList();
        setTimeout(() => { saveCartBtn.disabled = false; }, 1000); // Re-enable after modal opens
      });

      closeSaveCartModal.addEventListener('click', function() {
        saveCartModal.classList.remove('active');
      });

      saveCartModal.addEventListener('click', function(e) {
        if (e.target === saveCartModal) {
          saveCartModal.classList.remove('active');
        }
      });

      confirmSaveCartBtn.addEventListener('click', function() {
        if (confirmSaveCartBtn.disabled) return;
        confirmSaveCartBtn.disabled = true;
        const cartName = document.getElementById('savedCartName').value.trim();
        const cartNote = document.getElementById('savedCartNote').value.trim();

        if (!cartName) {
          Swal.fire({
            icon: 'warning',
            title: 'Cart Name Required',
            text: 'Please enter a name for the cart',
            confirmButtonColor: '#3085d6'
          });
          confirmSaveCartBtn.disabled = false;
          return;
        }

        // Calculate total
        const cartTotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        // Prepare cart data for submission
        const saveData = {
          cart_name: cartName,
          cart_note: cartNote,
          customer_id: selectedCustomer.id,
          customer_name: selectedCustomer.name,
          total: cartTotal,
          discount: 0,
          items: cartItems.map(function(item) {
            return {
              id: item.id,
              type: item.type,
              name: item.name,
              price: item.price,
              quantity: item.quantity,
              note: item.note || '',
              img: item.img
            };
          })
        };

        // Save to database
        fetch('/manager/save_cart', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(saveData)
        })
        .then(response => response.json())
        .then(data => {
          confirmSaveCartBtn.disabled = false;
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Cart Saved!',
              text: 'Cart saved successfully',
              showConfirmButton: false,
              timer: 1500
            });

            // Clear current cart
            cartItems = [];
            selectedCustomer = { id: null, name: 'Walk-in Customer' };
            document.getElementById('customerName').textContent = 'Walk-in Customer';
            updateCartUI();

            saveCartModal.classList.remove('active');
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed!',
              text: 'Failed to save cart: ' + (data.message || 'Unknown error'),
              confirmButtonColor: '#d33'
            });
          }
        })
        .catch(error => {
          confirmSaveCartBtn.disabled = false;
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while saving the cart. Please try again.',
            confirmButtonColor: '#d33'
          });
        });
      });

      function loadSavedCartsList() {
        const savedCartsList = document.getElementById('savedCartsList');

        // Fetch saved carts from database
        fetch('/manager/get_saved_carts', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success && data.carts.length > 0) {
            savedCartsList.innerHTML = '';
            data.carts.forEach(function(cart) {
              const date = new Date(cart.created_at).toLocaleString();
              const cartItem = document.createElement('div');
              cartItem.className = 'saved-cart-item';
              cartItem.style.cursor = 'pointer';
              cartItem.dataset.sessionId = cart.session_id;
              cartItem.innerHTML = `
                <div class="saved-cart-item-name">${cart.cart_name}</div>
                <div class="saved-cart-item-details">${cart.customer_name} • ${cart.items_count} items • ₦${parseFloat(cart.total).toLocaleString()}</div>
                <div class="saved-cart-item-date">${date}</div>
                <button class="delete-cart-btn" data-session-id="${cart.session_id}" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                  <i class="bi bi-trash"></i>
                </button>
              `;

              // Load cart on click
              cartItem.addEventListener('click', function(e) {
                if (!e.target.closest('.delete-cart-btn')) {
                  loadCartFromDatabase(cart.session_id);
                }
              });

              // Delete cart button
              const deleteBtn = cartItem.querySelector('.delete-cart-btn');
              deleteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                deleteCartFromDatabase(cart.session_id);
              });

              savedCartsList.appendChild(cartItem);
            });
          } else {
            savedCartsList.innerHTML = '<small>No saved carts yet</small>';
          }
        })
        .catch(error => {
          console.error('Error loading saved carts:', error);
          savedCartsList.innerHTML = '<small>Error loading saved carts</small>';
        });
      }

      // Load cart from database
      function loadCartFromDatabase(sessionId) {
        fetch(`/manager/load_saved_cart/${sessionId}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Clear current cart
            cartItems = [];

            // Set customer
            selectedCustomer = {
              id: data.cart.customer_id,
              name: data.cart.customer_name
            };
            document.getElementById('customerName').textContent = data.cart.customer_name;

            // Load cart items
            data.cart.items.forEach(function(item) {
              cartItems.push({
                id: item.item_id,
                type: item.item_type,
                name: item.item_name,
                price: parseFloat(item.price),
                quantity: parseInt(item.quantity),
                note: item.note || '',
                img: item.item_image || '/manager_asset/images/salespilot logo1.png'
              });
            });

            updateCartUI();
            saveCartModal.classList.remove('active');
            Swal.fire({
              icon: 'success',
              title: 'Cart Loaded!',
              text: 'Cart loaded successfully',
              showConfirmButton: false,
              timer: 1500
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed!',
              text: 'Failed to load cart: ' + (data.message || 'Unknown error'),
              confirmButtonColor: '#d33'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while loading the cart.',
            confirmButtonColor: '#d33'
          });
        });
      }

      // Delete cart from database
      function deleteCartFromDatabase(sessionId) {
        Swal.fire({
          title: 'Are you sure?',
          text: 'Do you want to delete this saved cart?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`/manager/delete_saved_cart/${sessionId}`, {
              method: 'DELETE',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'Deleted!',
                  text: 'Cart deleted successfully',
                  showConfirmButton: false,
                  timer: 1500
                });
                loadSavedCartsList();
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Failed!',
                  text: 'Failed to delete cart: ' + (data.message || 'Unknown error'),
                  confirmButtonColor: '#d33'
                });
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while deleting the cart.',
                confirmButtonColor: '#d33'
              });
            });
          }
        });
      }

      // Checkout functionality
      checkoutBtn.addEventListener('click', function() {
        if (checkoutBtn.disabled) return;
        checkoutBtn.disabled = true;

        // Show loading spinner
        const originalButtonText = checkoutBtn.innerHTML;
        checkoutBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

        if (cartItems.length === 0) {
          Swal.fire({
            icon: 'warning',
            title: 'Cart Empty',
            text: 'Add items before checkout.',
            confirmButtonColor: '#3085d6'
          });
          checkoutBtn.disabled = false;
          checkoutBtn.innerHTML = originalButtonText;
          return;
        }

        // Calculate subtotal
        const cartSubtotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        // Get discount amount and id
        let discountAmount = 0;
        let discountId = null;
        if (window.selectedDiscount) {
          discountAmount = parseFloat(window.selectedDiscount.discount_rate) || 0;
          discountId = window.selectedDiscount.id || null;
        }

        // Prepare cart data for submission
        const checkoutData = {
          customer_id: selectedCustomer.id,
          customer_name: selectedCustomer.name,
          total: cartSubtotal,
          discount: discountAmount,
          discount_id: discountId,
          items: cartItems.map(function(item) {
            return {
              id: item.id,
              type: item.type,
              name: item.name,
              price: item.price,
              quantity: item.quantity,
              note: item.note || '',
              img: item.img
            };
          })
        };

        // Submit to database
        fetch('/manager/checkout', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(checkoutData)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Generate and show receipt first
            generateReceipt();
            receiptModal.classList.add('active');

            // Show success message then reload
            Swal.fire({
              icon: 'success',
              title: 'Sale Complete!',
              text: 'Order has been sold successfully! Receipt #' + (data.receipt_number || 'N/A'),
              confirmButtonColor: '#28a745',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              // Reload the page after SweetAlert closes
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Checkout Failed',
              text: data.message || 'Unknown error',
              confirmButtonColor: '#d33'
            });
            checkoutBtn.disabled = false;
            checkoutBtn.innerHTML = originalButtonText;
          }
        })
        .catch(error => {
          checkoutBtn.disabled = false;
          checkoutBtn.innerHTML = originalButtonText;
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred during checkout. Please try again.',
            confirmButtonColor: '#d33'
          });
        });
      });

      function generateReceipt() {
        const receiptNumber = 'RCP' + Date.now();
        const currentDate = new Date();
        const formattedDate = currentDate.toLocaleString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        });

        document.getElementById('receiptDate').textContent = formattedDate;
        document.getElementById('receiptNumber').textContent = receiptNumber;
        document.getElementById('receiptCustomer').textContent = selectedCustomer.name;

        const receiptItemsList = document.getElementById('receiptItemsList');
        receiptItemsList.innerHTML = '';

        let subtotal = 0;
        cartItems.forEach(function(item) {
          const itemTotal = item.price * item.quantity;
          subtotal += itemTotal;

          const itemHTML = `
            <div class="receipt-item">
              <div class="receipt-item-name">${item.name}</div>
              <div class="receipt-item-details">
                <span>${item.quantity} × ₦${item.price.toLocaleString()}</span>
                <span>₦${itemTotal.toLocaleString()}</span>
              </div>
              ${item.note ? '<div class="receipt-item-note">Note: ' + item.note + '</div>' : ''}
            </div>
          `;
          receiptItemsList.insertAdjacentHTML('beforeend', itemHTML);
        });

        document.getElementById('receiptSubtotal').textContent = '₦' + subtotal.toLocaleString();

        // Show discount and discounted total if applied
        let discountAmount = 0;
        if (window.selectedDiscount) {
          discountAmount = parseFloat(window.selectedDiscount.discount_rate) || 0;
        }
        const receiptDiscount = document.getElementById('receiptDiscount');
        if (receiptDiscount) {
          if (discountAmount > 0) {
            receiptDiscount.textContent = '-₦' + discountAmount.toLocaleString();
            receiptDiscount.style.display = '';
          } else {
            receiptDiscount.textContent = '';
            receiptDiscount.style.display = 'none';
          }
        }
        const discountedTotal = subtotal - discountAmount;
        document.getElementById('receiptTotal').textContent = '₦' + (discountedTotal > 0 ? discountedTotal.toLocaleString() : '0');
      }

      closeReceiptBtn.addEventListener('click', function() {
        receiptModal.classList.remove('active');
        cartItems = [];
        selectedCustomer = { id: null, name: 'Walk-in Customer' };
        document.getElementById('customerName').textContent = 'Walk-in Customer';
        updateCartUI();
      });

      receiptModal.addEventListener('click', function(e) {
        if (e.target === receiptModal) {
          receiptModal.classList.remove('active');
          cartItems = [];
          selectedCustomer = { id: null, name: 'Walk-in Customer' };
          document.getElementById('customerName').textContent = 'Walk-in Customer';
          updateCartUI();
        }
      });

      printReceiptBtn.addEventListener('click', function() {
        window.print();
      });

      // Open modal when item card is clicked
      itemCards.forEach(function(card) {
        card.addEventListener('click', function() {
          const stock = parseInt(this.dataset.stock || '0');
          if (stock === 0) return; // Prevent opening modal for sold out items
          let itemType = 'standard';
          let itemId = this.dataset.id;
          if (this.dataset.type === 'variant' || this.dataset.productVariant === 'true') {
            itemType = 'variant';
          }
          currentItem = {
            id: itemId,
            type: itemType,
            name: this.dataset.name,
            price: parseFloat(this.dataset.price),
            stock: stock,
            img: this.dataset.img,
            costPrice: parseFloat(this.dataset.costPrice || 0)
          };
          document.getElementById('modalTitle').textContent = currentItem.name;
          document.getElementById('modalItemName').textContent = currentItem.name;
          document.getElementById('modalItemStock').textContent = 'Available: ' + currentItem.stock + ' units';
          document.getElementById('modalItemImg').src = currentItem.img;
          itemQuantity.value = 1;
          itemQuantity.max = currentItem.stock;
          itemNote.value = '';
          // Show/hide selling price input
          if (needsManualPrice(this)) {
            sellingPriceGroup.style.display = '';
            modalSellingPrice.value = '';
            document.getElementById('modalItemPrice').textContent = '₦0.00';
          } else {
            sellingPriceGroup.style.display = 'none';
            document.getElementById('modalItemPrice').textContent = '₦' + currentItem.price.toLocaleString();
          }
          modal.classList.add('active');
        });
      });

      // Close modal
      closeModal.addEventListener('click', function() {
        modal.classList.remove('active');
      });

      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          modal.classList.remove('active');
        }
      });

      // Quantity controls
      decreaseQty.addEventListener('click', function() {
        let qty = parseInt(itemQuantity.value);
        if (qty > 1) {
          itemQuantity.value = qty - 1;
        }
      });

      // Increase quantity with stock check
      increaseQty.addEventListener('click', function() {
        let qty = parseInt(itemQuantity.value);
        let max = parseInt(itemQuantity.max);
        if (qty < max) {
          itemQuantity.value = qty + 1;
        }

        // Check stock before increasing quantity in cart
        const index = parseInt(this.dataset.index);
        const cartItem = cartItems[index];
        let stock = null;
        // Determine stock based on item type
        if (cartItem.type === 'standard') {
          stock = typeof cartItem.stock !== 'undefined' ? cartItem.stock : null;
        } else if (cartItem.type === 'variant') {
          stock = typeof cartItem.stock !== 'undefined' ? cartItem.stock : null;
        }
        if (stock === 0) {
          alert('Cannot increase quantity. This item is SOLD OUT.');
          return;
        }
        if (cartItem.quantity >= stock) {
          alert('Cannot increase quantity beyond available stock.');
          return;
        }
        cartItems[index].quantity++;
        updateCartUI();
      });

      // Update price display and total when price or quantity changes
      modalSellingPrice.addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        document.getElementById('modalItemPrice').textContent = '₦' + price.toLocaleString();
      });
      itemQuantity.addEventListener('input', function() {
        if (sellingPriceGroup.style.display !== 'none') {
          const price = parseFloat(modalSellingPrice.value) || 0;
          document.getElementById('modalItemPrice').textContent = '₦' + (price * parseInt(itemQuantity.value || 1)).toLocaleString();
        }
      });

      // Add to cart
      addToCartBtn.addEventListener('click', function() {
        if (!currentItem) return;
        const quantity = parseInt(itemQuantity.value);
        const note = itemNote.value.trim();
        let price = currentItem.price;

        // Show loading spinner
        const originalButtonText = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

        // If manual price is needed, get from input
        if (sellingPriceGroup.style.display !== 'none') {
          price = parseFloat(modalSellingPrice.value);
          if (!price || price <= 0) {
            Swal.fire({
              icon: 'warning',
              title: 'Invalid Price',
              text: 'Please enter a valid selling price',
              confirmButtonColor: '#3085d6'
            });
            this.disabled = false;
            this.innerHTML = originalButtonText;
            modalSellingPrice.focus();
            return;
          }
        }

        const existingIndex = cartItems.findIndex(item => item.id === currentItem.id && item.type === currentItem.type);
        if (existingIndex >= 0) {
          cartItems[existingIndex].quantity += quantity;
          if (note) {
            cartItems[existingIndex].note = note;
          }
          if (sellingPriceGroup.style.display !== 'none') {
            cartItems[existingIndex].price = price;
          }
        } else {
          cartItems.push({
            id: currentItem.id,
            type: currentItem.type,
            name: currentItem.name,
            price: price,
            quantity: quantity,
            note: note,
            img: currentItem.img
          });
        }

        updateCartUI();
        modal.classList.remove('active');

        // Show success notification
        Swal.fire({
          icon: 'success',
          title: 'Added to Cart!',
          text: currentItem.name + ' has been added to your cart',
          timer: 1500,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        });

        // Reset button
        this.disabled = false;
        this.innerHTML = originalButtonText;
      });

      // Search functionality
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        clearSearch.style.display = searchTerm ? 'block' : 'none';
        filterItems();
      });

      clearSearch.addEventListener('click', function() {
        searchInput.value = '';
        this.style.display = 'none';
        filterItems();
      });

      categoryFilter.addEventListener('change', filterItems);

      function filterItems() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        let visibleCount = 0;

        // Get or create the no results message element
        let noResultsMsg = document.querySelector('.no-results-message');
        if (!noResultsMsg) {
          noResultsMsg = document.createElement('div');
          noResultsMsg.className = 'no-results-message';
          noResultsMsg.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 40px; display: none;';
          noResultsMsg.innerHTML = `
            <i class="bi bi-search" style="font-size: 48px; color: #ccc;"></i>
            <p style="color: #999; margin-top: 20px; font-size: 18px;">No items found</p>
            <small style="color: #bbb;">Try adjusting your search or filter</small>
          `;
          document.querySelector('.items-grid').appendChild(noResultsMsg);
        }

        itemCards.forEach(function(card) {
          const itemName = card.dataset.name.toLowerCase();
          const itemCategory = (card.dataset.category || '').toLowerCase();
          const matchesSearch = itemName.includes(searchTerm);
          const matchesCategory = selectedCategory === '' || itemCategory === selectedCategory;

          if (matchesSearch && matchesCategory) {
            card.style.display = 'block';
            visibleCount++;
          } else {
            card.style.display = 'none';
          }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
          noResultsMsg.style.display = 'block';
        } else {
          noResultsMsg.style.display = 'none';
        }

        itemCount.textContent = visibleCount + ' Item' + (visibleCount !== 1 ? 's' : '');
      }
      // Initialize visible items & count on page load
      filterItems();

      // Update cart UI
      function updateCartUI() {
        cartItemsContainer.innerHTML = '';

        if (cartItems.length === 0) {
          cartItemsContainer.innerHTML = `
            <div class="cart-empty">
              <i class="bi bi-cart-x cart-empty-icon"></i>
              <p>Your cart is empty</p>
              <small>Add items to get started</small>
            </div>
          `;
          cartTotalElement.textContent = '₦0.00';
          checkoutBtn.disabled = true;
          // Remove discount info if not applied
          let discountInfo = document.getElementById('cartDiscountInfo');
          if (discountInfo) discountInfo.remove();
          return;
        }

        checkoutBtn.disabled = false;
        let total = 0;

        cartItems.forEach(function(item, index) {
          const itemTotal = item.price * item.quantity;
          total += itemTotal;

          // Always update the stock property from the latest item-card DOM element
          // This ensures we have the most current stock info
          const card = Array.from(document.querySelectorAll('.item-card')).find(card => card.dataset.id == item.id && card.dataset.type == item.type);
          if (card) {
            item.stock = parseInt(card.dataset.stock || '0');
          }
          // Generate cart item HTML
          const cartItemHTML = `
            <div class="cart-item" data-index="${index}">
              <div class="cart-item-main">
                <div class="cart-item-img">
                  <img src="${item.img}" alt="${item.name}" onerror="this.src='{{ asset('manager_asset/images/salespilot logo1.png') }}'">
                </div>
                <div class="cart-item-info">
                  <div class="cart-item-name">${item.name}</div>
                  <div class="cart-item-price-unit">₦${item.price.toLocaleString()}</div>
                  ${item.note ? '<div class="cart-item-note"><i class="bi bi-sticky"></i> ' + item.note + '</div>' : ''}
                </div>
              </div>
              <div class="cart-item-actions">
                <div class="cart-item-quantity">
                  <button class="qty-btn qty-decrease" data-index="${index}">
                    <i class="bi bi-dash"></i>
                  </button>
                  <input type="number" class="qty-input" value="${item.quantity}" min="1" data-index="${index}">
                  <button class="qty-btn qty-increase" data-index="${index}">
                    <i class="bi bi-plus"></i>
                  </button>
                </div>
                <div class="cart-item-total">₦${itemTotal.toLocaleString()}</div>
                <button class="cart-item-remove" data-index="${index}" title="Remove item">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          `;
          cartItemsContainer.insertAdjacentHTML('beforeend', cartItemHTML);
        });

        // Discount logic
        if (window.selectedDiscount && cartTotalElement) {
          let discountAmount = parseFloat(window.selectedDiscount.discount_rate);
          let newTotal = total - discountAmount;
          if (newTotal < 0) newTotal = 0;
          cartTotalElement.textContent = formatCurrency(newTotal);
          // Optionally show discount info somewhere
          let discountInfo = document.getElementById('cartDiscountInfo');
          if (!discountInfo) {
            discountInfo = document.createElement('div');
            discountInfo.id = 'cartDiscountInfo';
            discountInfo.className = 'text-success fw-bold';
            cartTotalElement.parentNode.insertBefore(discountInfo, cartTotalElement);
          }
          let discountLabel = '';
          if (window.selectedDiscount.type === 'percentage') {
            discountLabel = `<span style='color:#d32f2f;'>-${window.selectedDiscount.discount_rate}%</span>`;
          } else {
            discountLabel = `<span style='color:#d32f2f;'>-${formatCurrency(discountAmount)}</span>`;
          }
          discountInfo.innerHTML = `<span style="background:#e0f7fa;color:#00796b;padding:4px 10px;border-radius:16px;font-weight:bold;display:inline-block; font-size:13px">
            <i class='bi bi-tag-fill' style='color:#009688;margin-right:4px;'></i>
            Discount: ${discountLabel} <span style='color:#1976d2;'>(${window.selectedDiscount.discount_name})</span>
          </span>`;
        } else {
          // Remove discount info if not applied
          let discountInfo = document.getElementById('cartDiscountInfo');
          if (discountInfo) discountInfo.remove();
          cartTotalElement.textContent = formatCurrency(total);
        }

        // Add event listeners for quantity controls
        document.querySelectorAll('.qty-decrease').forEach(function(btn) {
          btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            if (cartItems[index].quantity > 1) {
              cartItems[index].quantity--;
              updateCartUI();
              Swal.fire({
                icon: 'info',
                title: 'Quantity decreased',
                text: cartItems[index].name,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
              });
            }
          });
        });

        // Add event listeners for quantity increase buttons
        document.querySelectorAll('.qty-increase').forEach(function(btn) {
          btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);

            // Check stock before increasing quantity in cart
            const cartItem = cartItems[index];
            let stock = null;
            // Determine stock based on item type
            if (cartItem.type === 'standard') {
              stock = typeof cartItem.stock !== 'undefined' ? cartItem.stock : null;
            } else if (cartItem.type === 'variant') {
              stock = typeof cartItem.stock !== 'undefined' ? cartItem.stock : null;
            }
            if (stock === 0) {
              Swal.fire({
                icon: 'warning',
                title: 'Out of Stock',
                text: 'Cannot increase quantity. This item is SOLD OUT.',
                confirmButtonColor: '#d33'
              });
              return;
            }
            if (cartItem.quantity >= stock) {
              Swal.fire({
                icon: 'warning',
                title: 'Stock Limit Reached',
                text: 'Cannot increase quantity beyond available stock.',
                confirmButtonColor: '#d33'
              });
              return;
            }
            cartItems[index].quantity++;
            updateCartUI();
            Swal.fire({
              icon: 'success',
              title: 'Quantity increased',
              text: cartItem.name,
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 1500,
              timerProgressBar: true
            });
          });
        });

        document.querySelectorAll('.qty-input').forEach(function(input) {
          input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            const newQty = parseInt(this.value);
            if (newQty > 0) {
              cartItems[index].quantity = newQty;
              updateCartUI();
            } else {
              this.value = cartItems[index].quantity;
            }
          });
        });

        // Add event listeners for remove buttons
        document.querySelectorAll('.cart-item-remove').forEach(function(btn) {
          btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            Swal.fire({
              title: 'Remove Item?',
              text: 'Remove ' + cartItems[index].name + ' from cart?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#d33',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Yes, remove it!',
              cancelButtonText: 'Cancel'
            }).then((result) => {
              if (result.isConfirmed) {
                cartItems.splice(index, 1);
                updateCartUI();
                Swal.fire({
                  icon: 'success',
                  title: 'Item removed',
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 1500,
                  timerProgressBar: true
                });
              }
            });
          });
        });
      }
    });
