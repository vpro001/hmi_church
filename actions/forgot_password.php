<?php
/**
 * Forgot password action
 */

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('forgot_password.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token.');
    redirect('forgot_password.php');
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('danger', 'Please enter a valid email address.');
    redirect('forgot_password.php');
}

// Always return success to prevent email enumeration
request_password_reset($email);

flash('success', 'If an account with that email exists, a password reset link has been sent.');
redirect('forgot_password.php?step=sent');