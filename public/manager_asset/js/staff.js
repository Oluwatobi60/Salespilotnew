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

});
