
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            const isCollapsed = sidebar.classList.toggle('collapsed');
            if (overlay) overlay.style.display = isCollapsed ? 'none' : 'block';
        });
    }

    // Close the sidebar when overlay is clicked
    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.add('collapsed');
            overlay.style.display = 'none';
        });
    }

    // Allow Escape to close the sidebar on small screens
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && !sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('collapsed');
            if (overlay) overlay.style.display = 'none';
        }
    });

    // Product search filter (restores the Week7 table search behavior for the grid)
    const productSearch = document.getElementById('productSearch');
    if (productSearch) {
        productSearch.addEventListener('input', function(e) {
            const q = e.target.value.trim().toLowerCase();
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                const text = (card.innerText || '').toLowerCase();
                card.style.display = text.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }
});