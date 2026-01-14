// inventory_valuation.js
// Handle search and category filter for inventory valuation

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const categoryCheckboxes = document.querySelectorAll('.categoryCheckbox');

    // Preserve search value from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchValue = urlParams.get('search');
    if (searchValue && searchInput) {
        searchInput.value = searchValue;
    }

    // Preserve category selections from URL
    const categoriesValue = urlParams.get('categories');
    if (categoriesValue && categoryCheckboxes.length > 0) {
        const selectedCategories = categoriesValue.split(',');
        categoryCheckboxes.forEach(checkbox => {
            if (selectedCategories.includes(checkbox.value)) {
                checkbox.checked = true;
            }
        });
        updateCategoryLabel();
    }

    // Real-time search on input
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                applyFilters();
            }, 500); // Debounce for 500ms
        });
    }
});

function updateCategoryFilter() {
    applyFilters();
}

function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryCheckboxes = document.querySelectorAll('.categoryCheckbox');

    const params = new URLSearchParams();

    // Add search parameter
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }

    // Add category parameters
    const selectedCategories = [];
    categoryCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedCategories.push(checkbox.value);
        }
    });

    if (selectedCategories.length > 0) {
        params.append('categories', selectedCategories.join(','));
    }

    // Reload page with filters
    window.location.search = params.toString();
}

function updateCategoryLabel() {
    const categoryCheckboxes = document.querySelectorAll('.categoryCheckbox');
    const selectedCategoryLabel = document.getElementById('selectedCategoryLabel');

    const selectedCategories = [];
    categoryCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedCategories.push(checkbox.value);
        }
    });

    if (selectedCategories.length === 0) {
        selectedCategoryLabel.textContent = 'All Categories';
    } else if (selectedCategories.length === 1) {
        selectedCategoryLabel.textContent = selectedCategories[0];
    } else {
        selectedCategoryLabel.textContent = `${selectedCategories.length} Categories`;
    }
}

// Update label when checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const categoryCheckboxes = document.querySelectorAll('.categoryCheckbox');
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCategoryLabel);
    });
});
