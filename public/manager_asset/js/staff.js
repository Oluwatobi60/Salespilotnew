document.addEventListener('DOMContentLoaded', function() {

  // Side panel controls for adding staff
  var openAddStaffBtn = document.getElementById('openAddStaffBtn');
  var addStaffPanel = document.getElementById('addStaffPanel');
  var closeSidePanel = document.getElementById('closeSidePanel');
  var cancelAddStaff = document.getElementById('cancelAddStaff');
  var sidePanelOverlay = document.getElementById('sidePanelOverlay');

  // Open side panel
  if (openAddStaffBtn && addStaffPanel && sidePanelOverlay) {
    openAddStaffBtn.addEventListener('click', function() {
      addStaffPanel.classList.add('active');
      sidePanelOverlay.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
  }

  // Close side panel function
  function closePanel() {
    if (addStaffPanel) {
      addStaffPanel.classList.remove('active');
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

  // Close side panel on cancel button click
  if (cancelAddStaff) {
    cancelAddStaff.addEventListener('click', closePanel);
  }

  // Close side panel on overlay click
  if (sidePanelOverlay) {
    sidePanelOverlay.addEventListener('click', closePanel);
  }

  // Close side panel on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && addStaffPanel && addStaffPanel.classList.contains('active')) {
      closePanel();
    }
  });


// Auto-generate Staff ID from surname (last word in fullname) and random 3 digits

  const fullnameInput = document.getElementById('fullname');
  const staffIdInput = document.getElementById('staff_id');
  function generateStaffId() {
    let fullname = fullnameInput.value.trim();
    if (!fullname) {
      staffIdInput.value = '';
      return;
    }
    // Get surname (last word)
    let parts = fullname.split(' ');
    let surname = parts.length > 1 ? parts[parts.length - 1] : parts[0];
    let prefix = surname.substring(0, 3).toUpperCase().padEnd(3, 'X');
    let randomDigits = Math.floor(100 + Math.random() * 900); // 3 digits
    staffIdInput.value = prefix + randomDigits;
  }
  fullnameInput.addEventListener('input', generateStaffId);
  // If form is reset or loaded with value, generate
  if (fullnameInput.value) generateStaffId();



});
