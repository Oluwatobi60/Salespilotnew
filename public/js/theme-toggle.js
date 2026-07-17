document.addEventListener('DOMContentLoaded', () => {
    const toggleBtns = document.querySelectorAll('.theme-toggle-btn');
    
    // Set initial icon based on current theme
    const isDarkMode = document.documentElement.classList.contains('dark-mode') || document.body.classList.contains('dark-mode');
    
    toggleBtns.forEach(btn => {
        updateToggleIcon(btn, isDarkMode);
        
        btn.addEventListener('click', () => {
            const willBeDark = !document.documentElement.classList.contains('dark-mode');
            
            if (willBeDark) {
                document.documentElement.classList.add('dark-mode');
                document.body.classList.add('dark-mode'); // For fallback
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark-mode');
                document.body.classList.remove('dark-mode');
                localStorage.setItem('theme', 'light');
            }
            
            toggleBtns.forEach(b => updateToggleIcon(b, willBeDark));
        });
    });
    
    function updateToggleIcon(btn, isDark) {
        if (isDark) {
            btn.innerHTML = '<i class="bi bi-sun-fill"></i>';
            btn.setAttribute('title', 'Switch to Light Mode');
        } else {
            btn.innerHTML = '<i class="bi bi-moon-stars-fill"></i>';
            btn.setAttribute('title', 'Switch to Dark Mode');
        }
    }
});
