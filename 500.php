<?php
http_response_code(500);
$page_key   = '500';
$page_title = 'Server Error';
$page_desc  = 'An unexpected error occurred.';
$page_path  = '500.php';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container text-center" style="max-width:640px">
    <div class="icon-chip mx-auto" style="width:72px;height:72px;font-size:2rem"><i class="bi bi-exclamation-triangle-fill" aria-hidden="true" style="color:var(--red)"></i></div>
    <h1 class="h1 mt-3">Something went wrong</h1>
    <p class="lead mx-auto">We're sorry, but an unexpected error occurred. Our team has been notified.</p>
    <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
      <a class="btn btn-hmi-green btn-lg" href="<?= h(url('index.php')) ?>">Go home</a>
      <a class="btn btn-hmi-outline btn-lg" href="<?= h(url('contact.php')) ?>">Contact us</a>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>