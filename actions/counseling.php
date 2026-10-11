<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('counseling.php'), true, 303);
    exit;
}
$back = url('counseling.php');

if (!csrf_valid()) {
    flash('danger', 'Your session expired. Please try again.');
    redirect_back($back, '#request');
}
if (too_fast_or_spam()) {
    flash('danger', 'That was a little too quick. Please wait a few seconds and try again.');
    redirect_back($back, '#request');
}

$data = [
    'name'    => clean_line($_POST['name'] ?? '', 100),
    'partner' => clean_line($_POST['partner'] ?? '', 100),
    'email'   => clean_line($_POST['email'] ?? '', 150),
    'phone'   => clean_line($_POST['phone'] ?? '', 30),
    'status'  => clean_line($_POST['status'] ?? '', 60),
    'topic'   => clean_line($_POST['topic'] ?? '', 60),
    'mode'    => clean_line($_POST['mode'] ?? '', 30),
    'message' => clean_text($_POST['message'] ?? '', 1500),
];

$errors = [];
if (mb_strlen($data['name']) < 2) $errors['name'] = 'Please enter your name.';
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Please enter a valid email address.';
if (!preg_match('/^[+\d][\d\s().-]{6,}$/', $data['phone'])) $errors['phone'] = 'Please enter a phone number we can reach you on.';
if (empty($_POST['consent'])) $errors['consent'] = 'Please tick the box so we can contact you.';

if ($errors) {
    flash('danger', 'Please check the highlighted fields and try again.', $data, $errors);
    redirect_back($back, '#request');
}

$_SESSION['last_submit'] = time();
$saved = save_submission('counseling', $data);
send_notification(
    'New counseling request: ' . $data['name'],
    "Name: {$data['name']}\nPartner: {$data['partner']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nSituation: {$data['status']}\nConcern: {$data['topic']}\nMeeting: {$data['mode']}\n\n{$data['message']}",
    $data['email']
);

if ($saved) {
    flash('success', 'Thank you, ' . strtok($data['name'], ' ') . '. Your request has been received and someone will be in touch with you soon.');
} else {
    flash('danger', 'We could not save your request just now. Please call ' . SITE['phones'][0] . ' instead.', $data);
}
redirect_back($back, '');
