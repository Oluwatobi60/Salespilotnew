
    document.addEventListener('DOMContentLoaded', function() {
      // Customer details panel elements
      const customersTable = document.getElementById('customersTable');
      const detailsPanel = document.getElementById('customerDetailsPanel');
      const panelBackdrop = document.getElementById('panelBackdrop');
      const closeDetailsBtn = document.getElementById('closeDetailsBtn');

      // Current customer ID tracker
      let currentCustomerId = null;

      // Sample detailed customer data
      const customerDetails = {
        1: {
          id: 'CUST-001',
          name: 'John Doe',
          email: 'john.doe@example.com',
          phone: '+1 234 567 8900',
          address: '123 Main Street, Anytown, AT 12345',
          registrationDate: 'Nov 12, 2023',
          addedBy: 'Admin',
          lastUpdated: 'Nov 15, 2023',
          status: 'Active',
          totalOrders: '12',
          totalSpent: '$2,450.00',
          lastPurchase: 'Nov 10, 2023'
        },
        2: {
          id: 'CUST-002',
          name: 'Jane Smith',
          email: 'jane.smith@example.com',
          phone: '+1 234 567 8901',
          address: '456 Oak Avenue, Somewhere, SW 54321',
          registrationDate: 'Nov 13, 2023',
          addedBy: 'Staff 4',
          lastUpdated: 'Nov 14, 2023',
          status: 'Active',
          totalOrders: '8',
          totalSpent: '$1,230.50',
          lastPurchase: 'Nov 8, 2023'
        },
        3: {
          id: 'CUST-003',
          name: 'Michael Brown',
          email: 'michael.brown@example.com',
          phone: '+1 234 567 8902',
          address: '789 Pine Road, Elsewhere, EW 67890',
          registrationDate: 'Nov 14, 2023',
          addedBy: 'Admin',
          lastUpdated: 'Nov 16, 2023',
          status: 'Active',
          totalOrders: '15',
          totalSpent: '$3,120.75',
          lastPurchase: 'Nov 12, 2023'
        },
        4: {
          id: 'CUST-004',
          name: 'Sarah Johnson',
          email: 'sarah.johnson@example.com',
          phone: '+1 234 567 8903',
          address: '321 Elm Street, Nowhere, NW 13579',
          registrationDate: 'Nov 15, 2023',
          addedBy: 'Staff 2',
          lastUpdated: 'Nov 17, 2023',
          status: 'Inactive',
          totalOrders: '3',
          totalSpent: '$450.25',
          lastPurchase: 'Oct 28, 2023'
        },
        5: {
          id: 'CUST-005',
          name: 'David Wilson',
          email: 'david.wilson@example.com',
          phone: '+1 234 567 8904',
          address: '654 Maple Drive, Anywhere, AW 24680',
          registrationDate: 'Nov 16, 2023',
          addedBy: 'Admin',
          lastUpdated: 'Nov 18, 2023',
          status: 'Active',
          totalOrders: '7',
          totalSpent: '$890.00',
          lastPurchase: 'Nov 5, 2023'
        }
      };

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

            // Get customer ID from the row (S/N column)
            const customerId = row.cells[0].textContent.trim();
            showCustomerDetails(customerId);
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

          // Show the edit modal
          const editModalElement = document.getElementById('editCustomerModal');
          if (editModalElement) {
            const modal = new bootstrap.Modal(editModalElement);
            modal.show();
          }
        }

        // Delete button functionality
        if (e.target.closest('.delete-btn')) {
          e.preventDefault();
          e.stopPropagation();
          const button = e.target.closest('.delete-btn');
          const customerId = button.getAttribute('data-customer-id');

          const customer = customerDetails[customerId];
          if (!customer) {
            showErrorAlert('Customer not found');
            return;
          }

          // Show confirmation dialog
          if (confirm(`Are you sure you want to delete customer "${customer.name}"? This action cannot be undone.`)) {
            // Add loading state
            button.classList.add('loading');

            setTimeout(() => {
              // Remove from customer data
              delete customerDetails[customerId];

              // Remove from table
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

            }, 1000);
          }
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
      const staffFilter = document.getElementById('staffFilter');
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
      const editCustomerModal = document.getElementById('editCustomerModal');
      const editCustomerForm = document.getElementById('editCustomerForm');

      // Edit Customer functionality - Open modal with customer data
      if (editCustomerBtn && editCustomerModal) {
        editCustomerBtn.addEventListener('click', function() {
          if (!currentCustomerId) {
            alert('No customer selected');
            return;
          }

          // Get the row data from the table
          const tableRows = document.querySelectorAll('#customersTable tbody tr');
          let customerData = null;

          tableRows.forEach(row => {
            if (row.cells[0].textContent.trim() === currentCustomerId) {
              customerData = {
                id: currentCustomerId,
                name: row.cells[1].textContent.trim(),
                email: row.cells[2].textContent.trim(),
                phone: row.cells[3].textContent.trim()
              };
            }
          });

          if (!customerData) {
            alert('Customer not found');
            return;
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

          // Show the modal
          const modal = new bootstrap.Modal(editCustomerModal);
          modal.show();
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
          fetch(`/staff/update_customer/${customerId}`, {
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
                if (row.cells[0].textContent.trim() === customerId) {
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

              // Hide modal
              const modal = bootstrap.Modal.getInstance(editCustomerModal);
              modal.hide();

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


      // Delete Customer functionality
      if (deleteCustomerBtn) {
        deleteCustomerBtn.addEventListener('click', function() {
          if (!currentCustomerId) return;

          const customer = customerDetails[currentCustomerId];
          if (!customer) return;

          // Show confirmation dialog
          if (confirm(`Are you sure you want to delete customer "${customer.name}"? This action cannot be undone.`)) {
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

            // Simulate API call with timeout
            setTimeout(() => {
              // Remove from customer data
              delete customerDetails[currentCustomerId];

              // Remove from table
              const tableRows = document.querySelectorAll('#customersTable tbody tr');
              tableRows.forEach(row => {
                if (row.cells[0].textContent.trim() === currentCustomerId) {
                  row.remove();
                }
              });

              // Hide details panel
              hideCustomerDetails();

              // Reset button state
              this.disabled = false;
              this.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';

              // Show success message
              showSuccessAlert('Customer deleted successfully!');

            }, 1500); // Simulate network delay
          }
        });
      }

      // View Orders functionality
      if (viewOrdersBtn) {
        viewOrdersBtn.addEventListener('click', function() {
          if (!currentCustomerId) return;

          const customer = customerDetails[currentCustomerId];
          if (!customer) return;

          // Switch to Purchase History tab
          const purchasesTab = document.getElementById('purchases-tab');
          const purchasesTabPane = document.getElementById('purchases');

          // Activate the purchases tab
          document.querySelectorAll('#customerTabs .nav-link').forEach(tab => tab.classList.remove('active'));
          document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
          });

          purchasesTab.classList.add('active');
          purchasesTabPane.classList.add('show', 'active');

          // Simulate loading purchase history
          const purchaseHistory = document.getElementById('purchase-history');
          purchaseHistory.innerHTML = `
            <tr>
              <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div class="mt-2">Loading purchase history...</div>
              </td>
            </tr>
          `;

          // Simulate API call to load purchase data
          setTimeout(() => {
            purchaseHistory.innerHTML = `
              <tr>
                <td>#ORD-001</td>
                <td>Nov 10, 2023</td>
                <td>3 items</td>
                <td class="text-success">$450.00</td>
                <td><span class="badge bg-success">Completed</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary">View</button>
                </td>
              </tr>
              <tr>
                <td>#ORD-002</td>
                <td>Nov 5, 2023</td>
                <td>1 item</td>
                <td class="text-success">$120.50</td>
                <td><span class="badge bg-success">Completed</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary">View</button>
                </td>
              </tr>
              <tr>
                <td>#ORD-003</td>
                <td>Oct 28, 2023</td>
                <td>5 items</td>
                <td class="text-success">$890.00</td>
                <td><span class="badge bg-warning">Pending</span></td>
                <td>
                  <button class="btn btn-sm btn-outline-primary">View</button>
                </td>
              </tr>
            `;
          }, 1000);

          // Show success message
          showSuccessAlert('Switched to purchase history view');
        });
      }

      // Send Email functionality
      if (sendEmailBtn) {
        sendEmailBtn.addEventListener('click', function() {
          if (!currentCustomerId) return;

          const customer = customerDetails[currentCustomerId];
          if (!customer) return;

          // Show loading state
          this.disabled = true;
          this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Preparing...';

          // Simulate email preparation
          setTimeout(() => {
            // Create mailto link with pre-filled subject and body
            const subject = encodeURIComponent('Hello from Your Store');
            const body = encodeURIComponent(`Dear ${customer.name},\n\nThank you for being a valued customer.\n\nBest regards,\nYour Store Team`);
            const mailtoLink = `mailto:${customer.email}?subject=${subject}&body=${body}`;

            // Open email client
            window.location.href = mailtoLink;

            // Reset button state
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-envelope me-1"></i>Send Email';

            // Show success message
            showSuccessAlert('Email client opened successfully');
          }, 1000);
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

          // Update data-customer-id attributes for action buttons
          const actionButtons = row.querySelectorAll('.action-buttons button');
          actionButtons.forEach(button => {
            button.setAttribute('data-customer-id', index + 1);
          });
        });
      }

      // Store current customer ID when showing details
      function showCustomerDetails(customerId) {
        currentCustomerId = customerId;
        const customer = customerDetails[customerId];

        if (!customer) {
          alert('Customer data not found for ID: ' + customerId);
          console.error('Customer not found:', customerId);
          return;
        }

        // Check if required elements exist
        const requiredElements = [
          'detailCustomerId', 'detailCustomerName', 'detailCustomerEmail',
          'detailCustomerPhone', 'detailCustomerAddress', 'detailRegistrationDate',
          'detailAddedBy', 'detailLastUpdated', 'detailTotalOrders',
          'detailTotalSpent', 'detailLastPurchase', 'detailCustomerStatus'
        ];

        let missingElements = [];
        requiredElements.forEach(id => {
          if (!document.getElementById(id)) {
            missingElements.push(id);
          }
        });

        if (missingElements.length > 0) {
          console.error('Missing elements:', missingElements);
          alert('Customer details panel is not properly configured. Missing: ' + missingElements.join(', '));
          return;
        }

        // Update basic info
        document.getElementById('detailCustomerId').textContent = customer.id;
        document.getElementById('detailCustomerName').textContent = customer.name;
        document.getElementById('detailCustomerEmail').textContent = customer.email;
        document.getElementById('detailCustomerPhone').textContent = customer.phone;
        document.getElementById('detailCustomerAddress').textContent = customer.address;
        document.getElementById('detailRegistrationDate').textContent = customer.registrationDate;
        document.getElementById('detailAddedBy').textContent = customer.addedBy;
        document.getElementById('detailLastUpdated').textContent = customer.lastUpdated;
        document.getElementById('detailTotalOrders').textContent = customer.totalOrders;
        document.getElementById('detailTotalSpent').textContent = customer.totalSpent;
        document.getElementById('detailLastPurchase').textContent = customer.lastPurchase;

        // Set status with appropriate styling
        const statusElement = document.getElementById('detailCustomerStatus');
        statusElement.textContent = customer.status;

        if (!detailsPanel || !panelBackdrop) {
          alert('Customer details panel elements not found in the page');
          console.error('Panel elements missing');
          return;
        }

        // Show the backdrop and panel with animation
        panelBackdrop.classList.add('show');
        detailsPanel.classList.add('show');

        // Prevent body scroll on mobile
        if (window.innerWidth <= 768) {
          document.body.style.overflow = 'hidden';
        }
      }


    });
