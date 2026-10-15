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

<section class="section pt-5">
  <div class="container">
    <?php
    $onSale = $product['sale_price'] && $product['sale_price'] < $product['price'];
    $coverVariant = 'v' . ((crc32($product['slug']) % 4) + 1);
    ?>
    <a class="product-back" href="<?= h(url('shop.php')) ?>">Back to shop</a>

    <div class="row g-5">
      <div class="col-lg-6">
        <div class="product-frame">
          <span class="corner tl" aria-hidden="true"></span>
          <span class="corner br" aria-hidden="true"></span>
          <div class="product-frame-inner">
            <?php if (!empty($product['cover_image'])): ?>
              <img src="<?= h(url($product['cover_image'])) ?>" alt="Cover of <?= h($product['title']) ?>">
            <?php else: ?>
              <div class="book-cover <?= h($coverVariant) ?>" role="img" aria-label="Cover of <?= h($product['title']) ?>">
                <span class="bk-tag">E-book</span>
                <span class="bk-title"><?= h($product['title']) ?></span>
                <span class="bk-by">Healing My-Marriage International</span>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <span class="badge-hmi mb-3"><?= h($product['category_name'] ?? 'Christian E-book') ?></span>
        <h1 class="h2 mt-2"><?= h($product['title']) ?></h1>
        <p class="lead text-muted"><?= h($product['format'] ?? 'E-book (digital)') ?></p>

        <div class="product-price-card">
          <?php if ($onSale): ?>
            <span class="now on-sale"><?= money($product['sale_price']) ?></span>
            <span class="was"><?= money($product['price']) ?></span>
            <span class="save">You save <?= money($product['price'] - $product['sale_price']) ?></span>
          <?php else: ?>
            <span class="now"><?= money($product['price']) ?></span>
          <?php endif; ?>
        </div>

        <?php if (!empty($product['description'])): ?>
        <div class="prose mb-4">
          <?= nl2br(h($product['description'])) ?>
        </div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-3 mb-4">
          <button class="btn btn-hmi-red btn-lg flex-grow-1 add-to-cart" type="button" data-id="<?= h($product['slug']) ?>" data-title="<?= h($product['title']) ?>" data-price="<?= (int)($product['sale_price'] ?? $product['price']) ?>">
            <i class="bi bi-bag-plus me-2" aria-hidden="true"></i> Add to Cart
          </button>
          <a href="<?= h(url('shop.php')) ?>" class="btn btn-hmi-outline btn-lg">Continue Shopping</a>
        </div>

        <ul class="detail-list mb-4">
          <li><i class="bi bi-file-earmark-arrow-down-fill" aria-hidden="true"></i> Instant digital download (PDF)</li>
          <li><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Secure order &mdash; the ministry contacts you with payment options</li>
          <li><i class="bi bi-envelope-heart-fill" aria-hidden="true"></i> Delivered to your email inbox</li>
          <li><i class="bi bi-headset" aria-hidden="true"></i> Need help? Call <a href="<?= h(tel_link(site_phones_raw()[0] ?? '+2347042226633')) ?>"><?= h(site_phones()[0] ?? '+234 704 222 6633') ?></a></li>
        </ul>

        <div class="trust-strip">
          <div class="trust-item"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>Digital Download</span></div>
          <div class="trust-item"><i class="bi bi-shield-lock" aria-hidden="true"></i><span>Secure Order</span></div>
          <div class="trust-item"><i class="bi bi-envelope" aria-hidden="true"></i><span>Email Delivery</span></div>
          <div class="trust-item"><i class="bi bi-headset" aria-hidden="true"></i><span>Support Available</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>