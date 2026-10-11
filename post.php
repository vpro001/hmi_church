<?php
require_once __DIR__ . '/includes/functions.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['slug'] ?? '')));
$post = null;
foreach (get_posts() as $p) {
    if ($p['slug'] === $slug) { $post = $p; break; }
}
if (!$post) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$page_key   = 'resources';
$page_title = $post['title'];
$page_desc  = $post['excerpt'];
$page_path  = 'post.php?slug=' . $post['slug'];
require __DIR__ . '/includes/header.php';
$hero_title = $post['title'];
$hero_crumb = $post['title'];
$hero_sub   = fmt_date($post['date']) . '  |  ' . $post['category'];
require __DIR__ . '/includes/page-hero.php';

$others = array_values(array_filter(get_posts(), fn($x) => $x['slug'] !== $post['slug']));
$others = array_slice($others, 0, 3);
?>

<section class="section pt-0">
  <div class="container">
    <div class="row g-5">
      <article class="col-lg-8">
        <div class="prose">
          <?php foreach ($post['body'] as $para): ?><p><?= h($para) ?></p><?php endforeach; ?>
        </div>
        <hr class="my-5">
        <div class="d-flex flex-wrap gap-3 align-items-center">
          <a class="btn btn-hmi-red" href="<?= h(url('counseling.php#request')) ?>">Book for counseling</a>
          <a class="link-arrow" href="<?= h(url('resources.php')) ?>">Back to resources</a>
        </div>
      </article>
      <aside class="col-lg-4" aria-label="More articles">
        <h2 class="h5 mb-3">More to read</h2>
        <div class="d-grid gap-3">
          <?php foreach ($others as $o): ?>
            <a class="hmi-card text-decoration-none" href="<?= h(url('post.php?slug=' . $o['slug'])) ?>"><div class="body py-3">
              <span class="badge-hmi align-self-start mb-2"><?= h($o['category']) ?></span>
              <strong style="font-family:var(--font-display);color:var(--forest-900)"><?= h($o['title']) ?></strong>
            </div></a>
          <?php endforeach; ?>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
