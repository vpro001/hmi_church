<?php
/**
 * Resume/Pay for an existing pending order via Paystack
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/paystack.php';

customer_require_login();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = customer_user();

if (!$orderId || !$user) {
    flash('danger', 'Invalid order request.');
    redirect('account.php#orders');
}

$order = Database::fetchOne("SELECT * FROM orders WHERE id = ? AND user_id = ?", [$orderId, $user['id']]);

if (!$order) {
    flash('danger', 'Order not found.');
    redirect('account.php#orders');
}

if (in_array($order['status'], ['paid', 'completed'])) {
    flash('success', 'Order has already been paid.');
    redirect('order_success.php?ref=' . urlencode($order['order_ref']));
}

$items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
$callbackUrl = url('actions/paystack_callback.php');

$payload = [
    'id' => $order['id'],
    'order_ref' => $order['order_ref'],
    'user_id' => $user['id'],
    'customer_name' => $order['customer_name'],
    'customer_email' => $order['customer_email'],
    'customer_phone' => $order['customer_phone'],
    'total' => $order['total'],
    'items' => $items
];

$init = paystack_initialize_transaction($payload, $callbackUrl);

if (!empty($init['status']) && !empty($init['authorization_url'])) {
    $payRef = $init['reference'] ?? $order['order_ref'];
    Database::execute("UPDATE orders SET payment_method = 'paystack', payment_reference = ? WHERE id = ?", [$payRef, $order['id']]);
    header('Location: ' . $init['authorization_url'], true, 303);
    exit;
} else {
    flash('danger', $init['message'] ?? 'Could not initialize Paystack checkout.');
    redirect('account.php#orders');
}
