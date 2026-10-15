<?php
require_once __DIR__ . '/includes/functions.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['slug'] ?? '')));
$event = null;

if ($slug !== '') {
    $event = get_event_by_slug($slug);
    // Allow admins to preview draft/completed events
    if (!$event && is_admin()) {
        $event = Database::fetchOne("SELECT * FROM events WHERE slug = ?", [$slug]);
    }
}

if (!$event) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$page_key   = 'events';
$page_title = $event['title'];
$page_desc  = !empty($event['summary']) ? $event['summary'] : (!empty($event['description']) ? mb_substr(strip_tags($event['description']), 0, 160) : 'Christian marriage and family event by Healing My-Marriage International.');
$page_path  = 'event.php?slug=' . $event['slug'];
require __DIR__ . '/includes/header.php';

$hero_title = $event['title'];
$hero_crumb = $event['title'];
$hero_sub   = (!empty($event['category']) ? $event['category'] . '  |  ' : '') . fmt_date($event['start_datetime'], 'l, j F Y');
require __DIR__ . '/includes/page-hero.php';

$otherEvents = Database::fetchAll(
    "SELECT * FROM events WHERE id != ? AND status = 'published' AND start_datetime >= NOW() ORDER BY start_datetime ASC LIMIT 3",
    [$event['id']]
);
if (empty($otherEvents)) {
    $otherEvents = Database::fetchAll(
        "SELECT * FROM events WHERE id != ? AND status = 'published' ORDER BY start_datetime DESC LIMIT 3",
        [$event['id']]
    );
}

$fullUrl = url('event.php?slug=' . $event['slug']);
?>

