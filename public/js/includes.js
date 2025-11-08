// Load Header and Footer
document.addEventListener('DOMContentLoaded', function() {
    // Determine base URL based on current page location
    const isInPagesFolder = window.location.pathname.includes('/pages/');
    const baseUrl = isInPagesFolder ? '../' : '';
    
    // Load Header
    fetch(`${baseUrl}includes/header.html`)
        .then(response => response.text())
        .then(data => {
            // Replace {BASE_URL} placeholder with actual base URL
            const headerHTML = data.replace(/{BASE_URL}/g, baseUrl);
            document.getElementById('header-placeholder').innerHTML = headerHTML;
            
            // Update cart count after header is loaded
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        })
        .catch(error => console.error('Error loading header:', error));
    
    // Load Footer
    fetch(`${baseUrl}includes/footer.html`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-placeholder').innerHTML = data;
        })
        .catch(error => console.error('Error loading footer:', error));
});
