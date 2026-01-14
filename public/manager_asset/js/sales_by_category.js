// sales_by_category.js
// Populates category dropdown and filters sales report by date and category

document.addEventListener('DOMContentLoaded', function () {
    populateCategoryDropdown();
    setupDateFilter();

    const categoryFilter = document.getElementById('categoryFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    if (categoryFilter) categoryFilter.addEventListener('change', filterSalesTable);
    if (dateRangeFilter) dateRangeFilter.addEventListener('change', filterSalesTable);
    if (customStartDate) customStartDate.addEventListener('change', filterSalesTable);
    if (customEndDate) customEndDate.addEventListener('change', filterSalesTable);
});

function populateCategoryDropdown() {
    fetch('/manager/get-categories-list')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const categoryFilter = document.getElementById('categoryFilter');
            if (!categoryFilter) return;

            // Get current category from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const currentCategory = urlParams.get('category_id');

            categoryFilter.innerHTML = '<option value="">All Categories</option>';
            data.forEach(category => {
                const selected = currentCategory == category.id ? 'selected' : '';
                categoryFilter.innerHTML += `<option value="${category.id}" ${selected}>${category.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Failed to load categories list:', error);
        });
}

function setupDateFilter() {
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    if (dateRangeFilter) {
        dateRangeFilter.addEventListener('change', function () {
            const customRangeInputs = document.getElementById('customRangeInputs');
            if (customRangeInputs) {
                customRangeInputs.style.display = this.value === 'custom' ? 'block' : 'none';
            }
        });
    }
}

function toggleCustomRangeInputs() {
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customRangeInputs = document.getElementById('customRangeInputs');
    if (dateRangeFilter && customRangeInputs) {
        customRangeInputs.style.display = dateRangeFilter.value === 'custom' ? 'block' : 'none';
    }
}

function filterSalesTable() {
    const categoryFilter = document.getElementById('categoryFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    if (!dateRangeFilter) return;

    const categoryId = categoryFilter ? categoryFilter.value : '';
    const dateRange = dateRangeFilter.value;
    let startDate = '';
    let endDate = '';

    if (dateRange === 'custom' && customStartDate && customEndDate) {
        startDate = customStartDate.value;
        endDate = customEndDate.value;
    }

    // Reload page with query params for backend filtering
    const params = new URLSearchParams();
    if (categoryId) params.append('category_id', categoryId);
    if (dateRange) params.append('date_range', dateRange);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    window.location.search = params.toString();
}