<section class="section pt-0">
  <div class="container">
    <div class="mb-4">
      <a class="link-arrow text-decoration-none" href="<?= h(url('events.php')) ?>">Back to all events</a>
    </div>

    <div class="row g-5">
      <!-- Main Content Area -->
      <article class="col-lg-8">
        
        <!-- EVENT FLYER PICTURE SHOWCASE -->
        <?php if (!empty($event['featured_image'])): ?>
          <div class="event-flyer-container mb-5">
            <div class="event-flyer-header d-flex justify-content-between align-items-center mb-3">
              <span class="badge-hmi"><i class="bi bi-image me-1"></i> Official Event Flyer</span>
              <div class="d-flex gap-2">
                <a href="<?= h(url($event['featured_image'])) ?>" target="_blank" class="btn btn-sm btn-outline-forest">
                  <i class="bi bi-arrows-fullscreen me-1"></i> Full Size
                </a>
                <a href="<?= h(url($event['featured_image'])) ?>" download class="btn btn-sm btn-outline-forest">
                  <i class="bi bi-download me-1"></i> Download
                </a>
              </div>
            </div>

            <div class="event-flyer-frame shadow-lg">
              <a href="#" data-bs-toggle="modal" data-bs-target="#flyerModal" title="Click to view full flyer picture">
                <img src="<?= h(url($event['featured_image'])) ?>" alt="Flyer for <?= h($event['title']) ?>" class="img-fluid w-100 event-flyer-img" loading="eager">
                <div class="flyer-zoom-hint"><i class="bi bi-zoom-in"></i> Click to enlarge flyer</div>
              </a>
            </div>
          </div>
        <?php endif; ?>

        <!-- ABOUT THE EVENT -->
        <div class="event-section mb-5">
          <h2 class="h3 mb-3" style="font-family:var(--font-display);color:var(--forest-950);">About This Event</h2>
          
          <?php if (!empty($event['summary'])): ?>
            <p class="lead text-forest-900 mb-4 fw-semibold"><?= h($event['summary']) ?></p>
          <?php endif; ?>

          <?php if (!empty($event['description'])): ?>
            <div class="prose">
              <?php
              $paras = preg_split('/\r\n\r\n|\n\n/', trim($event['description']));
              foreach ($paras as $p):
                $p = trim($p);
                if ($p !== ''):
              ?>
                <p><?= nl2br(h($p)) ?></p>
              <?php endif; endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- PROGRAM DETAILS & SCHEDULE -->
        <?php if (!empty($event['program_details']) || !empty($event['program_image'])): ?>
          <div class="event-section mb-5">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
              <div>
                <p class="eyebrow mb-1">Itinerary & Schedule</p>
                <h2 class="h3 mb-0" style="font-family:var(--font-display);color:var(--forest-950);">Program Details</h2>
              </div>
              <span class="badge-hmi"><i class="bi bi-clock-history me-1"></i> Order of Program</span>
            </div>

            <?php if (!empty($event['program_details'])): ?>
              <div class="card p-4 border-0 shadow-sm bg-white rounded-4 mb-4">
                <?= render_program_schedule($event['program_details']) ?>
              </div>
            <?php endif; ?>

            <?php if (!empty($event['program_image'])): ?>
              <div class="program-image-card card border-0 shadow-sm bg-white p-4 rounded-4 mt-4">
                <h3 class="h5 mb-3" style="font-family:var(--font-display);color:var(--forest-950);">
                  <i class="bi bi-card-image text-gold me-2"></i> Program Schedule Graphic
                </h3>
                <div class="event-flyer-frame shadow-sm">
                  <a href="#" data-bs-toggle="modal" data-bs-target="#programImageModal" title="Click to enlarge program graphic">
                    <img src="<?= h(url($event['program_image'])) ?>" alt="Program order graphic for <?= h($event['title']) ?>" class="img-fluid w-100 event-flyer-img" loading="lazy">
                    <div class="flyer-zoom-hint"><i class="bi bi-zoom-in"></i> Click to enlarge schedule</div>
                  </a>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <!-- MINISTERING / FACILITATORS -->
        <div class="event-section mb-5">
          <h2 class="h4 mb-3" style="font-family:var(--font-display);color:var(--forest-950);">Ministering</h2>
          <div class="minister-card p-4 rounded-4 shadow-sm bg-white d-flex align-items-center gap-4 flex-wrap flex-sm-nowrap">
            <img src="<?= h(asset('img/live/img-13.jpg')) ?>" alt="Rev. Ogboo Akujuobi, PhD" class="minister-avatar flex-shrink-0" width="80" height="80">
            <div>
              <h3 class="h6 mb-1 fw-bold text-forest-950">Rev. Ogboo Akujuobi, PhD</h3>
              <p class="text-muted small mb-1">Founder & Serving Overseer, Healing My-Marriage International</p>
              <p class="small mb-0 text-muted">Author, Marriage & Family Counselor, and Teacher with decades of clinical and pastoral experience.</p>
            </div>
          </div>
        </div>

        <!-- VENUE & LOCATION -->
        <div class="event-section mb-4">
          <h2 class="h4 mb-3" style="font-family:var(--font-display);color:var(--forest-950);">Venue & Directions</h2>
          <div class="card p-4 border-0 shadow-sm bg-white rounded-4">
            <div class="d-flex align-items-start gap-3">
              <div class="icon-chip green flex-shrink-0"><i class="bi bi-geo-alt-fill"></i></div>
              <div>
                <h3 class="h6 mb-1 fw-bold"><?= h($event['location'] ?: 'Mercyland Central / Diamond Estate, Rivers State, Nigeria') ?></h3>
                <p class="text-muted mb-3">Healing My-Marriage International Ministry Headquarters & Fellowship Center.</p>
                <a class="btn btn-sm btn-outline-forest" href="https://maps.google.com/?q=<?= urlencode($event['location'] ?: 'Mercyland Central, Rivers State, Nigeria') ?>" target="_blank" rel="noopener">
                  <i class="bi bi-map me-1"></i> Open in Google Maps
                </a>
              </div>
            </div>
          </div>
        </div>

      </article>

      <!-- Sidebar -->
      <aside class="col-lg-4">
        <div class="sticky-top" style="top: 100px;">
          
          <!-- Event Info Box -->
          <div class="card event-sidebar-card border-0 shadow-sm bg-white rounded-4 p-4 mb-4">
            <span class="badge-hmi align-self-start mb-3"><?= h($event['category'] ?: 'Gathering') ?></span>
            
            <div class="d-flex align-items-center gap-3 mb-4">
              <div class="event-date-sidebar">
                <span class="day"><?= fmt_date($event['start_datetime'], 'j') ?></span>
                <span class="month"><?= fmt_date($event['start_datetime'], 'M') ?></span>
              </div>
              <div>
                <h3 class="h6 mb-0 fw-bold"><?= fmt_date($event['start_datetime'], 'l') ?></h3>
                <small class="text-muted"><?= fmt_date($event['start_datetime'], 'F j, Y') ?></small>
              </div>
            </div>

            <ul class="list-unstyled mb-4 d-grid gap-3">
              <li class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-fill text-gold fs-5"></i>
                <div>
                  <strong>Time:</strong><br>
                  <span class="text-muted">
                    <?= fmt_date($event['start_datetime'], 'g:i a') ?>
                    <?php if (!empty($event['end_datetime'])): ?>
                      to <?= fmt_date($event['end_datetime'], 'g:i a') ?>
                    <?php endif; ?>
                  </span>
                </div>
              </li>

              <li class="d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-gold fs-5"></i>
                <div>
                  <strong>Location:</strong><br>
                  <span class="text-muted"><?= h($event['location'] ?: 'Mercyland Central, Rivers State') ?></span>
                </div>
              </li>

              <li class="d-flex align-items-center gap-2">
                <i class="bi bi-ticket-perforated-fill text-gold fs-5"></i>
                <div>
                  <strong>Admission:</strong><br>
                  <span class="text-muted"><?= !empty($event['registration_required']) ? 'Registration Required' : 'Free / Open to All' ?></span>
                </div>
              </li>

              <?php if (strtotime($event['start_datetime']) > time()): ?>
                <li class="d-flex align-items-center gap-2">
                  <i class="bi bi-hourglass-split text-gold fs-5"></i>
                  <div>
                    <strong>Starts In:</strong><br>
                    <span class="fw-bold text-forest" data-countdown="<?= h($event['start_datetime']) ?>"></span>
                  </div>
                </li>
              <?php endif; ?>
            </ul>

            <!-- Registration Button -->
            <?php if (!empty($event['registration_link'])): ?>
              <a class="btn btn-hmi-red btn-lg w-100 text-center mb-2" href="<?= h($event['registration_link']) ?>" target="_blank" rel="noopener">
                Register Now
              </a>
            <?php else: ?>
              <a class="btn btn-hmi-red btn-lg w-100 text-center mb-2" href="<?= h(url('contact.php?subject=' . rawurlencode('Registration: ' . $event['title']))) ?>#message">
                Register Attendance
              </a>
            <?php endif; ?>
            
            <a class="btn btn-hmi-outline w-100 text-center" href="<?= h(url('counseling.php#request')) ?>">
              Book Counseling Session
            </a>
          </div>

          <!-- Share Card -->
          <div class="card border-0 shadow-sm bg-white rounded-4 p-4 mb-4 text-center">
            <h3 class="h6 fw-bold mb-3">Invite Family & Friends</h3>
            <p class="small text-muted mb-3">Share this program with couples, singles, and friends who need encouragement.</p>
            <div class="d-flex justify-content-center gap-2">
              <a href="https://api.whatsapp.com/send?text=<?= rawurlencode($event['title'] . ' - ' . $fullUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-forest" title="Share on WhatsApp">
                <i class="bi bi-whatsapp"></i> WhatsApp
              </a>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($fullUrl) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-forest" title="Share on Facebook">
                <i class="bi bi-facebook"></i> Facebook
              </a>
              <button type="button" class="btn btn-sm btn-outline-forest copy-link-btn" data-url="<?= h($fullUrl) ?>" title="Copy event link">
                <i class="bi bi-link-45deg"></i> Copy
              </button>
            </div>
          </div>

          <!-- More Events -->
          <?php if (!empty($otherEvents)): ?>
            <div class="card border-0 shadow-sm bg-white rounded-4 p-4">
              <h3 class="h6 fw-bold mb-3">More Gatherings</h3>
              <div class="d-grid gap-3">
                <?php foreach ($otherEvents as $oe): ?>
                  <a href="<?= h(url('event.php?slug=' . $oe['slug'])) ?>" class="d-flex gap-3 text-decoration-none align-items-center group-hover">
                    <div class="event-date-mini flex-shrink-0">
                      <span class="d"><?= fmt_date($oe['start_datetime'], 'j') ?></span>
                      <span class="m"><?= fmt_date($oe['start_datetime'], 'M') ?></span>
                    </div>
                    <div>
                      <span class="badge-hmi mb-1" style="font-size:0.65rem;"><?= h($oe['category'] ?: 'Event') ?></span>
                      <strong class="d-block text-forest-900 small"><?= h($oe['title']) ?></strong>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </aside>
    </div>
  </div>
