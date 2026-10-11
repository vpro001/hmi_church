<?php
/**
 * Admin Dashboard
 */

$page_title = 'Dashboard';
$currentPage = 'dashboard';
require_once __DIR__ . '/includes/header.php';

// Fetch stats
$stats = [
    'products' => Database::fetchOne("SELECT COUNT(*) as c FROM products WHERE is_active = 1")['c'] ?? 0,
    'orders_total' => Database::fetchOne("SELECT COUNT(*) as c FROM orders")['c'] ?? 0,
    'orders_pending' => Database::fetchOne("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'")['c'] ?? 0,
    'orders_paid' => Database::fetchOne("SELECT COUNT(*) as c FROM orders WHERE status = 'paid'")['c'] ?? 0,
    'customers' => Database::fetchOne("SELECT COUNT(*) as c FROM users WHERE is_active = 1")['c'] ?? 0,
    'submissions_unread' => Database::fetchOne("SELECT COUNT(*) as c FROM submissions WHERE is_read = 0")['c'] ?? 0,
    'posts_published' => Database::fetchOne("SELECT COUNT(*) as c FROM posts WHERE status = 'published'")['c'] ?? 0,
    'events_upcoming' => Database::fetchOne("SELECT COUNT(*) as c FROM events WHERE status = 'published' AND start_datetime > NOW()")['c'] ?? 0,
];

// Recent orders
$recentOrders = Database::fetchAll(
    "SELECT o.*, u.full_name as user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5"
);

// Recent submissions
$recentSubmissions = Database::fetchAll(
    "SELECT * FROM submissions ORDER BY created_at DESC LIMIT 5"
);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0">Dashboard</h2>
        <p class="text-muted mb-0">Welcome back, <?= h($admin['full_name']) ?>. Here's what's happening.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= h(url('admin/products.php')) ?>" class="btn btn-forest"><i class="bi bi-plus-lg me-2"></i> Add Product</a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon products"><i class="bi bi-book"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['products'] ?></div>
            <div class="stat-label">Active Products</div>
        </div>
        <a href="<?= h(url('admin/products.php')) ?>" class="btn btn-sm btn-outline-forest">Manage</a>
    </div>
    <div class="stat-card">
        <div class="stat-icon orders"><i class="bi bi-cart-check"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['orders_total'] ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <a href="<?= h(url('admin/orders.php')) ?>" class="btn btn-sm btn-outline-forest">View All</a>
    </div>
    <div class="stat-card">
        <div class="stat-icon orders" style="background: rgba(243,156,18,0.12); color: var(--yellow);"><i class="bi bi-hourglass"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['orders_pending'] ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <a href="<?= h(url('admin/orders.php?status=pending')) ?>" class="btn btn-sm btn-outline-forest">Review</a>
    </div>
    <div class="stat-card">
        <div class="stat-icon users"><i class="bi bi-people"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['customers'] ?></div>
            <div class="stat-label">Registered Customers</div>
        </div>
        <a href="<?= h(url('admin/users.php')) ?>" class="btn btn-sm btn-outline-forest">Manage</a>
    </div>
    <div class="stat-card">
        <div class="stat-icon submissions"><i class="bi bi-envelope"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['submissions_unread'] ?></div>
            <div class="stat-label">Unread Messages</div>
        </div>
        <a href="<?= h(url('admin/submissions.php')) ?>" class="btn btn-sm btn-outline-forest">Read</a>
    </div>
    <div class="stat-card">
        <div class="stat-icon users" style="background: rgba(52,152,219,0.12); color: #3498db;"><i class="bi bi-calendar-event"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['events_upcoming'] ?></div>
            <div class="stat-label">Upcoming Events</div>
        </div>
        <a href="<?= h(url('admin/events.php')) ?>" class="btn btn-sm btn-outline-forest">Manage</a>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= h(url('admin/products.php?action=add')) ?>" class="btn btn-outline-forest text-start"><i class="bi bi-book me-2"></i> Add New Product</a>
                    <a href="<?= h(url('admin/posts.php?action=add')) ?>" class="btn btn-outline-forest text-start"><i class="bi bi-journal-plus me-2"></i> Write New Post</a>
                    <a href="<?= h(url('admin/events.php?action=add')) ?>" class="btn btn-outline-forest text-start"><i class="bi bi-calendar-plus me-2"></i> Create Event</a>
                    <a href="<?= h(url('admin/pages.php?action=add')) ?>" class="btn btn-outline-forest text-start"><i class="bi bi-file-earmark-plus me-2"></i> Add Page</a>
                    <a href="<?= h(url('admin/settings.php')) ?>" class="btn btn-outline-secondary text-start"><i class="bi bi-gear me-2"></i> Site Settings</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php
                    $activities = Database::fetchAll("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10");
                    if ($activities):
                        foreach ($activities as $a):
                    ?>
                    <div class="list-group-item px-3 py-2 border-0 border-bottom">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?= h($a['action']) ?></strong>
                                <small class="text-muted ms-2"><?= h($a['description']) ?></small>
                            </div>
                            <small class="text-muted"><?= fmt_date($a['created_at'], 'M j, g:i a') ?></small>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="list-group-item px-3 py-4 text-center text-muted">No recent activity</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Submissions -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="<?= h(url('admin/orders.php')) ?>" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if ($recentOrders): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td><code><?= h($o['order_ref']) ?></code></td>
                                <td>
                                    <div><?= h($o['customer_name']) ?></div>
                                    <small class="text-muted"><?= h($o['customer_email']) ?></small>
                                </td>
                                <td><?= money($o['total']) ?></td>
                                <td><span class="badge bg-<?= $o['status'] === 'pending' ? 'pending' : ($o['status'] === 'paid' ? 'paid' : 'completed') ?>"><?= ucfirst($o['status']) ?></span></td>
                                <td><?= fmt_date($o['created_at'], 'M j, Y') ?></td>
                                <td>
                                    <a href="<?= h(url('admin/orders.php?view=' . $o['id'])) ?>" class="btn btn-action btn-outline-secondary" title="View">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state"><i class="bi bi-cart"></i><h6>No orders yet</h6><p class="mb-0">Orders will appear here when customers place them.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Messages</h5>
                <a href="<?= h(url('admin/submissions.php')) ?>" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if ($recentSubmissions): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentSubmissions as $s): ?>
                    <a href="<?= h(url('admin/submissions.php?view=' . $s['id'])) ?>" class="list-group-item list-group-item-action px-3 py-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 me-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary"><?= ucfirst($s['type']) ?></span>
                                    <strong><?= h($s['name']) ?></strong>
                                </div>
                                <small class="text-muted d-block mt-1"><?= h($s['email']) ?> • <?= h($s['subject'] ?: ($s['message'] ? mb_substr($s['message'], 0, 60) . '...' : '')) ?></small>
                            </div>
                            <small class="text-muted flex-shrink-0"><?= fmt_date($s['created_at'], 'M j, g:i a') ?></small>
                        </div>
                        <?php if (!$s['is_read']): ?>
                        <span class="badge bg-danger mt-2">Unread</span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state p-4"><i class="bi bi-envelope"></i><h6>No messages</h6><p class="mb-0">Contact form submissions will appear here.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>