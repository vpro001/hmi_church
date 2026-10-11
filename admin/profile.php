<?php
/**
 * Admin Profile
 */

$page_title = 'Profile';
$currentPage = 'profile';
require_once __DIR__ . '/includes/header.php';

$adminData = admin_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        if (isset($_POST['update_profile'])) {
            $name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            if ($name && $email) {
                Database::execute("UPDATE admin_users SET full_name = ?, email = ? WHERE id = ?", [$name, $email, $adminData['id']]);
                $_SESSION['admin_name'] = $name;
                flash('success', 'Profile updated.');
            } else {
                flash('danger', 'Name and email are required.');
            }
        } elseif (isset($_POST['change_password'])) {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            $user = Database::fetchOne("SELECT password_hash FROM admin_users WHERE id = ?", [$adminData['id']]);
            if ($current !== $user['password_hash']) {
                flash('danger', 'Current password is incorrect.');
            } elseif ($new !== $confirm) {
                flash('danger', 'New passwords do not match.');
            } elseif (strlen($new) < 8) {
                flash('danger', 'Password must be at least 8 characters.');
            } else {
                Database::execute("UPDATE admin_users SET password_hash = ? WHERE id = ?", [$new, $adminData['id']]);
                flash('success', 'Password changed.');
            }
        }
        redirect('admin/profile.php');
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="h3 mb-0">My Profile</h2>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar-circle mx-auto mb-3" style="width: 100px; height: 100px; font-size: 3rem;"><?= h(strtoupper($adminData['full_name'][0])) ?></div>
                <h4><?= h($adminData['full_name']) ?></h4>
                <p class="text-muted mb-1"><?= h($adminData['username']) ?></p>
                <span class="badge bg-<?= $adminData['role'] === 'superadmin' ? 'danger' : 'primary' ?>"><?= ucfirst($adminData['role']) ?></span>
                <hr>
                <p class="mb-1"><small class="text-muted">Last login</small></p>
                <p class="mb-0"><?= $adminData['last_login'] ? fmt_date($adminData['last_login'], 'M j, Y g:i a') : 'Never' ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Profile Information</h5></div>
            <div class="card-body">
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="update_profile" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input class="form-control" name="full_name" value="<?= h($adminData['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input class="form-control" value="<?= h($adminData['username']) ?>" readonly>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" value="<?= h($adminData['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input class="form-control" value="<?= ucfirst($adminData['role']) ?>" readonly>
                        </div>
                    </div>
                    <button class="btn btn-forest mt-3" type="submit">Save Changes</button>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header"><h5 class="mb-0">Change Password</h5></div>
            <div class="card-body">
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="change_password" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Current Password</label>
                            <input class="form-control" type="password" name="current_password" required autocomplete="current-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input class="form-control" type="password" name="new_password" required minlength="8" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input class="form-control" type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
                        </div>
                    </div>
                    <button class="btn btn-outline-secondary mt-3" type="submit">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>