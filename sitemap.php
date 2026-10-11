<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=UTF-8');
$base = base_url();
$pages = ['', 'about.php', 'counseling.php', 'programs.php', 'events.php', 'resources.php', 'shop.php', 'contact.php', 'privacy.php'];
foreach (get_posts() as $p) { $pages[] = 'post.php?slug=' . $p['slug']; }
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
  <url><loc><?= h($base . '/' . $p) ?></loc></url>
<?php endforeach; ?>
</urlset>
