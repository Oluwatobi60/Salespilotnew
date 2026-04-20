@extends('brm.layouts.layout')

@section('brms_page_title')
My Commissions
@endsection

@section('brms_page_content')
<link rel="stylesheet" href="{{ asset('brm_asset/css/commission.css') }}">

<!-- Page Header -->
<div class="page-header">
  <h1><i class="bi bi-cash-stack"></i> My Commissions</h1>
  <p>Track your commission earnings and payment history</p>
</div>

<!-- Wallet Section -->
<div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
  <div class="stat-card total" style="flex: 0 0 auto; min-width: 280px; padding: 1.25rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <div class="stat-icon" style="margin-bottom: 0;">
        <i class="bi bi-wallet2"></i>
      </div>
      <div>
        <h3 style="margin-bottom: 0.25rem;" id="walletBalance">₦{{ number_format($walletStats['balance'] ?? 0, 2, '.', ',') }}</h3>
        <p style="margin-bottom: 0;">Wallet Balance</p>
      </div>
    </div>
  </div>
  <div style="display: flex; gap: 0.75rem;">
    <button onclick="openAddAccountModal()" style="background: linear-gradient(135deg, #20c997 0%, #167a5c 100%); color: white; border: none; padding: 0.75rem 1.25rem; border-radius: 6px; font-size: 0.9rem; cursor: pointer; transition: all 0.3s ease; white-space: nowrap;">
      <i class="bi bi-bank"></i> Add Account
    </button>
    <button onclick="openWithdrawModal()" style="background: white; color: #dc3545; border: 1px solid #dc3545; padding: 0.75rem 1.25rem; border-radius: 6px; font-size: 0.9rem; cursor: pointer; transition: all 0.3s ease; white-space: nowrap;">
      <i class="bi bi-cash"></i> Withdraw
    </button>
  </div>
</div>

<!-- Wallet Modals -->
<div id="addAccountModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 450px; width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
    <h5 style="margin-bottom: 1.5rem; color: #2c3e50;">
      <i class="bi bi-bank" style="color: #20c997;"></i> Add Bank Account
    </h5>
    <div id="addAccountError" style="display: none; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;"></div>
    <form id="addAccountForm" onsubmit="processAddAccount(event)">
      @csrf
      <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; color: #6c757d; font-size: 0.9rem;">Account Number</label>
        <input type="text" id="accountNumber" name="account_number" placeholder="Enter 10-digit account number" maxlength="10" required style="width: 100%; padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 1rem;">
        <small id="accountNumberStatus" style="display: block; margin-top: 0.25rem; color: #6c757d;"></small>
      </div>
      <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; color: #6c757d; font-size: 0.9rem;">Bank Name</label>
        <select id="bankName" name="bank_name" required style="width: 100%; padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem;">
          <option value="">Select Bank</option>
          <option value="Access Bank" data-code="044">Access Bank (044)</option>
          <option value="Access Bank Diamond" data-code="063">Access Bank Diamond (063)</option>
          <option value="Ecobank Nigeria" data-code="050">Ecobank Nigeria (050)</option>
          <option value="Fidelity Bank" data-code="070">Fidelity Bank (070)</option>
          <option value="First Bank of Nigeria" data-code="011">First Bank of Nigeria (011)</option>
          <option value="First City Monument Bank" data-code="214">First City Monument Bank (214)</option>
          <option value="Guaranty Trust Bank" data-code="058">Guaranty Trust Bank (058)</option>
          <option value="Heritage Bank" data-code="030">Heritage Bank (030)</option>
          <option value="Jaiz Bank" data-code="301">Jaiz Bank (301)</option>
          <option value="Keystone Bank" data-code="082">Keystone Bank (082)</option>
          <option value="Parallex Bank" data-code="526">Parallex Bank (526)</option>
          <option value="Polaris Bank" data-code="076">Polaris Bank (076)</option>
          <option value="Providus Bank" data-code="101">Providus Bank (101)</option>
          <option value="Stanbic IBTC Bank" data-code="221">Stanbic IBTC Bank (221)</option>
          <option value="Standard Chartered Bank" data-code="068">Standard Chartered Bank (068)</option>
          <option value="Sterling Bank" data-code="232">Sterling Bank (232)</option>
          <option value="Suntrust Bank" data-code="100">Suntrust Bank (100)</option>
          <option value="Union Bank of Nigeria" data-code="032">Union Bank of Nigeria (032)</option>
          <option value="United Bank for Africa" data-code="033">United Bank for Africa (033)</option>
          <option value="Unity Bank" data-code="215">Unity Bank (215)</option>
          <option value="Wema Bank" data-code="035">Wema Bank (035)</option>
          <option value="Zenith Bank" data-code="057">Zenith Bank (057)</option>
        </select>
        <input type="hidden" id="bankCode" name="bank_code">
      </div>
      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; margin-bottom: 0.5rem; color: #6c757d; font-size: 0.9rem;">Account Name</label>
        <input type="text" id="accountName" name="account_name" placeholder="Enter account name" required style="width: 100%; padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 1rem;">
      </div>
      <div style="display: flex; gap: 0.75rem;">
        <button type="button" onclick="closeAddAccountModal()" style="flex: 1; background: #f8f9fa; color: #6c757d; border: none; padding: 0.75rem; border-radius: 6px; cursor: pointer;">Cancel</button>
        <button type="submit" id="addAccountBtn" style="flex: 1; background: #20c997; color: white; border: none; padding: 0.75rem; border-radius: 6px; cursor: pointer;">Add Account</button>
      </div>
    </form>
  </div>
