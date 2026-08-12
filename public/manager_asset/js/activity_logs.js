		document.addEventListener('DOMContentLoaded', function() {
		  // Initialize Bootstrap dropdowns for profile menu
		  var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
		  var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
		    return new bootstrap.Dropdown(dropdownToggleEl);
		  });

		  // Profile dropdown specific handler
		  var userDropdownToggle = document.getElementById('UserDropdown');
		  if (userDropdownToggle) {
		    console.log('Profile dropdown initialized');

		    // Ensure dropdown is properly initialized
		    var dropdown = bootstrap.Dropdown.getOrCreateInstance(userDropdownToggle);

		    // Add click event listener
		    userDropdownToggle.addEventListener('click', function(e) {
		      e.preventDefault();
		      console.log('Profile picture clicked');

		      var dropdownMenu = this.nextElementSibling;
		      if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
		        // Toggle dropdown visibility
		        if (dropdownMenu.classList.contains('show')) {
		          dropdown.hide();
		        } else {
		          dropdown.show();
		        }
		      }
		    });
		  }


		});


		document.addEventListener('DOMContentLoaded', function() {
			const dateFilter = document.getElementById('dateFilter');
			const customDateInputs = document.getElementById('customDateInputs');
			const startDateInput = document.getElementById('startDate');
			const endDateInput = document.getElementById('endDate');
			const table = document.getElementById('table');
			const tableBody = table.querySelector('tbody');
			const tableRows = Array.from(tableBody.querySelectorAll('tr'));

			// Show/hide custom date overlay based on date filter selection
			function showCustomDateOverlay() {
				const customDateInputs = document.getElementById('customDateInputs');
				customDateInputs.classList.add('show');
			}

			function hideCustomDateOverlay() {
				const customDateInputs = document.getElementById('customDateInputs');
				customDateInputs.classList.remove('show');
			}

            // Initialize custom date overlay if custom is selected on load
            if (dateFilter && dateFilter.value === 'custom') {
                showCustomDateOverlay();
            }

			// Date filter change handler
			if (dateFilter) {
				dateFilter.addEventListener('change', function() {
					if (this.value === 'custom') {
						showCustomDateOverlay();
					} else {
						hideCustomDateOverlay();
					}
					// Auto submit form when date filter changes (except for custom, which requires start/end inputs)
					if (this.value !== 'custom') {
						document.getElementById('filterForm').submit();
					}
				});
			}

			// ESC key handler for closing custom date overlay
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' || e.keyCode === 27) {
					if (customDateInputs && customDateInputs.classList.contains('show')) {
						hideCustomDateOverlay();
					}
				}
			});

			// Click outside overlay to close
			document.addEventListener('click', function(e) {
				const isClickInsideFilter = e.target.closest('.date-filter-wrapper');
				if (customDateInputs && !isClickInsideFilter && customDateInputs.classList.contains('show')) {
					hideCustomDateOverlay();
				}
			});

            // Make custom dates auto submit when both are filled
            if (startDateInput && endDateInput) {
                const autoSubmitCustomDates = function() {
                    if (startDateInput.value && endDateInput.value) {
                        document.getElementById('filterForm').submit();
                    }
                };
                startDateInput.addEventListener('change', autoSubmitCustomDates);
                endDateInput.addEventListener('change', autoSubmitCustomDates);
            }

            // Auto submit when dropdowns change
            const accessTypeFilter = document.getElementById('accessTypeFilter');
            const staffFilter = document.getElementById('staffFilter');
            
            if (accessTypeFilter) {
                accessTypeFilter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
            if (staffFilter) {
                staffFilter.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }



			// Table row click functionality for activity details
			const activityDetailsPanel = document.getElementById('activityDetailsPanel');
			const panelOverlay = document.getElementById('panelOverlay');
			const closePanelBtn = document.getElementById('closePanelBtn');

			// Add click event to table rows
			tableRows.forEach((row, index) => {
				row.addEventListener('click', function(e) {
					// Remove clicked class from all rows
					tableRows.forEach(r => r.classList.remove('clicked'));

					// Add clicked class to current row
					this.classList.add('clicked');

					// Extract activity data from row
					const cells = this.querySelectorAll('td');
					const activityData = {
						id: cells[0].textContent.trim(),
						dateTime: cells[1].textContent.trim(),
						activity: cells[2].textContent.trim(),
						staffName: cells[3].textContent.trim(),
						accessType: cells[4].textContent.trim().toLowerCase()
					};

					// Populate panel with activity data
					populateActivityPanel(activityData);

					// Show panel
					showActivityPanel();
				});

				// Add data attribute for styling
				row.setAttribute('data-clickable', 'true');
			});

			// Function to show activity panel
			function showActivityPanel() {
				activityDetailsPanel.classList.add('active');
				panelOverlay.classList.add('active');
				document.body.style.overflow = 'hidden'; // Prevent background scrolling
			}

			// Function to hide activity panel
			function hideActivityPanel() {
				activityDetailsPanel.classList.remove('active');
				panelOverlay.classList.remove('active');
				document.body.style.overflow = ''; // Restore scrolling

				// Remove clicked class from all rows
				tableRows.forEach(r => r.classList.remove('clicked'));
			}

			// Panel close event listeners
			if (closePanelBtn) {
				closePanelBtn.addEventListener('click', hideActivityPanel);
			}

			if (panelOverlay) {
				panelOverlay.addEventListener('click', hideActivityPanel);
			}

			// ESC key to close panel
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && activityDetailsPanel.classList.contains('active')) {
					hideActivityPanel();
				}
			});

			// Function to populate activity panel
			function populateActivityPanel(data) {
				// Generate mock additional data based on activity type
				const mockData = generateMockData(data);

				document.getElementById('detailLogId').textContent = `#LOG${data.id.padStart(6, '0')}`;
				document.getElementById('detailDateTime').textContent = data.dateTime;
				document.getElementById('detailActivity').textContent = data.activity;
				document.getElementById('detailStaffName').textContent = data.staffName;

				// Set access type with proper styling
				const accessBadge = document.getElementById('detailAccessType');
				accessBadge.textContent = data.accessType.charAt(0).toUpperCase() + data.accessType.slice(1);
				accessBadge.className = `access-badge ${data.accessType}`;

				// Populate additional information
				document.getElementById('detailSessionId').textContent = mockData.sessionId;
				document.getElementById('detailIpAddress').textContent = mockData.ipAddress;
				document.getElementById('detailBrowser').textContent = mockData.browser;
				document.getElementById('detailStatus').textContent = mockData.status;
				document.getElementById('detailStatus').className = `badge bg-${mockData.statusColor}`;
			}

			// Function to generate mock additional data
			function generateMockData(data) {
				const sessions = ['SES_001234567', 'SES_002345678', 'SES_003456789'];
				const ips = ['192.168.1.101', '192.168.1.102', '192.168.1.103', '10.0.0.25'];
				const browsers = ['Chrome 118.0', 'Firefox 119.0', 'Safari 17.0', 'Edge 118.0'];
				const statuses = [
					{ text: 'Completed', color: 'success' },
					{ text: 'In Progress', color: 'warning' },
					{ text: 'Failed', color: 'danger' },
					{ text: 'Pending', color: 'info' }
				];

				const randomIndex = parseInt(data.id) % 4;
				const status = statuses[randomIndex];

				return {
					sessionId: sessions[randomIndex],
					ipAddress: ips[randomIndex],
					browser: browsers[randomIndex],
					status: status.text,
					statusColor: status.color
				};
			}

			// Export functionality
			document.querySelector('.btn-outline-success').addEventListener('click', function() {
				const visibleRows = Array.from(tableBody.querySelectorAll('tr'));
				let csvContent = 'S/N,Date,Activity,Staff Name,Access Type\n';

				visibleRows.forEach(row => {
					const cells = row.querySelectorAll('td');
					if (cells.length === 5) {
						const rowData = Array.from(cells).map(cell =>
							'"' + cell.textContent.trim().replace(/"/g, '""') + '"'
						).join(',');
						csvContent += rowData + '\n';
					}
				});

				// Create and download file
				const blob = new Blob([csvContent], { type: 'text/csv' });
				const url = window.URL.createObjectURL(blob);
				const link = document.createElement('a');
				link.href = url;
				link.download = 'activity_logs_' + new Date().toISOString().split('T')[0] + '.csv';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
				window.URL.revokeObjectURL(url);
			});
		});


