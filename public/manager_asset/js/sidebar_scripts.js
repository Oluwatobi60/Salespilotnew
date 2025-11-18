
(function() {
  'use strict';
  
  console.log('Initializing sidebar toggle...');
  
  function initSidebarToggle() {
    var toggle = document.getElementById('sidebarToggle');
    var body = document.body;
    
    if (!toggle) {
      console.error('Sidebar toggle button not found! Checking DOM...');
      console.log('Available elements with sidebarToggle:', document.querySelectorAll('[id*="sidebar"]'));
      return;
    }
    
    console.log('Sidebar toggle button found:', toggle);
    console.log('Button classes:', toggle.className);
    console.log('Button parent:', toggle.parentElement);
    
    // Load saved state from localStorage
    var savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
      body.classList.add('sidebar-collapsed');
      console.log('Restored collapsed state from localStorage');
    }
    
    // Remove any existing event listeners to prevent duplicates
    toggle.removeEventListener('click', handleToggleClick);
    
    // Add click event listener
    toggle.addEventListener('click', handleToggleClick);
    
    console.log('Sidebar toggle initialized successfully');
  }
  
  function handleToggleClick(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var body = document.body;
    body.classList.toggle('sidebar-collapsed');
    
    // Save state to localStorage
    var isCollapsed = body.classList.contains('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed.toString());
    
    console.log('Sidebar toggled:', isCollapsed ? 'collapsed' : 'expanded');
  }
  
  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebarToggle);
  } else {
    initSidebarToggle();
  }
  // ...existing code...
  
  // Backup initialization with longer delay
  setTimeout(function() {
    initSidebarToggle();
  }, 500);
  
  // Additional backup after all resources are loaded
  window.addEventListener('load', initSidebarToggle);
  
  // Emergency fallback - try every second for 10 seconds if button not found
  var attempts = 0;
  var maxAttempts = 10;
  var fallbackInterval = setInterval(function() {
    attempts++;
    if (document.getElementById('sidebarToggle') || attempts >= maxAttempts) {
      clearInterval(fallbackInterval);
      if (document.getElementById('sidebarToggle')) {
        console.log('Fallback initialization successful');
        initSidebarToggle();
      }
    }
  }, 1000);
  
  // Global function for manual testing
  window.testSidebarToggle = function() {
    console.log('Manual test initiated');
    var button = document.getElementById('sidebarToggle');
    if (button) {
      console.log('Button found, triggering click');
      button.click();
    } else {
      console.log('Button not found');
    }
  };
})();
