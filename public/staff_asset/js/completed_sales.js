// Global variables
let allSales = [];
let currentSaleId = null;

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const completedSalesTable = document.getElementById('completedSalesTable');
    const searchInput = document.getElementById('searchSales');
    const sellerFilter = document.getElementById('sellerFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const exportReportBtn = document.getElementById('exportReport');

    // Custom date inputs
    const customDateInputs = document.getElementById('customDateInputs');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    // Panel elements
    const detailsPanel = document.getElementById('detailsPanel');
    const detailsBackdrop = document.getElementById('detailsBackdrop');
    const closePanelBtn = document.getElementById('closePanelBtn');
    const printReceiptBtn = document.getElementById('printReceiptBtn');
    const exportSaleBtn = document.getElementById('exportSaleBtn');

    // Initialize - collect all sales from table
    function initializeSales() {
        const rows = completedSalesTable.querySelectorAll('tbody tr.sale-row');
        allSales = [];

        rows.forEach(row => {
            const cells = row.cells;
            if (cells.length > 1 && !row.querySelector('.empty-state')) {
                const sale = {
                    sn: cells[0]?.textContent.trim() || '',
                    receipt: cells[1]?.textContent.trim() || '',
                    date: cells[2]?.textContent.trim() || '',
                    customer: cells[3]?.textContent.trim() || '',
                    seller: cells[4]?.textContent.trim() || '',
                    items: cells[5]?.textContent.trim() || '',
                    total: cells[6]?.textContent.trim() || '',
                    status: cells[7]?.querySelector('.badge')?.textContent.trim() || '',
                    receiptNumber: row.dataset.receiptNumber || '',
                    discount: row.dataset.discount !== undefined ? row.dataset.discount : '-',
                    rowElement: row
                };
                allSales.push(sale);

                // Add click event to row
                row.addEventListener('click', function() {
                    showSaleDetails(sale);
                });
            }
        });

        console.log('Initialized sales:', allSales.length);
    }

    // Initialize on load
    initializeSales();

    // Search functionality
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedSeller = sellerFilter.value;
        const selectedStatus = statusFilter.value;
        const dateRange = dateRangeFilter.value;

        let filtered = allSales.filter(sale => {
            // Search filter
            const matchesSearch = !searchTerm ||
                sale.receipt.toLowerCase().includes(searchTerm) ||
                sale.customer.toLowerCase().includes(searchTerm) ||
                sale.seller.toLowerCase().includes(searchTerm) ||
                sale.total.toLowerCase().includes(searchTerm);

            // Seller filter
            const matchesSeller = !selectedSeller || sale.seller === selectedSeller;

            // Status filter
            const matchesStatus = !selectedStatus || sale.status === selectedStatus;

            // Date filter
            let matchesDate = true;
            if (dateRange && dateRange !== 'custom') {
                const saleDate = new Date(sale.date);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                switch(dateRange) {
                    case 'today':
                        matchesDate = saleDate >= today;
                        break;
                    case 'yesterday':
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);
                        matchesDate = saleDate >= yesterday && saleDate < today;
                        break;
                    case 'last7':
                        const last7 = new Date(today);
                        last7.setDate(last7.getDate() - 7);
                        matchesDate = saleDate >= last7;
                        break;
                    case 'last30':
                        const last30 = new Date(today);
                        last30.setDate(last30.getDate() - 30);
                        matchesDate = saleDate >= last30;
                        break;
                    case 'thisMonth':
                        const firstDayThisMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                        matchesDate = saleDate >= firstDayThisMonth;
                        break;
                    case 'lastMonth':
                        const firstDayLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        const lastDayLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                        matchesDate = saleDate >= firstDayLastMonth && saleDate <= lastDayLastMonth;
                        break;
                }
            } else if (dateRange === 'custom') {
                if (customStartDate.value && customEndDate.value) {
                    const saleDate = new Date(sale.date);
                    const start = new Date(customStartDate.value);
                    const end = new Date(customEndDate.value);
                    end.setHours(23, 59, 59);
                    matchesDate = saleDate >= start && saleDate <= end;
                }
            }

            return matchesSearch && matchesSeller && matchesStatus && matchesDate;
        });

        renderFilteredSales(filtered);
    }

    // Render filtered sales
    function renderFilteredSales(sales) {
        // Hide all rows first
        allSales.forEach(sale => {
            sale.rowElement.style.display = 'none';
        });

        // Show filtered rows
        if (sales.length === 0) {
            showEmptyState();
        } else {
            hideEmptyState();
            sales.forEach((sale, index) => {
                sale.rowElement.style.display = '';
                // Update serial number
                sale.rowElement.cells[0].textContent = index + 1;
            });
        }

        updatePaginationInfo(sales.length);
    }

    // Show empty state
    function showEmptyState() {
        const tbody = completedSalesTable.querySelector('tbody');

        // Check if empty state already exists
        let emptyRow = tbody.querySelector('.empty-state-row');
        if (!emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.className = 'empty-state-row';
            emptyRow.innerHTML = `
                <td colspan="8" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>No Sales Found</h5>
                        <p class="text-muted">No sales match your current filters.</p>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
        emptyRow.style.display = '';
    }

    // Hide empty state
    function hideEmptyState() {
        const emptyRow = completedSalesTable.querySelector('.empty-state-row');
        if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    // Update pagination info
    function updatePaginationInfo(count) {
        const infoText = document.querySelector('.text-muted.small');
        if (infoText) {
            if (count === 0) {
                infoText.innerHTML = 'Showing <strong>0</strong> of <strong>0</strong> entries';
            } else {
                infoText.innerHTML = `Showing <strong>1-${count}</strong> of <strong>${count}</strong> entries`;
            }
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

    // Event listeners
    searchInput.addEventListener('input', performSearch);
    sellerFilter.addEventListener('change', performSearch);
    statusFilter.addEventListener('change', performSearch);
    applyFiltersBtn.addEventListener('click', performSearch);

    clearFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        sellerFilter.value = '';
        statusFilter.value = '';
        dateRangeFilter.value = '';
        customStartDate.value = '';
        customEndDate.value = '';
        customDateInputs.classList.remove('active');

        // Show all sales
        renderFilteredSales(allSales);
    });

    // Export functionality
    exportReportBtn.addEventListener('click', function() {
        exportToCSV();
    });

    // Export to CSV
    function exportToCSV() {
        const visibleSales = allSales.filter(sale => sale.rowElement.style.display !== 'none');

        if (visibleSales.length === 0) {
            alert('No data to export');
            return;
        }

        let csvContent = 'S/N,Receipt No.,Date,Customer,Sold By,Items,Total,Status\n';

        visibleSales.forEach(sale => {
            const row = [
                sale.sn,
                sale.receipt,
                sale.date,
                sale.customer,
                sale.seller,
                sale.items,
                sale.total,
                sale.status
            ].map(value => `"${value}"`).join(',');
            csvContent += row + '\n';
        });

        // Create and download CSV file
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', `completed_sales_${new Date().toISOString().slice(0, 10)}.csv`);
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Make performSearch available globally for inline calls
    window.performSearch = performSearch;

    // Side panel functionality
    function showSaleDetails(sale) {
        currentSaleId = sale.receiptNumber;

        // Populate panel with sale details
        document.getElementById('detailReceiptNumber').textContent = sale.receipt;
        document.getElementById('detailCustomerName').textContent = sale.customer;
        document.getElementById('detailSoldBy').textContent = sale.seller;
        document.getElementById('detailSaleDate').textContent = sale.date;
        document.getElementById('detailTotalItems').textContent = sale.items;
        document.getElementById('detailTotalAmount').textContent = sale.total;
        document.getElementById('detailStatus').innerHTML = `<span class="badge badge-opacity-success">${sale.status}</span>`;

        // Show discount if available (requires discount info in sale object)
        if (typeof sale.discount !== 'undefined' && document.getElementById('detailDiscount')) {
            document.getElementById('detailDiscount').textContent = sale.discount !== null ? sale.discount : '-';
        } else if (document.getElementById('detailDiscount')) {
            document.getElementById('detailDiscount').textContent = '-';
        }

        // Load sale items via AJAX
        loadSaleItems(sale.receiptNumber);

        // Show panel
        detailsPanel.classList.add('active');
        detailsBackdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideSaleDetails() {
        detailsPanel.classList.remove('active');
        detailsBackdrop.classList.remove('active');
        document.body.style.overflow = '';
        currentSaleId = null;
    }

    // Load sale items from server
    function loadSaleItems(receiptNumber) {
        const itemsList = document.getElementById('saleItemsList');
        itemsList.innerHTML = '<div class="text-center py-3"><i class="bi bi-hourglass-split"></i> Loading items...</div>';

        // You can implement AJAX call to fetch items
        // For now, showing a placeholder
        fetch(`/staff/get_sale_items/${receiptNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items) {
                    displaySaleItems(data.items);
                } else {
                    itemsList.innerHTML = '<div class="text-muted text-center py-3">No items found</div>';
                }
            })
            .catch(error => {
                console.error('Error loading sale items:', error);
                itemsList.innerHTML = '<div class="text-danger text-center py-3"><i class="bi bi-exclamation-triangle"></i> Failed to load items</div>';
            });
    }

    function displaySaleItems(items) {
        const itemsList = document.getElementById('saleItemsList');

        if (!items || items.length === 0) {
            itemsList.innerHTML = '<div class="text-muted text-center py-3">No items in this sale</div>';
            return;
        }

        let html = '<div class="list-group">';
        items.forEach(item => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.item_name}</h6>
                            <small class="text-muted">Qty: ${item.quantity} × ₦${parseFloat(item.item_price).toLocaleString()}</small>
                            ${item.note ? `<br><small class="text-info"><i class="bi bi-chat-left-text"></i> ${item.note}</small>` : ''}
                        </div>
                        <div class="text-end">
                            <strong class="text-success">₦${parseFloat(item.total).toLocaleString()}</strong>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        itemsList.innerHTML = html;
    }

    // Panel close event listeners
    if (closePanelBtn) {
        closePanelBtn.addEventListener('click', hideSaleDetails);
    }

    if (detailsBackdrop) {
        detailsBackdrop.addEventListener('click', hideSaleDetails);
    }

    // Print receipt functionality
    if (printReceiptBtn) {
        printReceiptBtn.addEventListener('click', function() {
            if (currentSaleId) {
                window.open(`/staff/print_receipt/${currentSaleId}`, '_blank');
            }
        });
    }

    // Export single sale
    if (exportSaleBtn) {
        exportSaleBtn.addEventListener('click', function() {
            if (currentSaleId) {
                const sale = allSales.find(s => s.receiptNumber === currentSaleId);
                if (sale) {
                    exportSingleSale(sale);
                }
            }
        });
    }

    function exportSingleSale(sale) {
        let csvContent = 'Field,Value\n';
        csvContent += `"Receipt Number","${sale.receipt}"\n`;
        csvContent += `"Customer","${sale.customer}"\n`;
        csvContent += `"Sold By","${sale.seller}"\n`;
        csvContent += `"Date","${sale.date}"\n`;
        csvContent += `"Items","${sale.items}"\n`;
        csvContent += `"Total","${sale.total}"\n`;
        csvContent += `"Status","${sale.status}"\n`;

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);

        link.setAttribute('href', url);
        link.setAttribute('download', `sale_${sale.receipt}_${new Date().toISOString().slice(0, 10)}.csv`);
        link.style.visibility = 'hidden';

        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Make functions available globally
    window.showSaleDetails = showSaleDetails;
    window.hideSaleDetails = hideSaleDetails;
});

// Helper function for custom date overlay (global for inline calls)
function hideCustomDateOverlay() {
    const customDateInputs = document.getElementById('customDateInputs');
    if (customDateInputs) {
        customDateInputs.classList.remove('active');
    }
}
