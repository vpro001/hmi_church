<?php
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('shop.php'), true, 303);
    exit;
}
$back = url('shop.php');

if (!csrf_valid()) {
    flash('danger', 'Your session expired. Please try again.');
    redirect_back($back);
}
if (too_fast_or_spam()) {
    flash('danger', 'That was a little too quick. Please wait a few seconds and try again.');
    redirect_back($back);
}

$name  = clean_line($_POST['name'] ?? '', 100);
$email = clean_line($_POST['email'] ?? '', 150);
$phone = clean_line($_POST['phone'] ?? '', 30);

if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[+\d][\d\s().-]{6,}$/', $phone)) {
    flash('danger', 'Please enter your name, a valid email address and a phone number.');
    redirect_back($back);
}

// Rebuild the order from the server's own price list. Never trust prices from the browser.
$catalog = [];
foreach (get_products() as $p) { $catalog[$p['id']] = $p; }
$raw = json_decode((string)($_POST['cart'] ?? '[]'), true);
$lines = [];
$total = 0;
if (is_array($raw)) {
    foreach ($raw as $row) {
        $id  = is_array($row) ? ($row['id'] ?? '') : '';
        $qty = is_array($row) ? (int)($row['qty'] ?? 0) : 0;
        if (!is_string($id) || !isset($catalog[$id]) || $qty < 1) continue;
        $qty = min($qty, 20);
        $price = (int)$catalog[$id]['price'];
        $lines[] = ['id' => $id, 'title' => $catalog[$id]['title'], 'qty' => $qty, 'price' => $price, 'subtotal' => $price * $qty];
        $total += $price * $qty;
    }
}
if (!$lines) {
    flash('danger', 'Your cart was empty. Please add an e-book and try again.');
    redirect_back($back);
}

$_SESSION['last_submit'] = time();
$ref = 'HMI-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
$saved = save_submission('orders', ['ref' => $ref, 'name' => $name, 'email' => $email, 'phone' => $phone, 'lines' => $lines, 'total' => $total]);

$body = "Order {$ref}\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\n\n";
foreach ($lines as $l) { $body .= "{$l['qty']} x {$l['title']} @ " . money($l['price']) . " = " . money($l['subtotal']) . "\n"; }
$body .= "\nTotal: " . money($total) . "\n";
send_notification('New e-book order request ' . $ref, $body, $email);

if ($saved) {
    $_SESSION['clear_cart'] = true;
    flash('success', "Thank you, " . strtok($name, ' ') . ". Your order request {$ref} was received. We will contact you with payment options and send your e-book.");
} else {
    flash('danger', 'We could not save your order just now. Please call ' . SITE['phones'][0] . ' instead.');
}
redirect_back($back);
