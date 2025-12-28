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
        .then(data => {
            const staffFilter = document.getElementById('staffFilter');
            if (!staffFilter) return;
            staffFilter.innerHTML = '<option value="">All Staff</option>';
            data.forEach(person => {
                staffFilter.innerHTML += `<option value="${person.id}">${person.name}</option>`;
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
