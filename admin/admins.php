<?php
/**
 * Admin Users Management
 */

$page_title = 'Admin Users';
$currentPage = 'admins';
require_once __DIR__ . '/../includes/auth.php';
admin_require_role('superadmin');
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$adminId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_admin'])) {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'role' => $_POST['role'] ?? 'admin',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        if ($action === 'edit' && $adminId) {
            if (!empty($_POST['password'])) {
                Database::execute("UPDATE admin_users SET username=?, email=?, full_name=?, role=?, is_active=?, password_hash=? WHERE id=?", 
                    [$data['username'], $data['email'], $data['full_name'], $data['role'], $data['is_active'], $_POST['password'], $adminId]);
            } else {
                Database::execute("UPDATE admin_users SET username=?, email=?, full_name=?, role=?, is_active=? WHERE id=?", 
                    [$data['username'], $data['email'], $data['full_name'], $data['role'], $data['is_active'], $adminId]);
            }
            log_activity($admin['id'], 'admin', 'admin_update', "Updated admin: {$data['username']}");
            flash('success', 'Admin updated.');
        } else {
            if (empty($_POST['password'])) { flash('danger', 'Password is required for new admin.'); redirect('admin/admins.php?action=add'); }
            Database::execute("INSERT INTO admin_users (username, email, password_hash, full_name, role, is_active) VALUES (?, ?, ?, ?, ?, ?)",
                [$data['username'], $data['email'], $_POST['password'], $data['full_name'], $data['role'], $data['is_active']]);
            log_activity($admin['id'], 'admin', 'admin_create', "Created admin: {$data['username']}");
            flash('success', 'Admin created.');
        }
        redirect('admin/admins.php');
    }
}

if ($action === 'delete' && $adminId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    elseif ($adminId === $_SESSION['admin_id']) flash('danger', 'Cannot delete yourself.');
    else {
        Database::execute("DELETE FROM admin_users WHERE id = ?", [$adminId]);
        log_activity($admin['id'], 'admin', 'admin_delete', "Deleted admin ID: $adminId");
        flash('success', 'Admin deleted.');
    }
    redirect('admin/admins.php');
}

$editAdmin = null;
if ($action === 'edit' && $adminId) {
    $editAdmin = Database::fetchOne("SELECT * FROM admin_users WHERE id = ?", [$adminId]);
    if (!$editAdmin) { flash('danger', 'Admin not found.'); redirect('admin/admins.php'); }
}

$admins = Database::fetchAll("SELECT id, username, email, full_name, role, is_active, last_login, created_at FROM admin_users ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0">Admin Users</h2>
        <p class="text-muted mb-0">Manage administrator accounts</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="<?= h(url('admin/admins.php?action=add')) ?>" class="btn btn-forest"><i class="bi bi-plus-lg me-2"></i> Add Admin</a>
    <?php else: ?>
    <a href="<?= h(url('admin/admins.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($admins): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $a): ?>
                    <tr>
                        <td><code><?= h($a['username']) ?></code></td>
                        <td><?= h($a['full_name']) ?></td>
                        <td><?= h($a['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $a['role'] === 'superadmin' ? 'danger' : ($a['role'] === 'admin' ? 'primary' : 'secondary') ?>">
                                <?= ucfirst($a['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $a['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $a['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td><?= $a['last_login'] ? fmt_date($a['last_login'], 'M j, Y g:i a') : 'Never' ?></td>
                        <td>
                            <?php if ($a['id'] !== $_SESSION['admin_id']): ?>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= h(url('admin/admins.php?action=edit&id=' . $a['id'])) ?>" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-outline-danger" data-confirm="Delete admin '<?= h($a['username']) ?>'?" onclick="deleteItem(<?= $a['id'] ?>, 'admins')"><i class="bi bi-trash"></i></button>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">Current</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-person-badge"></i><h5>No admin users</h5></div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body">
        <form method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="save_admin" value="1">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input class="form-control" name="username" required value="<?= h($editAdmin['username'] ?? '') ?>" <?= $editAdmin ? 'readonly' : '' ?>>
                    <?php if ($editAdmin): ?><small class="text-muted">Username cannot be changed</small><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input class="form-control" type="email" name="email" required value="<?= h($editAdmin['email'] ?? '') ?>">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input class="form-control" name="full_name" required value="<?= h($editAdmin['full_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role">
                        <option value="editor" <?= ($editAdmin['role'] ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
                        <option value="admin" <?= ($editAdmin['role'] ?? 'admin') === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="superadmin" <?= ($editAdmin['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label"><?= $editAdmin ? 'New Password (leave blank to keep current)' : 'Password <span class="text-danger">*</span>' ?></label>
                    <input class="form-control" type="password" name="password" <?= $editAdmin ? '' : 'required' ?> autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <input class="form-control" type="password" name="password_confirm" autocomplete="new-password">
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($editAdmin['is_active']) || $editAdmin['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-forest" type="submit"><i class="bi bi-save me-2"></i> Save Admin</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function deleteItem(id, type) {
    if (!confirm("Delete this item?")) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= h(url('admin/')) ?>' + type + '.php?action=delete&id=' + id;
    form.innerHTML = '<?= csrf_field() ?>';
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>