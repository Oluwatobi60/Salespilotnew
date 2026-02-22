      // Subscription Plan Toggles and Details
      document.addEventListener('DOMContentLoaded', function() {
        // Plan features data
        const planFeatures = {
          'basic': {
            name: 'Basic Plan',
            features: [
              '1 Manager/Administrator Account',
              '2 Staff Accounts',
              'Advanced Inventory Management',
              'Sales & Purchase Tracking',
              'Basic Reports & Analytics',
              'Priority Email Support',
            ]
          },
          'standard': {
            name: 'Standard Plan',
            features: [
             '2 Manager/Administrator Accounts',
            'Up to 4 Staff Accounts',
            'Allows 2 branches',
            'Advanced Inventory Management',
            'Sales & Purchase Tracking',
            'Basic Reports & Analytics',
            'Priority Email Support'
            ]
          },
          'premium': {
            name: 'Premium Plan',
            features: [
              '3 Manager/Administrator Accounts',
                'Unlimited Staff Accounts',
                'Full Inventory Management',
                'Advanced Reports & Analytics',
                'Multi-branch Support',
                '24/7 Priority Support',
                'Custom Integrations'
            ]
          }
        };

        // Plan prices data
        const planPrices = {
          'basic': {
            monthly: 5000,
            '3months': 14250,
            '6months': 27000,
            annual: 51000
          },
          'standard': {
            monthly: 10000,
            '3months': 28500,
            '6months': 54000,
            annual: 102000
          },
          'premium': {
            monthly: 20000,
            '3months': 57000,
            '6months': 108000,
            annual: 204000
          }
        };

        // Toggle plan details
        document.querySelectorAll('.plan-toggle').forEach(button => {
          button.addEventListener('click', function() {
            const row = this.closest('tr.plan-row');
            const detailsRow = row.nextElementSibling;

            if (detailsRow && detailsRow.classList.contains('plan-details')) {
              const isVisible = detailsRow.style.display !== 'none';

              // Hide all other plan details
              document.querySelectorAll('.plan-details').forEach(dr => {
                dr.style.display = 'none';
              });

              if (!isVisible) {
                // Extract plan name from button text
                const planName = this.textContent.trim().toLowerCase();
                const planData = planFeatures[planName];

                if (planData) {
                  // Build features list
                  let featuresHtml = '<div class="p-4 bg-light"><div class="row"><div class="col-md-6">';
                  featuresHtml += '<h6 class="mb-3 text-primary"><i class="bi bi-star-fill me-2"></i>' + planData.name + ' Features</h6>';
                  featuresHtml += '<ul class="list-unstyled">';
                  planData.features.forEach(feature => {
                    featuresHtml += '<li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>' + feature + '</li>';
                  });
                  featuresHtml += '</ul></div>';

                  // Add pricing options
                  featuresHtml += '<div class="col-md-6"><h6 class="mb-3 text-primary"><i class="bi bi-cash-stack me-2"></i>Choose Your Duration</h6>';
                  featuresHtml += '<div class="d-grid gap-2">';
                  const prices = planPrices[planName];
                  featuresHtml += `
                    <button class="btn btn-outline-primary text-start subscribe-btn" data-plan="${planName}" data-duration="monthly" data-amount="${prices.monthly}">
                      <div class="d-flex justify-content-between align-items-center">
                        <span>Monthly</span>
                        <strong>₦${prices.monthly.toLocaleString()}/month</strong>
                      </div>
                    </button>
                    <button class="btn btn-outline-primary text-start subscribe-btn" data-plan="${planName}" data-duration="3months" data-amount="${prices['3months']}">
                      <div class="d-flex justify-content-between align-items-center">
                        <span>3 Months <small class="text-success">(Save 5%)</small></span>
                        <strong>₦${prices['3months'].toLocaleString()}</strong>
                      </div>
                    </button>
                    <button class="btn btn-outline-primary text-start subscribe-btn" data-plan="${planName}" data-duration="6months" data-amount="${prices['6months']}">
                      <div class="d-flex justify-content-between align-items-center">
                        <span>6 Months <small class="text-success">(Save 10%)</small></span>
                        <strong>₦${prices['6months'].toLocaleString()}</strong>
                      </div>
                    </button>
                    <button class="btn btn-outline-primary text-start subscribe-btn" data-plan="${planName}" data-duration="annual" data-amount="${prices.annual}">
                      <div class="d-flex justify-content-between align-items-center">
                        <span>Annual <small class="text-success">(Save 15%)</small></span>
                        <strong>₦${prices.annual.toLocaleString()}/year</strong>
                      </div>
                    </button>
                  `;
                  featuresHtml += '</div></div></div></div>';

                  detailsRow.querySelector('td').innerHTML = featuresHtml;
                  detailsRow.style.display = 'table-row';
                }
              }
            }
          });
        });

        // Handle subscription button clicks
        document.addEventListener('click', function(e) {
          if (e.target.closest('.subscribe-btn')) {
            const btn = e.target.closest('.subscribe-btn');
            const plan = btn.getAttribute('data-plan');
            const duration = btn.getAttribute('data-duration');
            const amount = btn.getAttribute('data-amount');

            // Here you would typically redirect to payment gateway or show payment modal
            const confirmation = confirm(`Subscribe to ${plan.charAt(0).toUpperCase() + plan.slice(1)} Plan (${duration})?\n\nAmount: ₦${parseInt(amount).toLocaleString()}\n\nYou will be redirected to the payment page.`);

            if (confirmation) {
              // Redirect to payment page or trigger payment modal
              // window.location.href = `/payment?plan=${plan}&duration=${duration}&amount=${amount}`;
              alert('Payment integration coming soon! Plan: ' + plan + ', Duration: ' + duration);
            }
          }
        });
      });

      // Measurement units: add / edit / delete handling. Default units are protected.
      document.addEventListener('DOMContentLoaded', function() {
        const addUnitForm = document.getElementById('addUnitForm');
        const unitsTableBody = document.querySelector('#measurement table tbody');
        const editUnitModalEl = document.getElementById('editUnitModal');
        const editUnitModal = editUnitModalEl ? new bootstrap.Modal(editUnitModalEl) : null;
        let editingRow = null;

        function escapeHtml(str) {
          if (!str) return '';
          return String(str).replace(/[&<>"']/g, function(m) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; });
        }

        if (addUnitForm && unitsTableBody) {
          addUnitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('unitName').value.trim();
            const abbr = document.getElementById('unitAbbreviation').value.trim();
            const precision = document.getElementById('unitPrecision').value;
            if (!name || !abbr) { alert('Please fill Unit Name and Abbreviation'); return; }

            const tr = document.createElement('tr');
            tr.setAttribute('data-unit-type', 'Custom');
            tr.innerHTML = `
              <td>${escapeHtml(name)}</td>
              <td><span class="badge bg-secondary">${escapeHtml(abbr)}</span></td>
              <td>${escapeHtml(precision)}</td>
              <td><span class="badge bg-success">Custom</span></td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-primary edit-unit-btn" title="Edit Unit"><i class="bi bi-pencil"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger delete-unit-btn" title="Delete Unit"><i class="bi bi-trash"></i></button>
              </td>
            `;
            unitsTableBody.appendChild(tr);

            // Reset and close
            addUnitForm.reset();
            try { bootstrap.Modal.getInstance(document.getElementById('addUnitModal')).hide(); } catch(e){ }
          });
        }

        // Delegated handlers for edit/delete buttons in the units table
        if (unitsTableBody) {
          unitsTableBody.addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-unit-btn');
            const delBtn = e.target.closest('.delete-unit-btn');
            if (editBtn) {
              const tr = editBtn.closest('tr');
              if (!tr) return;
              if (tr.getAttribute('data-unit-type') !== 'Custom') { alert('Default units cannot be edited.'); return; }
              editingRow = tr;
              // populate edit modal
              const name = tr.cells[0].innerText.trim();
              const abbrEl = tr.cells[1].querySelector('span');
              const abbr = abbrEl ? abbrEl.innerText.trim() : tr.cells[1].innerText.trim();
              const precision = tr.cells[2].innerText.trim();
              document.getElementById('editUnitName').value = name;
              document.getElementById('editUnitAbbr').value = abbr;
              document.getElementById('editUnitPrecision').value = precision;
              if (editUnitModal) editUnitModal.show();
            } else if (delBtn) {
              const tr = delBtn.closest('tr');
              if (!tr) return;
              if (tr.getAttribute('data-unit-type') !== 'Custom') { alert('Default units cannot be deleted.'); return; }
              if (confirm('Delete this custom unit?')) tr.remove();
            }
          });
        }

        // Edit unit form submit
        const editUnitForm = document.getElementById('editUnitForm');
        if (editUnitForm) {
          editUnitForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!editingRow) return;
            const name = document.getElementById('editUnitName').value.trim();
            const abbr = document.getElementById('editUnitAbbr').value.trim();
            const precision = document.getElementById('editUnitPrecision').value;
            if (!name || !abbr) { alert('Please fill Unit Name and Abbreviation'); return; }
            editingRow.cells[0].innerText = name;
            editingRow.cells[1].innerHTML = '<span class="badge bg-secondary">'+ escapeHtml(abbr) +'</span>';
            editingRow.cells[2].innerText = precision;
            if (editUnitModal) editUnitModal.hide();
            editingRow = null;
          });
        }
      });
