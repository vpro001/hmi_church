<?php
http_response_code(404);
$page_key   = '404';
$page_title = 'Page not found';
$page_desc  = 'The page you were looking for could not be found.';
$page_path  = '404.php';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container text-center" style="max-width:640px">
    <div class="icon-chip mx-auto" style="width:72px;height:72px;font-size:2rem"><i class="bi bi-compass-fill" aria-hidden="true"></i></div>
    <h1 class="h1 mt-3">We could not find that page</h1>
    <p class="lead mx-auto">It may have moved. Let's get you back on track.</p>
    <div class="d-flex flex-wrap gap-3 justify-content-center">
      <a class="btn btn-hmi-green btn-lg" href="<?= h(url('index.php')) ?>">Go home</a>
      <a class="btn btn-hmi-outline btn-lg" href="<?= h(url('contact.php')) ?>">Contact us</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
