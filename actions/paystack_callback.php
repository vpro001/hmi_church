<?php
/**
 * Paystack Payment Callback
 * Handles redirect back from Paystack checkout and verifies transaction status
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/paystack.php';

$ref = clean_line($_GET['reference'] ?? $_GET['trxref'] ?? '', 100);

if (empty($ref)) {
    flash('danger', 'No payment reference provided.');
    redirect('shop.php');
}

$order = Database::fetchOne("SELECT * FROM orders WHERE order_ref = ? OR payment_reference = ?", [$ref, $ref]);

if (!$order) {
    flash('danger', 'Order not found for payment reference: ' . h($ref));
    redirect('shop.php');
}

// If already paid, take user to the confirmation page
if (in_array($order['status'], ['paid', 'completed'])) {
    $_SESSION['clear_cart'] = true;
    redirect('order_success.php?ref=' . urlencode($order['order_ref']));
}

$verify = paystack_verify_transaction($ref);

if (!empty($verify['status'])) {
    $paidAmount = (int)($verify['amount'] ?? 0);
    $expectedKobo = (int)($order['total'] * 100);

    // If using live Paystack, verify the amount received matches or exceeds the expected total
    if (paystack_is_configured() && $paidAmount > 0 && $paidAmount < $expectedKobo) {
        Database::execute(
            "UPDATE orders SET notes = CONCAT(IFNULL(notes,''), '\n[Underpaid: expected {$expectedKobo} kobo, received {$paidAmount} kobo]') WHERE id = ?",
            [$order['id']]
        );
        flash('danger', 'Payment amount was less than expected. Please contact ministry support.');
        redirect('account.php#orders');
    }

    $channel = $verify['channel'] ?? 'paystack';
    $paymentRef = $verify['reference'] ?? $ref;

    // Update order status to paid
    Database::execute(
        "UPDATE orders SET status = 'paid', payment_method = 'paystack', payment_reference = ?, paid_at = NOW() WHERE id = ?",
        [$paymentRef, $order['id']]
    );

    // Log customer activity
    if (!empty($order['user_id'])) {
        log_activity((int)$order['user_id'], 'customer', 'payment_success', "Paystack payment of ₦{$order['total']} verified for order {$order['order_ref']} (Ref: {$paymentRef})");
    }

    // Send notification
    $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
    $body = "Payment received for Order {$order['order_ref']}\n"
          . "Customer: {$order['customer_name']} ({$order['customer_email']})\n"
          . "Phone: {$order['customer_phone']}\n"
          . "Amount: " . money($order['total']) . "\n"
          . "Channel: {$channel}\n"
          . "Reference: {$paymentRef}\n\n"
          . "Purchased Items:\n";
    foreach ($items as $item) {
        $body .= "- {$item['qty']}x {$item['product_title']} (" . money($item['price']) . ")\n";
    }
    $body .= "\nPlease dispatch e-books to customer email.";
    
    send_notification('Order Paid - ' . $order['order_ref'], $body, $order['customer_email']);

    // Clear client-side cart
    $_SESSION['clear_cart'] = true;
    flash('success', 'Thank you! Your payment was successful and your order has been confirmed.');
    redirect('order_success.php?ref=' . urlencode($order['order_ref']));
} else {
    $errMsg = !empty($verify['message']) ? $verify['message'] : 'Payment was not successful or was cancelled.';
    flash('danger', 'Payment status: ' . $errMsg);
    redirect('shop.php');
}
