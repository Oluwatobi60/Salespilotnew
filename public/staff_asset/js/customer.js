
    document.addEventListener('DOMContentLoaded', function() {
      // Customer details panel elements
      const customersTable = document.getElementById('customersTable');
      const detailsPanel = document.getElementById('customerDetailsPanel');
      const panelBackdrop = document.getElementById('panelBackdrop');
      const closeDetailsBtn = document.getElementById('closeDetailsBtn');

      // Current customer ID tracker
      let currentCustomerId = null;

      // Function to hide customer details
      function hideCustomerDetails() {
        detailsPanel.classList.remove('show');
        panelBackdrop.classList.remove('show');
        currentCustomerId = null; // Reset current customer ID

        // Remove selected state from all rows
        document.querySelectorAll('#customersTable tbody tr').forEach(row => {
          row.classList.remove('selected');
        });

        // Re-enable body scroll
        document.body.style.overflow = 'auto';
      }

      // Add click event listener to table rows (but not on action buttons)
      if (customersTable) {
        customersTable.addEventListener('click', function(e) {
          const row = e.target.closest('tbody tr');
          if (row && !e.target.closest('button')) {
            // Remove selected class from all rows
            document.querySelectorAll('#customersTable tbody tr').forEach(r => {
              r.classList.remove('selected');
            });

            // Add selected class to clicked row
            row.classList.add('selected');

            // Get customer ID from the view button in this row
            const viewBtn = row.querySelector('.view-btn');
            if (viewBtn) {
              const customerId = viewBtn.getAttribute('data-customer-id');
              showCustomerDetails(customerId);
            }
          }
        });
      }

      // Action button functionality for table rows
      document.addEventListener('click', function(e) {
        // View button functionality
        if (e.target.closest('.view-btn')) {
          e.preventDefault();
          e.stopPropagation();
          const button = e.target.closest('.view-btn');
          const customerId = button.getAttribute('data-customer-id');

          // Remove selected class from all rows
          document.querySelectorAll('#customersTable tbody tr').forEach(r => {
            r.classList.remove('selected');
          });

          // Add selected class to current row
          const row = button.closest('tr');
          if (row) {
            row.classList.add('selected');
          }

          // Show customer details
          showCustomerDetails(customerId);
        }

        // Edit button functionality
        if (e.target.closest('.edit-btn')) {
          e.preventDefault();
          e.stopPropagation();
          const button = e.target.closest('.edit-btn');
          const customerId = button.getAttribute('data-customer-id');

          // Remove selected class from all rows
          document.querySelectorAll('#customersTable tbody tr').forEach(r => {
            r.classList.remove('selected');
          });

          // Add selected class to current row
          const row = button.closest('tr');
          if (row) {
            row.classList.add('selected');
          }

          // Set current customer ID
          currentCustomerId = customerId;

          // Get customer data from the row
          const customerData = {
            id: customerId,
            name: row.cells[1].textContent.trim(),
            email: row.cells[2].textContent.trim(),
            phone: row.cells[3].textContent.trim()
          };

          // Populate the edit form
          document.getElementById('editCustomerId').value = customerData.id;
          document.getElementById('editCustomerName').value = customerData.name;
          document.getElementById('editCustomerEmail').value = customerData.email;
          document.getElementById('editCustomerPhone').value = customerData.phone;
          document.getElementById('editCustomerAddress').value = '';

          // Show the edit side panel
          const editPanel = document.getElementById('editCustomerPanel');
          const editBackdrop = document.getElementById('editPanelBackdrop');
          if (editPanel && editBackdrop) {
            editPanel.classList.add('show');
            editBackdrop.classList.add('show');
            if (window.innerWidth <= 768) {
              document.body.style.overflow = 'hidden';
            }
          }
        }

        // Delete button functionality
        if (e.target.closest('.delete-btn')) {
          e.preventDefault();
          e.stopPropagation();
          const button = e.target.closest('.delete-btn');
          const customerId = button.getAttribute('data-customer-id');
          const customerName = button.getAttribute('data-customer-name');

          // Show SweetAlert2 confirmation dialog
          Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to delete "${customerName}"? This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              // Add loading state
              button.disabled = true;
              button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

              // Make DELETE request to the server
              fetch(`/staff/delete_customer/${customerId}`, {
                method: 'DELETE',
                headers: {
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  // Remove row from table with animation
                  const row = button.closest('tr');
                  row.style.transition = 'all 0.3s ease';
                  row.style.opacity = '0';
                  row.style.transform = 'translateX(-20px)';

                  setTimeout(() => {
                    row.remove();

                    // Update row numbers
                    updateRowNumbers();

                    // Hide details panel if this customer was selected
                    if (currentCustomerId === customerId) {
                      hideCustomerDetails();
                    }

                    // Show success message
                    Swal.fire({
                      icon: 'success',
                      title: 'Deleted!',
                      text: 'Customer deleted successfully!',
                      timer: 2000,
                      showConfirmButton: false
                    });
                  }, 300);
                } else {
                  // Re-enable button on error
                  button.disabled = false;
                  button.innerHTML = '<i class="bi bi-trash"></i>';
                  Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to delete customer'
                  });
                }
              })
              .catch(error => {
                console.error('Delete error:', error);
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-trash"></i>';
                Swal.fire({
                  icon: 'error',
                  title: 'Error!',
                  text: 'An error occurred while deleting the customer'
                });
              });
            }
          });
        }
      });

      // Close details panel
      if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', hideCustomerDetails);
      }

      // Close panel when clicking backdrop
      if (panelBackdrop) {
        panelBackdrop.addEventListener('click', hideCustomerDetails);
      }

      // Close panel when clicking outside the container
      if (detailsPanel) {
        detailsPanel.addEventListener('click', function(e) {
          if (e.target === detailsPanel) {
            hideCustomerDetails();
          }
        });
      }

      // Close panel with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && detailsPanel.classList.contains('show')) {
          hideCustomerDetails();
        }
        if (e.key === 'Escape') {
          const editPanel = document.getElementById('editCustomerPanel');
          if (editPanel && editPanel.classList.contains('show')) {
            hideEditPanel();
          }
        }
      });

      // Edit Panel Functions
      const editPanel = document.getElementById('editCustomerPanel');
      const editBackdrop = document.getElementById('editPanelBackdrop');
      const closeEditPanelBtn = document.getElementById('closeEditPanelBtn');
      const cancelEditBtn = document.getElementById('cancelEditBtn');
      const editCustomerForm = document.getElementById('editCustomerForm');

      function hideEditPanel() {
        if (editPanel) editPanel.classList.remove('show');
        if (editBackdrop) editBackdrop.classList.remove('show');
        document.body.style.overflow = 'auto';
      }

      // Close edit panel button
      if (closeEditPanelBtn) {
        closeEditPanelBtn.addEventListener('click', hideEditPanel);
      }

      // Cancel edit button
      if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', hideEditPanel);
      }

      // Close edit panel when clicking backdrop
      if (editBackdrop) {
        editBackdrop.addEventListener('click', hideEditPanel);
      }

      // Handle edit form submission
      if (editCustomerForm) {
        editCustomerForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const submitBtn = this.querySelector('button[type="submit"]');
          const spinner = document.getElementById('editCustomerSpinner');
          const customerId = document.getElementById('editCustomerId').value;

          // Show loading state
          submitBtn.disabled = true;
          spinner.classList.remove('d-none');

          // Get form data
          const formData = {
            customer_name: document.getElementById('editCustomerName').value,
            email: document.getElementById('editCustomerEmail').value,
            phone_number: document.getElementById('editCustomerPhone').value,
            address: document.getElementById('editCustomerAddress').value
          };

          // Send update request
          fetch(`/staff/update_customer/${customerId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Update the table row
              const tableRows = document.querySelectorAll('#customersTable tbody tr');
              tableRows.forEach(row => {
                const viewBtn = row.querySelector('.view-btn');
                if (viewBtn && viewBtn.getAttribute('data-customer-id') === customerId) {
                  row.cells[1].textContent = data.customer.customer_name;
                  row.cells[2].textContent = data.customer.email || '-';
                  row.cells[3].textContent = data.customer.phone_number || '-';
                }
              });

              // Update details panel if open
              if (detailsPanel.classList.contains('show') && currentCustomerId === customerId) {
                document.getElementById('detailCustomerName').textContent = data.customer.customer_name;
                document.getElementById('detailCustomerEmail').textContent = data.customer.email || '-';
                document.getElementById('detailCustomerPhone').textContent = data.customer.phone_number || '-';
                document.getElementById('detailCustomerAddress').textContent = data.customer.address || '-';
              }

              // Hide edit panel
              hideEditPanel();

              // Show success message
              Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to update customer'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: 'An error occurred while updating the customer'
            });
          })
          .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
          });
        });
      }

      // Search functionality
      const searchInput = document.getElementById('customerSearchInput');
      const exportBtn = document.getElementById('exportCustomers');
      const table = document.getElementById('customersTable');
      const tableBody = table.querySelector('tbody');

      // Real-time search
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const rows = Array.from(tableBody.querySelectorAll('tr'));

          rows.forEach(row => {
            const name = row.cells[1]?.textContent.toLowerCase() || '';
            const email = row.cells[2]?.textContent.toLowerCase() || '';
            const phone = row.cells[3]?.textContent.toLowerCase() || '';

            const matches = name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm);
            row.style.display = matches ? '' : 'none';
          });
        });
      }

      // Export functionality
      if (exportBtn) {
        exportBtn.addEventListener('click', function() {
          exportBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Exporting...';
          exportBtn.disabled = true;

          // Get visible table data
          const visibleRows = tableBody.querySelectorAll('tr:not([style*="display: none"])');
          let csvContent = 'S/N,Name,Email,Phone,Date Registered,Added By\n';

          visibleRows.forEach(row => {
            const rowData = [
              row.cells[0].textContent.trim(),
              row.cells[1].textContent.trim(),
              row.cells[2].textContent.trim(),
              row.cells[3].textContent.trim(),
              row.cells[4].textContent.trim(),
              row.cells[5].textContent.trim()
            ];
            csvContent += rowData.map(field => `"${field.replace(/"/g, '""')}"`).join(',') + '\n';
          });

          // Create and download CSV file
          const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
          const link = document.createElement('a');
          const url = URL.createObjectURL(blob);
          link.setAttribute('href', url);
          link.setAttribute('download', `customers_${new Date().toISOString().split('T')[0]}.csv`);
          link.style.visibility = 'hidden';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);

          // Reset button state
          setTimeout(() => {
            exportBtn.innerHTML = '<i class="bi bi-download"></i> Export';
            exportBtn.disabled = false;
          }, 1000);
        });
      }

      // Helper function to update row numbers after deletion
      function updateRowNumbers() {
        const tableRows = document.querySelectorAll('#customersTable tbody tr');
        tableRows.forEach((row, index) => {
          row.cells[0].textContent = index + 1;
        });
      }

      // Store current customer ID when showing details
      function showCustomerDetails(customerId) {
        currentCustomerId = customerId;

        if (!detailsPanel || !panelBackdrop) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Customer details panel elements not found'
          });
          return;
        }

        // Show loading state
        detailsPanel.classList.add('show');
        panelBackdrop.classList.add('show');

        // Show loading indicator
        const detailsContent = detailsPanel.querySelector('.customer-details-content');
        if (detailsContent) {
          detailsContent.innerHTML = `
            <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <div class="mt-3">Loading customer details...</div>
            </div>
          `;
        }

        // Fetch customer details from API (using staff endpoint)
        fetch(`/staff/get_customer_details/${customerId}`)
          .then(response => {
            if (!response.ok) throw new Error('Failed to fetch customer details');
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              throw new Error('Failed to load customer data');
            }

            const customer = data.customer;

            // Restore the original content structure
            detailsContent.innerHTML = `
              <!-- Modern Tab Navigation -->
              <div class="customer-tabs">
                <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                      <i class="bi bi-person me-1"></i>Overview
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                      <i class="bi bi-envelope me-1"></i>Contact
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases" type="button" role="tab">
                      <i class="bi bi-bag me-1"></i>Orders
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab">
                      <i class="bi bi-clock-history me-1"></i>Activity
                    </button>
                  </li>
                </ul>
              </div>

              <!-- Tab Content -->
              <div class="tab-content" id="customerTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                  <div class="info-section">
                    <div class="info-item">
                      <div class="info-label">Customer ID</div>
                      <div class="info-value" id="detailCustomerId">${customer.id}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Full Name</div>
                      <div class="info-value" id="detailCustomerName">${customer.name}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Total Orders</div>
                      <div class="info-value text-primary" id="detailTotalOrders">${customer.totalOrders || 0}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Total Spent</div>
                      <div class="info-value text-success" id="detailTotalSpent">${customer.totalSpent || '₦0.00'}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Account Status</div>
                      <div class="info-value">
                        <span class="badge bg-success" id="detailCustomerStatus">${customer.status}</span>
                      </div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Last Purchase</div>
                      <div class="info-value" id="detailLastPurchase">${customer.lastPurchase || 'Never'}</div>
                    </div>
                  </div>
                </div>

                <!-- Contact Info Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                  <div class="info-section">
                    <div class="info-item">
                      <div class="info-label">Email Address</div>
                      <div class="info-value" id="detailCustomerEmail">${customer.email}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Phone Number</div>
                      <div class="info-value" id="detailCustomerPhone">${customer.phone}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Address</div>
                      <div class="info-value" id="detailCustomerAddress">${customer.address}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Date Registered</div>
                      <div class="info-value" id="detailRegistrationDate">${customer.registrationDate}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Added by</div>
                      <div class="info-value" id="detailAddedBy">${customer.addedBy}</div>
                    </div>
                    <div class="info-item">
                      <div class="info-label">Last Updated</div>
                      <div class="info-value" id="detailLastUpdated">${customer.lastUpdated}</div>
                    </div>
                  </div>
                </div>

                <!-- Purchase History Tab -->
                <div class="tab-pane fade" id="purchases" role="tabpanel">
                  <div class="purchase-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Receipt #</th>
                          <th>Date</th>
                          <th>Items</th>
                          <th>Total Amount</th>
                        </tr>
                      </thead>
                      <tbody id="purchase-history">
                        ${customer.orders && customer.orders.length > 0 ?
                          customer.orders.map(order => `
                            <tr>
                              <td><strong>${order.receipt_number}</strong></td>
                              <td>${order.date}</td>
                              <td>${order.items_count} item${order.items_count > 1 ? 's' : ''}</td>
                              <td class="text-success fw-bold">₦${order.total}</td>
                            </tr>
                          `).join('')
                          :
                          `<tr>
                            <td colspan="4" class="text-center text-muted py-4">
                              <i class="bi bi-bag fa-2x mb-2"></i>
                              <div>No purchase history available</div>
                            </td>
                          </tr>`
                        }
                      </tbody>
                    </table>
                  </div>
                </div>

                <!-- Activity Log Tab -->
                <div class="tab-pane fade" id="activity" role="tabpanel">
                  <div class="activity-timeline" id="activity-timeline">
                    <div class="activity-item">
                      <div class="activity-date">Today</div>
                      <div class="activity-title">Customer profile viewed</div>
                      <div class="activity-description">Customer details were accessed</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-date">${customer.lastUpdated}</div>
                      <div class="activity-title">Profile updated</div>
                      <div class="activity-description">Customer information was last updated</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-date">${customer.registrationDate}</div>
                      <div class="activity-title">Account created</div>
                      <div class="activity-description">Customer account was created</div>
                    </div>
                  </div>
                </div>
              </div>
            `;

            // Prevent body scroll on mobile
            if (window.innerWidth <= 768) {
              document.body.style.overflow = 'hidden';
            }
          })
          .catch(error => {
            console.error('Error loading customer details:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to load customer details'
            });
            hideCustomerDetails();
          });
      }

    });
