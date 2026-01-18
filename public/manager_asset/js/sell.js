 document.addEventListener('DOMContentLoaded', function() {
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
              alert('Cart restored successfully! ' + cartItems.length + ' items loaded.');
            } else {
              console.error('No items found in cart data');
              alert('No items found in the saved cart.');
            }
          } catch (error) {
            console.error('Error restoring cart:', error);
            alert('Error restoring cart: ' + error.message);
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

        // Disable click for sold out items and add visual cues
        itemCards.forEach(function(card) {
          const stock = parseInt(card.dataset.stock || '0');
          if (stock === 0) {
            card.classList.add('disabled', 'sold-out');
            card.style.opacity = '0.6';
            card.style.cursor = 'not-allowed';
          }
        });
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
          Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please enter customer name',
            confirmButtonColor: '#3085d6'
          });
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

            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: 'Customer added successfully!',
              timer: 2000,
              showConfirmButton: false,
              toast: true,
              position: 'top-end'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Failed!',
              text: 'Failed to add customer: ' + (data.message || 'Unknown error'),
              confirmButtonColor: '#d33'
            });
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
            } else {
              alert('Validation Error:\n\n' + error.message.replace(/<br>/g, '\n'));
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
            } else {
              alert('Error!\n\n' + (error.message || 'An error occurred while adding the customer. Please try again.'));
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
          alert('Cart is empty. Add items before saving.');
          return;
        }

        saveCartModal.classList.add('active');
        document.getElementById('savedCartName').value = '';
        document.getElementById('savedCartNote').value = '';
        loadSavedCartsList();
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
        const cartName = document.getElementById('savedCartName').value.trim();
        const cartNote = document.getElementById('savedCartNote').value.trim();

        if (!cartName) {
          alert('Please enter a name for the cart');
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
          if (data.success) {
            alert('Cart saved successfully!');

            // Clear current cart
            cartItems = [];
            selectedCustomer = { id: null, name: 'Walk-in Customer' };
            document.getElementById('customerName').textContent = 'Walk-in Customer';
            updateCartUI();

            saveCartModal.classList.remove('active');
          } else {
            alert('Failed to save cart: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while saving the cart. Please try again.');
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
            document.getElementById('customerName').textContent = selectedCustomer.name;

            // Load cart items
            data.cart.items.forEach(function(item) {
              // Determine correct type and id
              let itemType = 'standard';
              let itemId = item.item_id;
              // If item_type is explicitly 'variant', or if product_variant_id exists, treat as variant
              if ((item.item_type && item.item_type === 'variant') || item.product_variant_id) {
                itemType = 'variant';
                itemId = item.product_variant_id || item.item_id;
              }
              cartItems.push({
                id: itemId,
                type: itemType,
                name: item.item_name,
                price: parseFloat(item.item_price),
                quantity: item.quantity,
                note: item.note || '',
                img: item.item_image || '/manager_asset/images/salespilot logo1.png'
              });
            });

            updateCartUI();
            saveCartModal.classList.remove('active');
            alert('Cart loaded successfully!');
          } else {
            alert('Failed to load cart: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while loading the cart.');
        });
      }

      // Delete cart from database
      function deleteCartFromDatabase(sessionId) {
        if (!confirm('Are you sure you want to delete this saved cart?')) {
          return;
        }

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
            alert('Cart deleted successfully!');
            loadSavedCartsList();
          } else {
            alert('Failed to delete cart: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while deleting the cart.');
        });
      }

      // Checkout functionality
      checkoutBtn.addEventListener('click', function() {
        if (cartItems.length === 0) {
          alert('Cart is empty. Add items before checkout.');
          return;
        }

        // Calculate total
        const cartTotal = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        // Prepare cart data for submission
        const checkoutData = {
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
            // Show success message
            alert('Order has been sold successfully! Receipt #' + (data.receipt_number || 'N/A'));

            // Generate and show receipt
            generateReceipt();
            receiptModal.classList.add('active');
          } else {
            alert('Checkout failed: ' + (data.message || 'Unknown error'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred during checkout. Please try again.');
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
        document.getElementById('receiptTotal').textContent = '₦' + subtotal.toLocaleString();
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

            // Determine correct item_type and id
            let itemType = 'standard';
            let itemId = this.dataset.id;
            // If the card has a data-variant or data-product-variant attribute, treat as variant
            if (this.dataset.type === 'variant' || this.dataset.productVariant === 'true') {
              itemType = 'variant';
            }
            currentItem = {
              id: itemId,
              type: itemType,
              name: this.dataset.name,
              price: parseFloat(this.dataset.price),
              stock: parseInt(this.dataset.stock),
              img: this.dataset.img
            };

            document.getElementById('modalTitle').textContent = currentItem.name;
            document.getElementById('modalItemName').textContent = currentItem.name;
            document.getElementById('modalItemPrice').textContent = '₦' + currentItem.price.toLocaleString();
            document.getElementById('modalItemStock').textContent = 'Available: ' + currentItem.stock + ' units';
            document.getElementById('modalItemImg').src = currentItem.img;



            // Debug: Log value before and after setting
            console.error('[DEBUG] Before set: itemQuantity.value =', itemQuantity.value);
            // Enforce a hard reset of the quantity input and its attributes
            itemQuantity.value = 1;
            itemQuantity.defaultValue = 1;
            itemQuantity.setAttribute('min', '1');
            itemQuantity.setAttribute('max', currentItem.stock);
            itemNote.value = '';
            // Also, remove any accidental autofill
            itemQuantity.autocomplete = 'off';
            console.error('[DEBUG] After set: itemQuantity.value =', itemQuantity.value);

            modal.classList.add('active');
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

      increaseQty.addEventListener('click', function() {
        let qty = parseInt(itemQuantity.value);
        let max = parseInt(itemQuantity.max);
        if (qty < max) {
          itemQuantity.value = qty + 1;
        }
      });

      // Add to cart
      addToCartBtn.addEventListener('click', function() {
        if (!currentItem) return;

        const quantity = 1; // Always add 1 per click
        const note = itemNote.value.trim();
        const stock = parseInt(currentItem.stock);

        const existingIndex = cartItems.findIndex(item => item.id === currentItem.id && item.type === currentItem.type);
        let currentCartQty = existingIndex >= 0 ? cartItems[existingIndex].quantity : 0;

        if (quantity + currentCartQty > stock) {
          alert('Cannot add more than available stock.');
          return;
        }

        if (existingIndex >= 0) {
          cartItems[existingIndex].quantity += quantity;
          if (note) {
            cartItems[existingIndex].note = note;
          }
        } else {
          cartItems.push({
            id: currentItem.id,
            type: currentItem.type,
            name: currentItem.name,
            price: currentItem.price,
            quantity: quantity,
            note: note,
            img: currentItem.img,
            stock: stock
          });
        }

        updateCartUI();
        modal.classList.remove('active');
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
          return;
        }

        checkoutBtn.disabled = false;
        let total = 0;

        cartItems.forEach(function(item, index) {
          const itemTotal = item.price * item.quantity;
          total += itemTotal;

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

        cartTotalElement.textContent = '₦' + total.toLocaleString();

        // Add event listeners for quantity controls
        document.querySelectorAll('.qty-decrease').forEach(function(btn) {
          btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            if (cartItems[index].quantity > 1) {
              cartItems[index].quantity--;
              updateCartUI();
            }
          });
        });

        document.querySelectorAll('.qty-increase').forEach(function(btn) {
          btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            const cartItem = cartItems[index];
            let stock = null;
            if (typeof cartItem.stock !== 'undefined') {
              stock = cartItem.stock;
            } else {
              const card = Array.from(document.querySelectorAll('.item-card')).find(card => card.dataset.id == cartItem.id && card.dataset.type == cartItem.type);
              if (card) {
                stock = parseInt(card.dataset.stock || '0');
              }
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
            if (confirm('Remove ' + cartItems[index].name + ' from cart?')) {
              cartItems.splice(index, 1);
              updateCartUI();
            }
          });
        });
      }
    });
  }); // <-- Close DOMContentLoaded













