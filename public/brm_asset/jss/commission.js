// Bank code mapping
const bankCodes = {
  'Access Bank': '044',
  'Access Bank Diamond': '063',
  'Ecobank Nigeria': '050',
  'Fidelity Bank': '070',
  'First Bank of Nigeria': '011',
  'First City Monument Bank': '214',
  'Guaranty Trust Bank': '058',
  'Heritage Bank': '030',
  'Jaiz Bank': '301',
  'Keystone Bank': '082',
  'Parallex Bank': '526',
  'Polaris Bank': '076',
  'Providus Bank': '101',
  'Stanbic IBTC Bank': '221',
  'Standard Chartered Bank': '068',
  'Sterling Bank': '232',
  'Suntrust Bank': '100',
  'Union Bank of Nigeria': '032',
  'United Bank for Africa': '033',
  'Unity Bank': '215',
  'Wema Bank': '035',
  'Zenith Bank': '057'
};

// ===== ADD ACCOUNT MODAL FUNCTIONS =====
function openAddAccountModal() {
  document.getElementById('addAccountModal').style.display = 'flex';
  resetAddAccountForm();
}

function closeAddAccountModal() {
  document.getElementById('addAccountModal').style.display = 'none';
  resetAddAccountForm();
}

function resetAddAccountForm() {
  document.getElementById('addAccountForm').reset();
  document.getElementById('addAccountError').style.display = 'none';
  document.getElementById('bankCode').value = '';
}

// Update bank code when bank selection changes
document.addEventListener('DOMContentLoaded', function() {
  const bankSelect = document.getElementById('bankName');
  if (bankSelect) {
    bankSelect.addEventListener('change', function() {
      const selectedBank = this.value;
      const bankCode = bankCodes[selectedBank] || '';
      document.getElementById('bankCode').value = bankCode;
    });
  }

  // Close modals when clicking outside
  document.addEventListener('click', function(e) {
    const addAccountModal = document.getElementById('addAccountModal');
    const withdrawModal = document.getElementById('withdrawModal');
    
    if (e.target === addAccountModal) {
      closeAddAccountModal();
    }
    if (e.target === withdrawModal) {
      closeWithdrawModal();
    }
  });
});

// Handle form submission for adding account
async function processAddAccount(e) {
  if (e) e.preventDefault();
  
  const form = document.getElementById('addAccountForm');
  const errorDiv = document.getElementById('addAccountError');
  const btn = document.getElementById('addAccountBtn');
  
  // Validate form
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Show loading state
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-arrow-repeat" style="animation: spin 1s linear infinite;"></i> Adding...';
  errorDiv.style.display = 'none';

  try {
    const formData = new FormData(form);
    const addAccountRoute = document.body.getAttribute('data-add-account-route') || '/brm/commissions/wallet/add-account';
    const response = await fetch(addAccountRoute, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        'Accept': 'application/json',
      },
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      // Show success message
      alert('✓ Account added successfully!');
      closeAddAccountModal();
      
      // Refresh the page or update the accounts list
      setTimeout(() => location.reload(), 1500);
    } else {
      // Show error message
      errorDiv.textContent = data.message || 'Failed to add account.';
      errorDiv.style.display = 'block';
      btn.disabled = false;
      btn.innerHTML = 'Add Account';
    }
  } catch (error) {
    console.error('Error:', error);
    errorDiv.textContent = 'An error occurred. Please try again.';
    errorDiv.style.display = 'block';
    btn.disabled = false;
    btn.innerHTML = 'Add Account';
  }
}

// ===== WITHDRAW MODAL FUNCTIONS =====
function openWithdrawModal() {
  const accountSelect = document.getElementById('withdrawBankAccount');
  if (accountSelect.querySelectorAll('option').length <= 1) {
    alert('Please add a bank account first before withdrawing.');
    openAddAccountModal();
    return;
  }
  document.getElementById('withdrawModal').style.display = 'flex';
  resetWithdrawForm();
}

function closeWithdrawModal() {
  document.getElementById('withdrawModal').style.display = 'none';
  resetWithdrawForm();
}

function resetWithdrawForm() {
  document.getElementById('withdrawForm').reset();
  document.getElementById('withdrawError').style.display = 'none';
  document.getElementById('processingStatus').style.display = 'none';
}

// Handle form submission for withdrawal
async function initiateWithdrawal(e) {
  if (e) e.preventDefault();
  
  const form = document.getElementById('withdrawForm');
  const errorDiv = document.getElementById('withdrawError');
  const processingDiv = document.getElementById('processingStatus');
  const btn = document.getElementById('withdrawActionBtn');

  // Validate form
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  // Validate amount
  const amount = parseFloat(document.getElementById('withdrawAmount').value);
  const walletBalanceElement = document.getElementById('availableBalance');
  const walletBalance = parseFloat(walletBalanceElement?.textContent?.replace(/[^0-9.]/g, '') || 0);

  if (amount > walletBalance) {
    errorDiv.textContent = 'Amount exceeds available balance.';
    errorDiv.style.display = 'block';
    return;
  }

  if (amount < 1) {
    errorDiv.textContent = 'Minimum withdrawal amount is ₦1.';
    errorDiv.style.display = 'block';
    return;
  }

  // Show loading state
  btn.disabled = true;
  processingDiv.style.display = 'block';
  errorDiv.style.display = 'none';

  try {
    const formData = new FormData(form);
    const withdrawRoute = document.body.getAttribute('data-withdraw-route') || '/brm/commissions/wallet/withdraw';
    const response = await fetch(withdrawRoute, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        'Accept': 'application/json',
      },
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      // Show success message
      alert('✓ Withdrawal request submitted successfully!');
      closeWithdrawModal();
      
      // Update wallet balance display
      document.getElementById('walletBalance').textContent = '₦' + formatNumber(data.new_balance || 0);
      document.getElementById('availableBalance').textContent = formatNumber(data.new_balance || 0);
      
      // Refresh the page after delay
      setTimeout(() => location.reload(), 1500);
    } else {
      // Show error message
      errorDiv.textContent = data.message || 'Withdrawal request failed.';
      errorDiv.style.display = 'block';
      btn.disabled = false;
      processingDiv.style.display = 'none';
    }
  } catch (error) {
    console.error('Error:', error);
    errorDiv.textContent = 'An error occurred. Please try again.';
    errorDiv.style.display = 'block';
    btn.disabled = false;
    processingDiv.style.display = 'none';
  }
}

// Utility function to format numbers
function formatNumber(num) {
  return parseFloat(num).toLocaleString('en-NG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// Animation for spinner
const style = document.createElement('style');
style.textContent = `
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
`;
document.head.appendChild(style);
