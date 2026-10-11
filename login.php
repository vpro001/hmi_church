<?php
/**
 * Customer Login Page
 */

require_once __DIR__ . '/includes/auth.php';

if (customer_check()) {
    redirect('account.php');
}

$page_key = 'login';
$page_title = 'Login';
$page_desc = 'Sign in to your Healing My-Marriage account to manage orders and view history.';
require_once __DIR__ . '/includes/header.php';

$flash = take_flash();
?>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card">
          <div class="card-header text-center">
            <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage" height="60" class="mb-2">
            <h3 class="mb-0">Welcome Back</h3>
            <p class="text-muted mb-0">Sign in to your account</p>
          </div>
          <div class="card-body">
            <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
              <?= h($flash['message']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?= h(url('actions/customer_login.php')) ?>" novalidate>
              <?= csrf_field() ?>
              <input type="hidden" name="redirect" value="<?= h($_GET['redirect'] ?? '') ?>">
              
              <div class="mb-3">
                <label class="form-label" for="email">Email Address</label>
                <input class="form-control" type="email" id="email" name="email" required autocomplete="email" value="<?= old($flash, 'email') ?>">
              </div>
              <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
              </div>
              <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
              </div>
              <button class="btn btn-hmi-red w-100" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i> Sign In</button>
            </form>
            
            <div class="text-center mt-3">
              <a href="<?= h(url('forgot_password.php')) ?>" class="text-decoration-none">Forgot password?</a>
            </div>
            <div class="text-center mt-3">
              <p class="mb-0">Don't have an account? <a href="<?= h(url('register.php')) ?>">Register</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>