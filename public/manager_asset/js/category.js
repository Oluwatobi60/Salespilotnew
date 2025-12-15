document.addEventListener('DOMContentLoaded', function() {
      // Side panel controls for adding category
      var openAddCategoryBtn = document.getElementById('addCategoryBtn');
      var addCategoryPanel = document.getElementById('addCategoryPanel');
      var closeSidePanel = document.getElementById('closeSidePanel');
      var cancelAddCategory = document.getElementById('cancelAddCategory');
      var sidePanelOverlay = document.getElementById('sidePanelOverlay');

      // Side panel controls for editing category
      var editCategoryPanel = document.getElementById('editCategoryPanel');
      var closeEditSidePanel = document.getElementById('closeEditSidePanel');
      var cancelEditCategory = document.getElementById('cancelEditCategory');

      // Open add category side panel
      if (openAddCategoryBtn && addCategoryPanel && sidePanelOverlay) {
        openAddCategoryBtn.addEventListener('click', function() {
          addCategoryPanel.classList.add('active');
          sidePanelOverlay.classList.add('active');
          document.body.style.overflow = 'hidden';
        });
      }

      // Close add category side panel function
      function closeAddPanel() {
        if (addCategoryPanel) {
          addCategoryPanel.classList.remove('active');
          document.body.style.overflow = '';
        }
        if (sidePanelOverlay) {
          sidePanelOverlay.classList.remove('active');
        }
      }

      // Close edit category side panel function
      function closeEditPanel() {
        if (editCategoryPanel) {
          editCategoryPanel.classList.remove('active');
          document.body.style.overflow = '';
        }
        if (sidePanelOverlay) {
          sidePanelOverlay.classList.remove('active');
        }
      }

      // Close add panel on close button click
      if (closeSidePanel) {
        closeSidePanel.addEventListener('click', closeAddPanel);
      }

      // Close add panel on cancel button click
      if (cancelAddCategory) {
        cancelAddCategory.addEventListener('click', closeAddPanel);
      }

      // Close edit panel on close button click
      if (closeEditSidePanel) {
        closeEditSidePanel.addEventListener('click', closeEditPanel);
      }

      // Close edit panel on cancel button click
      if (cancelEditCategory) {
        cancelEditCategory.addEventListener('click', closeEditPanel);
      }

      // Close panels on overlay click
      if (sidePanelOverlay) {
        sidePanelOverlay.addEventListener('click', function() {
          closeAddPanel();
          closeEditPanel();
        });
      }

      // Close panels on Escape key
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          if (addCategoryPanel && addCategoryPanel.classList.contains('active')) {
            closeAddPanel();
          }
          if (editCategoryPanel && editCategoryPanel.classList.contains('active')) {
            closeEditPanel();
          }
        }
      });

      // Search and Filter functionality
      const searchInput = document.getElementById('searchCategories');
      const table = document.getElementById('categoriesTable');
      const tableBody = table.querySelector('tbody');

      // Real-time search functionality
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = searchInput.value.toLowerCase();
          const rows = Array.from(tableBody.querySelectorAll('tr'));

          rows.forEach(row => {
            const categoryName = row.cells[1].textContent.toLowerCase();
            const itemsCount = row.cells[2].textContent.toLowerCase();

            const matchesSearch = categoryName.includes(searchTerm) ||
                                 itemsCount.includes(searchTerm);

            if (matchesSearch) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }

      // Filter functionality
      const itemsFilter = document.getElementById('itemsFilter');
      const applyFiltersBtn = document.getElementById('applyFilters');
      const clearFiltersBtn = document.getElementById('clearFilters');
      const exportBtn = document.getElementById('exportCategories');

      function performFilter() {
        const searchTerm = searchInput.value.toLowerCase();
        const itemsRange = itemsFilter.value;
        const rows = Array.from(tableBody.querySelectorAll('tr'));

        rows.forEach(row => {
          const categoryName = row.cells[1].textContent.toLowerCase();
          const itemsCount = parseInt(row.cells[2].textContent);

          let matchesSearch = categoryName.includes(searchTerm) ||
                             row.cells[2].textContent.toLowerCase().includes(searchTerm);

          let matchesItems = true;
          if (itemsRange) {
            if (itemsRange === '0-10') matchesItems = itemsCount >= 0 && itemsCount <= 10;
            else if (itemsRange === '11-25') matchesItems = itemsCount >= 11 && itemsCount <= 25;
            else if (itemsRange === '26-50') matchesItems = itemsCount >= 26;
          }

          if (matchesSearch && matchesItems) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      }

      // Apply filters button
      if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', performFilter);
      }

      // Clear filters button
      if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
          searchInput.value = '';
          itemsFilter.value = '';
          marginFilter.value = '';
          performFilter();
        });
      }

      // Export button
      if (exportBtn) {
        exportBtn.addEventListener('click', function() {
          // Export table to CSV
          function tableToCSV(tableId) {
            var table = document.getElementById(tableId);
            var rows = table.querySelectorAll('tr');
            var csv = [];
            for (var i = 0; i < rows.length; i++) {
              var row = [], cols = rows[i].querySelectorAll('th, td');
              for (var j = 0; j < cols.length - 1; j++) { // Exclude action column
                var text = cols[j].innerText.replace(/"/g, '""');
                if (text.indexOf(',') !== -1 || text.indexOf('"') !== -1) {
                  text = '"' + text + '"';
                }
                row.push(text);
              }
              csv.push(row.join(','));
            }
            return csv.join('\n');
          }

          function downloadCSV(csv, filename) {
            var csvFile = new Blob([csv], { type: 'text/csv' });
            var downloadLink = document.createElement('a');
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
          }

          var csv = tableToCSV('categoriesTable');
          downloadCSV(csv, 'categories_report.csv');
        });
      }

      // Action buttons functionality
      const addCategoryForm = document.getElementById('addCategoryForm');
      const editCategoryForm = document.getElementById('editCategoryForm');
      let editingRow = null;

      // Edit button functionality
      document.getElementById('categoriesTable').addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');

        if (editBtn) {
          const categoryId = editBtn.getAttribute('data-id');
          const category_name = editBtn.getAttribute('data-name');

          // Fill edit panel form with data
          document.getElementById('editCategoryId').value = categoryId;
          document.getElementById('editCategoryName').value = category_name;
          // Set form action dynamically
          const form = document.getElementById('editCategoryForm');
          form.action = '/manager/update_category/' + categoryId;

          // Show edit side panel
          if (editCategoryPanel && sidePanelOverlay) {
            editCategoryPanel.classList.add('active');
            sidePanelOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
          }
        }

        if (deleteBtn) {
          const categoryName = deleteBtn.getAttribute('data-name');
          if (confirm(`Are you sure you want to delete the category "${categoryName}"?`)) {
            row.remove();
            updateSerialNumbers();
          }
        }
      });

      // Add Category Form Submit
      addCategoryForm.addEventListener('submit', function(e) {
        const categoryName = document.getElementById('addCategoryName').value.trim();

        if (!categoryName) {
          e.preventDefault();
          alert('Please enter a category name.');
          return;
        }

        // Form will submit normally to the server
      });

      // Edit Category Form Submit
      editCategoryForm.addEventListener('submit', function(e) {
        const categoryName = document.getElementById('editCategoryName').value.trim();

        if (!categoryName) {
          e.preventDefault();
          alert('Please enter a category name.');
          return;
        }

        // Form will submit normally to the server
      });

      // Function to update serial numbers after deletion
      function updateSerialNumbers() {
        const rows = document.querySelectorAll('#categoriesTable tbody tr');
        rows.forEach((row, index) => {
          row.cells[0].textContent = index + 1;
        });
      }

      // Sidebar collapse/expand logic (keep mainPanel in sync) — moved to central handler
      const mainPanel = document.getElementById('mainPanel');
      const sidebar = document.querySelector('.sidebar');
      // Use a centralized approach in `sidebar_scripts.php` to manage events
      // Mirror sidebar collapsed class when it changes
      setTimeout(function() {
        if (sidebar && mainPanel) {
          if (sidebar.classList.contains('sidebar-collapsed')) {
            mainPanel.classList.add('sidebar-collapsed');
          } else {
            mainPanel.classList.remove('sidebar-collapsed');
          }
        }
      }, 300);

}); // Close DOMContentLoaded
