// Global variables
let currentSessionId = null;
let allCarts = [];
let allStaff = [];

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const savedCartsTable = document.getElementById('savedCartsTable');
    const searchInput = document.getElementById('searchCarts');
    const staffFilter = document.getElementById('staffFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const exportReportBtn = document.getElementById('exportReport');

    // Panel elements
    const detailsPanel = document.getElementById('detailsPanel');
    const detailsBackdrop = document.getElementById('detailsBackdrop');
    const closePanelBtn = document.getElementById('closePanelBtn');
    const restoreCartBtn = document.getElementById('restoreCartBtn');
    const deleteCartBtn = document.getElementById('deleteCartBtn');

    // Custom date inputs
    const customDateInputs = document.getElementById('customDateInputs');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    // Check if elements exist
    if (!savedCartsTable) {
        console.error('Saved carts table not found');
        return;
    }

    // Fetch all staff and populate dropdown
    function fetchStaff() {
        fetch('/staff/get_all_staff', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.staff) {
                allStaff = data.staff;
                populateStaffFilter(data.staff);
            }
        })
        .catch(error => {
            console.error('Error fetching staff:', error);
        });
    }

    // Populate staff filter dropdown
    function populateStaffFilter(staff) {
        if (!staffFilter) return;

        // Keep the "All Staff" option
        staffFilter.innerHTML = '<option value="">All Staff</option>';

        // Add staff options
        staff.forEach(function(member) {
            const option = document.createElement('option');
            option.value = member.name;
            option.textContent = member.name;
            staffFilter.appendChild(option);
        });

        console.log('Staff filter populated with', staff.length, 'staff members');
    }

    // Fetch staff on page load
    fetchStaff();

    // Initialize - collect and attach events to existing rows
    function initializeTableRows() {
        const rows = savedCartsTable.querySelectorAll('tbody tr.cart-row');
        allCarts = [];

        rows.forEach((row, index) => {
            const sessionId = row.dataset.sessionId;

            if (sessionId) {
                // Collect cart data from row
                const cells = row.cells;
                if (cells.length >= 7) {
                    const cart = {
                        session_id: sessionId,
                        cart_name: cells[1]?.textContent.trim() || '',
                        user_name: cells[2]?.textContent.trim() || '',
                        items_count: cells[3]?.textContent.trim() || '',
                        created_at: cells[4]?.textContent.trim() || '',
                        total: cells[5]?.textContent.trim() || '',
                        rowElement: row,
                        rowIndex: index
                    };
                    allCarts.push(cart);
                }

                // Add click event to row
                row.addEventListener('click', function(e) {
                    if (!e.target.closest('.delete-cart')) {
                        showCartDetails(sessionId);
                    }
                });

                // Add click event to delete button
                const deleteBtn = row.querySelector('.delete-cart');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        deleteCart(sessionId);
                    });
                }
            }
        });

        console.log('Initialized saved carts:', allCarts.length);
    }

    // Show cart details in side panel
    function showCartDetails(sessionId) {
        currentSessionId = sessionId;

        fetch(`/staff/load_saved_cart/${sessionId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cart = data.cart;

                // Populate panel with cart details
                document.getElementById('detailCartId').textContent = cart.customer_name;
                document.getElementById('detailCartStatus').textContent = cart.cart_name;
                document.getElementById('detailCreatedBy').textContent = cart.user_name || 'Unknown User';
                document.getElementById('detailSavedDate').textContent = new Date(cart.created_at).toLocaleString();
                document.getElementById('detailTotalItems').textContent = cart.items.length + ' items';
                document.getElementById('detailCartTotal').textContent = '₦' + parseFloat(cart.total).toLocaleString();

                // Render cart items
                const cartItemsList = document.getElementById('cartItemsList');
                cartItemsList.innerHTML = '';

                cart.items.forEach(item => {
                    const itemCard = document.createElement('div');
                    itemCard.className = 'item-card';
                    itemCard.innerHTML = `
                        <div class="item-name">${item.item_name}</div>
                        <div class="item-details">
                            <span class="item-quantity">Qty: ${item.quantity}</span>
                            <span class="item-price">₦${parseFloat(item.item_price).toLocaleString()}</span>
                        </div>
                        ${item.note ? '<div class="text-muted small mt-2"><i class="bi bi-sticky"></i> ' + item.note + '</div>' : ''}
                    `;
                    cartItemsList.appendChild(itemCard);
                });

                // Show panel
                detailsPanel.classList.add('show');
                detailsBackdrop.classList.add('show');
            }
        })
        .catch(error => {
            console.error('Error loading cart details:', error);
            alert('Failed to load cart details');
        });
    }

    // Close panel
    function closePanel() {
        detailsPanel.classList.remove('show');
        detailsBackdrop.classList.remove('show');
        currentSessionId = null;
    }

    // Restore cart (redirect to sell page)
    function restoreCart() {
        if (!currentSessionId) {
            console.error('No session ID selected');
            return;
        }

        if (restoreCartBtn) restoreCartBtn.disabled = true;
        console.log('Restoring cart with session ID:', currentSessionId);

        // Fetch the cart data first
        fetch(`/staff/load_saved_cart/${currentSessionId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Received cart data:', data);

            if (data.success) {
                console.log('Storing cart data in sessionStorage');
                console.log('Cart items:', data.cart.items);

                // Store cart data in sessionStorage
                sessionStorage.setItem('restoreCartData', JSON.stringify(data.cart));
                sessionStorage.setItem('restoreCartSessionId', currentSessionId);

                // Remove the cart row from the table immediately
                const cartToRemove = allCarts.find(cart => cart.session_id === currentSessionId);
                if (cartToRemove && cartToRemove.rowElement) {
                    cartToRemove.rowElement.remove();
                    allCarts = allCarts.filter(cart => cart.session_id !== currentSessionId);
                }

                // Delete the cart from the database (status saved)
                fetch(`/staff/delete_saved_cart/${currentSessionId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(deleteData => {
                    if (deleteData.success) {
                        showSuccessMessage('Cart restored and removed from saved carts!');
                    } else {
                        alert('Cart restored, but failed to delete from database: ' + (deleteData.message || 'Unknown error'));
                    }
                    // Redirect to sell product page
                    window.location.href = '/staff/sell_product';
                })
                .catch(deleteError => {
                    alert('Cart restored, but error deleting from database.');
                    window.location.href = '/staff/sell_product';
                });
            } else {
                if (restoreCartBtn) restoreCartBtn.disabled = false;
                console.error('Failed to load cart data:', data.message);
                alert('Failed to load cart data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            if (restoreCartBtn) restoreCartBtn.disabled = false;
            console.error('Error loading cart for restore:', error);
            alert('An error occurred while loading the cart.');
        });
    }

    // Delete cart with page reload
    function deleteCart(sessionId) {
        if (!confirm('Are you sure you want to delete this saved cart? This action cannot be undone.')) {
            return;
        }

        fetch(`/staff/delete_saved_cart/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove row from table
                const cartToDelete = allCarts.find(cart => cart.session_id === sessionId);
                if (cartToDelete && cartToDelete.rowElement) {
                    cartToDelete.rowElement.remove();
                    allCarts = allCarts.filter(cart => cart.session_id !== sessionId);
                }

                closePanel();

                // Show success message
                showSuccessMessage('Cart deleted successfully!');

                // Reload page after short delay to update pagination
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Failed to delete cart: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error deleting cart:', error);
            alert('An error occurred while deleting the cart.');
        });
    }

    // Show success message
    function showSuccessMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    // Search and filter functionality
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedStaff = staffFilter.value;
        const dateRange = dateRangeFilter.value;

        let visibleCount = 0;

        allCarts.forEach(cart => {
            // Search filter
            const matchesSearch = !searchTerm ||
                cart.cart_name.toLowerCase().includes(searchTerm) ||
                cart.user_name.toLowerCase().includes(searchTerm) ||
                cart.session_id.toLowerCase().includes(searchTerm);

            // Staff filter
            const matchesStaff = !selectedStaff || cart.user_name === selectedStaff;

            // Date filter
            let matchesDate = true;
            if (dateRange && dateRange !== 'custom') {
                const cartDate = new Date(cart.created_at);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                switch(dateRange) {
                    case 'today':
                        matchesDate = cartDate >= today;
                        break;
                    case 'yesterday':
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);
                        matchesDate = cartDate >= yesterday && cartDate < today;
                        break;
                    case 'last7':
                        const last7 = new Date(today);
                        last7.setDate(last7.getDate() - 7);
                        matchesDate = cartDate >= last7;
                        break;
                    case 'last30':
                        const last30 = new Date(today);
                        last30.setDate(last30.getDate() - 30);
                        matchesDate = cartDate >= last30;
                        break;
                }
            } else if (dateRange === 'custom') {
                if (customStartDate.value && customEndDate.value) {
                    const cartDate = new Date(cart.created_at);
                    const start = new Date(customStartDate.value);
                    const end = new Date(customEndDate.value);
                    end.setHours(23, 59, 59);
                    matchesDate = cartDate >= start && cartDate <= end;
                }
            }

            // Show/hide row based on filters
            if (matchesSearch && matchesStaff && matchesDate) {
                cart.rowElement.style.display = '';
                visibleCount++;
                // Update serial number
                cart.rowElement.cells[0].textContent = visibleCount;
            } else {
                cart.rowElement.style.display = 'none';
            }
        });

        // Show empty state if no results
        if (visibleCount === 0) {
            showFilteredEmptyState();
        } else {
            hideFilteredEmptyState();
        }
    }

    // Show filtered empty state
    function showFilteredEmptyState() {
        const tbody = savedCartsTable.querySelector('tbody');
        let emptyRow = tbody.querySelector('.filtered-empty-state');

        if (!emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.className = 'filtered-empty-state';
            emptyRow.innerHTML = `
                <td colspan="7" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <h5>No Results Found</h5>
                        <p class="text-muted">No carts match your search criteria.</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
        emptyRow.style.display = '';
    }

    // Hide filtered empty state
    function hideFilteredEmptyState() {
        const emptyRow = savedCartsTable.querySelector('.filtered-empty-state');
        if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    // Show/hide custom date inputs
    dateRangeFilter.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateInputs.classList.add('active');
        } else {
            customDateInputs.classList.remove('active');
            performSearch();
        }
    });

    // Empty state
    function showEmptyState() {
        const tbody = savedCartsTable.querySelector('tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-cart-x"></i>
                        <h5>No Saved Carts</h5>
                        <p class="text-muted">You don't have any saved carts yet.</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Error state
    function showErrorState() {
        const tbody = savedCartsTable.querySelector('tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-exclamation-triangle text-danger"></i>
                        <h5>Error Loading Carts</h5>
                        <p class="text-muted">Failed to load saved carts. Please refresh the page.</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Event listeners
    if (closePanelBtn) closePanelBtn.addEventListener('click', closePanel);
    if (detailsBackdrop) detailsBackdrop.addEventListener('click', closePanel);
    if (restoreCartBtn) restoreCartBtn.addEventListener('click', restoreCart);
    if (deleteCartBtn) {
        deleteCartBtn.addEventListener('click', function() {
            if (currentSessionId) {
                deleteCart(currentSessionId);
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', performSearch);
    if (staffFilter) staffFilter.addEventListener('change', performSearch);
    if (applyFiltersBtn) applyFiltersBtn.addEventListener('click', performSearch);
    if (clearFiltersBtn) clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        staffFilter.value = '';
        dateRangeFilter.value = '';
        customStartDate.value = '';
        customEndDate.value = '';
        customDateInputs.classList.remove('active');

        // Show all carts
        allCarts.forEach((cart, index) => {
            cart.rowElement.style.display = '';
            cart.rowElement.cells[0].textContent = index + 1;
        });

        hideFilteredEmptyState();
    });

    if (exportReportBtn) exportReportBtn.addEventListener('click', function() {
        exportToCSV();
    });

    // Export visible carts to CSV
    function exportToCSV() {
        const visibleCarts = allCarts.filter(cart => cart.rowElement.style.display !== 'none');

        if (visibleCarts.length === 0) {
            alert('No data to export');
            return;
        }

        let csvContent = 'S/N,Session ID,Created By,Items,Saved Date,Cart Total\n';

        visibleCarts.forEach((cart, index) => {
            const row = [
                index + 1,
                cart.session_id,
                cart.user_name,
                cart.items_count,
                cart.created_at,
                cart.total
            ].map(value => `"${value}"`).join(',');
            csvContent += row + '\n';
        });

        // Create and download CSV file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', `saved_carts_${new Date().toISOString().slice(0, 10)}.csv`);
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showSuccessMessage('Export completed successfully!');
    }

    // Initialize existing table rows
    initializeTableRows();

    // Make performSearch available globally for inline calls
    window.performSearch = performSearch;
});

// Helper function for custom date overlay (global for inline calls)
function hideCustomDateOverlay() {
    document.getElementById('customDateInputs').classList.remove('active');
}

// Test function (global for inline calls)
function testCartPanel() {
    const detailsPanel = document.getElementById('detailsPanel');
    const detailsBackdrop = document.getElementById('detailsBackdrop');

    detailsPanel.classList.add('show');
    detailsBackdrop.classList.add('show');


}
