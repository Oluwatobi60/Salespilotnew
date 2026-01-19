// inventory_valuation.js
// Handle search and category filter for inventory valuation

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const tableRows = document.querySelectorAll('#inventoryValuationTable tbody tr');

    // Real-time search and filter on input
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterTable();
        });
    }

    // Apply filter when category changes
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            filterTable();
        });
    }

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value.toLowerCase().trim();
        let visibleCount = 0;

        tableRows.forEach(function(row) {
            // Skip empty state rows
            if (row.querySelector('td[colspan]')) {
                row.style.display = 'none';
                return;
            }

            const cells = row.getElementsByTagName('td');
            if (cells.length < 3) return;

            const itemName = cells[1].textContent.toLowerCase();
            const categoryName = cells[2].textContent.toLowerCase();

            // Check if row matches search term (item name or category)
            const matchesSearch = !searchTerm ||
                                itemName.includes(searchTerm) ||
                                categoryName.includes(searchTerm);

            // Check if row matches selected category
            const matchesCategory = !selectedCategory ||
                                  categoryName === selectedCategory;

            // Show row only if it matches both filters
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide "no results" message
        updateNoResultsMessage(visibleCount, searchTerm, selectedCategory);
    }

    function updateNoResultsMessage(visibleCount, searchTerm, selectedCategory) {
        let noResultsRow = document.querySelector('#inventoryValuationTable tbody tr.no-results-row');

        if (visibleCount === 0) {
            // Hide all original rows
            tableRows.forEach(row => {
                if (!row.classList.contains('no-results-row')) {
                    row.style.display = 'none';
                }
            });

            // Create or show no results message
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3 mb-0" id="noResultsMessage">No items found</p>
                    </td>
                `;
                document.querySelector('#inventoryValuationTable tbody').appendChild(noResultsRow);
            }

            // Update message based on filters
            const messageEl = document.getElementById('noResultsMessage');
            if (messageEl) {
                if (selectedCategory) {
                    messageEl.textContent = `No items found in category "${categoryFilter.options[categoryFilter.selectedIndex].text}"`;
                } else if (searchTerm) {
                    messageEl.textContent = `No items found matching "${searchTerm}"`;
                } else {
                    messageEl.textContent = 'No inventory items available';
                }
            }

            noResultsRow.style.display = '';
        } else {
            // Hide no results message
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }
    }
});


