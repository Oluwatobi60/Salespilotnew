
    document.addEventListener('DOMContentLoaded', function() {
      // Populate staff filter
      populateStaffDropdown();

      // Staff filter event listener
      const staffFilter = document.getElementById('staffFilter');
      if (staffFilter) {
        staffFilter.addEventListener('change', function() {
          const params = new URLSearchParams(window.location.search);
          if (this.value) {
            params.set('staff_id', this.value);
          } else {
            params.delete('staff_id');
          }
          window.location.search = params.toString();
        });
      }

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
          if (row && !e.target.closest('button') && !e.target.closest('.action-buttons')) {
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
              fetch(`/manager/delete_customer/${customerId}`, {
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
                    showSuccessAlert('Customer deleted successfully!');
                  }, 300);
                } else {
                  // Re-enable button on error
                  button.disabled = false;
                  button.innerHTML = '<i class="bi bi-trash"></i>';
                  showErrorAlert(data.message || 'Failed to delete customer');
                }
              })
              .catch(error => {
                console.error('Delete error:', error);
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-trash"></i>';
                showErrorAlert('An error occurred while deleting the customer');
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
      });

      // Search and Filter functionality
      const searchInput = document.getElementById('customerSearchInput');
      const dateFilter = document.getElementById('dateFilter');
      const applyFiltersBtn = document.getElementById('applyFilters');
      const clearFiltersBtn = document.getElementById('clearFilters');
      const importBtn = document.getElementById('importCustomers');
      const exportBtn = document.getElementById('exportCustomers');
      const table = document.getElementById('customersTable');
      const tableBody = table.querySelector('tbody');

      // Apply filters function
      function applyAllFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStaff = staffFilter ? staffFilter.value : '';
        const selectedDate = dateFilter ? dateFilter.value : '';
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        rows.forEach(row => {
          // Search by name, email, phone, staff
          const name = row.cells[1]?.textContent.toLowerCase() || '';
          const email = row.cells[2]?.textContent.toLowerCase() || '';
          const phone = row.cells[3]?.textContent.toLowerCase() || '';
          const staff = row.cells[4]?.textContent.toLowerCase() || '';
          const date = row.cells[5]?.textContent || '';
          let matchesSearch = name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm) || staff.includes(searchTerm);
          let matchesStaff = !selectedStaff || staff === selectedStaff.toLowerCase();
          let matchesDate = !selectedDate || date === selectedDate;
          if (matchesSearch && matchesStaff && matchesDate) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      }

      // Real-time search
      if (searchInput) {
        searchInput.addEventListener('input', applyAllFilters);
      }

      // Filter change events
      if (staffFilter) {
        staffFilter.addEventListener('change', applyAllFilters);
      }
      if (dateFilter) {
        dateFilter.addEventListener('change', applyAllFilters);
      }

      // Apply filters button
      if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyAllFilters);
      }

      // Clear filters button
      if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
          searchInput.value = '';
          staffFilter.value = '';
          dateFilter.value = '';
          applyAllFilters();
        });
      }

      // Export functionality
      if (exportBtn) {
        exportBtn.addEventListener('click', function() {
          exportBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Exporting...';
          exportBtn.disabled = true;

          // Get visible table data
          const visibleRows = tableBody.querySelectorAll('tr:not([style*="display: none"])');
          let csvContent = 'S/N,Name,Email,Phone,Added By,Date Registered\n';

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

      // Import functionality (placeholder)
      if (importBtn) {
        importBtn.addEventListener('click', function() {
          alert('Import functionality will be implemented here');
        });
      }

      // Edit and Delete functionality
      const editCustomerBtn = document.getElementById('editCustomerBtn');
      const deleteCustomerBtn = document.getElementById('deleteCustomerBtn');
      const viewOrdersBtn = document.getElementById('viewOrdersBtn');
      const sendEmailBtn = document.getElementById('sendEmailBtn');
      const editCustomerPanel = document.getElementById('editCustomerPanel');
      const editPanelBackdrop = document.getElementById('editPanelBackdrop');
      const closeEditPanelBtn = document.getElementById('closeEditPanelBtn');
      const cancelEditBtn = document.getElementById('cancelEditBtn');
      const editCustomerForm = document.getElementById('editCustomerForm');

      // Function to show edit panel
      function showEditPanel() {
        if (editCustomerPanel && editPanelBackdrop) {
          editCustomerPanel.classList.add('show');
          editPanelBackdrop.classList.add('show');
          if (window.innerWidth <= 768) {
            document.body.style.overflow = 'hidden';
          }
        }
      }

      // Function to hide edit panel
      function hideEditPanel() {
        if (editCustomerPanel && editPanelBackdrop) {
          editCustomerPanel.classList.remove('show');
          editPanelBackdrop.classList.remove('show');
          document.body.style.overflow = 'auto';
        }
      }

      // Close edit panel event listeners
      if (closeEditPanelBtn) {
        closeEditPanelBtn.addEventListener('click', hideEditPanel);
      }

      if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', hideEditPanel);
      }

      if (editPanelBackdrop) {
        editPanelBackdrop.addEventListener('click', function(e) {
          if (e.target === editPanelBackdrop) {
            hideEditPanel();
          }
        });
      }

      // Close edit panel with Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && editCustomerPanel && editCustomerPanel.classList.contains('show')) {
          hideEditPanel();
        }
      });

      // Edit Customer functionality - Open side panel with customer data
      if (editCustomerBtn && editCustomerPanel) {
        editCustomerBtn.addEventListener('click', function() {
          if (!currentCustomerId) {
            alert('No customer selected');
            return;
          }

          // Get the row data from the table
          const tableRows = document.querySelectorAll('#customersTable tbody tr');
          let customerData = null;

          tableRows.forEach(row => {
            const viewBtn = row.querySelector('.view-btn');
            if (viewBtn && viewBtn.getAttribute('data-customer-id') === currentCustomerId.toString()) {
              customerData = {
                id: currentCustomerId,
                name: row.cells[1].textContent.trim(),
                email: row.cells[2].textContent.trim(),
                phone: row.cells[3].textContent.trim()
              };
            }
          });

          if (!customerData) {
            // Fallback to getting data from detail panel
            customerData = {
              id: currentCustomerId,
              name: document.getElementById('detailCustomerName')?.textContent || '',
              email: document.getElementById('detailCustomerEmail')?.textContent || '',
              phone: document.getElementById('detailCustomerPhone')?.textContent || ''
            };
          }

          // Populate the edit form
          document.getElementById('editCustomerId').value = customerData.id;
          document.getElementById('editCustomerName').value = customerData.name;
          document.getElementById('editCustomerEmail').value = customerData.email;
          document.getElementById('editCustomerPhone').value = customerData.phone;

          // Get address from details if available
          const addressElement = document.getElementById('detailCustomerAddress');
          if (addressElement) {
            document.getElementById('editCustomerAddress').value = addressElement.textContent.trim();
          }

          // Show the side panel
          showEditPanel();
        });
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
          const formData = new FormData(this);

          // Send update request
          fetch(`/manager/update_customer/${customerId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              customer_name: formData.get('customer_name'),
              email: formData.get('email'),
              phone_number: formData.get('phone_number'),
              address: formData.get('address')
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Update the table row
              const tableRows = document.querySelectorAll('#customersTable tbody tr');
              tableRows.forEach(row => {
                const viewBtn = row.querySelector('.view-btn');
                if (viewBtn && viewBtn.getAttribute('data-customer-id') === customerId.toString()) {
                  row.cells[1].textContent = data.customer.customer_name;
                  row.cells[2].textContent = data.customer.email || '-';
                  row.cells[3].textContent = data.customer.phone_number || '-';
                }
              });

              // Update details panel if open
              if (detailsPanel.classList.contains('show') && currentCustomerId == customerId) {
                document.getElementById('detailCustomerName').textContent = data.customer.customer_name;
                document.getElementById('detailCustomerEmail').textContent = data.customer.email || '-';
                document.getElementById('detailCustomerPhone').textContent = data.customer.phone_number || '-';
                document.getElementById('detailCustomerAddress').textContent = data.customer.address || '-';
              }

              // Hide edit panel
              hideEditPanel();

              // Show success message
              showSuccessAlert(data.message);
            } else {
              showErrorAlert(data.message || 'Failed to update customer');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showErrorAlert('An error occurred while updating the customer');
          })
          .finally(() => {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
          });
        });
      }


      // Delete Customer functionality from side panel
      if (deleteCustomerBtn) {
        deleteCustomerBtn.addEventListener('click', function() {
          if (!currentCustomerId) return;

          // Show confirmation dialog
          if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

            // Make DELETE request to the server
            fetch(`/manager/delete_customer/${currentCustomerId}`, {
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
                // Hide details panel
                hideCustomerDetails();

                // Show success message and reload
                showSuccessAlert(data.message || 'Customer deleted successfully!');

                // Reload page after a short delay
                setTimeout(() => {
                  window.location.reload();
                }, 1500);
              } else {
                // Reset button state on error
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
                showErrorAlert(data.message || 'Failed to delete customer');
              }
            })
            .catch(error => {
              console.error('Delete error:', error);
              this.disabled = false;
              this.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
              showErrorAlert('An error occurred while deleting the customer');
            });
          }
        });
      }

      // View Orders functionality
      if (viewOrdersBtn) {
        viewOrdersBtn.addEventListener('click', function() {
          if (!currentCustomerId) return;

          // Wait a bit for the content to be loaded if needed
          setTimeout(() => {
            // Switch to Purchase History tab
            const purchasesTab = document.getElementById('purchases-tab');
            const purchasesTabPane = document.getElementById('purchases');

            if (!purchasesTab || !purchasesTabPane) {
              console.error('Purchase tab elements not found');
              return;
            }

            // Activate the purchases tab
            document.querySelectorAll('#customerTabs .nav-link').forEach(tab => {
              tab.classList.remove('active');
              tab.setAttribute('aria-selected', 'false');
            });
            document.querySelectorAll('.tab-pane').forEach(pane => {
              pane.classList.remove('show', 'active');
            });

            purchasesTab.classList.add('active');
            purchasesTab.setAttribute('aria-selected', 'true');
            purchasesTabPane.classList.add('show', 'active');

            // Show success message
            showSuccessAlert('Switched to purchase history view');
          }, 100);
        });
      }

      // Send Email functionality
      if (sendEmailBtn) {
        sendEmailBtn.addEventListener('click', function() {
          if (!currentCustomerId) return;

          // Get customer email from the detail panel
          const customerEmail = document.getElementById('detailCustomerEmail')?.textContent;
          const customerName = document.getElementById('detailCustomerName')?.textContent;

          if (!customerEmail || customerEmail === '-') {
            showErrorAlert('No email address available for this customer');
            return;
          }

          // Show loading state
          this.disabled = true;
          this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Preparing...';

          // Create mailto link with pre-filled subject and body
          setTimeout(() => {
            const subject = encodeURIComponent('Hello from SalesPilot');
            const body = encodeURIComponent(`Dear ${customerName},\n\nThank you for being a valued customer.\n\nBest regards,\nSalesPilot Team`);
            const mailtoLink = `mailto:${customerEmail}?subject=${subject}&body=${body}`;

            // Open email client
            window.location.href = mailtoLink;

            // Reset button state
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-envelope me-1"></i>Send Email';

            // Show success message
            showSuccessAlert('Email client opened successfully');
          }, 500);
        });
      }

      // Helper function to show success alerts
      function showSuccessAlert(message) {
        // Create alert element
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
          <i class="bi bi-check-circle me-2"></i>${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Add to page
        document.body.appendChild(alert);

        // Auto-remove after 3 seconds
        setTimeout(() => {
          if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
          }
        }, 3000);
      }

      // Helper function to show error alerts
      function showErrorAlert(message) {
        // Create alert element
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
          <i class="bi bi-exclamation-triangle me-2"></i>${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Add to page
        document.body.appendChild(alert);

        // Auto-remove after 4 seconds
        setTimeout(() => {
          if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
          }
        }, 4000);
      }

      // Helper function to update row numbers after deletion
      function updateRowNumbers() {
        const tableRows = document.querySelectorAll('#customersTable tbody tr');
        tableRows.forEach((row, index) => {
          row.cells[0].textContent = index + 1;
          // Don't update data-customer-id - it should remain the database ID
        });
      }

      // Store current customer ID when showing details
      function showCustomerDetails(customerId) {
        currentCustomerId = customerId;

        if (!detailsPanel || !panelBackdrop) {
          alert('Customer details panel elements not found in the page');
          console.error('Panel elements missing');
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

        // Fetch customer details from API
        fetch(`/manager/get_customer_details/${customerId}`)
          .then(response => {
            if (!response.ok) throw new Error('Failed to fetch customer details');
            return response.json();
          })
          .then(data => {
            console.log('Customer data received:', data);

            if (!data.success) {
              throw new Error('Failed to load customer data');
            }

            const customer = data.customer;
            console.log('Customer details:', customer);

            // Restore the original content structure
            detailsContent.innerHTML = `
              <!-- Modern Tab Navigation -->
              <div class="customer-tabs">
                <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                      <i class="bi bi-person me-1"></i>Overview
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                      <i class="bi bi-envelope me-1"></i>Contact
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="purchases-tab" data-bs-toggle="tab" data-bs-target="#purchases" type="button" role="tab" aria-controls="purchases" aria-selected="false">
                      <i class="bi bi-bag me-1"></i>Orders
                    </button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity" type="button" role="tab" aria-controls="activity" aria-selected="false">
                      <i class="bi bi-clock-history me-1"></i>Activity
                    </button>
                  </li>
                </ul>
              </div>

              <!-- Tab Content -->
              <div class="tab-content" id="customerTabContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
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
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
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
                <div class="tab-pane fade" id="purchases" role="tabpanel" aria-labelledby="purchases-tab">
                  <div class="purchase-table">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Receipt #</th>
                          <th>Date</th>
                          <th>Items</th>
                          <th>Total Amount</th>
                          <th>Actions</th>
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
                              <td>
                                <button class="btn btn-sm btn-outline-primary view-order-details" data-receipt="${order.receipt_number}">
                                  <i class="bi bi-eye"></i> View
                                </button>
                              </td>
                            </tr>
                            <tr class="order-details-row d-none" id="details-${order.receipt_number}">
                              <td colspan="5">
                                <div class="p-3 bg-light">
                                  <h6 class="mb-3"><i class="bi bi-bag me-2"></i>Order Items:</h6>
                                  <table class="table table-sm table-borderless mb-0">
                                    <thead>
                                      <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      ${order.items.map(item => `
                                        <tr>
                                          <td>${item.name}</td>
                                          <td>${item.quantity}</td>
                                          <td>₦${item.price}</td>
                                          <td>₦${item.subtotal}</td>
                                        </tr>
                                      `).join('')}
                                    </tbody>
                                  </table>
                                </div>
                              </td>
                            </tr>
                          `).join('')
                          :
                          `<tr>
                            <td colspan="5" class="text-center text-muted py-4">
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
                <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                  <div class="activity-timeline" id="activity-timeline">
                    <div class="activity-item">
                      <div class="activity-date">Today</div>
                      <div class="activity-title">Customer profile viewed</div>
                      <div class="activity-description">Customer details were accessed by admin</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-date">${customer.lastUpdated}</div>
                      <div class="activity-title">Profile updated</div>
                      <div class="activity-description">Customer information was last updated</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-date">${customer.registrationDate}</div>
                      <div class="activity-title">Account created</div>
                      <div class="activity-description">Customer account was created by ${customer.addedBy}</div>
                    </div>
                  </div>
                </div>
              </div>
            `;

            // Add event listener for view order details buttons
            setTimeout(() => {
              const viewOrderBtns = document.querySelectorAll('.view-order-details');
              viewOrderBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                  const receiptNumber = this.getAttribute('data-receipt');
                  const detailsRow = document.getElementById(`details-${receiptNumber}`);

                  if (detailsRow) {
                    if (detailsRow.classList.contains('d-none')) {
                      // Hide all other details rows first
                      document.querySelectorAll('.order-details-row').forEach(row => {
                        row.classList.add('d-none');
                      });
                      // Reset all button icons
                      document.querySelectorAll('.view-order-details').forEach(b => {
                        b.innerHTML = '<i class="bi bi-eye"></i> View';
                      });
                      // Show this details row
                      detailsRow.classList.remove('d-none');
                      this.innerHTML = '<i class="bi bi-eye-slash"></i> Hide';
                    } else {
                      detailsRow.classList.add('d-none');
                      this.innerHTML = '<i class="bi bi-eye"></i> View';
                    }
                  }
                });
              });
            }, 100);

            // Prevent body scroll on mobile
            if (window.innerWidth <= 768) {
              document.body.style.overflow = 'hidden';
            }
          })
          .catch(error => {
            console.error('Error loading customer details:', error);
            showErrorAlert('Failed to load customer details');
            hideCustomerDetails();
          });
      }


    });

// Populate staff dropdown for filtering
function populateStaffDropdown() {
    fetch('/manager/get-staff-user-list')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(response => {
            const staffFilter = document.getElementById('staffFilter');
            if (!staffFilter) return;

            // Get current staff_id from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const currentStaffId = urlParams.get('staff_id');

            // Keep the "All Staff" option
            const allStaffOption = staffFilter.querySelector('option[value=""]');
            staffFilter.innerHTML = '';
            if (allStaffOption) {
                staffFilter.appendChild(allStaffOption);
            } else {
                staffFilter.innerHTML = '<option value="">All Staff</option>';
            }

            // Access the staffUsers array from the response
            const staffUsers = response.staffUsers || [];

            staffUsers.forEach(person => {
                // Extract numeric ID from 'staff_123' or 'user_456' format
                const numericId = person.id.replace(/^(staff_|user_)/, '');
                const selected = currentStaffId == numericId ? 'selected' : '';
                const option = document.createElement('option');
                option.value = numericId;
                option.textContent = person.name;
                if (selected) option.selected = true;
                staffFilter.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Failed to load staff/user list:', error);
        });
}
