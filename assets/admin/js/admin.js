/**
 * Admin Panel JavaScript
 * Healing My-Marriage International
 */

(function() {
    'use strict';

    // Sidebar toggle for mobile
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        // Close sidebar when clicking a link on mobile
        document.querySelectorAll('.sidebar-nav .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('show');
                }
            });
        });
    }

    // Confirm delete actions
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-confirm]');
        if (btn) {
            const msg = btn.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });

    // Auto-dismiss flash messages
    setTimeout(function() {
        document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
            try {
                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            } catch (e) {}
        });
    }, 8000);

    // Form validation enhancement
    document.querySelectorAll('form.needs-validation').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // Image preview for file inputs
    document.addEventListener('change', function(e) {
        if (e.target.matches('input[type="file"][data-preview]')) {
            const previewId = e.target.getAttribute('data-preview');
            const preview = document.getElementById(previewId);
            const file = e.target.files[0];
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Sortable table rows (simple)
    document.querySelectorAll('th[data-sort]').forEach(function(th) {
        th.style.cursor = 'pointer';
        th.addEventListener('click', function() {
            const table = th.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const colIndex = Array.from(th.parentNode.children).indexOf(th);
            const isAsc = th.classList.toggle('sort-asc');
            th.classList.toggle('sort-desc', !isAsc);

            rows.sort(function(a, b) {
                const aVal = a.children[colIndex]?.textContent.trim() || '';
                const bVal = b.children[colIndex]?.textContent.trim() || '';
                return isAsc ? aVal.localeCompare(bVal, undefined, {numeric: true}) : bVal.localeCompare(aVal, undefined, {numeric: true});
            });

            rows.forEach(function(row) { tbody.appendChild(row); });
        });
    });

    // Search/filter functionality
    window.setupTableFilter = function(searchId, tableId) {
        const search = document.getElementById(searchId);
        const table = document.getElementById(tableId);
        if (!search || !table) return;

        const rows = Array.from(table.querySelectorAll('tbody tr'));

        search.addEventListener('input', function() {
            const q = search.value.toLowerCase();
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    };

    // Delete with AJAX
    window.deleteItem = function(url, rowId, message) {
        if (!confirm(message || 'Delete this item?')) return;
        fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById(rowId);
                    if (row) row.remove();
                    showToast('Deleted successfully', 'success');
                } else {
                    showToast(data.message || 'Delete failed', 'danger');
                }
            })
            .catch(() => showToast('Error occurred', 'danger'));
    };

    // Toast notifications
    window.showToast = function(message, type) {
        const container = document.getElementById('toastContainer') || createToastContainer();
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
        container.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    };

    function createToastContainer() {
        const div = document.createElement('div');
        div.id = 'toastContainer';
        div.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        div.style.zIndex = '1080';
        document.body.appendChild(div);
        return div;
    }

    // Format currency
    window.formatMoney = function(amount) {
        return '₦' + Number(amount).toLocaleString('en-NG');
    };

})();