/* js/app.js — Decorum Bookshop */

document.addEventListener('DOMContentLoaded', function () {

    /* ── SIDEBAR TOGGLE ── */
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar   = document.getElementById('mainSidebar');
    const overlay   = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        sidebar.classList.remove('collapsed');
        if (overlay) {
            overlay.style.display = 'block';
            // small delay so display:block takes effect before opacity transition
            requestAnimationFrame(() => overlay.classList.add('visible'));
        }
        if (toggleBtn) toggleBtn.classList.add('active');
        document.body.style.overflow = window.innerWidth < 900 ? 'hidden' : '';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) {
            overlay.classList.remove('visible');
            // wait for fade-out then hide
            overlay.addEventListener('transitionend', function hide() {
                overlay.style.display = 'none';
                overlay.removeEventListener('transitionend', hide);
            });
        }
        if (toggleBtn) toggleBtn.classList.remove('active');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('open');
        if (isOpen) {
            closeSidebar();
            sidebar.classList.add('collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            openSidebar();
        }
    }

    /* Desktop: sidebar open by default */
    if (window.innerWidth >= 900) {
        openSidebar();
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', closeSidebar);
    }

    /* Escape key closes sidebar */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    /* Close sidebar when a nav link is clicked on mobile */
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-nav a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 900) closeSidebar();
            });
        });
    }

    /* Resize: auto-open on desktop, auto-close on mobile */
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (window.innerWidth >= 900) {
                if (!sidebar.classList.contains('collapsed')) openSidebar();
                document.body.style.overflow = '';
            } else {
                if (!sidebar.classList.contains('open')) closeSidebar();
            }
        }, 100);
    });

    /* ── PRODUCT SEARCH FILTER ── */
    const productSearch = document.getElementById('productSearch');
    if (productSearch) {
        productSearch.addEventListener('input', function (e) {
            const q = e.target.value.trim().toLowerCase();
            document.querySelectorAll('.product-card').forEach(function (card) {
                card.style.display = card.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    /* ── TABLE ROW SEARCH (users, requisitions) ── */
    const tableSearch = document.querySelector('.search-bar input[type="text"]');
    if (tableSearch) {
        tableSearch.addEventListener('input', function (e) {
            const q = e.target.value.trim().toLowerCase();
            document.querySelectorAll('tbody tr').forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    /* ── FLASH MESSAGE AUTO-DISMISS ── */
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity   = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

});
