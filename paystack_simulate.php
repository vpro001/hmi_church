<?php
/**
 * Paystack Test Sandbox Simulator
 * This page simulates Paystack checkout when in test mode without requiring live keys.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/paystack.php';

$ref = clean_line($_GET['ref'] ?? '', 50);
$order = Database::fetchOne("SELECT * FROM orders WHERE order_ref = ? OR payment_reference = ?", [$ref, $ref]);

if (!$order) {
    flash('danger', 'Order not found.');
    redirect('shop.php');
}

if ($order['status'] === 'paid' || $order['status'] === 'completed') {
    flash('success', 'Order ' . $order['order_ref'] . ' has already been paid for.');
    redirect('order_success.php?ref=' . urlencode($order['order_ref']));
}

$items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);

$page_title = 'Paystack Test Checkout';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paystack Simulator - <?= h($order['order_ref']) ?></title>
  <link href="<?= h(asset('vendor/bootstrap/bootstrap.min.css')) ?>" rel="stylesheet">
  <link href="<?= h(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f8;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .paystack-modal {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.05);
      max-width: 460px;
      width: 100%;
      overflow: hidden;
      border: 1px solid #e1e6eb;
    }
    .paystack-header {
      background: #0ba4db;
      color: #fff;
      padding: 1.5rem 1.75rem;
      position: relative;
    }
    .paystack-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255, 255, 255, 0.2);
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      margin-bottom: 0.75rem;
    }
    .merchant-name {
      font-size: 1.1rem;
      font-weight: 700;
      margin: 0;
    }
    .pay-amount {
      font-size: 1.75rem;
      font-weight: 800;
      margin-top: 0.25rem;
    }
    .paystack-body {
      padding: 1.75rem;
    }
    .test-banner {
      background-color: #fff8e6;
      border-left: 4px solid #f5a623;
      padding: 0.75rem 1rem;
      margin-bottom: 1.25rem;
      font-size: 0.85rem;
      color: #7a5200;
      border-radius: 4px;
    }
    .order-summary {
      background: #f8fafc;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.25rem;
      font-size: 0.9rem;
    }
    .order-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.35rem;
      color: #4b5563;
    }
    .order-row:last-child {
      margin-bottom: 0;
    }
    .btn-paystack-success {
      background: #09a5db;
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 0.85rem;
      border-radius: 8px;
      width: 100%;
      font-size: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.2s;
    }
    .btn-paystack-success:hover {
      background: #088ec0;
      color: #fff;
    }
    .btn-paystack-cancel {
      background: transparent;
      border: 1px solid #d1d5db;
      color: #6b7280;
      font-weight: 500;
      padding: 0.7rem;
      border-radius: 8px;
      width: 100%;
      font-size: 0.9rem;
      margin-top: 0.75rem;
    }
    .btn-paystack-cancel:hover {
      background: #f3f4f6;
      color: #374151;
    }
    .secured-by {
      text-align: center;
      font-size: 0.8rem;
      color: #9ca3af;
      margin-top: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
    }
  </style>
</head>
<body>

<div class="paystack-modal">
  <div class="paystack-header">
    <div class="paystack-badge">
      <i class="bi bi-shield-check"></i> Test Mode Simulator
    </div>
    <h1 class="merchant-name"><?= h(site_setting('site_short_name', 'Healing My-Marriage')) ?></h1>
    <div class="pay-amount"><?= money($order['total']) ?></div>
  </div>

  <div class="paystack-body">
    <div class="test-banner">
      <strong><i class="bi bi-info-circle-fill me-1"></i> Developer Notice:</strong>
      This is a Paystack test sandbox simulator. Use this to verify your purchasing and e-book fulfillment flow without real cards. To use live/test Paystack servers, enter your API keys in <em>Admin &rarr; Settings &rarr; Payment</em>.
    </div>

    <div class="order-summary">
      <div class="order-row">
        <span>Order Reference:</span>
        <strong><?= h($order['order_ref']) ?></strong>
      </div>
      <div class="order-row">
        <span>Customer:</span>
        <span><?= h($order['customer_name']) ?></span>
      </div>
      <div class="order-row">
        <span>Email:</span>
        <span><?= h($order['customer_email']) ?></span>
      </div>
      <?php if (!empty($items)): ?>
      <hr class="my-2">
      <div class="order-row">
        <span>Items:</span>
        <span><?= count($items) ?> e-book(s)</span>
      </div>
      <?php foreach ($items as $item): ?>
      <div class="order-row small text-muted">
        <span><?= $item['qty'] ?>x <?= h($item['product_title']) ?></span>
        <span><?= money($item['subtotal']) ?></span>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <form method="get" action="<?= h(url('actions/paystack_callback.php')) ?>">
      <input type="hidden" name="reference" value="<?= h($order['order_ref']) ?>">
      <input type="hidden" name="simulated" value="1">
      <button type="submit" class="btn btn-paystack-success">
        <i class="bi bi-lock-fill"></i> Simulate Successful Payment (<?= money($order['total']) ?>)
      </button>
    </form>

    <a href="<?= h(url('shop.php')) ?>" class="btn btn-paystack-cancel text-center text-decoration-none d-block">
      Cancel Payment and Return to Shop
    </a>

    <div class="secured-by">
      <i class="bi bi-shield-lock-fill text-success"></i> Secured by Paystack Payment Gateway
    </div>
  </div>
</div>

</body>
</html>
