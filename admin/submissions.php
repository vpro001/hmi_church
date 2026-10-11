<?php
/**
 * Admin Submissions Management
 */

$page_title = 'Submissions';
$currentPage = 'submissions';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$subId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';

if ($subId) {
    $sub = Database::fetchOne("SELECT * FROM submissions WHERE id = ?", [$subId]);
    if (!$sub) { flash('danger', 'Submission not found.'); redirect('admin/submissions.php'); }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
        else {
            if (isset($_POST['mark_read'])) {
                Database::execute("UPDATE submissions SET is_read = 1 WHERE id = ?", [$subId]);
                flash('success', 'Marked as read.');
            } elseif (isset($_POST['mark_unread'])) {
                Database::execute("UPDATE submissions SET is_read = 0 WHERE id = ?", [$subId]);
                flash('success', 'Marked as unread.');
            } elseif (isset($_POST['delete_sub'])) {
                Database::execute("DELETE FROM submissions WHERE id = ?", [$subId]);
                flash('success', 'Submission deleted.');
                redirect('admin/submissions.php');
            }
            redirect('admin/submissions.php?view=' . $subId);
        }
    }
}

$where = [];
$params = [];
if ($typeFilter) { $where[] = "type = ?"; $params[] = $typeFilter; }
if ($statusFilter === 'unread') { $where[] = "is_read = 0"; }
elseif ($statusFilter === 'read') { $where[] = "is_read = 1"; }
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$submissions = Database::fetchAll("
    SELECT * FROM submissions 
    $whereSql
    ORDER BY created_at DESC
    LIMIT 200
", $params);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0"><?= $subId ? 'Submission Details' : 'Submissions' ?></h2>
        <p class="text-muted mb-0"><?= $subId ? 'From ' . h($sub['name']) : 'Contact, counseling, and newsletter submissions' ?></p>
    </div>
    <?php if (!$subId): ?>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= h(url('admin/submissions.php')) ?>" class="btn btn-outline-secondary <?= !$typeFilter && !$statusFilter ? 'active' : '' ?>">All</a>
        <a href="<?= h(url('admin/submissions.php?type=contact')) ?>" class="btn btn-outline-primary <?= $typeFilter === 'contact' ? 'active' : '' ?>">Contact</a>
        <a href="<?= h(url('admin/submissions.php?type=counseling')) ?>" class="btn btn-outline-info <?= $typeFilter === 'counseling' ? 'active' : '' ?>">Counseling</a>
        <a href="<?= h(url('admin/submissions.php?type=newsletter')) ?>" class="btn btn-outline-success <?= $typeFilter === 'newsletter' ? 'active' : '' ?>">Newsletter</a>
        <a href="<?= h(url('admin/submissions.php?type=order')) ?>" class="btn btn-outline-warning <?= $typeFilter === 'order' ? 'active' : '' ?>">Orders</a>
        <a href="<?= h(url('admin/submissions.php?status=unread')) ?>" class="btn btn-outline-danger <?= $statusFilter === 'unread' ? 'active' : '' ?>">Unread</a>
    </div>
    <?php else: ?>
    <a href="<?= h(url('admin/submissions.php')) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
    <?php endif; ?>
</div>

<?php if ($subId): ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary me-2"><?= ucfirst($sub['type']) ?></span>
                    <strong><?= h($sub['name']) ?></strong>
                </div>
                <div>
                    <span class="badge bg-<?= $sub['is_read'] ? 'success' : 'danger' ?>"><?= $sub['is_read'] ? 'Read' : 'Unread' ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><strong>Email</strong><br><?= h($sub['email']) ?></div>
                    <div class="col-md-6"><strong>Phone</strong><br><?= h($sub['phone'] ?: '—') ?></div>
                    <div class="col-md-6"><strong>Subject</strong><br><?= h($sub['subject'] ?: '—') ?></div>
                    <div class="col-md-6"><strong>Date</strong><br><?= fmt_date($sub['created_at'], 'M j, Y g:i a') ?></div>
                </div>
                
                <h6>Message</h6>
                <div class="p-3 bg-light rounded"><?= nl2br(h($sub['message'])) ?></div>
                
                <?php if ($sub['metadata']): ?>
                <h6 class="mt-4">Additional Data</h6>
                <pre class="bg-dark text-light p-3 rounded small"><?= h(json_encode(json_decode($sub['metadata'], true), JSON_PRETTY_PRINT)) ?></pre>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="mb-0">Actions</h6></div>
            <div class="card-body">
                <form method="post" class="d-grid gap-2">
                    <?= csrf_field() ?>
                    <?php if (!$sub['is_read']): ?>
                    <button name="mark_read" class="btn btn-success">Mark as Read</button>
                    <?php else: ?>
                    <button name="mark_unread" class="btn btn-outline-secondary">Mark as Unread</button>
                    <?php endif; ?>
                    <hr>
                    <button name="delete_sub" class="btn btn-danger" data-confirm="Delete this submission?">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($submissions): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $s): ?>
                    <tr class="<?= !$s['is_read'] ? 'table-warning' : '' ?>">
                        <td><span class="badge bg-<?= $s['type'] === 'contact' ? 'primary' : ($s['type'] === 'counseling' ? 'info' : ($s['type'] === 'newsletter' ? 'success' : 'warning')) ?>"><?= ucfirst($s['type']) ?></span></td>
                        <td><?= h($s['name']) ?></td>
                        <td><?= h($s['email']) ?></td>
                        <td><?= h($s['subject'] ?: mb_substr($s['message'], 0, 50) . '...') ?></td>
                        <td><?= fmt_date($s['created_at'], 'M j, Y g:i a') ?></td>
                        <td><span class="badge bg-<?= $s['is_read'] ? 'success' : 'danger' ?>"><?= $s['is_read'] ? 'Read' : 'Unread' ?></span></td>
                        <td><a href="<?= h(url('admin/submissions.php?view=' . $s['id'])) ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-envelope"></i><h5>No submissions</h5></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>