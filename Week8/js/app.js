
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('mainSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function() {
            const isOpen = sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('visible', isOpen);
        });
    }

    // Close the sidebar when overlay is clicked
    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('visible');
        });
    }

    // Allow Escape to close the sidebar on small screens
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('visible');
        }
    });

    // Generic table search support for any input marked with data-table-search
    document.querySelectorAll('input[data-table-search]').forEach(function (input) {
        const tableId = input.getAttribute('data-table-search');
        const table = document.getElementById(tableId);
        if (!table) return;

        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = query === '' || text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    });

    // Generic card search support for any input marked with data-card-search
    document.querySelectorAll('input[data-card-search]').forEach(function (input) {
        const cards = document.querySelectorAll('.product-card');
        if (!cards.length) return;

        input.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            cards.forEach(function (card) {
                const text = (card.innerText || '').toLowerCase();
                card.style.display = query === '' || text.indexOf(query) !== -1 ? '' : 'none';
            });
        });
    });
});