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
$suppress_flash_display = true; // render flash inside the form panel instead
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-shell">
  <div class="auth-visual">
    <img class="auth-photo" src="<?= h(asset('img/live/prayer.jpg')) ?>" alt="" aria-hidden="true">
    <div class="auth-visual-scrim" aria-hidden="true"></div>
    <div class="auth-visual-inner">
      <a class="auth-brand" href="<?= h(url('index.php')) ?>" aria-label="Healing My-Marriage International home">
        <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International" width="508" height="286">
      </a>
      <div class="auth-visual-copy">
        <p class="eyebrow eyebrow-light">Welcome back</p>
        <h2>Your marriage, your books, <em>one place.</em></h2>
        <p>Sign in to manage your orders, revisit the teaching you&rsquo;ve bought and stay connected with the ministry.</p>
      </div>
      <ul class="auth-trust">
        <li><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Secure and confidential</li>
        <li><i class="bi bi-journal-text" aria-hidden="true"></i> Access your e-books anytime</li>
      </ul>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-panel-inner">
      <div class="auth-head">
        <p class="eyebrow">Sign in</p>
        <h1>Welcome back</h1>
        <p class="auth-sub">Enter your details to continue.</p>
      </div>

      <?php if (isset($_GET['notice']) && $_GET['notice'] === 'login_required'): ?>
      <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-info-circle-fill me-1"></i> Please sign in or create an account to add items to your cart and buy e-books.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= h($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <form class="auth-form" method="post" action="<?= h(url('actions/customer_login.php')) ?>" novalidate>
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
        <div class="auth-links">
          <div class="form-check m-0">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          <a href="<?= h(url('forgot_password.php')) ?>">Forgot password?</a>
        </div>
        <button class="btn btn-hmi-red w-100" type="submit"><i class="bi bi-box-arrow-in-right me-2" aria-hidden="true"></i> Sign In</button>
      </form>

      <div class="auth-alt">
        <p class="mb-0">Don&rsquo;t have an account? <a href="<?= h(url('register.php' . (!empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''))) ?>">Create one</a></p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
