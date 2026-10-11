<?php
/**
 * Admin login action
 */

require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/login.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token. Please try again.');
    redirect('admin/login.php');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    flash('danger', 'Please enter both username and password.', ['username' => $username]);
    redirect('admin/login.php');
}

$result = admin_login($username, $password);

if ($result) {
    flash('success', 'Welcome back, ' . $result['full_name'] . '!');
    redirect('admin/index.php');
} else {
    flash('danger', 'Invalid username or password.', ['username' => $username]);
    redirect('admin/login.php');
}