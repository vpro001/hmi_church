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
            <div class="event-date" aria-hidden="true"><span class="d"><?= h(fmt_date($e['start_datetime'], 'j')) ?></span><span class="m"><?= h(fmt_date($e['start_datetime'], 'M Y')) ?></span></div>
            <div>
              <div class="mb-1"><span class="badge-hmi"><?= h($e['category'] ?? 'Event') ?></span></div>
              <h3><?= h($e['title']) ?></h3>
              <p class="mb-2"><?= h($e['summary']) ?></p>
              <div class="meta">
                <span><i class="bi bi-calendar-event" aria-hidden="true"></i> <?= h(fmt_date($e['start_datetime'], 'l, j F Y')) ?></span>
                <span><i class="bi bi-clock" aria-hidden="true"></i> <?= h(fmt_date($e['start_datetime'], 'g:i a')) ?> to <?= h(fmt_date($e['end_datetime'] ?? $e['start_datetime'], 'g:i a')) ?></span>
                <span><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= h($e['location'] ?? 'Mercyland Central / Diamond Estate, Rivers State') ?></span>
                <span class="fw-semibold" data-countdown="<?= h($e['start_datetime']) ?>"></span>
              </div>
            </div>
            <?php if (!empty($e['registration_link'])): ?>
              <a class="btn btn-hmi-red" href="<?= h($e['registration_link']) ?>" target="_blank" rel="noopener">Register</a>
            <?php elseif (!empty($e['registration_required'])): ?>
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
        <a class="btn btn-hmi-green" href="<?= h(facebook_url()) ?>" target="_blank" rel="noopener">Follow on Facebook</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="section section-white">
  <div class="container text-center reveal">
    <h2>Watch past messages</h2>
    <p class="lead mx-auto">Catch up on teaching and gatherings on our YouTube channel.</p>
    <a class="btn btn-hmi-outline btn-lg" href="<?= h(youtube_url()) ?>" target="_blank" rel="noopener"><i class="bi bi-youtube" aria-hidden="true"></i> Open YouTube channel</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>