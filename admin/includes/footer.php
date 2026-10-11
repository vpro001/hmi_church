</main>
    </div>
</div>

<!-- Sidebar overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<script src="<?= h(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= h(asset('admin/js/admin.js')) ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load dashboard stats
    fetch('<?= h(url('admin/actions/stats.php')) ?>')
        .then(r => r.json())
        .then(data => {
            document.getElementById('productCount')?.textContent = data.products || 0;
            document.getElementById('pendingOrders')?.textContent = data.pendingOrders || 0;
            document.getElementById('unreadSubmissions')?.textContent = data.unreadSubmissions || 0;
        })
        .catch(console.error);
});
</script>
</body>
</html>