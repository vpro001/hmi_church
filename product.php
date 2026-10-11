<?php
require_once __DIR__ . '/includes/functions.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['slug'] ?? '')));
$product = get_product_by_slug($slug);

if (!$product) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$page_key = 'shop';
$page_title = $product['title'];
$page_desc = $product['short_description'] ?? $product['description'] ?? 'Christian e-book from Healing My-Marriage International.';
$page_path = 'product.php?slug=' . $product['slug'];
require __DIR__ . '/includes/header.php';
$hero_title = $product['title'];
$hero_sub = $product['category_name'] ?? 'Christian E-book';
require __DIR__ . '/includes/page-hero.php';
?>

<section class="section pt-0">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-6">
        <?php if (!empty($product['cover_image'])): ?>
        <div class="ratio ratio-3x4" style="max-width: 400px; margin: 0 auto;">
          <img src="<?= h(url($product['cover_image'])) ?>" alt="Cover of <?= h($product['title']) ?>" class="rounded shadow-sm" style="object-fit: cover; width: 100%; height: 100%;">
        </div>
        <?php else: ?>
        <div class="book-cover v1 d-flex flex-column align-items-center justify-content-center" style="max-width: 400px; margin: 0 auto; height: 500px; background: linear-gradient(135deg, var(--forest-700), var(--forest-500)); color: white; border-radius: 12px;">
          <span class="bk-tag">E-book</span>
          <h2 class="bk-title mt-3"><?= h($product['title']) ?></h2>
          <span class="bk-by">Healing My-Marriage International</span>
        </div>
        <?php endif; ?>
      </div>
      <div class="col-lg-6">
        <span class="badge-hmi mb-3"><?= h($product['category_name'] ?? 'Christian E-book') ?></span>
        <h1 class="h3"><?= h($product['title']) ?></h1>
        <p class="lead text-muted"><?= h($product['format'] ?? 'E-book (digital)') ?></p>
        
        <?php if ($product['sale_price'] && $product['sale_price'] < $product['price']): ?>
        <div class="mb-3">
          <span class="text-decoration-line-through text-muted me-2"><?= money($product['price']) ?></span>
          <strong class="text-success fs-4"><?= money($product['sale_price']) ?></strong>
        </div>
        <?php else: ?>
        <div class="mb-3">
          <strong class="fs-4"><?= money($product['price']) ?></strong>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($product['description'])): ?>
        <div class="prose mb-4">
          <?= nl2br(h($product['description'])) ?>
        </div>
        <?php endif; ?>
        
        <div class="d-flex flex-wrap gap-3">
          <button class="btn btn-hmi-green btn-lg flex-grow-1 add-to-cart" type="button" data-id="<?= h($product['slug']) ?>" data-title="<?= h($product['title']) ?>" data-price="<?= (int)($product['sale_price'] ?? $product['price']) ?>">
            <i class="bi bi-bag-plus me-2"></i> Add to Cart
          </button>
          <a href="<?= h(url('shop.php')) ?>" class="btn btn-hmi-outline btn-lg">Continue Shopping</a>
        </div>
        
        <hr class="my-4">
        
        <div class="row g-3 text-center">
          <div class="col-6 col-md-3">
            <i class="bi bi-file-earmark-text fs-3 text-success"></i>
            <div class="small mt-1">Digital Download</div>
          </div>
          <div class="col-6 col-md-3">
            <i class="bi bi-shield-lock fs-3 text-success"></i>
            <div class="small mt-1">Secure Order</div>
          </div>
          <div class="col-6 col-md-3">
            <i class="bi bi-envelope fs-3 text-success"></i>
            <div class="small mt-1">Email Delivery</div>
          </div>
          <div class="col-6 col-md-3">
            <i class="bi bi-headset fs-3 text-success"></i>
            <div class="small mt-1">Support Available</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>