</div>

<div id="withdrawModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 450px; width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
    <h5 style="margin-bottom: 1.5rem; color: #2c3e50;">
      <i class="bi bi-cash" style="color: #dc3545;"></i> Withdraw Funds
    </h5>
    <div id="withdrawError" style="display: none; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;"></div>
    <form id="withdrawForm" onsubmit="initiateWithdrawal(event)">
      @csrf
      <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; color: #6c757d; font-size: 0.9rem;">Amount (₦)</label>
        <input type="number" id="withdrawAmount" name="amount" placeholder="Enter amount" min="1" step="0.01" required style="width: 100%; padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 1rem;">
        <small style="color: #6c757d; display: block; margin-top: 0.25rem;">Available: ₦<span id="availableBalance">{{ number_format($walletStats['balance'] ?? 0, 2, '.', ',') }}</span></small>
      </div>
      <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; color: #6c757d; font-size: 0.9rem;">Bank Account</label>
        <select id="withdrawBankAccount" name="account_id" required style="width: 100%; padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem;">
          <option value="">Select Bank Account</option>
          @forelse($walletStats['accounts'] ?? [] as $account)
            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ substr($account->account_number, -4) }} ({{ $account->account_name }})</option>
          @empty
            <option value="" disabled>No accounts saved. Please add one first.</option>
          @endforelse
        </select>
      </div>
      <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; color: #6c757d; font-size: 0.9rem;">Reason (Optional)</label>
        <textarea id="withdrawReason" name="reason" placeholder="Enter withdrawal reason" style="width: 100%; padding: 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; resize: vertical; min-height: 60px;"></textarea>
      </div>

      <div id="processingStatus" style="display: none; margin-bottom: 1rem; color: #667eea; font-size: 0.9rem; text-align: center;">
        <i class="bi bi-arrow-repeat" style="animation: spin 1s linear infinite;"></i> Processing your withdrawal...
      </div>

      <div style="display: flex; gap: 0.75rem;">
        <button type="button" onclick="closeWithdrawModal()" style="flex: 1; background: #f8f9fa; color: #6c757d; border: none; padding: 0.75rem; border-radius: 6px; cursor: pointer;">Cancel</button>
        <button type="submit" id="withdrawActionBtn" style="flex: 1; background: #dc3545; color: white; border: none; padding: 0.75rem; border-radius: 6px; cursor: pointer;">Withdraw</button>
      </div>
    </form>
  </div>
