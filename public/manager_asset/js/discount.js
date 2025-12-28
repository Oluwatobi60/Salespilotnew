document.addEventListener('DOMContentLoaded', function() {

  // Side panel controls for adding discount
  var openAddDiscountBtn = document.getElementById('openAddDiscountBtn');
  var addDiscountPanel = document.getElementById('addDiscountPanel');
  var closeSidePanel = document.getElementById('closeSidePanel');
  var sidePanelOverlay = document.getElementById('sidePanelOverlay');

  // Open side panel
  if (openAddDiscountBtn && addDiscountPanel && sidePanelOverlay) {
    openAddDiscountBtn.addEventListener('click', function() {
      addDiscountPanel.classList.add('open');
      sidePanelOverlay.classList.add('active');
      document.body.style.overflow = 'hidden'; // Prevent background scrolling
    });
  }

  // Close side panel function
  function closePanel() {
    if (addDiscountPanel) {
      addDiscountPanel.classList.remove('open');
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

  // Close side panel on overlay click
  if (sidePanelOverlay) {
    sidePanelOverlay.addEventListener('click', closePanel);
  }

  // Close side panel on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && addDiscountPanel && addDiscountPanel.classList.contains('active')) {
      closePanel();
    }
  });



});
