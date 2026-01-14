document.addEventListener('DOMContentLoaded', function() {

		  console.log('DOM Content Loaded - All Items JS initialized'); // Debug log

		  // Items Table Functionality

		  // Select All functionality
		  const selectAllCheckbox = document.getElementById('selectAllItems');
		  const itemCheckboxes = document.querySelectorAll('.item-checkbox');
		  const bulkActions = document.getElementById('bulkActions');
		  const deselectAllBtn = document.getElementById('deselectAllBtn');
		  const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');

		  console.log('Found', itemCheckboxes.length, 'item checkboxes'); // Debug log

		  // Function to toggle bulk actions visibility
		  function toggleBulkActions() {
		    const selectedCheckboxes = Array.from(itemCheckboxes).filter(cb => cb.checked);

		    if (selectedCheckboxes.length > 0) {
		      bulkActions.style.display = 'inline-flex';
		      deleteSelectedBtn.textContent = `Delete ${selectedCheckboxes.length} Selected`;

		      // Highlight selected rows
		      itemCheckboxes.forEach(checkbox => {
		        const row = checkbox.closest('tr');
		        if (checkbox.checked) {
		          row.classList.add('selected');
		        } else {
		          row.classList.remove('selected');
		        }
		      });
		    } else {
		      bulkActions.style.display = 'none';

		      // Remove highlighting from all rows
		      itemCheckboxes.forEach(checkbox => {
		        const row = checkbox.closest('tr');
		        row.classList.remove('selected');
		      });
		    }
		  }

		  if (selectAllCheckbox) {
		    selectAllCheckbox.addEventListener('change', function() {
		      itemCheckboxes.forEach(checkbox => {
		        checkbox.checked = this.checked;
		      });
		      toggleBulkActions();
		    });
		  }

		  // Individual checkbox change
		  itemCheckboxes.forEach(checkbox => {
		    checkbox.addEventListener('change', function() {
		      const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
		      const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);

		      if (selectAllCheckbox) {
		        selectAllCheckbox.checked = allChecked;
		        selectAllCheckbox.indeterminate = someChecked && !allChecked;
		      }

		      toggleBulkActions();
		    });
		  });

		  // Deselect All button functionality
		  if (deselectAllBtn) {
		    deselectAllBtn.addEventListener('click', function() {
		      itemCheckboxes.forEach(checkbox => {
		        checkbox.checked = false;
		      });

		      if (selectAllCheckbox) {
		        selectAllCheckbox.checked = false;
		        selectAllCheckbox.indeterminate = false;
		      }

		      toggleBulkActions();
		    });
		  }

		  // Delete Selected button functionality
		  if (deleteSelectedBtn) {
		    deleteSelectedBtn.addEventListener('click', function() {
		      const selectedCheckboxes = Array.from(itemCheckboxes).filter(cb => cb.checked);
		      const selectedCount = selectedCheckboxes.length;

		      if (selectedCount === 0) return;

		      // Show confirmation dialog
		      const confirmMessage = `Are you sure you want to delete ${selectedCount} selected item${selectedCount > 1 ? 's' : ''}? This action cannot be undone.`;

		      if (confirm(confirmMessage)) {
		        // Get selected items with their IDs and types
		        const selectedItems = selectedCheckboxes.map(cb => ({
		          id: cb.value,
		          type: cb.getAttribute('data-type')
		        }));

		        // Show loading state
		        deleteSelectedBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Deleting...';
		        deleteSelectedBtn.disabled = true;

		        // Send delete request to server
		        fetch('/manager/all_items/delete_multiple', {
		          method: 'POST',
		          headers: {
		            'Content-Type': 'application/json',
		            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
		          },
		          body: JSON.stringify({
		            items: selectedItems
		          })
		        })
		        .then(response => response.json())
		        .then(data => {
		          if (data.success) {
		            // Remove selected rows from table
		            selectedCheckboxes.forEach(checkbox => {
		              const row = checkbox.closest('tr');
		              row.remove();
		            });

		            // Reset button state
		            deleteSelectedBtn.innerHTML = '<i class="bi bi-trash"></i> Delete Selected';
		            deleteSelectedBtn.disabled = false;

		            // Hide bulk actions
		            bulkActions.style.display = 'none';

		            // Reset select all checkbox
		            if (selectAllCheckbox) {
		              selectAllCheckbox.checked = false;
		              selectAllCheckbox.indeterminate = false;
		            }

		            // Show success message
		            alert(data.message || `Successfully deleted ${selectedCount} item${selectedCount > 1 ? 's' : ''}.`);

		            // Reload page to update pagination
		            window.location.reload();
		          } else {
		            // Show error message
		            alert(data.message || 'Error deleting items. Please try again.');

		            // Reset button state
		            deleteSelectedBtn.innerHTML = '<i class="bi bi-trash"></i> Delete Selected';
		            deleteSelectedBtn.disabled = false;
		          }
		        })
		        .catch(error => {
		          console.error('Error:', error);
		          alert('Error deleting items. Please try again.');

		          // Reset button state
		          deleteSelectedBtn.innerHTML = '<i class="bi bi-trash"></i> Delete Selected';
		          deleteSelectedBtn.disabled = false;
		        });
		      }
		    });
		  }

		  // Table row click functionality for item details
		  const itemsTable = document.getElementById('itemsTable');
		  const itemDetailsPanel = document.getElementById('itemDetailsPanel');
		  const panelOverlay = document.getElementById('panelOverlay');
		  const closePanelBtn = document.getElementById('closePanelBtn');
		  const closePanelFooterBtn = document.getElementById('closePanelFooterBtn');

		  // Add click event to eye icon buttons using event delegation
		  if (itemsTable) {
		    itemsTable.addEventListener('click', function(e) {
		      // Check if the clicked element is an edit button or its child icon
		      const editBtn = e.target.closest('.edit-btn');

		      if (editBtn) {
		        e.preventDefault();
		        e.stopPropagation();

		        console.log('Eye icon clicked'); // Debug log

		        // Get item data from button attributes
		        const itemId = editBtn.getAttribute('data-id');
		        const itemType = editBtn.getAttribute('data-type');

		        console.log('Item ID:', itemId, 'Type:', itemType); // Debug log

		        // Fetch item details from server
		        fetchItemDetails(itemType, itemId);
		      }
		    });
		  }

		  // Function to fetch item details via AJAX
		  function fetchItemDetails(type, id) {
		    console.log('Fetching details for:', type, id); // Debug log

		    // Show loading state
		    if (itemDetailsPanel) {
		      showItemPanel();
		      document.getElementById('panelItemName').value = 'Loading...';
		    }

		    // Make AJAX request to fetch item details
		    fetch(`/manager/Show_Item_Details/${type}/${id}`)
		      .then(response => {
		        if (!response.ok) {
		          throw new Error('Network response was not ok');
		        }
		        return response.json();
		      })
		      .then(data => {
		        console.log('Fetched item data:', data); // Debug log

		        // Populate panel with fetched data
		        populateItemPanelFromServer(data.item, type);
		      })
		      .catch(error => {
		        console.error('Error fetching item details:', error);
		        alert('Error loading item details. Please try again.');
		        hideItemPanel();
		      });
		  }

		  // Function to populate panel with data from server
		  function populateItemPanelFromServer(item, type) {
		    console.log('Populating panel with:', item); // Debug log

		    // Set image
		    const itemImage = document.getElementById('panelItemImage');
		    if (itemImage) {
		      let imagePath = '/manager_asset/images/faces/face1.jpg'; // Default image

		      if (type === 'standard' && item.item_image) {
		        imagePath = `/${item.item_image}`;
		      } else if (type === 'variant' && item.item_image) {
		        imagePath = `/${item.item_image}`;
		      } else if (type === 'bundle' && item.bundle_image) {
		        imagePath = `/${item.bundle_image}`;
		      }

		      itemImage.src = imagePath;
		      itemImage.onerror = function() {
		        this.src = '/manager_asset/images/faces/face1.jpg';
		      };
		    }

		    // Set basic information based on item type
		    const itemName = type === 'bundle' ? item.bundle_name : item.item_name;
		    const itemCode = type === 'bundle' ? item.bundle_code : item.item_code;
		    const costPrice = type === 'bundle' ? item.total_bundle_cost : item.cost_price;
		    const sellingPrice = type === 'bundle' ? item.bundle_selling_price : item.selling_price;

		    document.getElementById('panelItemName').value = itemName || 'N/A';
		    document.getElementById('panelItemSku').value = itemCode || 'N/A';
		    document.getElementById('panelItemCategory').value = item.category || 'N/A';

		    // Set unit
		    let unitValue = 'N/A';
		    if (type === 'standard') {
		      unitValue = item.unit || 'N/A';
		    } else if (type === 'variant' || type === 'bundle') {
		      unitValue = item.unit ? (item.unit.name || item.unit) : 'N/A';
		    }
		    document.getElementById('panelItemUnit').value = unitValue;

		    // Set supplier
		    const supplierValue = item.supplier ? item.supplier.name : 'N/A';
		    document.getElementById('panelItemSupplier').value = supplierValue;

		    // Set stock value
		    const stockValue = parseInt(item.current_stock) || 0;
		    document.getElementById('panelItemStock').value = stockValue;

		    // Set prices
		    const formattedSellingPrice = sellingPrice ? `₦${parseFloat(sellingPrice).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : 'N/A';
		    const formattedCostPrice = costPrice ? `₦${parseFloat(costPrice).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : 'N/A';

		    document.getElementById('panelItemSellingPrice').value = formattedSellingPrice;
		    document.getElementById('panelItemCostPrice').value = formattedCostPrice;

		    // Calculate and display profit margin
		    if (sellingPrice && costPrice && costPrice > 0) {
		      const profitMargin = ((sellingPrice - costPrice) / costPrice * 100).toFixed(2);
		      document.getElementById('panelItemProfit').textContent = `${profitMargin}%`;

		      // Calculate total value
		      const totalValue = (sellingPrice * stockValue).toLocaleString('en-NG', {
		        style: 'currency',
		        currency: 'NGN',
		        minimumFractionDigits: 0
		      });
		      document.getElementById('panelItemTotalValue').textContent = totalValue;
		    } else {
		      document.getElementById('panelItemProfit').textContent = '0%';
		      document.getElementById('panelItemTotalValue').textContent = '₦0';
		    }

		    // Set stock status
		    const statusElement = document.getElementById('panelStockStatus');
		    const lowStockThreshold = item.low_stock_threshold || 10;

		    if (stockValue === 0) {
		      statusElement.textContent = 'Out of Stock';
		      statusElement.className = 'input-group-text stock-status out-of-stock';
		    } else if (stockValue <= lowStockThreshold) {
		      statusElement.textContent = 'Low Stock';
		      statusElement.className = 'input-group-text stock-status low-stock';
		    } else {
		      statusElement.textContent = 'In Stock';
		      statusElement.className = 'input-group-text stock-status in-stock';
		    }

		    // Set last updated date
		    const updatedAt = item.updated_at ? new Date(item.updated_at) : new Date();
		    document.getElementById('panelItemLastUpdated').value = updatedAt.toLocaleDateString('en-US', {
		      year: 'numeric',
		      month: 'long',
		      day: 'numeric',
		      hour: '2-digit',
		      minute: '2-digit'
		    });

		    // Store item ID and type for edit functionality
		    const editBtn = document.getElementById('editItemPanelBtn');
		    if (editBtn) {
		      editBtn.setAttribute('data-item-id', item.id);
		      editBtn.setAttribute('data-item-type', type);
		    }
		  }		  // Function to show item panel
		  function showItemPanel() {
		    console.log('showItemPanel called'); // Debug log
		    console.log('itemDetailsPanel element:', itemDetailsPanel); // Debug log

		    if (itemDetailsPanel) {
		      itemDetailsPanel.classList.add('active');
		      document.body.style.overflow = 'hidden'; // Prevent background scrolling
		      console.log('Panel should be visible now'); // Debug log
		    } else {
		      console.error('itemDetailsPanel element not found!');
		    }
		  }

		  // Function to hide item panel
		  function hideItemPanel() {
		    console.log('hideItemPanel called'); // Debug log
		    if (itemDetailsPanel) {
		      itemDetailsPanel.classList.remove('active');
		      document.body.style.overflow = ''; // Restore scrolling
		    }
		  }

		  // Panel close event listeners
		  if (closePanelBtn) {
		    closePanelBtn.addEventListener('click', hideItemPanel);
		  }

		  if (closePanelFooterBtn) {
		    closePanelFooterBtn.addEventListener('click', hideItemPanel);
		  }

		  if (panelOverlay) {
		    panelOverlay.addEventListener('click', hideItemPanel);
		  }

		  // ESC key to close panel
		  document.addEventListener('keydown', function(e) {
		    if (e.key === 'Escape' && itemDetailsPanel.classList.contains('active')) {
		      hideItemPanel();
		    }
		  });

		  // Function to populate item panel
		  function populateItemPanel(itemData) {
		    // Set image
		    if (itemData.image) {
		      document.getElementById('panelItemImage').src = itemData.image;
		    }

		    // Set basic information
		    document.getElementById('panelItemName').value = itemData.name || 'N/A';
		    document.getElementById('panelItemSku').value = itemData.code || 'N/A';
		    document.getElementById('panelItemCategory').value = itemData.category || 'N/A';
		    document.getElementById('panelItemUnit').value = itemData.unit || 'N/A';
		    document.getElementById('panelItemSupplier').value = itemData.supplier || 'N/A';

		    // Set stock value
		    const stockValue = parseInt(itemData.stock) || 0;
		    document.getElementById('panelItemStock').value = stockValue;

		    // Set prices
		    document.getElementById('panelItemSellingPrice').value = itemData.sellingPrice || 'N/A';
		    document.getElementById('panelItemCostPrice').value = itemData.costPrice || 'N/A';

		    // Calculate and display profit margin
		    if (itemData.sellingPrice !== 'N/A' && itemData.costPrice !== 'N/A') {
		      const sellingPrice = parseFloat(itemData.sellingPrice.replace(/[₦,]/g, ''));
		      const costPrice = parseFloat(itemData.costPrice.replace(/[₦,]/g, ''));

		      if (!isNaN(sellingPrice) && !isNaN(costPrice) && costPrice > 0) {
		        const profitMargin = ((sellingPrice - costPrice) / costPrice * 100).toFixed(2);
		        document.getElementById('panelItemProfit').textContent = `${profitMargin}%`;

		        // Calculate total value
		        const totalValue = (sellingPrice * stockValue).toLocaleString('en-NG', {
		          style: 'currency',
		          currency: 'NGN',
		          minimumFractionDigits: 0
		        });
		        document.getElementById('panelItemTotalValue').textContent = totalValue;
		      } else {
		        document.getElementById('panelItemProfit').textContent = '0%';
		        document.getElementById('panelItemTotalValue').textContent = '₦0';
		      }
		    } else {
		      document.getElementById('panelItemProfit').textContent = '0%';
		      document.getElementById('panelItemTotalValue').textContent = '₦0';
		    }

		    // Set stock status
		    const statusElement = document.getElementById('panelStockStatus');
		    if (stockValue === 0) {
		      statusElement.textContent = 'Out of Stock';
		      statusElement.className = 'input-group-text stock-status out-of-stock';
		    } else if (stockValue <= 10) {
		      statusElement.textContent = 'Low Stock';
		      statusElement.className = 'input-group-text stock-status low-stock';
		    } else {
		      statusElement.textContent = 'In Stock';
		      statusElement.className = 'input-group-text stock-status in-stock';
		    }

		    // Set last updated date (current date for demo)
		    const now = new Date();
		    document.getElementById('panelItemLastUpdated').value = now.toLocaleDateString('en-US', {
		      year: 'numeric',
		      month: 'long',
		      day: 'numeric',
		      hour: '2-digit',
		      minute: '2-digit'
		    });

		    // Store item ID and type for edit functionality
		    const editBtn = document.getElementById('editItemPanelBtn');
		    editBtn.setAttribute('data-item-id', itemData.id);
		    editBtn.setAttribute('data-item-type', itemData.type || 'standard');
		  }

		  // Edit item button functionality
		  const editItemPanelBtn = document.getElementById('editItemPanelBtn');
		  if (editItemPanelBtn) {
		    editItemPanelBtn.addEventListener('click', function() {
		      const itemId = this.getAttribute('data-item-id');
		      const itemType = this.getAttribute('data-item-type');

		      if (itemId && itemType) {
		        console.log('Edit item - ID:', itemId, 'Type:', itemType);
		        // Redirect to edit page
		        window.location.href = `/manager/all_items/edit/${itemType}/${itemId}`;
		      } else {
		        console.error('Item ID or Type not found');
		        alert('Unable to edit item. Please try again.');
		      }
		    });
		  }

		  // Search and Filter functionality
		  const searchInput = document.getElementById('searchItems');
		  const categoryFilter = document.getElementById('categoryFilter');
		  const inventoryFilter = document.getElementById('inventoryFilter');
		  const supplierFilter = document.getElementById('supplierFilter');
		  const applyFiltersBtn = document.getElementById('applyFilters');
		  const clearFiltersBtn = document.getElementById('clearFilters');

		  // Function to determine stock status based on quantity
		  function getStockStatus(quantity) {
		    const qty = parseInt(quantity);
		    if (qty === 0) return 'out-of-stock';
		    if (qty <= 10) return 'low-stock';
		    return 'in-stock';
		  }

		  // Function to apply all filters
		  function applyAllFilters() {
		    const searchTerm = searchInput.value.toLowerCase();
		    const selectedCategory = categoryFilter.value.toLowerCase();
		    const selectedInventory = inventoryFilter.value;
		    const selectedSupplier = supplierFilter.value;
		    const tableRows = document.querySelectorAll('#itemsTable tbody tr');

		    tableRows.forEach(row => {
		      const itemName = row.querySelector('h6').textContent.toLowerCase();
		      const itemCode = row.querySelector('small.text-muted')?.textContent.toLowerCase() || '';
		      const category = row.cells[3].textContent.trim().toLowerCase(); // Category column
		      const stockQuantity = parseInt(row.cells[5].querySelector('span.badge')?.textContent.trim()) || 0; // Stock column
		      const stockStatus = getStockStatus(stockQuantity);

		      // Search filter - search in name, code, and category
		      const matchesSearch = itemName.includes(searchTerm) ||
		                           itemCode.includes(searchTerm) ||
		                           category.includes(searchTerm);

		      // Category filter - case-insensitive comparison
		      const matchesCategory = !selectedCategory || category === selectedCategory || category === 'n/a';

		      // Inventory filter
		      const matchesInventory = !selectedInventory || stockStatus === selectedInventory;

		      // Supplier filter
		      const rowSupplierId = row.getAttribute('data-supplier-id');
		      const matchesSupplier = !selectedSupplier || rowSupplierId === selectedSupplier;

		      // Show/hide row based on all filters
		      if (matchesSearch && matchesCategory && matchesInventory && matchesSupplier) {
		        row.style.display = '';
		      } else {
		        row.style.display = 'none';
		      }
		    });
		  }

		  // Real-time search
		  if (searchInput) {
		    searchInput.addEventListener('input', applyAllFilters);
		  }

		  // Apply filters button
		  if (applyFiltersBtn) {
		    applyFiltersBtn.addEventListener('click', applyAllFilters);
		  }

		  // Clear filters button
		  if (clearFiltersBtn) {
		    clearFiltersBtn.addEventListener('click', function() {
		      searchInput.value = '';
		      categoryFilter.value = '';
		      inventoryFilter.value = '';
		      supplierFilter.value = '';
		      applyAllFilters();
		    });
		  }

		  // Import functionality
		  const importBtn = document.getElementById('importItems');
		  const importFile = document.getElementById('importFile');

		  if (importBtn) {
		    importBtn.addEventListener('click', function() {
		      importFile.click();
		    });
		  }

		  if (importFile) {
		    importFile.addEventListener('change', function(e) {
		      const file = e.target.files[0];
		      if (file) {
		        // Check file type
		        const fileType = file.name.split('.').pop().toLowerCase();
		        if (['csv', 'xlsx', 'xls'].includes(fileType)) {
		          handleFileImport(file);
		        } else {
		          alert('Please select a valid file format (CSV, XLSX, or XLS)');
		        }
		      }
		    });
		  }

		  // Export functionality
		  const exportBtn = document.getElementById('exportItems');

		  if (exportBtn) {
		    exportBtn.addEventListener('click', function() {
		      exportItemsToCSV();
		    });
		  }

		  // Function to handle file import
		  function handleFileImport(file) {
		    const formData = new FormData();
		    formData.append('importFile', file);

		    // Show loading state
		    importBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Importing...';
		    importBtn.disabled = true;

		    // Simulate import process (replace with actual server call)
		    setTimeout(() => {
		      // Reset button state
		      importBtn.innerHTML = '<i class="bi bi-upload"></i> Import';
		      importBtn.disabled = false;

		      // Show success message
		      alert(`Successfully imported items from ${file.name}`);

		      // Refresh the page or update the table
		      location.reload();
		    }, 2000);

		    // Reset file input
		    importFile.value = '';
		  }

		  // Function to export items to CSV
		  function exportItemsToCSV() {
		    // Show loading state
		    exportBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Exporting...';
		    exportBtn.disabled = true;

		    // Get table data
		    const table = document.getElementById('itemsTable');
		    const rows = table.querySelectorAll('tbody tr');

		    // CSV headers
		    let csvContent = 'S/N,Item Name,Category,SKU,Quantity,Unit Price,Status,Action Date,Supplier\n';

		    // Add visible rows to CSV
		    rows.forEach((row, index) => {
		      if (row.style.display !== 'none') {
		        const cells = row.querySelectorAll('td');
		        if (cells.length > 2) { // Skip empty rows
		          const rowData = [
		            cells[0].textContent.trim(), // S/N
		            cells[2].textContent.trim(), // Item Name
		            cells[3].textContent.trim(), // Category
		            cells[4].textContent.trim(), // SKU
		            cells[5].textContent.trim(), // Quantity
		            cells[6].textContent.trim(), // Unit Price
		            cells[7].textContent.trim(), // Status
		            new Date().toLocaleDateString(), // Action Date
		            'N/A' // Supplier (placeholder)
		          ];
		          csvContent += rowData.map(field => `"${field.replace(/"/g, '""')}"`).join(',') + '\n';
		        }
		      }
		    });

		    // Create and download CSV file
		    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
		    const link = document.createElement('a');
		    const url = URL.createObjectURL(blob);
		    link.setAttribute('href', url);
		    link.setAttribute('download', `inventory_items_${new Date().toISOString().split('T')[0]}.csv`);
		    link.style.visibility = 'hidden';
		    document.body.appendChild(link);
		    link.click();
		    document.body.removeChild(link);

		    // Reset button state
		    setTimeout(() => {
		      exportBtn.innerHTML = '<i class="bi bi-download"></i> Export';
		      exportBtn.disabled = false;
		    }, 1000);
		  }

		  // Filter change events
		  [categoryFilter, inventoryFilter, supplierFilter].forEach(filter => {
		    if (filter) {
		      filter.addEventListener('change', applyAllFilters);
		    }
		  });

		  // Action buttons functionality
		  document.querySelectorAll('.btn-outline-primary').forEach(button => {
		    if (button.title === 'View') {
		      button.addEventListener('click', function() {
		        // View item functionality
		        console.log('View item clicked');
		        // Add your view logic here
		      });
		    }
		  });

		  document.querySelectorAll('.btn-outline-warning').forEach(button => {
		    if (button.title === 'Edit') {
		      button.addEventListener('click', function() {
		        // Edit item functionality
		        console.log('Edit item clicked');
		        // Add your edit logic here
		      });
		    }
		  });

		  // Add Item Quick Action functionality
		  var addItemBtn = document.getElementById('addItemQuickAction');
		  if (addItemBtn) {
		    console.log('Add Item button found, attaching event listener');
		    addItemBtn.addEventListener('click', function(e) {
		      e.preventDefault();
		      e.stopPropagation();

		      console.log('Add Item button clicked');

		      var modal = document.getElementById('itemTypeModal');
		      if (modal) {
		        try {
		          // Check if Bootstrap is loaded
		          if (typeof bootstrap === 'undefined') {
		            console.error('Bootstrap JS is not loaded!');
		            alert('Bootstrap JavaScript is required for modals. Please refresh the page.');
		            return;
		          }

		          var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
		          bsModal.show();
		          console.log('Modal should be showing now');
		        } catch (error) {
		          console.error('Error showing modal:', error);
		          alert('Error opening modal: ' + error.message);
		        }
		      } else {
		        console.error('Modal element not found!');
		        alert('Modal not found on page!');
		      }
		    });
		  } else {
		    console.error('Add Item button not found!');
		  }
		});

		// Function to show item details when clicking on options
		function showItemDetails(type) {
		  console.log('Showing details for type:', type);

		  // Remove active class from all options and reset their styles
		  document.querySelectorAll('.item-option').forEach(option => {
		    option.classList.remove('active');
		    option.style.border = '2px solid transparent';
		    option.style.backgroundColor = '';
		  });

		  // Add active class to clicked option and apply active styling
		  const selectedOption = document.querySelector(`[data-type="${type}"]`);
		  if (selectedOption) {
		    selectedOption.classList.add('active');

		    // Apply type-specific active styling
		    switch(type) {
		      case 'standard':
		        selectedOption.style.border = '2px solid #007bff';
		        selectedOption.style.backgroundColor = '#e3f2fd';
		        break;
		      case 'variant':
		        selectedOption.style.border = '2px solid #28a745';
		        selectedOption.style.backgroundColor = '#d4edda';
		        break;
		      case 'bundled':
		        selectedOption.style.border = '2px solid #ffc107';
		        selectedOption.style.backgroundColor = '#fff3cd';
		        break;
		    }
		  }

		  // Hide all detail sections
		  document.querySelectorAll('.item-details').forEach(detail => {
		    detail.style.display = 'none';
		    detail.style.opacity = '0';
		    detail.classList.remove('active');
		  });

		  // Show selected detail section with animation
		  const selectedDetail = document.getElementById(`${type}-details`);
		  if (selectedDetail) {
		    selectedDetail.style.display = 'block';
		    selectedDetail.classList.add('active');

		    // Use setTimeout to ensure display:block is applied before opacity change
		    setTimeout(() => {
		      selectedDetail.style.opacity = '1';
		    }, 10);
		  }
		}

		// Function to handle item type selection
		function selectItemType(type) {
		  console.log('Selecting item type:', type);

		  // Close the modal first
		  const modalElement = document.getElementById('itemTypeModal');
		  if (modalElement) {
		    try {
		      const modal = bootstrap.Modal.getInstance(modalElement);
		      if (modal) {
		        modal.hide();
		      } else {
		        console.warn('Modal instance not found, creating new one to hide');
		        const newModal = bootstrap.Modal.getOrCreateInstance(modalElement);
		        newModal.hide();
		      }
		    } catch (error) {
		      console.error('Error closing modal:', error);
		    }
		  }

		  // Small delay to ensure modal closes before redirect
		  setTimeout(() => {
		    // Redirect based on the selected item type
		    switch(type) {
		      case 'standard':
		        console.log('Redirecting to standard item page');
		        window.location.href = 'add_item_standard.php';
		        break;
		      case 'variant':
		        console.log('Redirecting to variant item page');
		        window.location.href = 'add_item_variant.php';
		        break;
		      case 'bundled':
		        console.log('Redirecting to bundled item page');
		        window.location.href = 'add_item_bundled.php';
		        break;
		      default:
		        console.error('Unknown item type:', type);
		        alert('Unknown item type: ' + type);
		    }
		  }, 200);
		}

		// Function to handle Continue button click
		function proceedWithItemType() {
		  // Get the currently selected item type
		  const activeOption = document.querySelector('.item-option.active');
		  if (activeOption) {
		    const selectedType = activeOption.getAttribute('data-type');
		    console.log('Proceeding with item type:', selectedType);
		    selectItemType(selectedType);
		  } else {
		    console.warn('No item type selected');
		    alert('Please select an item type first.');
		  }
		}
