<?php
/**
 * Forgot Password Page
 */

require_once __DIR__ . '/includes/auth.php';

if (customer_check()) {
    redirect('account.php');
}

$page_key = 'forgot_password';
$page_title = 'Forgot Password';
$page_desc = 'Reset your Healing My-Marriage account password.';
require_once __DIR__ . '/includes/header.php';

$flash = take_flash();
$step = $_GET['step'] ?? 'request';
$token = $_GET['token'] ?? '';
?>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card">
          <div class="card-header text-center">
            <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage" height="60" class="mb-2">
            <h3 class="mb-0"><?= $step === 'reset' ? 'Reset Password' : 'Forgot Password' ?></h3>
            <p class="text-muted mb-0"><?= $step === 'reset' ? 'Enter your new password' : 'Enter your email to receive a reset link' ?></p>
          </div>
          <div class="card-body">
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
              <?= h($flash['message']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($step === 'request'): ?>
            <form method="post" action="<?= h(url('actions/forgot_password.php')) ?>" novalidate>
              <?= csrf_field() ?>
              <div class="mb-3">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-control" type="email" id="email" name="email" required autocomplete="email" value="<?= old($flash, 'email') ?>">
              </div>
              <button class="btn btn-hmi-red w-100" type="submit">Send Reset Link</button>
            </form>
            <div class="text-center mt-3">
              <a href="<?= h(url('login.php')) ?>" class="text-decoration-none">Back to login</a>
            </div>
            
            <?php elseif ($step === 'reset' && $token): ?>
            <form method="post" action="<?= h(url('actions/reset_password.php')) ?>" novalidate>
              <?= csrf_field() ?>
              <input type="hidden" name="token" value="<?= h($token) ?>">
              <div class="mb-3">
                <label class="form-label" for="password">New Password</label>
                <input class="form-control" type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                <small class="text-muted">At least 8 characters</small>
              </div>
              <div class="mb-3">
                <label class="form-label" for="confirm_password">Confirm New Password</label>
                <input class="form-control" type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
              </div>
              <button class="btn btn-hmi-red w-100" type="submit">Reset Password</button>
            </form>
            
            <?php else: ?>
            <div class="text-center">
              <i class="bi bi-envelope-check fs-1 text-success mb-3"></i>
              <h4>Check Your Email</h4>
              <p class="text-muted">We've sent a password reset link to your email address. Please check your inbox (and spam folder).</p>
              <a href="<?= h(url('login.php')) ?>" class="btn btn-hmi-green mt-3">Back to Login</a>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>