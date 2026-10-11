<?php
/**
 * Customer profile update action
 */

require_once __DIR__ . '/../includes/auth.php';

customer_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['update_profile'])) {
    redirect('account.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token.');
    redirect('account.php#profile');
}

$name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('danger', 'Name and valid email are required.');
    redirect('account.php#profile');
}

// Check if email is taken by another user
$existing = Database::fetchOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $_SESSION['customer_id']]);
if ($existing) {
    flash('danger', 'This email is already in use.');
    redirect('account.php#profile');
}

Database::execute("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?", [$name, $email, $phone, $address, $_SESSION['customer_id']]);
$_SESSION['customer_name'] = $name;
$_SESSION['customer_email'] = $email;

flash('success', 'Profile updated successfully.');
redirect('account.php#profile');