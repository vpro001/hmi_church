<?php
/**
 * Customer password change action
 */

require_once __DIR__ . '/../includes/auth.php';

customer_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['change_password'])) {
    redirect('account.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token.');
    redirect('account.php#security');
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$user = Database::fetchOne("SELECT password_hash FROM users WHERE id = ?", [$_SESSION['customer_id']]);
if (!$user || $current !== $user['password_hash']) {
    flash('danger', 'Current password is incorrect.');
    redirect('account.php#security');
}

if ($new !== $confirm) {
    flash('danger', 'New passwords do not match.');
    redirect('account.php#security');
}

if (mb_strlen($new) < 8) {
    flash('danger', 'Password must be at least 8 characters.');
    redirect('account.php#security');
}

Database::execute("UPDATE users SET password_hash = ? WHERE id = ?", [$new, $_SESSION['customer_id']]);

flash('success', 'Password changed successfully.');
redirect('account.php#security');