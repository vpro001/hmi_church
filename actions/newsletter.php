<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('index.php'), true, 303);
    exit;
}
$back = url('index.php');

if (!csrf_valid()) {
    flash('danger', 'Your session expired. Please try again.');
    redirect_back($back);
}
if (too_fast_or_spam()) {
    flash('danger', 'That was a little too quick. Please wait a few seconds and try again.');
    redirect_back($back);
}

$email = clean_line($_POST['email'] ?? '', 150);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('danger', 'Please enter a valid email address.');
    redirect_back($back);
}

$_SESSION['last_submit'] = time();
$saved = save_submission('newsletter', ['email' => $email]);
send_notification('New newsletter subscriber', "Email: {$email}", $email);

flash($saved ? 'success' : 'danger', $saved ? 'You are subscribed. Thank you for joining us.' : 'We could not complete your sign-up just now. Please try again later.');
redirect_back($back);
