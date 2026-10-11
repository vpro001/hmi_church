<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('contact.php'), true, 303);
    exit;
}
$back = url('contact.php');

if (!csrf_valid()) {
    flash('danger', 'Your session expired. Please try again.');
    redirect_back($back, '#message');
}
if (too_fast_or_spam()) {
    flash('danger', 'That was a little too quick. Please wait a few seconds and try again.');
    redirect_back($back, '#message');
}

$data = [
    'name'    => clean_line($_POST['name'] ?? '', 100),
    'email'   => clean_line($_POST['email'] ?? '', 150),
    'phone'   => clean_line($_POST['phone'] ?? '', 30),
    'type'    => clean_line($_POST['type'] ?? '', 40),
    'subject' => clean_line($_POST['subject'] ?? '', 120),
    'message' => clean_text($_POST['message'] ?? '', 2000),
];

$errors = [];
if (mb_strlen($data['name']) < 2) $errors['name'] = 'Please enter your name.';
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
if (mb_strlen($data['message']) < 5) $errors['message'] = 'Please write a short message.';

if ($errors) {
    flash('danger', 'Please check the highlighted fields and try again.', $data, $errors);
    redirect_back($back, '#message');
}

$_SESSION['last_submit'] = time();
$saved = save_submission('contact', $data);
send_notification(
    '[' . $data['type'] . '] ' . ($data['subject'] ?: 'Website message from ' . $data['name']),
    "Name: {$data['name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nType: {$data['type']}\n\n{$data['message']}",
    $data['email']
);

if ($saved) {
    flash('success', 'Thank you. Your message has been received and we will respond as soon as we can.');
} else {
    flash('danger', 'We could not save your message just now. Please call ' . SITE['phones'][0] . ' instead.', $data);
}
redirect_back($back, '');
