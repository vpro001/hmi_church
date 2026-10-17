<?php
/**
 * Customer register action
 */

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

$redirect = trim($_POST['redirect'] ?? '');
$redirectQuery = !empty($redirect) ? '?redirect=' . urlencode($redirect) : '';

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token. Please try again.');
    redirect('register.php' . $redirectQuery);
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$address = trim($_POST['address'] ?? '');

$errors = [];
if (mb_strlen($firstName) < 1) $errors['first_name'] = 'First name is required.';
if (mb_strlen($lastName) < 1) $errors['last_name'] = 'Last name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required.';
if ($phone && !preg_match('/^[+\d][\d\s().-]{6,}$/', $phone)) $errors['phone'] = 'Invalid phone format.';
if (mb_strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';
if ($password !== $confirm) $errors['confirm_password'] = 'Passwords do not match.';
if (empty($_POST['terms'])) $errors['terms'] = 'You must accept the terms.';

if ($errors) {
    flash('danger', 'Please check the highlighted fields.', ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'phone' => $phone, 'address' => $address], $errors);
    redirect('register.php' . $redirectQuery);
}

$name = $firstName . ' ' . $lastName;
$result = customer_register(['email' => $email, 'password' => $password, 'name' => $name, 'phone' => $phone, 'address' => $address]);

if (isset($result['error'])) {
    flash('danger', $result['message'], ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'phone' => $phone, 'address' => $address]);
    redirect('register.php' . $redirectQuery);
}

// Auto-login the user immediately
customer_login($email, $password);

flash('success', 'Account created successfully! Welcome to Healing My-Marriage.');

// Redirect safely
$target = 'account.php';
if (!empty($redirect)) {
    if (!preg_match('#^https?://#i', $redirect) || str_starts_with($redirect, base_url())) {
        $target = $redirect;
    }
}
redirect($target);