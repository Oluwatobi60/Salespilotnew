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
			// Get DOM elements
			const searchInput = document.getElementById('searchActivities');
			const accessTypeFilter = document.getElementById('accessTypeFilter');
			const staffFilter = document.getElementById('staffFilter');
			const dateFilter = document.getElementById('dateFilter');
			const customDateInputs = document.getElementById('customDateInputs');
			const startDateInput = document.getElementById('startDate');
			const endDateInput = document.getElementById('endDate');
			const applyFiltersBtn = document.getElementById('applyFilters');
			const clearFiltersBtn = document.getElementById('clearFilters');
			const table = document.getElementById('table');
			const tableBody = table.querySelector('tbody');
			const tableRows = Array.from(tableBody.querySelectorAll('tr'));

			// Set default dates for custom range
			const today = new Date();
			const todayStr = today.toISOString().split('T')[0];
			const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
			const weekAgoStr = weekAgo.toISOString().split('T')[0];

			startDateInput.value = weekAgoStr;
			endDateInput.value = todayStr;

			// Show/hide custom date overlay based on date filter selection
			function showCustomDateOverlay() {
				const customDateInputs = document.getElementById('customDateInputs');
				customDateInputs.classList.add('show');

				// Focus on start date for better UX
				setTimeout(() => {
					startDateInput.focus();
				}, 200);
			}

			function hideCustomDateOverlay() {
				const customDateInputs = document.getElementById('customDateInputs');
				customDateInputs.classList.remove('show');
			}

			// Date filter change handler
			dateFilter.addEventListener('change', function() {
				if (this.value === 'custom') {
					showCustomDateOverlay();
				} else {
					hideCustomDateOverlay();
				}
				// Trigger search when date filter changes
				performSearch();
			});

			// ESC key handler for closing custom date overlay
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' || e.keyCode === 27) {
					if (customDateInputs.classList.contains('show')) {
						// Reset date filter to empty and hide overlay
						dateFilter.value = '';
						hideCustomDateOverlay();
						performSearch();
						e.preventDefault();
					}
				}
			});

			// Click outside overlay to close
			document.addEventListener('click', function(e) {
				const isClickInsideFilter = e.target.closest('.date-filter-wrapper');
				if (!isClickInsideFilter && customDateInputs.classList.contains('show')) {
					dateFilter.value = '';
					hideCustomDateOverlay();
					performSearch();
				}
			});

			// Store original table data
			const originalData = tableRows.map(row => {
				const cells = row.querySelectorAll('td');
				return {
					element: row,
					sn: cells[0]?.textContent.trim() || '',
					date: cells[1]?.textContent.trim() || '',
					activity: cells[2]?.textContent.trim() || '',
					staffName: cells[3]?.textContent.trim() || '',
					accessType: cells[4]?.textContent.trim() || ''
				};
			});

			// Search functionality
			function performSearch() {
				const searchTerm = searchInput.value.toLowerCase();
				const accessType = accessTypeFilter.value;
				const staff = staffFilter.value;
				const dateRange = dateFilter.value;

				const filteredData = originalData.filter(item => {
					// Text search across activity and staff name
					const matchesSearch = searchTerm === '' ||
						item.activity.toLowerCase().includes(searchTerm) ||
						item.staffName.toLowerCase().includes(searchTerm);

					// Access type filter
					const matchesAccessType = accessType === '' || item.accessType === accessType;

					// Staff filter
					const matchesStaff = staff === '' || item.staffName === staff;

					// Date filter with enhanced logic
					let matchesDate = true;
					if (dateRange && dateRange !== '') {
						const itemDate = new Date(item.date.split(' ')[0]); // Extract date part only
						const currentDate = new Date();

						switch (dateRange) {
							case 'today':
								const today = new Date();
								today.setHours(0, 0, 0, 0);
								const tomorrow = new Date(today);
								tomorrow.setDate(tomorrow.getDate() + 1);
								matchesDate = itemDate >= today && itemDate < tomorrow;
								break;

							case 'yesterday':
								const yesterday = new Date();
								yesterday.setDate(yesterday.getDate() - 1);
								yesterday.setHours(0, 0, 0, 0);
								const todayStart = new Date(yesterday);
								todayStart.setDate(todayStart.getDate() + 1);
								matchesDate = itemDate >= yesterday && itemDate < todayStart;
								break;

							case 'last7days':
								const sevenDaysAgo = new Date();
								sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
								sevenDaysAgo.setHours(0, 0, 0, 0);
								matchesDate = itemDate >= sevenDaysAgo;
								break;

							case 'last30days':
								const thirtyDaysAgo = new Date();
								thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
								thirtyDaysAgo.setHours(0, 0, 0, 0);
								matchesDate = itemDate >= thirtyDaysAgo;
								break;

							case 'custom':
								const startDate = new Date(startDateInput.value);
								const endDate = new Date(endDateInput.value);

								if (startDateInput.value && endDateInput.value) {
									startDate.setHours(0, 0, 0, 0);
									endDate.setHours(23, 59, 59, 999);
									matchesDate = itemDate >= startDate && itemDate <= endDate;
								} else if (startDateInput.value) {
									startDate.setHours(0, 0, 0, 0);
									matchesDate = itemDate >= startDate;
								} else if (endDateInput.value) {
									endDate.setHours(23, 59, 59, 999);
									matchesDate = itemDate <= endDate;
								}
								break;

							default:
								matchesDate = true;
						}
					}

					return matchesSearch && matchesAccessType && matchesStaff && matchesDate;
				});

				// Clear table body
				tableBody.innerHTML = '';

				// Add filtered rows
				if (filteredData.length === 0) {
					const noResultsRow = document.createElement('tr');
					noResultsRow.innerHTML = `
						<td colspan="5" class="text-center text-muted py-4">
							<i class="bi bi-search"></i><br>
							No activities found matching your criteria
						</td>
					`;
					tableBody.appendChild(noResultsRow);
				} else {
					filteredData.forEach((item, index) => {
						// Update serial number for filtered results
						const cells = item.element.querySelectorAll('td');
						cells[0].textContent = index + 1;
						tableBody.appendChild(item.element);
					});
				}

				// Update results count
				updateResultsCount(filteredData.length);
			}

			// Update results count
			function updateResultsCount(count) {
				let countElement = document.getElementById('resultsCount');
				if (!countElement) {
					countElement = document.createElement('small');
					countElement.id = 'resultsCount';
					countElement.className = 'text-muted';
					const cardDescription = document.querySelector('.card-description');
					cardDescription.appendChild(document.createElement('br'));
					cardDescription.appendChild(countElement);
				}

				if (count === originalData.length) {
					countElement.textContent = `Showing all ${count} activities`;
				} else {
					countElement.textContent = `Showing ${count} of ${originalData.length} activities`;
				}
			}

			// Clear all filters
			function clearAllFilters() {
				searchInput.value = '';
				accessTypeFilter.value = '';
				staffFilter.value = '';
				dateFilter.value = '';
				startDateInput.value = weekAgoStr;
				endDateInput.value = todayStr;

				// Hide custom date overlay properly
				hideCustomDateOverlay();

				// Restore original table
				tableBody.innerHTML = '';
				originalData.forEach(item => {
					tableBody.appendChild(item.element);
				});

				updateResultsCount(originalData.length);
			}

			// Event listeners
			searchInput.addEventListener('input', performSearch);
			accessTypeFilter.addEventListener('change', performSearch);
			staffFilter.addEventListener('change', performSearch);
			// dateFilter change listener is already added above
			startDateInput.addEventListener('change', performSearch);
			endDateInput.addEventListener('change', performSearch);
			applyFiltersBtn.addEventListener('click', performSearch);
			clearFiltersBtn.addEventListener('click', clearAllFilters);

			// Enter key support for search
			searchInput.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					performSearch();
				}
			});

			// Initialize results count
			updateResultsCount(originalData.length);

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


