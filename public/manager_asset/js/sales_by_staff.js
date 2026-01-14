// sales_by_staff.js
// Populates staff/user dropdown and filters sales report by staff/user and date

document.addEventListener('DOMContentLoaded', function () {
    populateStaffDropdown();
    setupDateFilter();

    const staffFilter = document.getElementById('staffFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');
    if (staffFilter) staffFilter.addEventListener('change', filterSalesTable);
    if (dateRangeFilter) dateRangeFilter.addEventListener('change', filterSalesTable);
    if (customStartDate) customStartDate.addEventListener('change', filterSalesTable);
    if (customEndDate) customEndDate.addEventListener('change', filterSalesTable);
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
    document.getElementById('dateRangeFilter').addEventListener('change', function () {
        if (this.value === 'custom') {
            document.getElementById('customRangeInputs').style.display = '';
        } else {
            document.getElementById('customRangeInputs').style.display = 'none';
        }
    });
}

function filterSalesTable() {
    const staffFilter = document.getElementById('staffFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const customStartDate = document.getElementById('customStartDate');
    const customEndDate = document.getElementById('customEndDate');
    if (!staffFilter || !dateRangeFilter) return;
    const staffId = staffFilter.value;
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
