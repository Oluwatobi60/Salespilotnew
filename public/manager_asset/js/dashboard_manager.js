document.addEventListener('DOMContentLoaded', function() {
function applyDateFilter(start, end) {
    const url = new URL(window.location.href);
    url.searchParams.set('start_date', start);
    url.searchParams.set('end_date', end);
    window.location.href = url.toString();
}
document.getElementById('applyCustomRange').addEventListener('click', function() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;
    if (start && end) {
    applyDateFilter(start, end);
    }
});
document.querySelectorAll('.timeframe-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            let today = new Date();
            let start, end;
            switch(btn.dataset.range) {
                case 'today':
                    start = end = today.toISOString().slice(0,10);
                    break;
                case 'week':
                    let firstDay = new Date(today);
                    firstDay.setDate(today.getDate() - today.getDay());
                    let lastDay = new Date(today);
                    lastDay.setDate(today.getDate() - today.getDay() + 6);
                    start = firstDay.toISOString().slice(0,10);
                    end = lastDay.toISOString().slice(0,10);
                    break;
                case 'month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().slice(0,10);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().slice(0,10);
                    break;
                case 'year':
                    start = new Date(today.getFullYear(), 0, 1).toISOString().slice(0,10);
                    end = new Date(today.getFullYear(), 11, 31).toISOString().slice(0,10);
                    break;
            }
            applyDateFilter(start, end);
        });
});




// ...existing code for date filtering and UI logic only...




});
