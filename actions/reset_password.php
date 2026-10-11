<?php
/**
 * Reset password action
 */

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('forgot_password.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token.');
    redirect('forgot_password.php');
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (!$token || !$password || !$confirm) {
    flash('danger', 'All fields are required.');
    redirect('forgot_password.php');
}

if ($password !== $confirm) {
    flash('danger', 'Passwords do not match.');
    redirect('forgot_password.php?token=' . urlencode($token));
}

if (mb_strlen($password) < 8) {
    flash('danger', 'Password must be at least 8 characters.');
    redirect('forgot_password.php?token=' . urlencode($token));
}

$result = reset_password($token, $password);

if (isset($result['error'])) {
    flash('danger', $result['message']);
    redirect('forgot_password.php');
}

flash('success', $result['message']);
redirect('login.php');