</section>

<!-- FLYER MODAL -->
<?php if (!empty($event['featured_image'])): ?>
<div class="modal fade" id="flyerModal" tabindex="-1" aria-labelledby="flyerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
      <div class="modal-header border-0 bg-forest text-white py-3">
        <h3 class="modal-title h6 mb-0 text-white" id="flyerModalLabel"><i class="bi bi-image me-2 text-gold"></i> <?= h($event['title']) ?> — Event Flyer</h3>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 text-center bg-dark">
        <img src="<?= h(url($event['featured_image'])) ?>" alt="<?= h($event['title']) ?>" class="img-fluid" style="max-height:85vh; object-fit:contain;">
      </div>
      <div class="modal-footer border-0 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted">Healing My-Marriage International Ministry</span>
        <a href="<?= h(url($event['featured_image'])) ?>" download class="btn btn-sm btn-forest">
          <i class="bi bi-download me-1"></i> Download High Res Flyer
        </a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- PROGRAM IMAGE MODAL -->
<?php if (!empty($event['program_image'])): ?>
<div class="modal fade" id="programImageModal" tabindex="-1" aria-labelledby="progModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
      <div class="modal-header border-0 bg-forest text-white py-3">
        <h3 class="modal-title h6 mb-0 text-white" id="progModalLabel"><i class="bi bi-card-image me-2 text-gold"></i> Program Schedule Graphic</h3>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 text-center bg-dark">
        <img src="<?= h(url($event['program_image'])) ?>" alt="Program Graphic" class="img-fluid" style="max-height:85vh; object-fit:contain;">
      </div>
      <div class="modal-footer border-0 py-2 bg-light d-flex justify-content-between">
        <span class="small text-muted">Healing My-Marriage International Ministry</span>
        <a href="<?= h(url($event['program_image'])) ?>" download class="btn btn-sm btn-forest">
          <i class="bi bi-download me-1"></i> Download Schedule Graphic
        </a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const copyBtn = document.querySelector('.copy-link-btn');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      const url = this.getAttribute('data-url') || window.location.href;
      navigator.clipboard.writeText(url).then(() => {
        const orig = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check2 text-success"></i> Copied!';
        setTimeout(() => { this.innerHTML = orig; }, 1800);
      });
    });
  }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
