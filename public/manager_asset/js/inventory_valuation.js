// inventory_valuation.js
// Handle search and category filter for inventory valuation

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');

    // Preserve search value from URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchValue = urlParams.get('search');
    if (searchValue && searchInput) {
        searchInput.value = searchValue;
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

    // Apply filter when category changes
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
});

function applyFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');

    const params = new URLSearchParams();

    // Add search parameter
    if (searchInput && searchInput.value.trim()) {
        params.append('search', searchInput.value.trim());
    }

    // Add category parameter
    if (categoryFilter && categoryFilter.value) {
        params.append('category', categoryFilter.value);
    }

    // Reload page with filters
    window.location.search = params.toString();
}


