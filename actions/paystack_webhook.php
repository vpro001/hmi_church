<?php
/**
 * Paystack Webhook Handler
 * Processes asynchronous events sent by Paystack (e.g. charge.success)
 */

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/paystack.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$input = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

// Only verify signature if Paystack secret key is configured
if (paystack_is_configured()) {
    if (!paystack_verify_webhook($input, $signature)) {
        http_response_code(401);
        exit('Invalid signature');
    }
}

$event = json_decode((string)$input, true);

if (!empty($event['event']) && $event['event'] === 'charge.success') {
    $data = $event['data'] ?? [];
    $ref = $data['reference'] ?? '';

    if (!empty($ref)) {
        $order = Database::fetchOne("SELECT * FROM orders WHERE order_ref = ? OR payment_reference = ?", [$ref, $ref]);

        if ($order && !in_array($order['status'], ['paid', 'completed'])) {
            $paidAmount = (int)($data['amount'] ?? 0);
            $expectedKobo = (int)($order['total'] * 100);

            if ($paidAmount >= $expectedKobo) {
                Database::execute(
                    "UPDATE orders SET status = 'paid', payment_method = 'paystack', payment_reference = ?, paid_at = NOW() WHERE id = ?",
                    [$ref, $order['id']]
                );

                if (!empty($order['user_id'])) {
                    log_activity((int)$order['user_id'], 'customer', 'webhook_payment_success', "Paystack webhook confirmed payment for order {$order['order_ref']} (Ref: {$ref})");
                }

                // Send notification
                $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);
                $body = "Webhook: Payment confirmed for Order {$order['order_ref']}\n"
                      . "Customer: {$order['customer_name']} ({$order['customer_email']})\n"
                      . "Amount: " . money($order['total']) . "\n"
                      . "Reference: {$ref}\n";
                send_notification('Order Paid (Webhook) - ' . $order['order_ref'], $body, $order['customer_email']);
            }
        }
    }
}

http_response_code(200);
echo json_encode(['status' => 'success']);
