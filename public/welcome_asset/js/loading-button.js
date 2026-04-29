/**
 * Loading Button Component
 * 
 * A reusable button component that shows a loading state with spinner
 * 
 * Usage:
 * 
 * 1. Add classes to your button:
 *    <button type="submit" class="btn-primary-custom btn-loading" data-loading-text="Processing...">
 *        <span class="btn-text">Sign In</span>
 *        <span class="btn-spinner"></span>
 *    </button>
 * 
 * 2. Initialize on form submit:
 *    LoadingButton.init(); // Auto-initializes all forms with .btn-loading buttons
 * 
 * 3. Or manually control:
 *    const btn = document.querySelector('.btn-loading');
 *    LoadingButton.start(btn);
 *    // Later: LoadingButton.stop(btn);
 * 
 * Options via data attributes:
 * - data-loading-text: Custom loading text (default: "Processing...")
 * - data-loading-timeout: Auto-stop after X milliseconds (optional)
 */

const LoadingButton = {
    /**
     * Initialize all forms with loading buttons
     */
    init: function() {
        document.addEventListener('DOMContentLoaded', () => {
            // Find all forms with loading buttons
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                const loadingBtn = form.querySelector('.btn-loading');
                
                if (loadingBtn) {
                    // Prevent multiple initializations
                    if (loadingBtn.dataset.loadingInitialized) return;
                    loadingBtn.dataset.loadingInitialized = 'true';
                    
                    form.addEventListener('submit', (e) => {
                        // Only start loading if form is valid
                        if (form.checkValidity()) {
                            this.start(loadingBtn);
                            
                            // Auto-stop after timeout if specified
                            const timeout = loadingBtn.dataset.loadingTimeout;
                            if (timeout) {
                                setTimeout(() => {
                                    this.stop(loadingBtn);
                                }, parseInt(timeout));
                            }
                        }
                    });
                    
                    // Stop loading on page show (back button)
                    window.addEventListener('pageshow', (event) => {
                        if (event.persisted) {
                            this.stop(loadingBtn);
                        }
                    });
                }
            });
        });
    },

    /**
     * Start loading state
     * @param {HTMLElement} button - The button element
     * @param {string} loadingText - Optional loading text
     */
    start: function(button, loadingText = null) {
        if (!button) return;
        
        // Store original text if not already stored
        if (!button.dataset.originalText) {
            const textElement = button.querySelector('.btn-text');
            if (textElement) {
                button.dataset.originalText = textElement.textContent;
            }
        }
        
        // Add loading class
        button.classList.add('loading');
        button.disabled = true;
        
        // Update text if custom loading text provided
        const textElement = button.querySelector('.btn-text');
        if (textElement && (loadingText || button.dataset.loadingText)) {
            textElement.textContent = loadingText || button.dataset.loadingText;
        }
    },

    /**
     * Stop loading state
     * @param {HTMLElement} button - The button element
     */
    stop: function(button) {
        if (!button) return;
        
        button.classList.remove('loading');
        button.disabled = false;
        
        // Restore original text
        const textElement = button.querySelector('.btn-text');
        if (textElement && button.dataset.originalText) {
            textElement.textContent = button.dataset.originalText;
        }
    },

    /**
     * Toggle loading state
     * @param {HTMLElement} button - The button element
     */
    toggle: function(button) {
        if (!button) return;
        
        if (button.classList.contains('loading')) {
            this.stop(button);
        } else {
            this.start(button);
        }
    },

    /**
     * Create a loading button element
     * @param {Object} options - Button configuration
     * @returns {HTMLElement} The created button element
     */
    create: function(options = {}) {
        const {
            text = 'Submit',
            loadingText = 'Processing...',
            type = 'submit',
            className = 'btn-primary-custom btn-loading',
            icon = null
        } = options;
        
        const button = document.createElement('button');
        button.type = type;
        button.className = className;
        button.dataset.loadingText = loadingText;
        
        // Add icon if provided
        if (icon) {
            const iconEl = document.createElement('i');
            iconEl.className = icon + ' btn-icon';
            button.appendChild(iconEl);
        }
        
        // Add text
        const textSpan = document.createElement('span');
        textSpan.className = 'btn-text';
        textSpan.textContent = text;
        button.appendChild(textSpan);
        
        // Add spinner
        const spinner = document.createElement('span');
        spinner.className = 'btn-spinner';
        button.appendChild(spinner);
        
        return button;
    }
};

// Auto-initialize on page load
LoadingButton.init();

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LoadingButton;
}
