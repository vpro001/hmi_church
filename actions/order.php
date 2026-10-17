<?php
/**
 * Order action - Paystack Integration & Authentication Enforcement
 * Healing My-Marriage International
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/paystack.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('shop.php');
}

$back = url('shop.php');

// 1. MUST BE LOGGED IN TO BUY OR PLACE ORDER
if (!customer_check()) {
    flash('danger', 'You must be signed in to purchase e-books from the shop.');
    redirect('login.php?redirect=' . urlencode('shop.php'));
}

if (!csrf_valid()) {
    flash('danger', 'Your session expired. Please try again.');
    redirect_back($back);
}

if (too_fast_or_spam()) {
    flash('danger', 'That was a little too quick. Please wait a few seconds and try again.');
    redirect_back($back);
}

$customer = customer_user();
if (!$customer) {
    flash('danger', 'Customer account not found. Please log in again.');
    redirect('login.php?redirect=' . urlencode('shop.php'));
}

$userId = (int)$customer['id'];
$name   = $customer['full_name'];
$email  = $customer['email'];
$phone  = clean_line($_POST['phone'] ?? ($customer['phone'] ?? ''), 30);
$address = clean_line($_POST['address'] ?? ($customer['address'] ?? ''), 200);

// If phone in profile was empty, update it with what was entered in checkout
if (empty($customer['phone']) && !empty($phone)) {
    Database::execute("UPDATE users SET phone = ? WHERE id = ?", [$phone, $userId]);
    $_SESSION['customer_phone'] = $phone;
}

if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('danger', 'Please make sure your account has a valid name and email address.');
    redirect_back($back);
}

// Rebuild the order from the server's own product catalog. Never trust prices from browser.
$catalog = [];
foreach (get_products() as $p) {
    $catalog[$p['slug']] = $p;
    $catalog[(string)$p['id']] = $p;
}

$raw = json_decode((string)($_POST['cart'] ?? '[]'), true);
$lines = [];
$total = 0;

if (is_array($raw)) {
    foreach ($raw as $row) {
        $id  = is_array($row) ? ($row['id'] ?? '') : '';
        $qty = is_array($row) ? (int)($row['qty'] ?? 0) : 0;
        $idKey = is_scalar($id) ? (string)$id : '';

        if ($idKey === '' || !isset($catalog[$idKey]) || $qty < 1) continue;

        $qty = min($qty, 20);
        $prod = $catalog[$idKey];
        $onSale = !empty($prod['sale_price']) && $prod['sale_price'] < $prod['price'];
        $unitPrice = (int)($onSale ? $prod['sale_price'] : $prod['price']);

        $lines[] = [
            'product_id' => (int)$prod['id'],
            'title' => $prod['title'],
            'slug' => $prod['slug'],
            'qty' => $qty,
            'price' => $unitPrice,
            'subtotal' => $unitPrice * $qty,
        ];
        $total += $unitPrice * $qty;
    }
}

if (!$lines || $total <= 0) {
    flash('danger', 'Your cart was empty. Please add an e-book and try again.');
    redirect_back($back);
}

$_SESSION['last_submit'] = time();

// Create order in database with status pending and payment_method paystack
$orderData = [
    'user_id' => $userId,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'address' => $address,
    'subtotal' => $total,
    'total' => $total,
    'items' => $lines,
];

$result = create_order($orderData);

if (!$result) {
    flash('danger', 'We could not save your order just now. Please try again.');
    redirect_back($back);
}

$orderId = (int)$result['id'];
$orderRef = $result['ref'];

// Update payment method to Paystack
Database::execute("UPDATE orders SET payment_method = 'paystack' WHERE id = ?", [$orderId]);

// Save to submissions for record
save_submission('order', [
    'ref' => $orderRef,
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'lines' => $lines,
    'total' => $total
]);

// Initialize Paystack payment
$callbackUrl = url('actions/paystack_callback.php');
$orderPayload = [
    'id' => $orderId,
    'order_ref' => $orderRef,
    'user_id' => $userId,
    'customer_name' => $name,
    'customer_email' => $email,
    'customer_phone' => $phone,
    'total' => $total,
    'items' => $lines
];

$init = paystack_initialize_transaction($orderPayload, $callbackUrl);

if (!empty($init['status']) && !empty($init['authorization_url'])) {
    $payRef = $init['reference'] ?? $orderRef;
    Database::execute("UPDATE orders SET payment_reference = ? WHERE id = ?", [$payRef, $orderId]);
    
    // Redirect customer to Paystack authorization URL
    header('Location: ' . $init['authorization_url'], true, 303);
    exit;
} else {
    $errMsg = !empty($init['message']) ? $init['message'] : 'Payment initialization failed. Please try again.';
    flash('danger', $errMsg);
    redirect_back($back);
}