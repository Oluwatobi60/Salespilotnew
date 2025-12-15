
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


// Profile dropdown and other sidebar functionality
document.addEventListener('DOMContentLoaded', function () {
  var sidebar = document.getElementById('sidebar');

  // Profile dropdown responsive handler
  var userDropdownToggle = document.getElementById('UserDropdown');
  if (userDropdownToggle) {
    var dropdownMenu = userDropdownToggle.nextElementSibling;
    var isDropdownOpen = false;

    // Function to show dropdown
    function showDropdown() {
      if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
        dropdownMenu.classList.add('show');
        userDropdownToggle.setAttribute('aria-expanded', 'true');
        isDropdownOpen = true;
        console.log('Dropdown opened');
      }
    }

    // Function to hide dropdown
    function hideDropdown() {
      if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
        dropdownMenu.classList.remove('show');
        userDropdownToggle.setAttribute('aria-expanded', 'false');
        isDropdownOpen = false;
        console.log('Dropdown closed');
      }
    }

    // Function to toggle dropdown
    function toggleDropdown() {
      if (isDropdownOpen) {
        hideDropdown();
      } else {
        showDropdown();
      }
    }

    // Click handler
    userDropdownToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      toggleDropdown();
    });

    // Touch handler for mobile devices (prevents double-tap zoom)
    var touchStartTime = 0;
    userDropdownToggle.addEventListener('touchstart', function(e) {
      touchStartTime = Date.now();
    }, { passive: true });

    userDropdownToggle.addEventListener('touchend', function(e) {
      var touchDuration = Date.now() - touchStartTime;
      // Only trigger if it's a quick tap (not a scroll)
      if (touchDuration < 200) {
        e.preventDefault();
        e.stopPropagation();
        toggleDropdown();
      }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (isDropdownOpen && !userDropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
        hideDropdown();
      }
    });

    // Close dropdown when tapping outside on mobile
    document.addEventListener('touchend', function(e) {
      if (isDropdownOpen && !userDropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
        hideDropdown();
      }
    });

    // Close dropdown on ESC key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && isDropdownOpen) {
        hideDropdown();
      }
    });

    // Prevent dropdown from closing when clicking inside it
    if (dropdownMenu) {
      dropdownMenu.addEventListener('click', function(e) {
        // Allow links to work but prevent immediate closing
        if (e.target.tagName === 'A') {
          // Let the link navigate, dropdown will close
        } else {
          e.stopPropagation();
        }
      });
    }
  }
});
// Global sidebar parent collapse handler: ensure single-open behavior and toggle on click
document.addEventListener('DOMContentLoaded', function () {
  function initSidebarCollapses() {
    var menuLinks = document.querySelectorAll('.sidebar .nav-link[data-bs-toggle="collapse"]');

    function sidebarCollapseClick(e) {
      e.preventDefault();
      e.stopPropagation();

      var link = this;
      // If sidebar is collapsed, expand it first so submenu is visible
      var body = document.body;
      if (body.classList.contains('sidebar-collapsed')) {
        body.classList.remove('sidebar-collapsed');
        try { localStorage.setItem('sidebarCollapsed', 'false'); } catch (err) {}
      }
      // support href="#id" or data-bs-target
      var targetSelector = link.getAttribute('href') || link.getAttribute('data-bs-target') || link.dataset.bsTarget;
      if (!targetSelector) return;
      // ensure selector starts with # for query
      if (targetSelector.indexOf('#') !== 0) {
        var idx = targetSelector.indexOf('#');
        if (idx !== -1) targetSelector = targetSelector.substring(idx);
      }

      var target = document.querySelector(targetSelector);
      if (!target) return;

      // Collapse any other open sidebar collapse elements
      var openCollapses = document.querySelectorAll('.sidebar .collapse.show');
      openCollapses.forEach(function(open) {
        if (open !== target) {
          try {
            bootstrap.Collapse.getOrCreateInstance(open).hide();
          } catch (err) {
            // defensive: ignore if bootstrap not available yet
            console.warn('Could not hide open collapse', err);
          }
        }
      });

      // Toggle the clicked target
      try {
        bootstrap.Collapse.getOrCreateInstance(target).toggle();
      } catch (err) {
        console.warn('Could not toggle target collapse', err);
      }
    }

    menuLinks.forEach(function(link) {
      // remove possible duplicate handlers first
      link.removeEventListener('click', sidebarCollapseClick);
      link.addEventListener('click', sidebarCollapseClick);
      // Add keyboard support: Enter or Space toggles the collapse
      link.removeEventListener('keydown', function(){});
      link.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
          e.preventDefault();
          sidebarCollapseClick.call(this, e);
        }
      });
    });
    // After collapse handlers are in place, try to apply active link styling
    try {
      applyActiveSidebarLinkWithRetries();
    } catch (err) {
      console.warn('applyActiveSidebarLinkWithRetries call failed', err);
    }
  }

  // initialize now and a short while later (in case of late DOM inserts)
  initSidebarCollapses();
  setTimeout(initSidebarCollapses, 300);
  window.addEventListener('load', initSidebarCollapses);

  // Ensure collapse <-> trigger stay in sync: update trigger classes and arrow rotation
  function syncCollapseTriggers() {
    var collapses = document.querySelectorAll('.sidebar .collapse');
    collapses.forEach(function(col) {
      // find trigger for this collapse
      var id = col.id ? ('#' + col.id) : null;
      if (!id) return;
      var trigger = document.querySelector('.sidebar .nav-link[href="' + id + '"]') || document.querySelector('.sidebar .nav-link[data-bs-target="' + id + '"]');
      if (!trigger) return;

      // find arrow element inside trigger
      var arrow = trigger.querySelector('.menu-arrow');

      // attach events once
      if (col.dataset.syncAttached) return;
      col.dataset.syncAttached = '1';

      col.addEventListener('shown.bs.collapse', function() {
        try { trigger.classList.remove('collapsed'); } catch(e){}
        try { trigger.setAttribute('aria-expanded', 'true'); } catch(e){}
        if (arrow) arrow.classList.add('rotated');
      });

      col.addEventListener('hidden.bs.collapse', function() {
        try { trigger.classList.add('collapsed'); } catch(e){}
        try { trigger.setAttribute('aria-expanded', 'false'); } catch(e){}
        if (arrow) arrow.classList.remove('rotated');
      });

      // set initial state
      if (col.classList.contains('show')) {
        trigger.classList.remove('collapsed');
        trigger.setAttribute('aria-expanded', 'true');
        if (arrow) arrow.classList.add('rotated');
      } else {
        trigger.classList.add('collapsed');
        trigger.setAttribute('aria-expanded', 'false');
        if (arrow) arrow.classList.remove('rotated');
      }
    });
  }

  // run sync after initialization and on delayed load
  try { syncCollapseTriggers(); setTimeout(syncCollapseTriggers, 250); window.addEventListener('load', syncCollapseTriggers); } catch (e) { console.warn('syncCollapseTriggers failed', e); }

  // Active link & arrow rotation: centralize active state handling with retries until Bootstrap is available
  function applyActiveSidebarLink() {
    // Get the current full pathname
    var currentPath = window.location.pathname;

    // Remove any leading/trailing slashes for comparison
    var normalizedPath = currentPath.replace(/^\/+|\/+$/g, '');

    var activeLink = null;
    var allLinks = document.querySelectorAll('.sidebar .nav-link[href]');
    var bestMatch = null;
    var bestMatchLength = 0;

    // First pass: Find the best matching link
    for (var i = 0; i < allLinks.length; i++) {
      var link = allLinks[i];
      var href = link.getAttribute('href') || '';

      // Skip collapse toggles (they have href="#...")
      if (href.startsWith('#')) continue;

      try {
        // Create URL object to properly parse the href
        var linkUrl = new URL(href, window.location.origin);
        var linkPath = linkUrl.pathname.replace(/^\/+|\/+$/g, '');

        // Exact match - highest priority
        if (currentPath === linkUrl.pathname || normalizedPath === linkPath) {
          activeLink = link;
          break;
        }

        // Check if current path starts with link path (for nested routes)
        if (currentPath.startsWith(linkUrl.pathname) && linkUrl.pathname.length > 1) {
          if (linkUrl.pathname.length > bestMatchLength) {
            bestMatch = link;
            bestMatchLength = linkUrl.pathname.length;
          }
        }
      } catch (e) {
        // If URL parsing fails, try simple string matching
        if (href && currentPath.indexOf(href) !== -1) {
          if (href.length > bestMatchLength) {
            bestMatch = link;
            bestMatchLength = href.length;
          }
        }
      }
    }

    // Use best match if no exact match found
    if (!activeLink && bestMatch) {
      activeLink = bestMatch;
    }

    if (!activeLink) return false;

    // Clear any existing active states first
    var existingActive = document.querySelectorAll('.sidebar .nav-link.active, .sidebar .nav-item.active');
    existingActive.forEach(function(el) {
      el.classList.remove('active');
    });

    // Mark the found link as active
    activeLink.classList.add('active');
    var navItem = activeLink.closest('.nav-item');
    if (navItem && !navItem.classList.contains('user-dropdown')) {
      navItem.classList.add('active');
    }

    // If the link is inside a collapsed submenu, expand its parent and style the trigger
    var parentCollapse = activeLink.closest('.collapse');
    if (parentCollapse && parentCollapse.id) {
      // Show the collapse
      parentCollapse.classList.add('show');

      if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
        try {
          var bsCollapse = bootstrap.Collapse.getOrCreateInstance(parentCollapse);
          bsCollapse.show();
        } catch (e){}
      }

      // Find the trigger that opens this collapse
      var trigger = document.querySelector('.sidebar .nav-link[href="#' + parentCollapse.id + '"]') ||
                    document.querySelector('.sidebar .nav-link[data-bs-target="#' + parentCollapse.id + '"]');
      if (trigger) {
        trigger.classList.remove('collapsed');
        trigger.setAttribute('aria-expanded', 'true');
        var triggerItem = trigger.closest('.nav-item');
        if (triggerItem && !triggerItem.classList.contains('user-dropdown')) {
          triggerItem.classList.add('active');
        }

        // Rotate the arrow
        var arrow = trigger.querySelector('.menu-arrow');
        if (arrow) {
          arrow.classList.add('rotated');
        }
      }
    }

    return true;
  }

  // Retry wrapper — attempt until Bootstrap is available or max attempts reached
  function applyActiveSidebarLinkWithRetries(maxAttempts = 10, interval = 100) {
    var attempts = 0;
    function tryOnce() {
      attempts++;
      var done = applyActiveSidebarLink();
      if (done) return;
      if (attempts < maxAttempts) {
        setTimeout(tryOnce, interval);
      }
    }
    tryOnce();
  }

  // Ensure a fallback call on DOMContentLoaded in case initSidebarCollapses didn't run in time
  document.addEventListener('DOMContentLoaded', function() { setTimeout(applyActiveSidebarLinkWithRetries, 150); });
});
