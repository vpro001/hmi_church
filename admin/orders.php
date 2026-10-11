<?php
/**
 * Admin Orders Management
 */

$page_title = 'Orders';
$currentPage = 'orders';
require_once __DIR__ . '/includes/header.php';

$statusFilter = $_GET['status'] ?? '';
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($viewId) {
    $order = Database::fetchOne("
        SELECT o.*, u.full_name as user_name, u.email as user_email 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?
    ", [$viewId]);
    
    if (!$order) {
        flash('danger', 'Order not found.');
        redirect('admin/orders.php');
    }
    
    $items = Database::fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$viewId]);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
        if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
        else {
            $newStatus = $_POST['status'] ?? 'pending';
            $notes = trim($_POST['admin_notes'] ?? '');
            Database::execute("UPDATE orders SET status = ?, admin_notes = ? WHERE id = ?", [$newStatus, $notes, $viewId]);
            if ($newStatus === 'paid' && $order['status'] !== 'paid') {
                Database::execute("UPDATE orders SET paid_at = NOW() WHERE id = ?", [$viewId]);
            }
            if ($newStatus === 'completed' && $order['status'] !== 'completed') {
                Database::execute("UPDATE orders SET completed_at = NOW() WHERE id = ?", [$viewId]);
            }
            log_activity($admin['id'], 'admin', 'order_update', "Updated order {$order['order_ref']} to $newStatus");
            flash('success', 'Order updated.');
            redirect('admin/orders.php?view=' . $viewId);
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0"><?= $viewId ? 'Order Details' : 'Orders' ?></h2>
        <p class="text-muted mb-0"><?= $viewId ? 'Order reference: ' . h($order['order_ref']) : 'Manage customer orders' ?></p>
    </div>
    <?php if (!$viewId): ?>
    <div class="d-flex gap-2">
        <a href="<?= h(url('admin/orders.php')) ?>" class="btn btn-outline-secondary <?= !$statusFilter ? 'active' : '' ?>">All</a>
        <a href="<?= h(url('admin/orders.php?status=pending')) ?>" class="btn btn-outline-warning <?= $statusFilter === 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="<?= h(url('admin/orders.php?status=paid')) ?>" class="btn btn-outline-info <?= $statusFilter === 'paid' ? 'active' : '' ?>">Paid</a>
        <a href="<?= h(url('admin/orders.php?status=completed')) ?>" class="btn btn-outline-success <?= $statusFilter === 'completed' ? 'active' : '' ?>">Completed</a>
    </div>
    <?php else: ?>
    <a href="<?= h(url('admin/orders.php')) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
    <?php endif; ?>
</div>

<?php if ($viewId): ?>
<!-- Order Detail View -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #<?= h($order['order_ref']) ?></h5>
                <span class="badge bg-<?= $order['status'] === 'pending' ? 'pending' : ($order['status'] === 'paid' ? 'paid' : ($order['status'] === 'completed' ? 'completed' : 'secondary')) ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <strong>Customer</strong>
                        <div><?= h($order['customer_name']) ?></div>
                        <small class="text-muted"><?= h($order['customer_email']) ?></small>
                        <?php if ($order['customer_phone']): ?><br><small><?= h($order['customer_phone']) ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Date</strong>
                        <div><?= fmt_date($order['created_at'], 'M j, Y g:i a') ?></div>
                        <?php if ($order['paid_at']): ?><small class="text-success">Paid: <?= fmt_date($order['paid_at'], 'M j, Y g:i a') ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment</strong>
                        <div><?= h($order['payment_method'] ?: 'Not specified') ?></div>
                        <?php if ($order['payment_reference']): ?><small class="text-muted">Ref: <?= h($order['payment_reference']) ?></small><?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Customer Type</strong>
                        <div><?= $order['user_id'] ? 'Registered User' : 'Guest' ?></div>
                    </div>
                </div>
                
                <h6>Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Price</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= h($item['product_title']) ?></td>
                                <td class="text-end"><?= (int)$item['qty'] ?></td>
                                <td class="text-end"><?= money($item['price']) ?></td>
                                <td class="text-end"><?= money($item['subtotal']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" class="text-end"><strong>Subtotal</strong></td><td class="text-end"><strong><?= money($order['subtotal']) ?></strong></td></tr>
                            <?php if ($order['discount']): ?>
                            <tr><td colspan="3" class="text-end">Discount</td><td class="text-end text-danger">-<?= money($order['discount']) ?></td></tr>
                            <?php endif; ?>
                            <tr class="table-active"><td colspan="3" class="text-end"><strong>Total</strong></td><td class="text-end"><strong><?= money($order['total']) ?></strong></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <?php if ($order['notes']): ?>
        <div class="card mt-3">
            <div class="card-header"><h6 class="mb-0">Customer Notes</h6></div>
            <div class="card-body"><?= h($order['notes']) ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Update Status</h5></div>
            <div class="card-body">
                <form method="post" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="update_order" value="1">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= $order['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="refunded" <?= $order['status'] === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea class="form-control" name="admin_notes" rows="4"><?= h($order['admin_notes'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-forest w-100" type="submit"><i class="bi bi-save me-2"></i> Update Order</button>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header"><h6 class="mb-0">Customer Info</h6></div>
            <div class="card-body">
                <p class="mb-1"><strong>IP:</strong> <?= h($order['ip_address'] ?: '—') ?></p>
                <p class="mb-0"><small class="text-muted">Created: <?= fmt_date($order['created_at'], 'M j, Y g:i a') ?></small></p>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Orders List -->
<div class="card">
    <div class="card-body p-0">
        <?php 
        $where = $statusFilter ? "WHERE o.status = ?" : "";
        $params = $statusFilter ? [$statusFilter] : [];
        
        $orders = Database::fetchAll("
            SELECT o.*, u.full_name as user_name 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            $where
            ORDER BY o.created_at DESC
            LIMIT 100
        ", $params);
        ?>
        
        <?php if ($orders): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                    <tr>
                        <td><code><?= h($o['order_ref']) ?></code></td>
                        <td>
                            <div><?= h($o['customer_name']) ?></div>
                            <small class="text-muted"><?= h($o['customer_email']) ?></small>
                            <?php if ($o['user_id']): ?><span class="badge bg-secondary ms-2">User</span><?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $itemCount = Database::fetchOne("SELECT COUNT(*) as c, SUM(qty) as q FROM order_items WHERE order_id = ?", [$o['id']]);
                            echo (int)($itemCount['c'] ?? 0) . ' product(s), ' . (int)($itemCount['q'] ?? 0) . ' items';
                            ?>
                        </td>
                        <td><strong><?= money($o['total']) ?></strong></td>
                        <td><span class="badge bg-<?= $o['status'] === 'pending' ? 'pending' : ($o['status'] === 'paid' ? 'paid' : ($o['status'] === 'completed' ? 'completed' : ($o['status'] === 'cancelled' ? 'cancelled' : 'secondary'))) ?>"><?= ucfirst($o['status']) ?></span></td>
                        <td><?= h($o['payment_method'] ?: '—') ?></td>
                        <td><?= fmt_date($o['created_at'], 'M j, Y') ?></td>
                        <td>
                            <a href="<?= h(url('admin/orders.php?view=' . $o['id'])) ?>" class="btn btn-sm btn-outline-secondary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-cart-check"></i><h5>No orders</h5><p class="mb-0"><?= $statusFilter ? 'No orders with status: ' . $statusFilter : 'Orders will appear here when customers place them.' ?></p></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>