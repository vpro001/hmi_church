<?php
/**
 * Customer Account Page
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

customer_require_login();

$user = customer_user();
$orders = get_user_orders($user['id']);

$page_key = 'account';
$page_title = 'My Account';
$page_desc = 'Manage your account, view order history, and update profile.';
require_once __DIR__ . '/includes/header.php';

$flash = take_flash();
?>

<div class="container py-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-lg-3">
      <div class="card mb-4">
        <div class="card-body text-center">
          <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;"><?= h(strtoupper($user['full_name'][0])) ?></div>
          <h5 class="mb-1"><?= h($user['full_name']) ?></h5>
          <p class="text-muted small mb-3"><?= h($user['email']) ?></p>
          <span class="badge bg-<?= $user['is_verified'] ? 'success' : 'warning' ?>"><?= $user['is_verified'] ? 'Verified' : 'Unverified' ?></span>
        </div>
      </div>
      <div class="card">
        <div class="list-group list-group-flush">
          <a href="<?= h(url('account.php')) ?>" class="list-group-item list-group-item-action active">Dashboard</a>
          <a href="<?= h(url('account.php#orders')) ?>" class="list-group-item list-group-item-action">My Orders</a>
          <a href="<?= h(url('account.php#profile')) ?>" class="list-group-item list-group-item-action">Profile</a>
          <a href="<?= h(url('account.php#security')) ?>" class="list-group-item list-group-item-action">Security</a>
          <a href="<?= h(url('logout.php')) ?>" class="list-group-item list-group-item-action text-danger">Logout</a>
        </div>
      </div>
    </div>
    
    <!-- Content -->
    <div class="col-lg-9">
      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4">
        <?= h($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>
      
      <!-- Dashboard -->
      <section id="dashboard" class="mb-5">
        <h2 class="h4 mb-3">Dashboard</h2>
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-value"><?= count($orders) ?></div>
                <div class="stat-label">Total Orders</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-value"><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></div>
                <div class="stat-label">Pending</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-value"><?= count(array_filter($orders, fn($o) => in_array($o['status'], ['paid', 'completed']))) ?></div>
                <div class="stat-label">Completed</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card stat-card">
              <div class="card-body text-center">
                <div class="stat-value"><?= money(array_sum(array_column(array_filter($orders, fn($o) => in_array($o['status'], ['paid', 'completed'])), 'total'))) ?></div>
                <div class="stat-label">Total Spent</div>
              </div>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Orders -->
      <section id="orders" class="mb-5">
        <h2 class="h4 mb-3">Order History</h2>
        <?php if ($orders): ?>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
              <tr>
                <td><code><?= h($o['order_ref']) ?></code></td>
                <td><?= fmt_date($o['created_at'], 'M j, Y') ?></td>
                <td>
                  <?php 
                  $items = Database::fetchAll("SELECT COUNT(*) as c, SUM(qty) as q FROM order_items WHERE order_id = ?", [$o['id']]);
                  echo (int)($items[0]['c'] ?? 0) . ' product(s), ' . (int)($items[0]['q'] ?? 0) . ' items';
                  ?>
                </td>
                <td><strong><?= money($o['total']) ?></strong></td>
                <td><span class="badge bg-<?= $o['status'] === 'pending' ? 'pending' : ($o['status'] === 'paid' ? 'paid' : 'completed') ?>"><?= ucfirst($o['status']) ?></span></td>
                <td>
                  <a href="<?= h(url('account.php?order=' . $o['id'])) ?>" class="btn btn-sm btn-outline-secondary">View</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-4"><i class="bi bi-cart"></i><h5>No orders yet</h5><p class="mb-0"><a href="<?= h(url('shop.php')) ?>" class="btn btn-hmi-green btn-sm mt-2">Browse Shop</a></p></div>
        <?php endif; ?>
      </section>
      
      <!-- Profile -->
      <section id="profile" class="mb-5">
        <h2 class="h4 mb-3">Profile Information</h2>
        <form method="post" action="<?= h(url('actions/customer_profile.php')) ?>" class="card card-body">
          <?= csrf_field() ?>
          <input type="hidden" name="update_profile" value="1">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input class="form-control" name="full_name" value="<?= h($user['full_name']) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input class="form-control" type="email" name="email" value="<?= h($user['email']) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input class="form-control" name="phone" type="tel" value="<?= h($user['phone'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2"><?= h($user['address'] ?? '') ?></textarea>
            </div>
          </div>
          <button class="btn btn-forest mt-3" type="submit">Save Changes</button>
        </form>
      </section>
      
      <!-- Security -->
      <section id="security">
        <h2 class="h4 mb-3">Security</h2>
        <form method="post" action="<?= h(url('actions/customer_password.php')) ?>" class="card card-body">
          <?= csrf_field() ?>
          <input type="hidden" name="change_password" value="1">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Current Password</label>
              <input class="form-control" type="password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="col-md-6">
              <label class="form-label">New Password</label>
              <input class="form-control" type="password" name="new_password" required minlength="8" autocomplete="new-password">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm New Password</label>
              <input class="form-control" type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
            </div>
          </div>
          <button class="btn btn-outline-secondary mt-3" type="submit">Change Password</button>
        </form>
      </section>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>