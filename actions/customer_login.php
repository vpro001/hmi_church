<?php
/**
 * Customer login action
 */

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$redirect = trim($_POST['redirect'] ?? '');

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token. Please try again.');
    redirect('login.php' . (!empty($redirect) ? '?redirect=' . urlencode($redirect) : ''));
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    flash('danger', 'Please enter both email and password.', ['email' => $email]);
    redirect('login.php' . (!empty($redirect) ? '?redirect=' . urlencode($redirect) : ''));
}

$result = customer_login($email, $password);

if (!$result) {
    flash('danger', 'Invalid email or password.', ['email' => $email]);
    redirect('login.php' . (!empty($redirect) ? '?redirect=' . urlencode($redirect) : ''));
}

if (isset($result['error'])) {
    flash('danger', $result['message'], ['email' => $email]);
    redirect('login.php' . (!empty($redirect) ? '?redirect=' . urlencode($redirect) : ''));
}

flash('success', 'Welcome back, ' . $result['full_name'] . '!');

// Safe redirect back
$target = 'account.php';
if (!empty($redirect)) {
    // Only accept relative paths or local URLs
    if (!preg_match('#^https?://#i', $redirect) || str_starts_with($redirect, base_url())) {
        $target = $redirect;
    }
}
redirect($target);