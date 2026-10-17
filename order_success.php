<?php
/**
 * Order Success & Receipt Page
 * Healing My-Marriage International
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$ref = clean_line($_GET['ref'] ?? '', 50);
$order = null;

if (!empty($ref)) {
    $order = Database::fetchOne("SELECT * FROM orders WHERE order_ref = ? OR payment_reference = ?", [$ref, $ref]);
}

if (!$order) {
    flash('danger', 'Order not found.');
    redirect('shop.php');
}

$items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$order['id']]);

$page_key   = 'shop';
$page_title = 'Order Confirmation - ' . $order['order_ref'];
$page_desc  = 'Thank you for your purchase from Healing My-Marriage International.';
$page_path  = 'order_success.php?ref=' . urlencode($order['order_ref']);

require __DIR__ . '/includes/header.php';
$hero_title = 'Order Confirmed';
$hero_sub   = 'Thank you for your purchase. Your payment has been received via Paystack.';
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section pt-5 pb-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="product-frame mb-4">
          <span class="corner tl" aria-hidden="true"></span>
          <span class="corner br" aria-hidden="true"></span>
          <div class="p-4 p-md-5 bg-white rounded-3 shadow-sm">
            
            <div class="text-center mb-4">
              <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
                <i class="bi bi-check-lg" aria-hidden="true"></i>
              </div>
              <h1 class="h2 mb-1">Thank You, <?= h(strtok($order['customer_name'], ' ')) ?>!</h1>
              <p class="text-muted mb-0">Your order has been placed and payment is confirmed.</p>
            </div>

            <div class="alert alert-success d-flex align-items-center gap-3 mb-4">
              <i class="bi bi-shield-check fs-3 flex-shrink-0" aria-hidden="true"></i>
              <div>
                <strong>Payment Successful via Paystack</strong>
                <div class="small">Transaction Reference: <code><?= h($order['payment_reference'] ?: $order['order_ref']) ?></code></div>
              </div>
            </div>

            <div class="card mb-4 border-0 bg-light">
              <div class="card-body">
                <div class="row g-3">
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">Order Reference</span>
                    <strong class="fs-6"><?= h($order['order_ref']) ?></strong>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">Order Date</span>
                    <strong class="fs-6"><?= fmt_date($order['paid_at'] ?: $order['created_at'], 'j F Y, g:i a') ?></strong>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">Customer Name</span>
                    <div><?= h($order['customer_name']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">Email Address</span>
                    <div><?= h($order['customer_email']) ?></div>
                  </div>
                  <?php if (!empty($order['customer_phone'])): ?>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">Phone</span>
                    <div><?= h($order['customer_phone']) ?></div>
                  </div>
                  <?php endif; ?>
                  <div class="col-sm-6">
                    <span class="text-muted d-block small">Status</span>
                    <span class="badge bg-success text-uppercase"><?= h($order['status']) ?></span>
                  </div>
                </div>
              </div>
            </div>

            <h2 class="h5 mb-3">Purchased Items</h2>
            <div class="table-responsive mb-4">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Item</th>
                    <th class="text-center" style="width: 80px;">Qty</th>
                    <th class="text-end" style="width: 120px;">Price</th>
                    <th class="text-end" style="width: 140px;">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <div class="fw-semibold"><?= h($item['product_title']) ?></div>
                      <small class="text-muted">Digital E-Book (PDF)</small>
                    </td>
                    <td class="text-center"><?= (int)$item['qty'] ?></td>
                    <td class="text-end"><?= money($item['price']) ?></td>
                    <td class="text-end fw-semibold"><?= money($item['subtotal']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="text-end fs-6"><strong>Total Paid:</strong></td>
                    <td class="text-end fs-5 text-success"><strong><?= money($order['total']) ?></strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>

            <div class="p-3 rounded-2 border bg-light mb-4">
              <h3 class="h6 mb-2"><i class="bi bi-envelope-heart-fill text-danger me-2" aria-hidden="true"></i>E-Book Delivery Notice</h3>
              <p class="small text-muted mb-0">
                A receipt and confirmation have been recorded for your account. Your e-book files will be delivered to <strong><?= h($order['customer_email']) ?></strong>.
                If you have any questions or require immediate assistance, feel free to contact us at <a href="mailto:<?= h(site_email()) ?>"><?= h(site_email()) ?></a> or call <a href="<?= h(tel_link(site_phones_raw()[0] ?? '+2347042226633')) ?>"><?= h(site_phones()[0] ?? '+234 704 222 6633') ?></a>.
              </p>
            </div>

            <div class="d-flex flex-wrap gap-3 justify-content-between">
              <?php if (is_customer()): ?>
                <a href="<?= h(url('account.php#orders')) ?>" class="btn btn-hmi-green">
                  <i class="bi bi-person-fill me-1" aria-hidden="true"></i> View in My Account
                </a>
              <?php else: ?>
                <a href="<?= h(url('login.php')) ?>" class="btn btn-hmi-green">
                  <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i> Sign In to Account
                </a>
              <?php endif; ?>
              <a href="<?= h(url('shop.php')) ?>" class="btn btn-hmi-outline">
                <i class="bi bi-bag-plus me-1" aria-hidden="true"></i> Continue Shopping
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
