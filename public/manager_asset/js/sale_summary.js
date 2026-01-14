// Sales Summary Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customDateInputs = document.getElementById('customDateInputs');
    const searchInput = document.getElementById('searchSummary');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const exportReportBtn = document.getElementById('exportReport');

    // Date range filter
    if (dateRangeFilter) {
        dateRangeFilter.addEventListener('change', function() {
            if (this.value === 'custom') {
                showCustomDateOverlay();
            } else {
                hideCustomDateOverlay();
                performSearch();
            }
        });
    }

    // Search functionality - real-time search
    if (searchInput) {
        searchInput.addEventListener('input', performSearch);
    }

    // Apply filters
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', performSearch);
    }

    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            if (dateRangeFilter) dateRangeFilter.value = '';
            const customStartDate = document.getElementById('customStartDate');
            const customEndDate = document.getElementById('customEndDate');
            if (customStartDate) customStartDate.value = '';
            if (customEndDate) customEndDate.value = '';
            hideCustomDateOverlay();
            performSearch();
        });
    }

    // Export to CSV
    if (exportReportBtn) {
        exportReportBtn.addEventListener('click', function() {
            exportToCSV();
        });
    }

    // Close overlay when clicking outside
    document.addEventListener('click', function(e) {
        if (customDateInputs && dateRangeFilter) {
            if (!customDateInputs.contains(e.target) && !dateRangeFilter.contains(e.target)) {
                if (customDateInputs.classList.contains('show')) {
                    hideCustomDateOverlay();
                }
            }
        }
    });

    // Close overlay with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCustomDateOverlay();
        }
    });

    // Initialize charts
    initializeCharts();
});

// Show custom date overlay
function showCustomDateOverlay() {
    const overlay = document.getElementById('customDateInputs');
    if (overlay) {
        overlay.classList.add('active', 'show');
    }
}

// Hide custom date overlay
function hideCustomDateOverlay() {
    const overlay = document.getElementById('customDateInputs');
    if (overlay) {
        overlay.classList.remove('active', 'show');
    }
}

// Get date range based on filter selection
function getDateRange(filterValue) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    let startDate, endDate;

    switch(filterValue) {
        case 'today':
            startDate = new Date(today);
            endDate = new Date(today);
            break;
        case 'yesterday':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 1);
            endDate = new Date(startDate);
            break;
        case 'last7':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 6);
            endDate = new Date(today);
            break;
        case 'last30':
            startDate = new Date(today);
            startDate.setDate(startDate.getDate() - 29);
            endDate = new Date(today);
            break;
        case 'thisMonth':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            endDate = new Date(today);
            break;
        case 'lastMonth':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
        default:
            return null;
    }

    return { startDate, endDate };
}

// Parse date from table cell (format: "Mon dd, yyyy")
function parseTableDate(dateStr) {
    const date = new Date(dateStr);
    date.setHours(0, 0, 0, 0);
    return date;
}