</div>

<style>
  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
</style>


<div class="commission-stats-container">
  <div class="stat-card primary">
    <div class="stat-header">
      <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
      <h4>Total Commissions</h4>
    </div>
    <h2>₦{{ number_format($totalCommissions, 2, '.', ',') }}</h2>
  </div>

  <div class="stat-card pending">
    <div class="stat-header">
      <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
      <h4>Pending</h4>
    </div>
    <h2>₦{{ number_format($pendingCommissions, 2, '.', ',') }}</h2>
    <p>{{ $pendingCount }} commission{{ $pendingCount !== 1 ? 's' : '' }}</p>
  </div>

  <div class="stat-card approved">
    <div class="stat-header">
      <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
      <h4>Approved</h4>
    </div>
    <h2>₦{{ number_format($approvedCommissions, 2, '.', ',') }}</h2>
    <p>{{ $approvedCount }} commission{{ $approvedCount !== 1 ? 's' : '' }}</p>
  </div>

  <div class="stat-card paid">
    <div class="stat-header">
      <div class="stat-icon"><i class="bi bi-check2-all"></i></div>
      <h4>Paid</h4>
    </div>
    <h2>₦{{ number_format($paidCommissions, 2, '.', ',') }}</h2>
    <p>{{ $paidCount }} payment{{ $paidCount !== 1 ? 's' : '' }}</p>
  </div>

  <div class="stat-card month">
    <div class="stat-header">
      <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
      <h4>This Month</h4>
    </div>
    <h2>₦{{ number_format($commissionsThisMonth, 2, '.', ',') }}</h2>
  </div>
</div>

<!-- Commission Breakdown -->
<div class="commission-breakdown-section">
  <div class="breakdown-card">
    <h4><i class="bi bi-diagram-2"></i> Commission Types</h4>
    <div class="breakdown-items">
      <div class="breakdown-item">
        <span class="item-label">Referral</span>
        <span class="item-value">₦{{ number_format($referralCommissions, 2, '.', ',') }}</span>
      </div>
      <div class="breakdown-item">
        <span class="item-label">Renewal</span>
        <span class="item-value">₦{{ number_format($renewalCommissions, 2, '.', ',') }}</span>
      </div>
      <div class="breakdown-item">
        <span class="item-label">Upgrade</span>
        <span class="item-value">₦{{ number_format($upgradeCommissions, 2, '.', ',') }}</span>
      </div>
    </div>
  </div>

  <div class="breakdown-card actions">
    <div class="action-buttons">
      <a href="{{ route('brm.commissions.history') }}" class="btn-primary">
        <i class="bi bi-clock-history"></i> View Full History
      </a>
      <a href="{{ route('brm.commissions.breakdown') }}" class="btn-secondary">
        <i class="bi bi-graph-up"></i> Detailed Breakdown
      </a>
    </div>
  </div>
</div>



<!-- Quick Actions -->
<div class="quick-actions-section">
  <div class="action-card">
    <div class="action-icon"><i class="bi bi-box-arrow-up-right"></i></div>
    <h5>Refer a Customer</h5>
    <p>Share your unique referral link to earn commissions</p>
    <button class="btn-link">Get My Referral Link</button>
  </div>

  <div class="action-card">
    <div class="action-icon"><i class="bi bi-file-earmark-pdf"></i></div>
    <h5>Download Report</h5>
    <p>Export your commission history as PDF or CSV</p>
    <button class="btn-link">Download Report</button>
  </div>

  <div class="action-card">
    <div class="action-icon"><i class="bi bi-question-circle"></i></div>
    <h5>How It Works</h5>
    <p>Learn about our commission structure and payouts</p>
    <button class="btn-link">View Terms</button>
  </div>
</div>

<style>
.commission-stats-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border-left: 4px solid #667eea;
}

.stat-card.pending {
  border-left-color: #ffc107;
}

