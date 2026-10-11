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

<section class="section pt-0" aria-labelledby="shopTitle">
  <div class="container">
    <h2 id="shopTitle" class="visually-hidden">Products</h2>

    <div class="shop-toolbar reveal">
      <div class="pill-filters" id="shopCats" role="group" aria-label="Product category">
        <button type="button" class="active" data-cat="all" aria-pressed="true">All</button>
        <?php foreach ($cats as $c): ?><button type="button" data-cat="<?= h($c) ?>" aria-pressed="false"><?= h($c) ?></button><?php endforeach; ?>
      </div>
      <div class="d-flex flex-wrap gap-3 align-items-end">
        <div>
          <label class="form-label mb-1" for="priceMin">Min price (<?= currency_symbol() ?>)</label>
          <input class="form-control" id="priceMin" type="number" inputmode="numeric" min="0" step="500" placeholder="<?= (int)$minP ?>" style="width:9rem">
        </div>
        <div>
          <label class="form-label mb-1" for="priceMax">Max price (<?= currency_symbol() ?>)</label>
          <input class="form-control" id="priceMax" type="number" inputmode="numeric" min="0" step="500" placeholder="<?= (int)$maxP ?>" style="width:9rem">
        </div>
        <div>
          <label class="form-label mb-1" for="shopSort">Sort by</label>
          <select class="form-select" id="shopSort">
            <option value="default">Default</option>
            <option value="price-asc">Price: low to high</option>
            <option value="price-desc">Price: high to low</option>
            <option value="name">Title: A to Z</option>
          </select>
        </div>
      </div>
    </div>

    <p class="text-muted mt-3 mb-4" id="shopCount" aria-live="polite"><?= count($products) ?> e-books</p>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-4" id="shopList">
      <?php foreach ($products as $i => $p): ?>
        <div class="col" data-product data-id="<?= h($p['slug']) ?>" data-cat="<?= h($p['category_name'] ?? '') ?>" data-price="<?= (int)$p['price'] ?>" data-name="<?= h(mb_strtolower($p['title'])) ?>" data-order="<?= $i ?>">
          <article class="hmi-card product-card">
            <?php if (!empty($p['cover_image'])): ?>
              <img class="product-img" src="<?= h(url($p['cover_image'])) ?>" alt="Cover of <?= h($p['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="book-cover v1" role="img" aria-label="Cover of <?= h($p['title']) ?>">
                <span class="bk-tag">E-book</span>
                <span class="bk-title"><?= h($p['title']) ?></span>
                <span class="bk-by">Healing My-Marriage International</span>
              </div>
            <?php endif; ?>
            <div class="body">
              <span class="badge-hmi align-self-start mb-2"><?= h($p['category_name'] ?? 'Christian E-book') ?></span>
              <h3 class="h6 product-title"><?= h($p['title']) ?></h3>
              <?php if (!empty($p['short_description'])): ?><p class="small"><?= h($p['short_description']) ?></p><?php endif; ?>
              <div class="d-flex justify-content-between align-items-center mt-auto pt-2 gap-2 flex-wrap">
                <strong class="price"><?= money($p['price']) ?></strong>
                <a href="<?= h(url('product.php?slug=' . $p['slug'])) ?>" class="btn btn-hmi-outline btn-sm"><i class="bi bi-eye me-1"></i> View</a>
                <button class="btn btn-hmi-green btn-sm add-to-cart" type="button" data-id="<?= h($p['slug']) ?>" data-title="<?= h($p['title']) ?>" data-price="<?= (int)$p['price'] ?>"><i class="bi bi-bag-plus" aria-hidden="true"></i> Add to cart</button>
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