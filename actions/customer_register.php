<?php
/**
 * Customer register action
 */

require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

if (!csrf_valid()) {
    flash('danger', 'Invalid CSRF token. Please try again.');
    redirect('register.php');
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
    redirect('register.php');
}

$name = $firstName . ' ' . $lastName;
$result = customer_register(['email' => $email, 'password' => $password, 'name' => $name, 'phone' => $phone, 'address' => $address]);

if (isset($result['error'])) {
    flash('danger', $result['message'], ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'phone' => $phone, 'address' => $address]);
    redirect('register.php');
}

flash('success', 'Account created successfully! You can now sign in.');
redirect('login.php');