.stat-card.approved {
  border-left-color: #17a2b8;
}

.stat-card.paid {
  border-left-color: #28a745;
}

.stat-card.month {
  border-left-color: #667eea;
}

.stat-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.stat-icon {
  font-size: 1.8rem;
  color: #667eea;
}

.stat-card.pending .stat-icon {
  color: #ffc107;
}

.stat-card.approved .stat-icon {
  color: #17a2b8;
}

.stat-card.paid .stat-icon {
  color: #28a745;
}

.stat-header h4 {
  margin: 0;
  font-size: 0.9rem;
  color: #6c757d;
  font-weight: 500;
}

.stat-card h2 {
  margin: 0.5rem 0 0;
  color: #2c3e50;
  font-size: 1.8rem;
}

.stat-card p {
  margin: 0.5rem 0 0;
  font-size: 0.85rem;
  color: #6c757d;
}

.commission-breakdown-section {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.breakdown-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.breakdown-card h4 {
  margin: 0 0 1.5rem;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1rem;
}

.breakdown-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.breakdown-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.item-label {
  color: #6c757d;
  font-weight: 500;
}

.item-value {
  color: #2c3e50;
  font-weight: 600;
  font-size: 1.1rem;
}

.breakdown-card.actions {
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
}

.btn-primary, .btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: white;
  color: #667eea;
  border: 2px solid #667eea;
}

.btn-secondary:hover {
  background: #f8f9fa;
}

.commission-table-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 2rem;
}

.commission-table-section h4 {
  margin: 0 0 1.5rem;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.table-responsive {
  overflow-x: auto;
}

.commission-table {
  width: 100%;
  border-collapse: collapse;
}

.commission-table thead tr {
  background: #f8f9fa;
  border-bottom: 2px solid #dee2e6;
}

.commission-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #495057;
  font-size: 0.9rem;
}

.commission-table td {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
  color: #495057;
}

.commission-table tbody tr:hover {
  background: #f8f9fa;
}

.amount-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
}

.type-badge {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 500;
}

.type-badge.referral {
  background: #e7f3ff;
  color: #0066cc;
}

.type-badge.renewal {
  background: #fff3e0;
  color: #f57c00;
}

.type-badge.upgrade {
  background: #f3e5f5;
  color: #6a1b9a;
}

.status-badge {
  display: inline-block;
  padding: 0.35rem 0.85rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}

.status-badge.approved {
  background: #d1ecf1;
  color: #0c5460;
}

.status-badge.paid {
  background: #d4edda;
  color: #155724;
}

.status-badge.rejected {
  background: #f8d7da;
  color: #721c24;
}

.quick-actions-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.action-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.action-icon {
  font-size: 2.5rem;
  color: #667eea;
  margin-bottom: 1rem;
  display: block;
}

.action-card h5 {
  margin: 0 0 0.5rem;
  color: #2c3e50;
}

.action-card p {
  margin: 0 0 1.5rem;
  font-size: 0.9rem;
  color: #6c757d;
}

.btn-link {
  background: none;
  border: none;
  color: #667eea;
  text-decoration: underline;
  cursor: pointer;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.btn-link:hover {
  color: #764ba2;
}

@media (max-width: 768px) {
  .commission-breakdown-section {
    grid-template-columns: 1fr;
  }

  .commission-stats-container {
    grid-template-columns: 1fr 1fr;
  }

  .table-responsive {
    font-size: 0.85rem;
  }

  .commission-table th,
  .commission-table td {
    padding: 0.75rem;
  }
}
</style>

        <!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              © 2026 SalesPilot. All rights reserved.
            </span>
          </div>
        </footer>

<!-- JavaScript for Wallet Operations -->
<script>
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
    const response = await fetch('{{ route("brm.wallet.add-account") }}', {
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
  const walletBalance = {{ $walletStats['balance'] ?? 0 }};

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
    const response = await fetch('{{ route("brm.wallet.withdraw") }}', {
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
</script>

@endsection
