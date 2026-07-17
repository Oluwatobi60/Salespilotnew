/**
 * AJAX Navigation Handler for Manager Dashboard
 * Loads page content without full page refresh
 */

class AjaxNavigationHandler {
    constructor() {
        this.currentUrl = window.location.href;
        this.isLoading = false;
        this.initEventListeners();
        // Add delay to ensure DOM is fully loaded
        setTimeout(() => {
            this.restoreActiveLink();
        }, 100);
    }

    /**
     * Initialize event listeners for all navigation links
     */
    initEventListeners() {
        // Delegate click event ONLY to nav links in the sidebar
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebar) {
            sidebar.addEventListener('click', (e) => {
                const link = e.target.closest('.nav-link');
                
                // Skip if not a nav link
                if (!link) return;
                
                // Skip collapse toggles
                if (link.getAttribute('data-bs-toggle') === 'collapse') return;
                
                // Skip dropdown toggles
                if (link.getAttribute('data-bs-toggle') === 'dropdown') return;
                
                // Skip disabled links
                if (link.hasAttribute('data-ajax-disabled')) return;
                
                // Skip hash links
                if (link.getAttribute('href') === '#') return;
                
                const href = link.getAttribute('href');
                if (!href || href.startsWith('javascript:')) return;

                e.preventDefault();
                e.stopPropagation();
                this.loadPageContent(href, link);
            });
        } else {
            console.warn('⚠️ Sidebar element not found');
        }

