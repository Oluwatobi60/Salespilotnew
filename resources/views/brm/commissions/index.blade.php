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


        <!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
              © {{ date('Y') }} {{ app_name() }}. All rights reserved.
            </span>
          </div>
        </footer>

<!-- JavaScript for Wallet Operations -->
<script>
  // Set routes for commission.js
  document.body.setAttribute('data-add-account-route', '{{ route("brm.wallet.add-account") }}');
  document.body.setAttribute('data-withdraw-route', '{{ route("brm.wallet.withdraw") }}');
</script>

<script src="{{ asset('brm_asset/jss/commission.js') }}"></script>

@endsection
