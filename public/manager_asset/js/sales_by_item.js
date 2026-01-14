// sales_by_item.js
// Populates category and item dropdowns and filters sales report by date, category, and item

document.addEventListener('DOMContentLoaded', function () {
    populateCategoryDropdown();
    populateItemDropdown();
    setupDateFilter();

    const categoryFilter = document.getElementById('categoryFilter');
    const itemFilter = document.getElementById('itemFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    if (categoryFilter) categoryFilter.addEventListener('change', filterSalesTable);
    if (itemFilter) itemFilter.addEventListener('change', filterSalesTable);
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

function populateItemDropdown() {
    fetch('/manager/get-items-list')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const itemFilter = document.getElementById('itemFilter');
            if (!itemFilter) return;

            // Get current item from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const currentItem = urlParams.get('item_id');
            const currentItemType = urlParams.get('item_type');

            itemFilter.innerHTML = '<option value="">All Items</option>';
            data.forEach(item => {
                const itemValue = `${item.type}_${item.id}`;
                const selected = (currentItem == item.id && currentItemType == item.type) ? 'selected' : '';
                itemFilter.innerHTML += `<option value="${itemValue}" ${selected}>${item.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Failed to load items list:', error);
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
    const itemFilter = document.getElementById('itemFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    if (!dateRangeFilter) return;

    const categoryId = categoryFilter ? categoryFilter.value : '';
    const itemValue = itemFilter ? itemFilter.value : '';
    const dateRange = dateRangeFilter.value;
    let startDate = '';
    let endDate = '';

    if (dateRange === 'custom' && customStartDate && customEndDate) {
        startDate = customStartDate.value;
        endDate = customEndDate.value;
    }

    // Parse item_id and item_type from itemValue (format: "standard_123" or "variant_456")
    let itemId = '';
    let itemType = '';
    if (itemValue) {
        const parts = itemValue.split('_');
        if (parts.length === 2) {
            itemType = parts[0];
            itemId = parts[1];
        }
    }

    // Reload page with query params for backend filtering
    const params = new URLSearchParams();
    if (categoryId) params.append('category_id', categoryId);
    if (itemId) params.append('item_id', itemId);
    if (itemType) params.append('item_type', itemType);
    if (dateRange) params.append('date_range', dateRange);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    window.location.search = params.toString();
}
