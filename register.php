<?php
/**
 * Customer Registration Page
 */

require_once __DIR__ . '/includes/auth.php';

if (customer_check()) {
    redirect('account.php');
}

$page_key = 'register';
$page_title = 'Register';
$page_desc = 'Create an account to manage orders and view history.';
require_once __DIR__ . '/includes/header.php';

$flash = take_flash();
?>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-6">
        <div class="card">
          <div class="card-header text-center">
            <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage" height="60" class="mb-2">
            <h3 class="mb-0">Create Account</h3>
            <p class="text-muted mb-0">Join Healing My-Marriage International</p>
          </div>
          <div class="card-body">
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
              <?= h($flash['message']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?= h(url('actions/customer_register.php')) ?>" novalidate>
              <?= csrf_field() ?>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="first_name">First Name</label>
                  <input class="form-control" type="text" id="first_name" name="first_name" required autocomplete="given-name" value="<?= old($flash, 'first_name') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="last_name">Last Name</label>
                  <input class="form-control" type="text" id="last_name" name="last_name" required autocomplete="family-name" value="<?= old($flash, 'last_name') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label" for="email">Email Address</label>
                  <input class="form-control" type="email" id="email" name="email" required autocomplete="email" value="<?= old($flash, 'email') ?>">
                </div>
                <div class="col-12">
                  <label class="form-label" for="phone">Phone Number</label>
                  <input class="form-control" type="tel" id="phone" name="phone" autocomplete="tel" value="<?= old($flash, 'phone') ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="password">Password</label>
                  <input class="form-control" type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                  <small class="text-muted">At least 8 characters</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="confirm_password">Confirm Password</label>
                  <input class="form-control" type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
                </div>
                <div class="col-12">
                  <label class="form-label" for="address">Address (optional)</label>
                  <textarea class="form-control" id="address" name="address" rows="2" autocomplete="street-address"><?= old($flash, 'address') ?></textarea>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">I agree to the <a href="<?= h(url('privacy.php#terms')) ?>" target="_blank">Terms of Use</a> and <a href="<?= h(url('privacy.php')) ?>" target="_blank">Privacy Policy</a></label>
                  </div>
                </div>
              </div>
              
              <button class="btn btn-hmi-red w-100 mt-3" type="submit"><i class="bi bi-person-plus me-2"></i> Create Account</button>
            </form>
            
            <div class="text-center mt-3">
              <p class="mb-0">Already have an account? <a href="<?= h(url('login.php')) ?>">Sign in</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>