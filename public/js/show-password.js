document.addEventListener('DOMContentLoaded', function () {
    // Find all password inputs on the page
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    passwordInputs.forEach((input, index) => {
        // Skip if this input already has a toggle (e.g. injected by another component)
        if (input.dataset.hasPasswordToggle || input.id === 'password_confirmation') return;
        
        // Special case: if we have a confirmation field, we want a single toggle to control both
        const confirmInput = document.querySelector('input[type="password"][name*="confirm"], input[type="password"][id*="confirm"]');
        
        // Create the checkbox container
        const container = document.createElement('div');
        
        // Detect layout style
        const isCustomCheckbox = document.querySelector('.custom-checkbox') !== null;
        container.className = isCustomCheckbox ? 'custom-checkbox mt-1' : 'form-check mt-1';
        
        const checkboxId = `show-password-toggle-${index}`;
        const labelText = confirmInput ? 'Show Passwords' : 'Show Password';
        
        // Build the checkbox HTML
        container.innerHTML = `
            <input type="checkbox" class="${isCustomCheckbox ? '' : 'form-check-input'}" id="${checkboxId}">
            <label class="${isCustomCheckbox ? '' : 'form-check-label small text-muted'}" for="${checkboxId}" style="${isCustomCheckbox ? 'font-size: 0.85rem; color: var(--text-muted);' : ''}">
                ${labelText}
            </label>
        `;
        
        // Insert container right after the input's parent/wrapper
        const wrapper = input.closest('.input-wrapper') || input.closest('.sp-input-wrap') || input;
        wrapper.parentNode.insertBefore(container, wrapper.nextSibling);
        
        // Mark both fields as having a toggle
        input.dataset.hasPasswordToggle = "true";
        if (confirmInput) {
            confirmInput.dataset.hasPasswordToggle = "true";
        }
        
        // Attach the event listener
        const checkbox = container.querySelector('input');
        checkbox.addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            input.type = type;
            if (confirmInput) {
                confirmInput.type = type;
            }
        });
    });
});
