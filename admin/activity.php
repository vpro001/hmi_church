<?php
/**
 * Admin Activity Log
 */

$page_title = 'Activity Log';
$currentPage = 'activity';
require_once __DIR__ . '/../includes/auth.php';
admin_require_role('superadmin', 'admin');
require_once __DIR__ . '/includes/header.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
$userType = $_GET['user_type'] ?? '';
$action = $_GET['action'] ?? '';
if ($userType) { $where[] = "user_type = ?"; $params[] = $userType; }
if ($action) { $where[] = "action = ?"; $params[] = $action; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = Database::fetchOne("SELECT COUNT(*) as c FROM activity_log $whereSql", $params)['c'] ?? 0;
$activities = Database::fetchAll("
    SELECT al.*, 
           CASE WHEN al.user_type = 'admin' THEN au.full_name ELSE u.full_name END as user_name
    FROM activity_log al
    LEFT JOIN admin_users au ON al.user_id = au.id AND al.user_type = 'admin'
    LEFT JOIN users u ON al.user_id = u.id AND al.user_type = 'customer'
    $whereSql
    ORDER BY al.created_at DESC
    LIMIT $perPage OFFSET $offset
", $params);

$userTypes = Database::fetchAll("SELECT DISTINCT user_type FROM activity_log ORDER BY user_type");
$actions = Database::fetchAll("SELECT DISTINCT action FROM activity_log ORDER BY action");
$totalPages = ceil($total / $perPage);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0">Activity Log</h2>
        <p class="text-muted mb-0">Track admin and customer activities</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">User Type</label>
                <select class="form-select" name="user_type">
                    <option value="">All</option>
                    <?php foreach ($userTypes as $t): ?>
                    <option value="<?= h($t['user_type']) ?>" <?= $userType === $t['user_type'] ? 'selected' : '' ?>><?= ucfirst($t['user_type']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Action</label>
                <select class="form-select" name="action">
                    <option value="">All</option>
                    <?php foreach ($actions as $a): ?>
                    <option value="<?= h($a['action']) ?>" <?= $action === $a['action'] ? 'selected' : '' ?>><?= ucfirst(str_replace('_', ' ', $a['action'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <button class="btn btn-forest" type="submit"><i class="bi bi-funnel me-2"></i> Filter</button>
                <a href="<?= h(url('admin/activity.php')) ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if ($activities): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $a): ?>
                    <tr>
                        <td>
                            <strong><?= h($a['user_name'] ?? 'Unknown') ?></strong>
                            <br><small class="text-muted">#<?= $a['user_id'] ?></small>
                        </td>
                        <td><span class="badge bg-<?= $a['user_type'] === 'admin' ? 'primary' : 'success' ?>"><?= ucfirst($a['user_type']) ?></span></td>
                        <td><code><?= h($a['action']) ?></code></td>
                        <td><?= h($a['description']) ?></td>
                        <td><small><?= h($a['ip_address']) ?></small></td>
                        <td><?= fmt_date($a['created_at'], 'M j, Y g:i a') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
            <nav><ul class="pagination mb-0 justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= h(url('admin/activity.php?page=' . $i . ($userType ? '&user_type=' . $userType : '') . ($action ? '&action=' . $action : ''))) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
            </ul></nav>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-activity"></i><h5>No activity logged</h5></div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>