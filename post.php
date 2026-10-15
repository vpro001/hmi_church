<?php
require_once __DIR__ . '/includes/functions.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['slug'] ?? '')));
$post = null;

if ($slug !== '') {
    $post = get_post_by_slug($slug);
    if (!$post && is_admin()) {
        $post = Database::fetchOne("SELECT * FROM posts WHERE slug = ?", [$slug]);
    }
}

if (!$post) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

// Track view count
if (!empty($post['id'])) {
    Database::execute("UPDATE posts SET view_count = view_count + 1 WHERE id = ?", [$post['id']]);
}

$page_key   = 'resources';
$page_title = $post['title'] ?? 'Article';
$page_desc  = $post['excerpt'] ?? '';
$page_path  = 'post.php?slug=' . ($post['slug'] ?? '');
require __DIR__ . '/includes/header.php';

$hero_title = $post['title'] ?? '';
$hero_crumb = $post['title'] ?? '';
$post_date  = $post['published_at'] ?? $post['created_at'] ?? $post['date'] ?? null;
$date_text  = fmt_date($post_date);
$category   = $post['category'] ?? 'Article';
$hero_sub   = $date_text !== '' ? ($date_text . '  |  ' . $category) : $category;

require __DIR__ . '/includes/page-hero.php';

$others = !empty($post['id'])
    ? get_recent_posts(3, (int)$post['id'])
    : array_slice(array_values(array_filter(get_posts(), fn($x) => ($x['slug'] ?? '') !== ($post['slug'] ?? ''))), 0, 3);
?>

<section class="section pt-5">
  <div class="container">
    <div class="row g-5">
      <article class="col-lg-8">
        <?php if (!empty($post['featured_image'])): ?>
          <div class="mb-4">
            <img src="<?= h(url($post['featured_image'])) ?>" alt="<?= h($post['title'] ?? '') ?>" class="img-fluid rounded" style="width:100%; max-height:450px; object-fit:cover;">
          </div>
        <?php endif; ?>

        <div class="prose">
          <?php
          if (!empty($post['body']) && is_array($post['body'])) {
              foreach ($post['body'] as $para) {
                  echo '<p>' . h($para) . '</p>';
              }
          } elseif (!empty($post['content'])) {
              if (strip_tags($post['content']) !== $post['content']) {
                  echo $post['content'];
              } else {
                  $paragraphs = preg_split('/\r\n\r\n|\n\n|\r\r/', trim($post['content']));
                  if (count($paragraphs) <= 1 && str_contains($post['content'], "\n")) {
                      $paragraphs = explode("\n", trim($post['content']));
                  }
                  foreach ($paragraphs as $para) {
                      $para = trim($para);
                      if ($para !== '') {
                          echo '<p>' . nl2br(h($para)) . '</p>';
                      }
                  }
              }
          } elseif (!empty($post['excerpt'])) {
              echo '<p>' . h($post['excerpt']) . '</p>';
          }
          ?>
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
            <a class="hmi-card text-decoration-none" href="<?= h(url('post.php?slug=' . ($o['slug'] ?? ''))) ?>"><div class="body py-3">
              <span class="badge-hmi align-self-start mb-2"><?= h($o['category'] ?? 'Article') ?></span>
              <strong style="font-family:var(--font-display);color:var(--forest-900)"><?= h($o['title'] ?? '') ?></strong>
            </div></a>
          <?php endforeach; ?>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
