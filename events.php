<?php
$page_key   = 'events';
$page_title = 'Events';
$page_desc  = 'Upcoming marriage workshops, retreats and prayer gatherings from Healing My-Marriage International in Rivers State, Nigeria.';
$page_path  = 'events.php';
require __DIR__ . '/includes/header.php';
$hero_title = 'Events';
$hero_sub   = 'Workshops, retreats and prayer gatherings for couples and families.';
require __DIR__ . '/includes/page-hero.php';

$upcoming = get_events(true);
?>

<section class="section pt-0" aria-labelledby="upTitle">
  <div class="container">
    <h2 id="upTitle" class="mb-4 reveal">Upcoming</h2>
    <?php if ($upcoming): ?>
      <div class="d-grid gap-3">
        <?php foreach ($upcoming as $e): ?>
          <article class="event-row reveal" id="<?= h($e['id']) ?>">
            <div class="event-date" aria-hidden="true"><span class="d"><?= h(fmt_date($e['start'], 'j')) ?></span><span class="m"><?= h(fmt_date($e['start'], 'M Y')) ?></span></div>
            <div>
              <div class="mb-1"><span class="badge-hmi"><?= h($e['category']) ?></span><?php if (!empty($e['sample'])): ?> <span class="badge-hmi badge-sample">Sample entry</span><?php endif; ?></div>
              <h3><?= h($e['title']) ?></h3>
              <p class="mb-2"><?= h($e['summary']) ?></p>
              <div class="meta">
                <span><i class="bi bi-calendar-event" aria-hidden="true"></i> <?= h(fmt_date($e['start'], 'l, j F Y')) ?></span>
                <span><i class="bi bi-clock" aria-hidden="true"></i> <?= h(fmt_date($e['start'], 'g:i a')) ?> to <?= h(fmt_date($e['end'], 'g:i a')) ?></span>
                <span><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= h($e['location']) ?></span>
                <span class="fw-semibold" data-countdown="<?= h($e['start']) ?>"></span>
              </div>
            </div>
            <?php if (!empty($e['register'])): ?>
              <a class="btn btn-hmi-red" href="<?= h(url('contact.php?subject=' . rawurlencode('Registration: ' . $e['title']))) ?>#message">Register interest</a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state reveal">
        <i class="bi bi-calendar-heart" aria-hidden="true"></i>
        <h3 class="mt-3">No events are scheduled right now</h3>
        <p class="mx-auto">New dates are announced on Facebook and in our newsletter.</p>
        <a class="btn btn-hmi-green" href="<?= h(SITE['facebook']) ?>" target="_blank" rel="noopener">Follow on Facebook</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section-white">
  <div class="container text-center reveal">
    <h2>Watch past messages</h2>
    <p class="lead mx-auto">Catch up on teaching and gatherings on our YouTube channel.</p>
    <a class="btn btn-hmi-outline btn-lg" href="<?= h(SITE['youtube']) ?>" target="_blank" rel="noopener"><i class="bi bi-youtube" aria-hidden="true"></i> Open YouTube channel</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