        // Handle browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.url) {
                this.loadPageContent(e.state.url, null, false);
            }
        });
    }

    /**
     * Load page content via AJAX
     * @param {string} url - The URL to load
     * @param {Element} link - The clicked link element
     * @param {boolean} pushState - Whether to push state to history
     */
    loadPageContent(url, link = null, pushState = true) {
        // Pages that require full page reload (pages with forms, add/edit/delete buttons)
        const fullPageReloadPages = [
            // POS & Sales
            '/manager/sell_product',
            '/manager/pos',
            '/manager/completed_sales',
            
            // Item Management (has forms and buttons)
            '/manager/add_item_standard',
            '/manager/add_item_variant',
            '/manager/edit_item',
            '/manager/all_items',  // Has edit/delete buttons
            
            // Category Management
            '/manager/all_categories',  // Has edit/delete buttons
            '/manager/add_category',
            '/manager/edit_category',
            
            // Customers (has add/edit/delete)
            '/manager/customers',
            '/manager/add_customer',
            '/manager/edit_customer',
            
            // Suppliers (has add/edit/delete)
            '/manager/suppliers',
            '/manager/add_supplier',
            '/manager/edit_supplier',
            
            // Staff Management (has add/edit/delete)
            '/manager/staff',
            '/manager/add_staff',
            '/manager/edit_staff',
            
            // Manager Management (has add/edit/delete)
            '/manager/manager',
            '/manager/add_manager',
            '/manager/edit_manager',
            
            // Branches (has add/edit/delete)
            '/manager/branches',
            '/manager/add_branch',
            '/manager/edit_branch',
            '/manager/inventory/branch-allocation',
            
            // Units (has add/edit/delete)
            '/manager/units',
            '/manager/add_unit',
            '/manager/edit_unit',
            
            // Discounts (has add/edit/delete)
            '/manager/add_discount',
            '/manager/edit_discount',
            
            // Saved Carts (has interact buttons)
            '/manager/view_saved_carts',
        ];
        
        const urlPath = new URL(url, window.location.origin).pathname;
        const needsFullReload = fullPageReloadPages.some(page => urlPath.includes(page));
        
        if (needsFullReload) {
            console.log('⚡ Full page reload for action page:', urlPath);
            window.location.href = url;
            return;
        }

        if (this.isLoading) {
            console.warn('⚠️ Already loading content, skipping request');
            return;
        }

        // Show loading indicator
        this.showLoadingState();
        this.isLoading = true;
        
        // Set a timeout to ensure loading state is cleared after 60 seconds
        const timeoutId = setTimeout(() => {
            console.error('❌ Request timeout after 60 seconds');
            this.isLoading = false;
            this.hideLoadingState();
        }, 60000);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text();
        })
        .then(html => {
            // Extract content from the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Get the main content area
            const newContent = doc.querySelector('.content-wrapper');
            const currentContent = document.querySelector('.content-wrapper');
            
            if (!newContent) {
                console.error('❌ Content wrapper not found in response');
                this.isLoading = false;
                this.hideLoadingState();
                window.location.href = url;
                return;
            }

            // Update page title
            const pageTitle = doc.querySelector('title');
            if (pageTitle) {
                document.title = pageTitle.textContent;
            }

            // Fade out current content
            currentContent.style.opacity = '0.5';
            
            // Replace content with fade in effect
            setTimeout(() => {
                currentContent.innerHTML = newContent.innerHTML;
                currentContent.style.opacity = '1';
                currentContent.style.pointerEvents = 'none'; // Disable during script execution
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Update active link styling
                if (link) {
                    this.updateActiveLink(link);
                }

                // Push to browser history
                if (pushState) {
                    window.history.pushState({ url: url }, '', url);
                }

                // Execute scripts and re-initialize
                this.executeLoadedScripts(doc);
                
                // Re-initialize event listeners for newly loaded content
                this.reinitializeEventListeners();

                // Trigger custom event for other scripts
                const event = new CustomEvent('ajaxContentLoaded', { detail: { url: url } });
                document.dispatchEvent(event);

                this.isLoading = false;
                this.hideLoadingState();
            }, 200);
        })
        .catch(error => {
            clearTimeout(timeoutId);
            console.error('❌ AJAX navigation error:', error);
            this.isLoading = false;
            this.hideLoadingState();
            // Fallback to regular navigation
            window.location.href = url;
        });
    }

    /**
     * Execute scripts from loaded HTML content
     * @param {Document} doc - Parsed HTML document
     */
    executeLoadedScripts(doc) {
        // Find all scripts in the loaded content
        const scripts = doc.querySelectorAll('script');
        let scriptCount = 0;
        let externalScriptsToLoad = [];
        
        console.log(`📝 Found ${scripts.length} scripts to process`);
        
        scripts.forEach((script, index) => {
            // Handle external scripts
            if (script.src) {
                console.log(`📥 Found external script: ${script.src}`);
                externalScriptsToLoad.push(script.src);
                return;
            }
            
            // Skip module scripts
            if (script.type === 'module') {
                console.log(`⏭️  Skipping module script`);
                return;
            }
            
            try {
                const scriptCode = script.textContent;
                
                if (!scriptCode.trim()) {
                    return;
                }
                
                // Modify code to handle DOMContentLoaded and other wrappers
                let modifiedCode = scriptCode;
                
                // Handle different DOMContentLoaded patterns
                modifiedCode = modifiedCode.replace(
                    /document\.addEventListener\s*\(\s*['"]DOMContentLoaded['"]\s*,\s*function\s*\(\s*\)\s*\{/g,
                    '(function() {'
                );
                
                modifiedCode = modifiedCode.replace(
                    /document\.addEventListener\s*\(\s*['"]DOMContentLoaded['"]\s*,\s*\(\s*\)\s*=>\s*\{/g,
                    '(() => {'
                );
                
                // Remove closing brackets if they're at the end
                const closingMatches = modifiedCode.match(/\}\s*\)\s*;?\s*$/);
                if (closingMatches) {
                    modifiedCode = modifiedCode.slice(0, -closingMatches[0].length) + '}';
                }
                
                // Execute the script in current scope
                try {
                    (function() {
                        eval(modifiedCode);
                    }).call(window);
                    
                    scriptCount++;
                    console.log(`✅ Script ${scriptCount} executed`);
                } catch(e) {
                    console.error('❌ Script execution error:', e.message);
                }
                
            } catch (e) {
                console.error('❌ Error processing script:', e);
            }
        });
        
        console.log(`✅ Inline scripts executed: ${scriptCount}`);
        
        // Load external scripts sequentially
        if (externalScriptsToLoad.length > 0) {
            this.loadExternalScripts(externalScriptsToLoad);
        }
    }

    /**
     * Load external scripts dynamically
     * @param {Array} scriptUrls - URLs of scripts to load
     */
    loadExternalScripts(scriptUrls) {
        let loaded = 0;
        
        scriptUrls.forEach(url => {
            const script = document.createElement('script');
            script.src = url;
            script.async = false; // Load sequentially
            script.onload = () => {
                loaded++;
                console.log(`✅ External script loaded (${loaded}/${scriptUrls.length}): ${url}`);
            };
            script.onerror = () => {
                console.warn(`⚠️  Failed to load script: ${url}`);
            };
            document.body.appendChild(script);
        });
    }

    /**
     * Clean up old content and event listeners
     */
    cleanupOldContent() {
        // Remove event listener delegators that might have been added to removed elements
        const contentWrapper = document.querySelector('.content-wrapper');
        
        // Clone and replace to remove all event listeners on content
        if (contentWrapper) {
            const newWrapper = contentWrapper.cloneNode(false);
            newWrapper.style.opacity = contentWrapper.style.opacity;
            newWrapper.style.pointerEvents = contentWrapper.style.pointerEvents;
            contentWrapper.parentNode.replaceChild(newWrapper, contentWrapper);
        }
    }
    updateActiveLink(link) {
        // Remove active class from all nav links and nav items
        document.querySelectorAll('.nav-link.active, .nav-item.active').forEach(el => {
            el.classList.remove('active');
        });

        // Add active class to current link
        link.classList.add('active');

        // Update parent nav-item and collapse if it exists
        const navItem = link.closest('.nav-item');
        if (navItem) {
            navItem.classList.add('active');
            
            // If this is a sub-menu link, expand its parent collapse
            const collapse = navItem.querySelector('.collapse');
            if (collapse && !collapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(collapse, { toggle: true });
                bsCollapse.show();
            }
        }
        
        // Save active link to localStorage for persistence
        localStorage.setItem('activeNavLink', link.getAttribute('href'));
    }

    /**
     * Restore active link on page load
     */
    restoreActiveLink() {
        const currentUrl = window.location.pathname;
        const currentHost = window.location.host;
        let foundMatch = false;
        const links = document.querySelectorAll('.nav-link');
        
        // Create array of links with their paths for sorting
        const linkData = [];
        links.forEach(link => {
            const linkHref = link.getAttribute('href');
            if (!linkHref) return;
            
            try {
                const linkUrl = new URL(linkHref, window.location.origin);
                const linkPath = linkUrl.pathname;
                linkData.push({ link, linkPath, linkHref });
            } catch (e) {
                console.warn('Invalid URL:', linkHref);
            }
        });
        
        // Sort by path length (longest first) to match most specific routes first
        linkData.sort((a, b) => b.linkPath.length - a.linkPath.length);
        
        // Find first matching link
        for (const { link, linkPath, linkHref } of linkData) {
            // Check for exact match or route prefix match
            const isMatch = 
                currentUrl === linkPath || 
                currentUrl.startsWith(linkPath + '/');
            
            if (isMatch && !foundMatch) {
                console.log('✅ Active link:', linkHref);
                this.updateActiveLink(link);
                foundMatch = true;
                break;
            }
        }
    }

    /**
     * Show loading indicator
     */
    showLoadingState() {
        const contentWrapper = document.querySelector('.content-wrapper');
        if (contentWrapper) {
            // Add fade effect
            contentWrapper.style.transition = 'opacity 0.3s ease-in-out';
            contentWrapper.style.pointerEvents = 'none';
        }

        // Show a loading spinner
        const spinner = document.getElementById('ajaxLoadingSpinner');
        if (spinner) {
            spinner.style.display = 'flex';
            console.log('⏳ Loading spinner shown');
        }
    }

    /**
     * Hide loading indicator
     */
    hideLoadingState() {
        const contentWrapper = document.querySelector('.content-wrapper');
        if (contentWrapper) {
            contentWrapper.style.pointerEvents = 'auto';
        }

        // Hide the loading spinner
        const spinner = document.getElementById('ajaxLoadingSpinner');
        if (spinner) {
            spinner.style.display = 'none';
            console.log('✅ Loading spinner hidden');
        }
    }

    /**
     * Re-initialize event listeners after content is loaded
     * This ensures new links in loaded content also work with AJAX
     */
    reinitializeEventListeners() {
        // Re-bind form submissions if needed
        document.querySelectorAll('form').forEach(form => {
            if (!form.hasAttribute('data-ajax-initialized')) {
                form.setAttribute('data-ajax-initialized', 'true');
            }
        });

        // Restore active link styling for newly loaded content
        this.restoreActiveLink();

        // Wait longer to ensure all scripts are complete
        // Using Promise to chain multiple timeouts
        Promise.resolve()
            .then(() => {
                // First delay for script execution
                return new Promise(resolve => setTimeout(resolve, 100));
            })
            .then(() => {
                // Second delay for event listener attachment
                return new Promise(resolve => setTimeout(resolve, 100));
            })
            .then(() => {
                console.log('⏳ Triggering initialization events...');
                
                // Dispatch custom initialization event
                const initEvent = new CustomEvent('ajaxPageLoaded', {
                    detail: { timestamp: Date.now() }
                });
                document.dispatchEvent(initEvent);
                
                // Also trigger DOMContentLoaded-like event
                const domEvent = new Event('DOMContentLoaded', {
                    bubbles: true,
                    cancelable: true
                });
                document.dispatchEvent(domEvent);
                
                // Dispatch another event for compatibility
                const detailEvent = new CustomEvent('pageContentReloaded', {
                    detail: { timestamp: Date.now() }
                });
                document.dispatchEvent(detailEvent);
                
                console.log('✅ Page fully initialized - buttons and forms should be active');
                
                // Re-enable content interaction
                const contentWrapper = document.querySelector('.content-wrapper');
                if (contentWrapper) {
                    contentWrapper.style.pointerEvents = 'auto';
                }
            });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new AjaxNavigationHandler();
});
