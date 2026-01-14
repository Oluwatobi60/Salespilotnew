document.addEventListener('DOMContentLoaded', function() {

  // Side panel controls for adding discount
  var openAddDiscountBtn = document.getElementById('openAddDiscountBtn');
  var addDiscountPanel = document.getElementById('addDiscountPanel');
  var closeSidePanel = document.getElementById('closeSidePanel');
  var sidePanelOverlay = document.getElementById('sidePanelOverlay');

  // Open side panel
  if (openAddDiscountBtn && addDiscountPanel && sidePanelOverlay) {
    openAddDiscountBtn.addEventListener('click', function() {
      addDiscountPanel.classList.add('open');
      sidePanelOverlay.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
  }

  // Close side panel function
  function closePanel() {
    if (addDiscountPanel) {
      addDiscountPanel.classList.remove('open');
      document.body.style.overflow = ''; // Restore scrolling
    }
    if (sidePanelOverlay) {
      sidePanelOverlay.classList.remove('active');
    }
  }

  // Close side panel on close button click
  if (closeSidePanel) {
    closeSidePanel.addEventListener('click', closePanel);
  }

  // Close side panel on overlay click
  if (sidePanelOverlay) {
    sidePanelOverlay.addEventListener('click', closePanel);
  }

  // Close side panel on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && addDiscountPanel && addDiscountPanel.classList.contains('active')) {
      closePanel();
    }
  });

  // Filter functionality
  populateStaffDropdown();
  setupDateFilter();

  const staffFilter = document.getElementById('staffFilter');
  const dateRangeFilter = document.getElementById('dateRangeFilter');
  const customStartDate = document.getElementById('customStartDate');
  const customEndDate = document.getElementById('customEndDate');

  if (staffFilter) staffFilter.addEventListener('change', filterDiscountTable);
  if (dateRangeFilter) dateRangeFilter.addEventListener('change', filterDiscountTable);
  if (customStartDate) customStartDate.addEventListener('change', filterDiscountTable);
  if (customEndDate) customEndDate.addEventListener('change', filterDiscountTable);

});

function populateStaffDropdown() {
    fetch('/manager/get-staff-user-list')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(response => {
            const staffFilter = document.getElementById('staffFilter');
            if (!staffFilter) return;

            // Get current staff_id from URL params
            const urlParams = new URLSearchParams(window.location.search);
            const currentStaffId = urlParams.get('staff_id');

            staffFilter.innerHTML = '<option value="">All Staff</option>';

            // Access the staffUsers array from the response
            const staffUsers = response.staffUsers || [];

            staffUsers.forEach(person => {
                // Extract numeric ID from 'staff_123' or 'user_456' format
                const numericId = person.id.replace(/^(staff_|user_)/, '');
                const selected = currentStaffId == numericId ? 'selected' : '';
                staffFilter.innerHTML += `<option value="${numericId}" ${selected}>${person.name}</option>`;
            });
        })
        .catch(error => {
            console.error('Failed to load staff/user list:', error);
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

function filterDiscountTable() {
    const staffFilter = document.getElementById('staffFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');

    if (!dateRangeFilter) return;

    const staffId = staffFilter ? staffFilter.value : '';
    const dateRange = dateRangeFilter.value;
    let startDate = '';
    let endDate = '';

    if (dateRange === 'custom' && customStartDate && customEndDate) {
        startDate = customStartDate.value;
        endDate = customEndDate.value;
    }

    // Reload page with query params for backend filtering
    const params = new URLSearchParams();
    if (staffId) params.append('staff_id', staffId);
    if (dateRange) params.append('date_range', dateRange);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    window.location.search = params.toString();
}
