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

<section class="section pt-5" aria-labelledby="upTitle">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <div>
        <p class="eyebrow mb-1">Gather with us</p>
        <h2 id="upTitle" class="mb-0 reveal">Upcoming Events & Gatherings</h2>
      </div>
      <span class="badge-hmi"><i class="bi bi-calendar3 me-1"></i> <?= count($upcoming) ?> <?= count($upcoming) === 1 ? 'Event' : 'Events' ?> Scheduled</span>
    </div>

    <?php if ($upcoming): ?>
      <div class="d-grid gap-4">
        <?php foreach ($upcoming as $e): ?>
          <article class="card event-card-modern border-0 shadow-sm bg-white rounded-4 overflow-hidden reveal" id="<?= h($e['id']) ?>">
            <div class="row g-0 align-items-stretch">
              
              <!-- FLYER PICTURE COLUMN -->
              <div class="col-lg-4 col-md-5 event-card-media-col">
                <div class="event-card-media h-100 position-relative">
                  <?php if (!empty($e['featured_image'])): ?>
                    <img src="<?= h(url($e['featured_image'])) ?>" alt="Flyer for <?= h($e['title']) ?>" class="event-flyer-thumb w-100 h-100 object-fit-cover" loading="lazy">
                    <button type="button" class="btn-flyer-zoom" data-bs-toggle="modal" data-bs-target="#flyerModal-<?= $e['id'] ?>" title="View full flyer">
                      <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                  <?php else: ?>
                    <div class="event-flyer-placeholder h-100 d-flex flex-column justify-content-center align-items-center p-4 text-center">
                      <i class="bi bi-calendar-event fs-1 text-gold mb-2"></i>
                      <strong class="text-white"><?= h($e['category'] ?? 'Event') ?></strong>
                      <span class="small text-white-50">Healing My-Marriage</span>
                    </div>
                  <?php endif; ?>

                  <!-- Floating Date Chip -->
                  <div class="event-date-chip">
                    <span class="d"><?= h(fmt_date($e['start_datetime'], 'j')) ?></span>
                    <span class="m"><?= h(fmt_date($e['start_datetime'], 'M')) ?></span>
                  </div>
                </div>
              </div>

              <!-- CONTENT COLUMN -->
              <div class="col-lg-8 col-md-7 d-flex flex-column p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                  <span class="badge-hmi"><?= h($e['category'] ?? 'Event') ?></span>
                  <span class="badge bg-forest-subtle text-forest border px-3 py-1 rounded-pill small" data-countdown="<?= h($e['start_datetime']) ?>"></span>
                </div>

                <h3 class="h4 mb-2" style="font-family:var(--font-display);">
                  <a href="<?= h(url('event.php?slug=' . $e['slug'])) ?>" class="text-decoration-none text-forest-950 event-title-link">
                    <?= h($e['title']) ?>
                  </a>
                </h3>

                <?php if (!empty($e['summary'])): ?>
                  <p class="text-muted mb-3"><?= h($e['summary']) ?></p>
                <?php endif; ?>

                <div class="event-meta-grid mb-4">
                  <div>
                    <i class="bi bi-calendar-check text-gold me-2"></i>
                    <span><?= h(fmt_date($e['start_datetime'], 'l, j F Y')) ?></span>
                  </div>
                  <div>
                    <i class="bi bi-clock text-gold me-2"></i>
                    <span>
                      <?= h(fmt_date($e['start_datetime'], 'g:i a')) ?>
                      <?php if (!empty($e['end_datetime'])): ?>
                        &mdash; <?= h(fmt_date($e['end_datetime'], 'g:i a')) ?>
                      <?php endif; ?>
                    </span>
                  </div>
                  <div class="w-100">
                    <i class="bi bi-geo-alt text-gold me-2"></i>
                    <span><?= h($e['location'] ?? 'Mercyland Central / Diamond Estate, Rivers State') ?></span>
                  </div>
                </div>

                <!-- COLLAPSIBLE PROGRAM DETAILS ACCORDION -->
                <?php if (!empty($e['program_details']) || !empty($e['program_image'])): ?>
                  <div class="mb-4">
                    <button class="btn btn-sm btn-program-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#progCollapse-<?= $e['id'] ?>" aria-expanded="false" aria-controls="progCollapse-<?= $e['id'] ?>">
                      <i class="bi bi-clock-history me-1 text-gold"></i> View Program Schedule & Order
                    </button>

                    <div class="collapse mt-3" id="progCollapse-<?= $e['id'] ?>">
                      <div class="card card-body bg-light border-0 rounded-3 p-3">
                        <?php if (!empty($e['program_details'])): ?>
                          <?= render_program_schedule($e['program_details']) ?>
                        <?php endif; ?>

                        <?php if (!empty($e['program_image'])): ?>
                          <div class="mt-3 pt-3 border-top text-center">
                            <span class="small fw-bold text-muted d-block mb-2">Program Order Graphic:</span>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#progImgModal-<?= $e['id'] ?>">
                              <img src="<?= h(url($e['program_image'])) ?>" alt="Program Graphic" style="max-height: 120px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,.1);">
                            </a>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>

                <!-- ACTION BUTTONS -->
                <div class="d-flex flex-wrap gap-2 align-items-center mt-auto pt-2">
                  <a class="btn btn-hmi-green" href="<?= h(url('event.php?slug=' . $e['slug'])) ?>">
                    <i class="bi bi-calendar-event me-1"></i> Event & Flyer Details
                  </a>

                  <?php if (!empty($e['featured_image'])): ?>
                    <button type="button" class="btn btn-sm btn-outline-forest" data-bs-toggle="modal" data-bs-target="#flyerModal-<?= $e['id'] ?>">
                      <i class="bi bi-image me-1"></i> View Flyer
                    </button>
                  <?php endif; ?>

                  <?php if (!empty($e['registration_link'])): ?>
                    <a class="btn btn-hmi-red" href="<?= h($e['registration_link']) ?>" target="_blank" rel="noopener">Register Online</a>
                  <?php elseif (!empty($e['registration_required'])): ?>
                    <a class="btn btn-hmi-red" href="<?= h(url('contact.php?subject=' . rawurlencode('Registration: ' . $e['title']))) ?>#message">Register Interest</a>
                  <?php endif; ?>
                </div>

              </div>
            </div>
          </article>

          <!-- FLYER LIGHTBOX MODAL FOR THIS EVENT -->
          <?php if (!empty($e['featured_image'])): ?>
          <div class="modal fade" id="flyerModal-<?= $e['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header border-0 bg-forest text-white py-3">
                  <h3 class="modal-title h6 mb-0 text-white"><i class="bi bi-image me-2 text-gold"></i> <?= h($e['title']) ?> &mdash; Flyer</h3>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark">
                  <img src="<?= h(url($e['featured_image'])) ?>" alt="<?= h($e['title']) ?>" class="img-fluid" style="max-height:85vh; object-fit:contain;">
                </div>
                <div class="modal-footer border-0 py-2 bg-light d-flex justify-content-between">
                  <a href="<?= h(url('event.php?slug=' . $e['slug'])) ?>" class="btn btn-sm btn-outline-forest">
                    <i class="bi bi-arrow-right me-1"></i> View Full Event Page
                  </a>
                  <a href="<?= h(url($e['featured_image'])) ?>" download class="btn btn-sm btn-forest">
                    <i class="bi bi-download me-1"></i> Download Flyer
                  </a>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- PROGRAM IMAGE MODAL IF ANY -->
          <?php if (!empty($e['program_image'])): ?>
          <div class="modal fade" id="progImgModal-<?= $e['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-header border-0 bg-forest text-white py-3">
                  <h3 class="modal-title h6 mb-0 text-white"><i class="bi bi-card-image me-2 text-gold"></i> Program Schedule Graphic</h3>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark">
                  <img src="<?= h(url($e['program_image'])) ?>" alt="Program Graphic" class="img-fluid" style="max-height:85vh; object-fit:contain;">
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

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