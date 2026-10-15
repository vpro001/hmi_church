<?php
$page_key   = 'shop';
$page_title = 'Shop: Christian E-books';
$page_desc  = 'Christian e-books on marriage, parenting and family life from Healing My-Marriage International. Prices in Nigerian Naira.';
$page_path  = 'shop.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Shop';
$hero_sub   = 'Christian e-books on marriage, parenting and family life, written to strengthen your home.';
require __DIR__ . '/includes/page-hero.php';

$products = get_products();
$cats = array_values(array_unique(array_column($products, 'category_name')));
sort($cats);
$minP = $products ? min(array_column($products, 'price')) : 0;
$maxP = $products ? max(array_column($products, 'price')) : 0;
?>

<section class="section pt-5" aria-labelledby="shopTitle">
  <div class="container">
    <h2 id="shopTitle" class="visually-hidden">Products</h2>

    <div class="shop-filters reveal">
      <div class="pill-filters" id="shopCats" role="group" aria-label="Product category">
        <button type="button" class="active" data-cat="all" aria-pressed="true">All</button>
        <?php foreach ($cats as $c): ?><button type="button" data-cat="<?= h($c) ?>" aria-pressed="false"><?= h($c) ?></button><?php endforeach; ?>
      </div>
      <div class="shop-filter-row">
        <div>
          <label class="form-label" for="priceMin">Min price (<?= currency_symbol() ?>)</label>
          <input class="form-control" id="priceMin" type="number" inputmode="numeric" min="0" step="500" placeholder="<?= (int)$minP ?>" style="width:9rem">
        </div>
        <div>
          <label class="form-label" for="priceMax">Max price (<?= currency_symbol() ?>)</label>
          <input class="form-control" id="priceMax" type="number" inputmode="numeric" min="0" step="500" placeholder="<?= (int)$maxP ?>" style="width:9rem">
        </div>
        <div>
          <label class="form-label" for="shopSort">Sort by</label>
          <select class="form-select" id="shopSort">
            <option value="default">Default</option>
            <option value="price-asc">Price: low to high</option>
            <option value="price-desc">Price: high to low</option>
            <option value="name">Title: A to Z</option>
          </select>
        </div>
      </div>
    </div>

    <p class="shop-count mt-4 mb-4" id="shopCount" aria-live="polite"><i class="bi bi-journal-bookmark-fill" aria-hidden="true"></i> <?= count($products) ?> e-books</p>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-3 g-4" id="shopList">
      <?php foreach ($products as $i => $p):
        $onSale = !empty($p['sale_price']) && $p['sale_price'] < $p['price'];
        $coverVariant = 'v' . (($i % 4) + 1);
        $cleanDesc = !empty($p['short_description']) ? $p['short_description'] : ($p['description'] ?? '');
        $cleanDesc = rtrim(preg_replace('/\.\.\.*$/', '', trim($cleanDesc)));
        $discountPct = $onSale ? round((($p['price'] - $p['sale_price']) / $p['price']) * 100) : 0;
      ?>
        <div class="col" data-product data-id="<?= h($p['slug']) ?>" data-cat="<?= h($p['category_name'] ?? '') ?>" data-price="<?= (int)($onSale ? $p['sale_price'] : $p['price']) ?>" data-name="<?= h(mb_strtolower($p['title'])) ?>" data-order="<?= $i ?>">
          <article class="product-card">
            <div class="product-media">
              <span class="badge-format"><i class="bi bi-file-earmark-pdf-fill" aria-hidden="true"></i> <?= h($p['format'] ?? 'E-Book') ?></span>

              <?php if ($onSale): ?>
                <span class="badge-sale-pill"><i class="bi bi-tag-fill me-1" aria-hidden="true"></i> <?= $discountPct > 0 ? '-' . $discountPct . '%' : 'Sale' ?></span>
              <?php elseif (!empty($p['is_featured'])): ?>
                <span class="badge-popular"><i class="bi bi-star-fill me-1" aria-hidden="true"></i> Featured</span>
              <?php endif; ?>

              <a class="product-media-link" href="<?= h(url('product.php?slug=' . $p['slug'])) ?>" aria-label="View details of <?= h($p['title']) ?>">
                <?php if (!empty($p['cover_image'])): ?>
                  <img class="product-img" src="<?= h(url($p['cover_image'])) ?>" alt="Cover of <?= h($p['title']) ?>" loading="lazy">
                <?php else: ?>
                  <div class="book-cover <?= h($coverVariant) ?>" role="img" aria-label="Cover of <?= h($p['title']) ?>">
                    <span class="bk-tag">E-book</span>
                    <span class="bk-title"><?= h($p['title']) ?></span>
                    <span class="bk-by">Healing My-Marriage International</span>
                  </div>
                <?php endif; ?>
              </a>

              <div class="product-quick-view-wrap">
                <a href="<?= h(url('product.php?slug=' . $p['slug'])) ?>" class="btn-quick-view">
                  <i class="bi bi-eye-fill me-1" aria-hidden="true"></i> Quick View
                </a>
              </div>
            </div>

            <div class="product-card-body">
              <div class="product-meta-row">
                <span class="product-cat-tag"><?= h($p['category_name'] ?? 'Christian E-Book') ?></span>
                <span class="product-delivery-tag"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Instant</span>
              </div>

              <h3 class="product-title">
                <a href="<?= h(url('product.php?slug=' . $p['slug'])) ?>"><?= h($p['title']) ?></a>
              </h3>

              <?php if (!empty($cleanDesc)): ?>
                <p class="product-synopsis"><?= h($cleanDesc) ?></p>
              <?php endif; ?>

              <div class="product-trust-row">
                <div class="rating-stars" aria-label="5 stars">
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                  <i class="bi bi-star-fill" aria-hidden="true"></i>
                </div>
                <span class="rating-label">Biblical Guidance</span>
              </div>

              <div class="product-footer">
                <div class="product-pricing">
                  <?php if ($onSale): ?>
                    <div class="price-current is-sale"><?= money($p['sale_price']) ?></div>
                    <div class="price-original"><?= money($p['price']) ?></div>
                    <span class="price-save-badge">Save <?= money($p['price'] - $p['sale_price']) ?></span>
                  <?php else: ?>
                    <span class="price-label">Price</span>
                    <div class="price-current"><?= money($p['price']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="product-actions">
                  <button class="btn btn-cart-action add-to-cart" type="button" data-id="<?= h($p['slug']) ?>" data-title="<?= h($p['title']) ?>" data-price="<?= (int)($onSale ? $p['sale_price'] : $p['price']) ?>">
                    <i class="bi bi-bag-plus-fill" aria-hidden="true"></i> <span>Add to Cart</span>
                  </button>
                  <a href="<?= h(url('product.php?slug=' . $p['slug'])) ?>" class="btn-card-detail" title="View details" aria-label="View details of <?= h($p['title']) ?>">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                  </a>
                </div>
              </div>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>

    <div id="shopEmpty" class="empty-state mt-4" hidden>
      <i class="bi bi-search" aria-hidden="true"></i>
      <h3 class="mt-3">No e-books match those filters</h3>
      <p class="mx-auto mb-3">Clear the price range or choose All.</p>
      <button class="btn btn-hmi-outline" type="button" id="shopReset">Reset filters</button>
    </div>
  </div>
</section>

<section class="section section-white" aria-labelledby="howTitle">
  <div class="container">
    <div class="section-head center reveal">
      <p class="eyebrow">How ordering works</p>
      <h2 id="howTitle">From cart to your inbox</h2>
    </div>
    <div class="steps">
      <div class="step reveal"><div class="chip"><i class="bi bi-bag-plus-fill" aria-hidden="true"></i></div><h3 class="h5">Add to cart</h3><p>Choose one or more e-books. Prices are in Nigerian Naira.</p></div>
      <div class="step reveal"><div class="chip"><i class="bi bi-send-fill" aria-hidden="true"></i></div><h3 class="h5">Send your request</h3><p>Open your cart, enter your name, email and phone, and send the order request.</p></div>
      <div class="step reveal"><div class="chip"><i class="bi bi-envelope-heart-fill" aria-hidden="true"></i></div><h3 class="h5">Receive your e-book</h3><p>The ministry contacts you with payment options and sends your e-book by email.</p></div>
    </div>
    <p class="text-center mt-4 mb-0 form-privacy mx-auto">Questions about an order? Call <a href="<?= h(tel_link(site_phones_raw()[0] ?? '+2347042226633')) ?>"><?= h(site_phones()[0] ?? '+234 704 222 6633') ?></a> or <a href="<?= h(url('contact.php')) ?>">send us a message</a>.</p>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>