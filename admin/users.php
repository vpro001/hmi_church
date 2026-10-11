<?php
/**
 * Admin Users (Customers) Management
 */

$page_title = 'Customers';
$currentPage = 'users';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action === 'delete' && $userId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        Database::execute("DELETE FROM users WHERE id = ?", [$userId]);
        log_activity($admin['id'], 'admin', 'user_delete', "Deleted user ID: $userId");
        flash('success', 'Customer deleted.');
    }
    redirect('admin/users.php');
}

if ($action === 'view' && $userId) {
    $user = Database::fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    if (!$user) { flash('danger', 'User not found.'); redirect('admin/users.php'); }
    
    $orders = Database::fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
}

$users = Database::fetchAll("
    SELECT u.*, 
           (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
           (SELECT SUM(total) FROM orders WHERE user_id = u.id AND status IN ('paid','completed')) as total_spent
    FROM users u
    WHERE u.is_active = 1
    ORDER BY u.created_at DESC
    LIMIT 200
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0">Customers</h2>
        <p class="text-muted mb-0">Registered customers and their orders</p>
    </div>
</div>

<?php if ($action === 'view' && $userId): ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;"><?= h(strtoupper($user['full_name'][0])) ?></div>
                <h4><?= h($user['full_name']) ?></h4>
                <p class="text-muted mb-1"><?= h($user['email']) ?></p>
                <?php if ($user['phone']): ?><p class="text-muted mb-3"><?= h($user['phone']) ?></p><?php endif; ?>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-<?= $user['is_verified'] ? 'success' : 'warning' ?>"><?= $user['is_verified'] ? 'Verified' : 'Unverified' ?></span>
                    <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></span>
                </div>
                <small class="text-muted">Joined: <?= fmt_date($user['created_at'], 'M j, Y') ?></small>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header"><h6 class="mb-0">Stats</h6></div>
            <div class="card-body">
                <p class="mb-1"><strong>Orders:</strong> <?= (int)$user['order_count'] ?></p>
                <p class="mb-0"><strong>Total Spent:</strong> <?= money((int)($user['total_spent'] ?? 0)) ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order History</h5>
            </div>
            <div class="card-body p-0">
                <?php if ($orders): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>Reference</th><th>Date</th><th>Status</th><th>Total</th><th></th></tr></thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                            <tr>
                                <td><code><?= h($o['order_ref']) ?></code></td>
                                <td><?= fmt_date($o['created_at'], 'M j, Y') ?></td>
                                <td><span class="badge bg-<?= $o['status'] === 'pending' ? 'pending' : ($o['status'] === 'paid' ? 'paid' : 'completed') ?>"><?= ucfirst($o['status']) ?></span></td>
                                <td><?= money($o['total']) ?></td>
                                <td><a href="<?= h(url('admin/orders.php?view=' . $o['id'])) ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state p-4"><i class="bi bi-cart"></i><h6>No orders yet</h6></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($users): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <strong><?= h($u['full_name']) ?></strong>
                            <br><small class="text-muted">#<?= $u['id'] ?></small>
                        </td>
                        <td><?= h($u['email']) ?></td>
                        <td><?= h($u['phone'] ?: '—') ?></td>
                        <td><?= (int)$u['order_count'] ?></td>
                        <td><?= money((int)($u['total_spent'] ?? 0)) ?></td>
                        <td>
                            <span class="badge bg-<?= $u['is_verified'] ? 'success' : 'warning' ?>"><?= $u['is_verified'] ? 'Verified' : 'Unverified' ?></span>
                        </td>
                        <td><?= fmt_date($u['created_at'], 'M j, Y') ?></td>
                        <td>
                            <a href="<?= h(url('admin/users.php?action=view&id=' . $u['id'])) ?>" class="btn btn-sm btn-outline-secondary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-people"></i><h5>No customers yet</h5><p class="mb-0">Customers will appear after they register or place orders.</p></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>