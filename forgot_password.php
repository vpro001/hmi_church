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
$suppress_flash_display = true; // render flash inside the form panel instead
require_once __DIR__ . '/includes/header.php';

$step  = $_GET['step'] ?? 'request';
$token = $_GET['token'] ?? '';
$isReset  = ($step === 'reset' && $token);
$isSent   = ($step === 'sent' || (!$isReset && $step !== 'request'));
?>

<section class="auth-shell">
  <div class="auth-visual">
    <img class="auth-photo" src="<?= h(asset('img/live/heart.webp')) ?>" alt="" aria-hidden="true">
    <div class="auth-visual-scrim" aria-hidden="true"></div>
    <div class="auth-visual-inner">
      <a class="auth-brand" href="<?= h(url('index.php')) ?>" aria-label="Healing My-Marriage International home">
        <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International" width="508" height="286">
      </a>
      <div class="auth-visual-copy">
        <p class="eyebrow eyebrow-light">We&rsquo;ve got you</p>
        <h2>A small step back to <em>what matters.</em></h2>
        <p>Resetting your password takes a moment. Your books, orders and connection to the ministry stay right where you left them.</p>
      </div>
      <ul class="auth-trust">
        <li><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Secure, confidential reset</li>
        <li><i class="bi bi-heart-fill" aria-hidden="true"></i> We&rsquo;re here if you need a hand</li>
      </ul>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-panel-inner">
      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= h($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <?php if ($isSent): ?>
        <div class="auth-head text-center">
          <div class="icon-chip green mx-auto mb-3" style="width:64px;height:64px;font-size:1.9rem"><i class="bi bi-envelope-check-fill" aria-hidden="true"></i></div>
          <h1>Check your email</h1>
          <p class="auth-sub mb-4">We&rsquo;ve sent a password reset link to your email address. Please check your inbox (and spam folder).</p>
        </div>
        <a class="btn btn-hmi-green w-100" href="<?= h(url('login.php')) ?>">Back to Login</a>

      <?php elseif ($isReset): ?>
        <div class="auth-head">
          <p class="eyebrow">Reset password</p>
          <h1>Choose a new password</h1>
          <p class="auth-sub">Enter a new password for your account.</p>
        </div>

        <form class="auth-form" method="post" action="<?= h(url('actions/reset_password.php')) ?>" novalidate>
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
          <button class="btn btn-hmi-red w-100 mt-2" type="submit">Reset Password</button>
        </form>

      <?php else: ?>
        <div class="auth-head">
          <p class="eyebrow">Account recovery</p>
          <h1>Forgot your password?</h1>
          <p class="auth-sub">Enter your email and we&rsquo;ll send you a link to reset it.</p>
        </div>

        <form class="auth-form" method="post" action="<?= h(url('actions/forgot_password.php')) ?>" novalidate>
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <input class="form-control" type="email" id="email" name="email" required autocomplete="email" value="<?= old($flash, 'email') ?>">
          </div>
          <button class="btn btn-hmi-red w-100" type="submit">Send Reset Link</button>
        </form>

        <div class="auth-alt">
          <p class="mb-0">Remembered it? <a href="<?= h(url('login.php')) ?>">Back to login</a></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
