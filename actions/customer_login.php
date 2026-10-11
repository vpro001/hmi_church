<?php
/**
 * Customer login action
 */

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token. Please try again.');
    redirect('login.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$redirect = $_POST['redirect'] ?? 'account.php';

if (!$email || !$password) {
    flash('danger', 'Please enter both email and password.', ['email' => $email]);
    redirect('login.php');
}

$result = customer_login($email, $password);

if (isset($result['error'])) {
    flash('danger', $result['message'], ['email' => $email]);
    redirect('login.php');
}

flash('success', 'Welcome back, ' . $result['full_name'] . '!');
redirect($redirect);