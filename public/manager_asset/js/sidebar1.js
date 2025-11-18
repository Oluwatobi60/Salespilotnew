
// Sidebar dropdown expand/collapse on single click
document.addEventListener('DOMContentLoaded', function () {
  var sidebar = document.getElementById('sidebar');
  if (!sidebar) return;
  sidebar.querySelectorAll('a.nav-link[data-bs-toggle="collapse"]').forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
      var target = document.querySelector(this.getAttribute('href'));
      if (target) {
        var bsCollapse = bootstrap.Collapse.getOrCreateInstance(target);
        if (target.classList.contains('show')) {
          bsCollapse.hide();
        } else {
          // Optionally close other open menus for exclusive expansion
          sidebar.querySelectorAll('div.collapse.show').forEach(function (open) {
            if (open !== target) bootstrap.Collapse.getOrCreateInstance(open).hide();
          });
          bsCollapse.show();
        }
        e.preventDefault();
      }
    });
  });
});


    
    document.addEventListener('DOMContentLoaded', function() {
      // Only one submenu open at a time, expand/collapse on one click
      document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
          e.preventDefault();
          var targetSelector = this.getAttribute('href');
          var target = document.querySelector(targetSelector);
          if (!target) return;
          // Collapse all other open submenus
          document.querySelectorAll('.sidebar .collapse.show').forEach(function(openMenu) {
            if (openMenu !== target) {
              var openCollapse = bootstrap.Collapse.getOrCreateInstance(openMenu);
              openCollapse.hide();
            }
          });
          // Toggle the clicked submenu
          var bsCollapse = bootstrap.Collapse.getOrCreateInstance(target);
          bsCollapse.toggle();
        });
      });

      // Add Item Quick Action fallback handler
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
    
    
    
    document.addEventListener('DOMContentLoaded', function() {
        // Get all timeframe buttons
        const timeframeButtons = document.querySelectorAll('.timeframe-btn');
        
        // Add click event listener to each button
        timeframeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                timeframeButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.classList.add('btn-outline-primary');
                    btn.classList.remove('btn-primary');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                // Get the selected time range
                const selectedRange = this.getAttribute('data-range');
                console.log('Selected time range:', selectedRange);
                
                // Here you can add functionality to update the dashboard data
                // based on the selected time range
                updateDashboardData(selectedRange);
            });
        });
        
        // Function to update dashboard data (placeholder)
        function updateDashboardData(range) {
            // This is where you would typically make an AJAX call
            // to fetch new data based on the selected time range
            
            // For now, just show a loading indicator or update UI
            console.log('Updating dashboard for range:', range);
            
            // Example: You could update the stat numbers here
            // updateStatCards(range);
        }
        
        // Optional: Function to update stat cards with new data
        function updateStatCards(range) {
            // Example implementation - you would replace with actual data
            const statNumbers = document.querySelectorAll('.stat-number');
            
            // Simulate loading state
            statNumbers.forEach(stat => {
                stat.style.opacity = '0.5';
            });
            
            // Simulate data update after a short delay
            setTimeout(() => {
                statNumbers.forEach(stat => {
                    stat.style.opacity = '1';
                });
            }, 300);
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
                    window.location.href = 'views/add_item_standard.php';
                    break;
                case 'variant':
                    console.log('Redirecting to variant item page');
                    window.location.href = 'views/add_item_variant.php';
                    break;
                case 'bundled':
                    console.log('Redirecting to bundled item page');
                    window.location.href = 'views/add_item_bundled.php';
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
   