<?php
$page_key   = 'resources';
$page_title = 'Resources and Updates';
$page_desc  = 'Teaching, news and encouragement for marriages and families from Healing My-Marriage International.';
$page_path  = 'resources.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Resources and updates';
$hero_sub   = 'Teaching, news and encouragement for your marriage and family.';
require __DIR__ . '/includes/page-hero.php';

$posts = get_posts();
$cats  = array_values(array_unique(array_column($posts, 'category')));
sort($cats);
?>

<section class="section pt-5">
  <div class="container">
    <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center mb-4 reveal">
      <div class="search-box w-100">
        <i class="bi bi-search" aria-hidden="true"></i>
        <label class="visually-hidden" for="resourceSearch">Search resources</label>
        <input class="form-control" type="search" id="resourceSearch" placeholder="Search by title or topic">
      </div>
      <div class="pill-filters" role="group" aria-label="Filter by topic">
        <button type="button" class="active" data-cat="all" aria-pressed="true">All</button>
        <?php foreach ($cats as $c): ?>
          <button type="button" data-cat="<?= h($c) ?>" aria-pressed="false"><?= h($c) ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="row g-4" id="resourceList" data-empty="#noResults">
      <?php foreach ($posts as $p): ?>
        <div class="col-md-6 col-lg-4" data-filter-item data-cat="<?= h($p['category'] ?? '') ?>" data-text="<?= h(mb_strtolower(($p['title'] ?? '') . ' ' . ($p['excerpt'] ?? '') . ' ' . ($p['category'] ?? ''))) ?>">
          <article class="hmi-card">
            <div class="cover v1" aria-hidden="true"><span class="tag"><?= h($p['category'] ?? 'Article') ?></span><i class="bi bi-journal-text"></i></div>
            <div class="body">
              <div class="meta"><span><?= h(fmt_date($p['published_at'] ?? $p['created_at'])) ?></span></div>
              <h3><a class="text-decoration-none" style="color:inherit" href="<?= h(url('post.php?slug=' . $p['slug'])) ?>"><?= h($p['title']) ?></a></h3>
              <p><?= h($p['excerpt']) ?></p>
              <a class="link-arrow mt-auto" href="<?= h(url('post.php?slug=' . $p['slug'])) ?>">Read more</a>
            </div>
          </article>
        </div>
      <?php endforeach; ?>
    </div>
    <div id="noResults" class="empty-state mt-4" hidden>
      <i class="bi bi-search" aria-hidden="true"></i>
      <h3 class="mt-3">Nothing matches that search</h3>
      <p class="mx-auto mb-0">Try a different word, or choose All.</p>
    </div>

    <div class="row mt-5 pt-3">
      <div class="col-lg-8 mx-auto">
        <div class="story text-center reveal">
          <h2 class="h3 text-white">Looking for books?</h2>
          <p class="mx-auto">Browse the ministry's Christian e-books in the shop.</p>
          <a class="btn btn-hmi-red" href="<?= h(url('shop.php')) ?>">Visit the shop</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>