// Perform search/filter
function performSearch() {
    const searchTerm = document.getElementById('searchSummary')?.value.toLowerCase() || '';
    const dateRangeValue = document.getElementById('dateRangeFilter')?.value || '';
    const customStartDate = document.getElementById('customStartDate')?.value;
    const customEndDate = document.getElementById('customEndDate')?.value;

    const tableRows = document.querySelectorAll('#salesSummaryTable tbody tr.summary-row');
    let visibleCount = 0;

    // Get date range
    let dateRange = null;
    if (dateRangeValue === 'custom' && customStartDate && customEndDate) {
        dateRange = {
            startDate: new Date(customStartDate),
            endDate: new Date(customEndDate)
        };
    } else if (dateRangeValue && dateRangeValue !== 'custom') {
        dateRange = getDateRange(dateRangeValue);
    }

    tableRows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        const dateCell = row.cells[1]?.textContent.trim(); // Date column

        // Search filter
        const matchesSearch = !searchTerm || rowText.includes(searchTerm);

        // Date filter
        let matchesDate = true;
        if (dateRange && dateCell) {
            const rowDate = parseTableDate(dateCell);
            matchesDate = rowDate >= dateRange.startDate && rowDate <= dateRange.endDate;
        }

        // Show/hide row
        if (matchesSearch && matchesDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show/hide empty state
    const emptyRow = document.querySelector('#salesSummaryTable tbody tr:not(.summary-row)');
    if (emptyRow) {
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
    }
}

// Export table to CSV
function exportToCSV() {
    const table = document.getElementById('salesSummaryTable');
    const rows = table.querySelectorAll('tr');
    let csv = [];

    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('th, td');

        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/"/g, '""');

            // Clean up badge text
            if (cols[j].querySelector('.badge')) {
                text = cols[j].querySelector('.badge').innerText;
            }

            if (text.indexOf(',') !== -1 || text.indexOf('"') !== -1) {
                text = '"' + text + '"';
            }
            row.push(text);
        }
        csv.push(row.join(','));
    }

    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', 'sales_summary_' + new Date().toISOString().slice(0, 10) + '.csv');
    link.style.visibility = 'hidden';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Initialize Charts
function initializeCharts() {

    // Use data passed from PHP (salesData is defined in blade template)
    console.log('salesData:', salesData);
    if (typeof salesData === 'undefined' || !salesData || salesData.length === 0) {
        console.log('No sales data available for charts');
        return;
    }

    // Prepare chart data (reverse to show oldest to newest)
    const chartData = salesData.slice().reverse();
    const dates = chartData.map(d => {
        const date = new Date(d.sale_date);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    });
    const grossSales = chartData.map(d => parseFloat(d.gross_sales));
    const costOfItems = chartData.map(d => parseFloat(d.cost_of_items));
    const grossProfit = chartData.map(d => parseFloat(d.gross_profit));
    const transactions = chartData.map(d => parseInt(d.transaction_count));
    console.log('Gross Sales:', grossSales);
    console.log('Cost of Items:', costOfItems);
    console.log('Gross Profit:', grossProfit);
    console.log('Transactions:', transactions);

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(255, 255, 255, 0.2)',
                borderWidth: 1
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Date',
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                }
            },
            y: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        }
    };

    // Gross Sales Chart
    if (document.getElementById('grossSalesLineChart')) {
        new Chart(document.getElementById('grossSalesLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Gross Sales',
                    data: grossSales,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    ...chartOptions.scales,
                    y: {
                        ...chartOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Gross Sales (₦)',
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }

    // Cost of Items Chart
    if (document.getElementById('costItemsLineChart')) {
        new Chart(document.getElementById('costItemsLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Cost of Items',
                    data: costOfItems,
                    borderColor: '#f6c23e',
                    backgroundColor: 'rgba(246, 194, 62, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    ...chartOptions.scales,
                    y: {
                        ...chartOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Cost (₦)',
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }

    // Transactions Chart
    if (document.getElementById('transactionsLineChart')) {
        new Chart(document.getElementById('transactionsLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Transactions',
                    data: transactions,
                    borderColor: '#36b9cc',
                    backgroundColor: 'rgba(54, 185, 204, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    ...chartOptions.scales,
                    y: {
                        ...chartOptions.scales.y,
                        ticks: {
                            callback: function(value) {
                                return value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Transactions',
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }

    // Gross Profit Chart
    if (document.getElementById('grossProfitLineChart')) {
        new Chart(document.getElementById('grossProfitLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Gross Profit',
                    data: grossProfit,
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                ...chartOptions,
                scales: {
                    ...chartOptions.scales,
                    y: {
                        ...chartOptions.scales.y,
                        title: {
                            display: true,
                            text: 'Profit (₦)',
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }
}

// Make functions available globally
window.performSearch = performSearch;
window.hideCustomDateOverlay = hideCustomDateOverlay;
