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
$suppress_flash_display = true; // render flash inside the form panel instead
require_once __DIR__ . '/includes/header.php';
?>

<section class="auth-shell">
  <div class="auth-visual">
    <img class="auth-photo" src="<?= h(asset('img/live/marriage-counselling.jpg')) ?>" alt="" aria-hidden="true">
    <div class="auth-visual-scrim" aria-hidden="true"></div>
    <div class="auth-visual-inner">
      <a class="auth-brand" href="<?= h(url('index.php')) ?>" aria-label="Healing My-Marriage International home">
        <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International" width="508" height="286">
      </a>
      <div class="auth-visual-copy">
        <p class="eyebrow eyebrow-light">Join the community</p>
        <h2>Reposition. Refix. <em>Rekindle</em> your marriage.</h2>
        <p>Create an account to manage your orders, keep your e-books in one place and stay close to the ministry&rsquo;s teaching.</p>
      </div>
      <ul class="auth-trust">
        <li><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Your details are kept in confidence</li>
        <li><i class="bi bi-heart-fill" aria-hidden="true"></i> A faith-based community that prays together</li>
      </ul>
    </div>
  </div>

  <div class="auth-panel">
    <div class="auth-panel-inner">
      <div class="auth-head">
        <p class="eyebrow">Create account</p>
        <h1>Start your journey with us</h1>
        <p class="auth-sub">Join Healing My-Marriage International in a moment.</p>
      </div>

      <?php if (isset($_GET['notice']) && $_GET['notice'] === 'login_required'): ?>
      <div class="alert alert-info alert-dismissible fade show">
        <i class="bi bi-info-circle-fill me-1"></i> Create an account to add items to your cart and buy e-books.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <?php if ($flash): ?>
      <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= h($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <form class="auth-form" method="post" action="<?= h(url('actions/customer_register.php')) ?>" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= h($_GET['redirect'] ?? '') ?>">

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="first_name">First Name</label>
            <input class="form-control<?= field_invalid($flash, 'first_name') ?>" type="text" id="first_name" name="first_name" required autocomplete="given-name" value="<?= old($flash, 'first_name') ?>">
            <?= field_error($flash, 'first_name') ?>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="last_name">Last Name</label>
            <input class="form-control<?= field_invalid($flash, 'last_name') ?>" type="text" id="last_name" name="last_name" required autocomplete="family-name" value="<?= old($flash, 'last_name') ?>">
            <?= field_error($flash, 'last_name') ?>
          </div>
          <div class="col-12">
            <label class="form-label" for="email">Email Address</label>
            <input class="form-control<?= field_invalid($flash, 'email') ?>" type="email" id="email" name="email" required autocomplete="email" value="<?= old($flash, 'email') ?>">
            <?= field_error($flash, 'email') ?>
          </div>
          <div class="col-12">
            <label class="form-label" for="phone">Phone Number</label>
            <input class="form-control<?= field_invalid($flash, 'phone') ?>" type="tel" id="phone" name="phone" autocomplete="tel" value="<?= old($flash, 'phone') ?>">
            <?= field_error($flash, 'phone') ?>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="password">Password</label>
            <input class="form-control<?= field_invalid($flash, 'password') ?>" type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <small class="text-muted">At least 8 characters</small>
            <?= field_error($flash, 'password') ?>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="confirm_password">Confirm Password</label>
            <input class="form-control<?= field_invalid($flash, 'confirm_password') ?>" type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
            <?= field_error($flash, 'confirm_password') ?>
          </div>
          <div class="col-12">
            <label class="form-label" for="address">Address <span class="text-muted">(optional)</span></label>
            <textarea class="form-control" id="address" name="address" rows="2" autocomplete="street-address"><?= old($flash, 'address') ?></textarea>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input<?= field_invalid($flash, 'terms') ?>" type="checkbox" name="terms" id="terms" required>
              <label class="form-check-label auth-terms" for="terms">I agree to the <a href="<?= h(url('privacy.php#terms')) ?>" target="_blank" rel="noopener">Terms of Use</a> and <a href="<?= h(url('privacy.php')) ?>" target="_blank" rel="noopener">Privacy Policy</a></label>
              <?= field_error($flash, 'terms') ?>
            </div>
          </div>
        </div>

        <button class="btn btn-hmi-red w-100 mt-4" type="submit"><i class="bi bi-person-plus me-2" aria-hidden="true"></i> Create Account</button>
      </form>

      <div class="auth-alt">
        <p class="mb-0">Already have an account? <a href="<?= h(url('login.php' . (!empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''))) ?>">Sign in</a></p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
