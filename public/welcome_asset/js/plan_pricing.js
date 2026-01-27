
// Price calculation logic
const discounts = {
    1: 0,     // 0% discount for 1 month
    3: 0.05,  // 5% discount for 3 months
    6: 0.10,  // 10% discount for 6 months
    12: 0.15  // 15% discount for 1 year
};

function formatCurrency(amount) {
    return '₦' + amount.toLocaleString('en-NG');
}

function updatePrices(months) {
    const priceElements = document.querySelectorAll('.price[data-monthly-price]');

    priceElements.forEach(priceEl => {
        const monthlyPrice = parseInt(priceEl.dataset.monthlyPrice);
        const totalWithoutDiscount = monthlyPrice * months;
        const discount = discounts[months];
        const totalWithDiscount = totalWithoutDiscount * (1 - discount);

        // Update calculated price
        const calculatedPrice = priceEl.querySelector('.calculated-price');
        calculatedPrice.textContent = formatCurrency(totalWithDiscount);

        // Update original price
        const originalPrice = priceEl.querySelector('.original-price');
        if (discount > 0) {
            originalPrice.textContent = formatCurrency(totalWithoutDiscount);
            originalPrice.style.display = 'block';
        } else {
            originalPrice.style.display = 'none';
        }

        // Update duration text
        const durationText = priceEl.querySelector('.duration-text');
        const durationLabel = months === 1 ? '1 month' : (months === 12 ? '1 year' : `${months} months`);
        durationText.textContent = `for ${durationLabel}`;
    });

    // Update all duration inputs
    document.querySelectorAll('.duration-input').forEach(input => {
        input.value = months;
    });
}

// Duration button click handlers
document.querySelectorAll('.duration-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.duration-btn').forEach(b => {
            b.classList.remove('active');
            b.style.background = 'transparent';
            b.style.color = '#666';
        });

        // Add active class to clicked button
        this.classList.add('active');
        this.style.background = '#4CAF50';
        this.style.color = 'white';

        // Update prices
        const months = parseInt(this.dataset.months);
        updatePrices(months);
    });
});

// Initialize with 1 month selected
updatePrices(1);
