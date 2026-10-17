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

<?php
// First name for a warmer greeting
$firstName = trim(explode(' ', $admin['full_name'])[0] ?? '');
// Flag anything that needs a human today
$needsAttention = ((int)$stats['orders_pending'] + (int)$stats['submissions_unread']) > 0;
$today = fmt_date(date('Y-m-d'), 'l, F j');
?>

<!-- Welcome band -->
<div class="welcome-band">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
        <div>
            <p class="eyebrow">Dashboard</p>
            <h2>Welcome back, <em><?= h($firstName) ?></em>.</h2>
            <p><?= h($today) ?> &mdash; <?= $needsAttention
                ? 'there are items waiting for your attention below.'
                : 'everything is up to date. Thank you for serving the ministry.' ?></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= h(url('admin/livestreams.php')) ?>" class="btn btn-outline-forest border-light" style="color:#eaf2ee; border-color:rgba(255,255,255,.3)!important;">
                <i class="bi bi-broadcast me-1"></i> Live Stream
            </a>
            <a href="<?= h(url('admin/index.php?action=refresh')) ?>" class="btn btn-outline-forest border-light" style="color:#eaf2ee; border-color:rgba(255,255,255,.3)!important;">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </a>
            <a href="<?= h(url('admin/products.php?action=add')) ?>" class="btn btn-forest" style="background:var(--gold); border-color:var(--gold); color:var(--forest-950);">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
        </div>
    </div>
</div>

<?php $adminLive = get_current_live(); ?>
<?php if ($adminLive): ?>
<div class="card border-danger border-2 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-body p-3 bg-danger bg-opacity-10 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-danger pulse-live py-2 px-3 rounded-pill fw-bold">
                <span class="live-dot-pulse me-1"></span> LIVE ON AIR
            </span>
            <div>
                <strong class="text-dark"><?= h($adminLive['title']) ?></strong>
                <span class="text-muted small ms-2">(<?= h(livestream_platform_label($adminLive['platform'])) ?>)</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= h(url('live.php')) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Page
            </a>
            <a href="<?= h(url('admin/livestreams.php')) ?>" class="btn btn-sm btn-danger">
                <i class="bi bi-broadcast me-1"></i> Control Stream
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Needs attention -->
<?php if ($needsAttention): ?>
<div class="row g-3 mb-4">
    <?php if ((int)$stats['orders_pending'] > 0): ?>
    <div class="col-md-6">
        <div class="attn-card">
            <div class="attn-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="attn-body">
                <strong><?= (int)$stats['orders_pending'] ?></strong>
                <span>order<?= ((int)$stats['orders_pending'] == 1) ? '' : 's' ?> awaiting confirmation</span>
            </div>
            <a href="<?= h(url('admin/orders.php?status=pending')) ?>" class="btn btn-sm btn-forest">Review</a>
        </div>
    </div>
    <?php endif; ?>
    <?php if ((int)$stats['submissions_unread'] > 0): ?>
    <div class="col-md-6">
        <div class="attn-card">
            <div class="attn-icon"><i class="bi bi-envelope-exclamation"></i></div>
            <div class="attn-body">
                <strong><?= (int)$stats['submissions_unread'] ?></strong>
                <span>unread message<?= ((int)$stats['submissions_unread'] == 1) ? '' : 's' ?> from the contact forms</span>
            </div>
            <a href="<?= h(url('admin/submissions.php')) ?>" class="btn btn-sm btn-forest">Read</a>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Stats Grid -->
<div class="stat-grid">
    <a class="stat-card" href="<?= h(url('admin/orders.php')) ?>">
        <div class="stat-icon orders"><i class="bi bi-cart-check"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['orders_total'] ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
    </a>
    <a class="stat-card" href="<?= h(url('admin/users.php')) ?>">
        <div class="stat-icon users"><i class="bi bi-people"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['customers'] ?></div>
            <div class="stat-label">Registered Customers</div>
        </div>
    </a>
    <a class="stat-card" href="<?= h(url('admin/products.php')) ?>">
        <div class="stat-icon products"><i class="bi bi-book"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['products'] ?></div>
            <div class="stat-label">Active Products</div>
        </div>
    </a>
    <a class="stat-card" href="<?= h(url('admin/orders.php?status=pending')) ?>">
        <div class="stat-icon attn"><i class="bi bi-hourglass"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['orders_pending'] ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </a>
    <a class="stat-card" href="<?= h(url('admin/submissions.php')) ?>">
        <div class="stat-icon submissions"><i class="bi bi-envelope"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['submissions_unread'] ?></div>
            <div class="stat-label">Unread Messages</div>
        </div>
    </a>
    <a class="stat-card" href="<?= h(url('admin/events.php')) ?>">
        <div class="stat-icon events"><i class="bi bi-calendar-event"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= (int)$stats['events_upcoming'] ?></div>
            <div class="stat-label">Upcoming Events</div>
        </div>
    </a>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header">
                <p class="eyebrow mb-1">Shortcuts</p>
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="quick-grid">
                    <a href="<?= h(url('admin/products.php?action=add')) ?>"><i class="bi bi-book"></i> Add New Product</a>
                    <a href="<?= h(url('admin/posts.php?action=add')) ?>"><i class="bi bi-journal-plus"></i> Write New Post</a>
                    <a href="<?= h(url('admin/events.php?action=add')) ?>"><i class="bi bi-calendar-plus"></i> Create Event</a>
                    <a href="<?= h(url('admin/pages.php?action=add')) ?>"><i class="bi bi-file-earmark-plus"></i> Add Page</a>
                    <a href="<?= h(url('admin/settings.php')) ?>"><i class="bi bi-gear"></i> Site Settings</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <p class="eyebrow mb-1">Audit trail</p>
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <?php
                $activities = Database::fetchAll("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 8");
                if ($activities): ?>
                <ul class="timeline mb-0">
                    <?php foreach ($activities as $a): ?>
                    <li>
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <strong style="color:var(--forest-900);"><?= h($a['action']) ?></strong>
                                <span class="text-muted ms-2"><?= h($a['description']) ?></span>
                            </div>
                            <small class="text-muted flex-shrink-0"><?= fmt_date($a['created_at'], 'M j, g:i a') ?></small>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="empty-state py-4"><i class="bi bi-activity"></i><h6 class="mb-0">No recent activity</h6></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Submissions -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <p class="eyebrow mb-1">Commerce</p>
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
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
                <div>
                    <p class="eyebrow mb-1">Inbox</p>
                    <h5 class="mb-0">Recent Messages</h5>
                </div>
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