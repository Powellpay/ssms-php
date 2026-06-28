// ─────────────────────────────────────────────────────────────────────────────
//  SSMS Uganda — App JavaScript
// ─────────────────────────────────────────────────────────────────────────────

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main    = document.querySelector('.main-content');
    if (!sidebar) return;

    if (window.innerWidth <= 900) {
        sidebar.classList.toggle('open');
    } else {
        sidebar.classList.toggle('collapsed');
        main && main.classList.toggle('expanded');
    }
}

// Auto-dismiss alerts after 5 s
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity .4s'; setTimeout(() => el.remove(), 400); }, 5000);
});

// Confirm destructive actions
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
});

// Live table search
const searchInput = document.getElementById('tableSearch');
if (searchInput) {
    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
