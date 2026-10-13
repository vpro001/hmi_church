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

$hero_title = 'My Account';
$hero_sub   = 'Welcome back, ' . $user['full_name'] . '. Manage your orders and profile here.';
require __DIR__ . '/includes/page-hero.php';
?>

<div class="container acct-shell">
  <div class="row g-4">
    <!-- Sidebar -->
    <div class="col-lg-3">
      <div class="acct-card acct-profile mb-4">
        <div class="avatar-circle"><?= h(strtoupper($user['full_name'][0])) ?></div>
        <h2><?= h($user['full_name']) ?></h2>
        <p class="email mb-3"><?= h($user['email']) ?></p>
        <span class="acct-verified <?= $user['is_verified'] ? 'yes' : 'no' ?>"><i class="bi bi-<?= $user['is_verified'] ? 'check-circle-fill' : 'exclamation-circle-fill' ?>" aria-hidden="true"></i> <?= $user['is_verified'] ? 'Verified' : 'Unverified' ?></span>
      </div>
      <div class="acct-card">
        <nav class="acct-nav" aria-label="Account navigation">
          <a href="<?= h(url('account.php')) ?>" class="active"><i class="bi bi-grid-fill" aria-hidden="true"></i> Dashboard</a>
          <a href="<?= h(url('account.php#orders')) ?>"><i class="bi bi-bag-fill" aria-hidden="true"></i> My Orders</a>
          <a href="<?= h(url('account.php#profile')) ?>"><i class="bi bi-person-fill" aria-hidden="true"></i> Profile</a>
          <a href="<?= h(url('account.php#security')) ?>"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Security</a>
          <a href="<?= h(url('logout.php')) ?>" class="logout"><i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout</a>
        </nav>
      </div>
    </div>

    <!-- Content -->
    <div class="col-lg-9">
      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4">
        <?= h($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <!-- Dashboard -->
      <section id="dashboard" class="mb-5">
        <div class="section-head mb-3" style="max-width:none">
          <p class="eyebrow">Overview</p>
          <h2 class="h3 mb-0">Dashboard</h2>
        </div>
        <div class="acct-stats">
          <div class="stat-card">
            <div class="stat-value"><?= count($orders) ?></div>
            <div class="stat-label">Total Orders</div>
          </div>
          <div class="stat-card">
            <div class="stat-value"><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></div>
            <div class="stat-label">Pending</div>
          </div>
          <div class="stat-card">
            <div class="stat-value"><?= count(array_filter($orders, fn($o) => in_array($o['status'], ['paid', 'completed']))) ?></div>
            <div class="stat-label">Completed</div>
          </div>
          <div class="stat-card">
            <div class="stat-value"><?= money(array_sum(array_column(array_filter($orders, fn($o) => in_array($o['status'], ['paid', 'completed'])), 'total'))) ?></div>
            <div class="stat-label">Total Spent</div>
          </div>
        </div>
      </section>

      <!-- Orders -->
      <section id="orders" class="mb-5">
        <div class="section-head mb-3" style="max-width:none">
          <p class="eyebrow">Your purchases</p>
          <h2 class="h3 mb-0">Order History</h2>
        </div>
        <?php if ($orders): ?>
        <div class="acct-card p-lg-2">
          <div class="table-responsive">
            <table class="table table-hover align-middle acct-table">
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
                  <td><span class="badge-order <?= h($o['status']) ?>"><?= ucfirst($o['status']) ?></span></td>
                  <td class="text-end">
                    <a href="<?= h(url('account.php?order=' . $o['id'])) ?>" class="btn btn-hmi-outline btn-sm">View</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="bi bi-bag" aria-hidden="true"></i><h3 class="mt-3">No orders yet</h3><p class="mx-auto mb-3">When you order an e-book, it will show up here.</p><a href="<?= h(url('shop.php')) ?>" class="btn btn-hmi-green">Browse Shop</a></div>
        <?php endif; ?>
      </section>

      <!-- Profile -->
      <section id="profile" class="mb-5">
        <div class="acct-section">
          <p class="eyebrow">Your details</p>
          <h2>Profile Information</h2>
          <form method="post" action="<?= h(url('actions/customer_profile.php')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="update_profile" value="1">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label" for="full_name">Full Name</label>
                <input class="form-control" id="full_name" name="full_name" value="<?= h($user['full_name']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="profile_email">Email</label>
                <input class="form-control" id="profile_email" type="email" name="email" value="<?= h($user['email']) ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label" for="profile_phone">Phone</label>
                <input class="form-control" id="profile_phone" name="phone" type="tel" value="<?= h($user['phone'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label" for="profile_address">Address</label>
                <textarea class="form-control" id="profile_address" name="address" rows="2"><?= h($user['address'] ?? '') ?></textarea>
              </div>
            </div>
            <button class="btn btn-hmi-green mt-4" type="submit"><i class="bi bi-check2 me-1" aria-hidden="true"></i> Save Changes</button>
          </form>
        </div>
      </section>

      <!-- Security -->
      <section id="security">
        <div class="acct-section">
          <p class="eyebrow">Keep it safe</p>
          <h2>Security</h2>
          <form method="post" action="<?= h(url('actions/customer_password.php')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="change_password" value="1">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label" for="current_password">Current Password</label>
                <input class="form-control" id="current_password" type="password" name="current_password" required autocomplete="current-password">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="new_password">New Password</label>
                <input class="form-control" id="new_password" type="password" name="new_password" required minlength="8" autocomplete="new-password">
              </div>
              <div class="col-md-4">
                <label class="form-label" for="confirm_password">Confirm New Password</label>
                <input class="form-control" id="confirm_password" type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
              </div>
            </div>
            <button class="btn btn-hmi-outline mt-4" type="submit"><i class="bi bi-shield-lock me-1" aria-hidden="true"></i> Change Password</button>
          </form>
        </div>
      </section>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
