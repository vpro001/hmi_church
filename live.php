<?php
$page_key   = 'live';
$page_title = 'Live Broadcast';
$page_desc  = 'Join live broadcasts, marriage workshops, counseling sessions, and biblical family teachings from Healing My-Marriage International.';
$page_path  = 'live.php';

require __DIR__ . '/includes/header.php';

$currentLive   = get_current_live();
$nextScheduled = get_next_scheduled_stream();
$featured      = get_featured_livestream();
$recentStreams = get_recent_livestreams(6, $currentLive['id'] ?? ($nextScheduled['id'] ?? 0));

$fbChannel = facebook_url();
$ytChannel = youtube_url();
?>

<!-- LIVE PAGE HEADER -->
<section class="section pb-4 bg-cream-50 border-bottom">
  <div class="container">
    <div class="row align-items-center justify-content-between g-4">
      <div class="col-lg-8">
        <p class="eyebrow mb-2">Live Broadcast Ministry</p>
        <h1 class="h2 mb-2" style="font-family: var(--font-display);">
          <?php if ($currentLive): ?>
            <span class="live-dot-pulse me-2"></span> Broadcasting Live Now
          <?php elseif ($nextScheduled): ?>
            Upcoming Live Broadcast
          <?php else: ?>
            Healing My-Marriage Live Broadcast
          <?php endif; ?>
        </h1>
        <p class="text-muted mb-0">
          <?php if ($currentLive): ?>
            We are live right now on <?= h(livestream_platform_label($currentLive['platform'])) ?>. Join in prayer, teaching, and impartation.
          <?php elseif ($nextScheduled): ?>
            Get ready for our next scheduled live broadcast. Set a reminder and invite a friend.
          <?php else: ?>
            Watch our live teaching, counseling sessions, and prayer broadcasts from Rivers State, Nigeria.
          <?php endif; ?>
        </p>
      </div>

      <div class="col-lg-4 text-lg-end">
        <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
          <a href="<?= h($ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger">
            <i class="bi bi-youtube me-1"></i> YouTube Channel
          </a>
          <a href="<?= h($fbChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-facebook me-1"></i> Facebook Page
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MAIN BROADCAST AREA -->
<section class="section pt-4">
  <div class="container">

    <?php if ($currentLive): ?>
      <!-- 🔴 ACTIVE LIVE STREAM -->
      <div class="row g-4">
        <div class="col-lg-9 mx-auto">
          
          <div class="live-player-card mb-4">
            
            <?php if ($currentLive['platform'] === 'both'): ?>
              <!-- TAB SWITCHER FOR BOTH YOUTUBE & FACEBOOK -->
              <ul class="nav platform-nav-tabs" id="streamTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="yt-tab" data-bs-toggle="tab" data-bs-target="#yt-player" type="button" role="tab" aria-controls="yt-player" aria-selected="true">
                    <i class="bi bi-youtube text-danger fs-5"></i> YouTube Live
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="fb-tab" data-bs-toggle="tab" data-bs-target="#fb-player" type="button" role="tab" aria-controls="fb-player" aria-selected="false">
                    <i class="bi bi-facebook text-primary fs-5"></i> Facebook Live
                  </button>
                </li>
              </ul>

              <div class="tab-content" id="streamTabsContent">
                <div class="tab-pane fade show active" id="yt-player" role="tabpanel" aria-labelledby="yt-tab">
                  <div class="live-player-responsive">
                    <?php 
                      $ytEmbed = get_youtube_embed_url($currentLive['youtube_url'] ?: $currentLive['youtube_video_id']);
                    ?>
                    <?php if ($ytEmbed): ?>
                      <iframe src="<?= h($ytEmbed) ?>" title="YouTube Live Stream" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <?php else: ?>
                      <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-center text-white">
                        <i class="bi bi-youtube fs-1 text-danger mb-2"></i>
                        <h5>YouTube Live Broadcast Active</h5>
                        <p class="small text-white-50 mb-3">Click below to open the live stream directly on YouTube.</p>
                        <a href="<?= h($currentLive['youtube_url'] ?: $ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-danger px-4">Open YouTube Stream</a>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="tab-pane fade" id="fb-player" role="tabpanel" aria-labelledby="fb-tab">
                  <div class="live-player-responsive">
                    <?php 
                      $fbEmbed = get_facebook_embed_url($currentLive['facebook_url']);
                    ?>
                    <?php if ($fbEmbed): ?>
                      <iframe src="<?= h($fbEmbed) ?>" title="Facebook Live Stream" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                    <?php else: ?>
                      <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-center text-white">
                        <i class="bi bi-facebook fs-1 text-primary mb-2"></i>
                        <h5>Facebook Live Broadcast Active</h5>
                        <p class="small text-white-50 mb-3">Click below to open the broadcast directly on our Facebook page.</p>
                        <a href="<?= h($currentLive['facebook_url'] ?: $fbChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary px-4">Open Facebook Live</a>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

            <?php elseif ($currentLive['platform'] === 'youtube'): ?>
              <!-- YOUTUBE ONLY -->
              <div class="live-player-responsive">
                <?php $ytEmbed = get_youtube_embed_url($currentLive['youtube_url'] ?: $currentLive['youtube_video_id']); ?>
                <?php if ($ytEmbed): ?>
                  <iframe src="<?= h($ytEmbed) ?>" title="YouTube Live Stream" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php else: ?>
                  <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-center text-white">
                    <i class="bi bi-youtube fs-1 text-danger mb-2"></i>
                    <h5>YouTube Live Broadcast</h5>
                    <a href="<?= h($currentLive['youtube_url'] ?: $ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-danger px-4 mt-2">Open YouTube Stream</a>
                  </div>
                <?php endif; ?>
              </div>

            <?php elseif ($currentLive['platform'] === 'facebook'): ?>
              <!-- FACEBOOK ONLY -->
              <div class="live-player-responsive">
                <?php $fbEmbed = get_facebook_embed_url($currentLive['facebook_url']); ?>
                <?php if ($fbEmbed): ?>
                  <iframe src="<?= h($fbEmbed) ?>" title="Facebook Live Stream" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowfullscreen></iframe>
                <?php else: ?>
                  <div class="d-flex flex-column align-items-center justify-content-center h-100 p-4 text-center text-white">
                    <i class="bi bi-facebook fs-1 text-primary mb-2"></i>
                    <h5>Facebook Live Broadcast</h5>
                    <a href="<?= h($currentLive['facebook_url'] ?: $fbChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary px-4 mt-2">Open Facebook Live</a>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

          </div>

          <!-- STREAM INFO & INTERACTION BAR -->
          <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge-live-pill"><span class="live-dot-pulse"></span> LIVE NOW</span>
                  <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                    <i class="bi bi-broadcast me-1 text-gold"></i> <?= h(livestream_platform_label($currentLive['platform'])) ?>
                  </span>
                </div>
                <h2 class="h3 mb-2" style="font-family: var(--font-display);"><?= h($currentLive['title']) ?></h2>
                <div class="text-muted small">
                  <span><i class="bi bi-clock me-1"></i> Started <?= fmt_date($currentLive['started_at'] ?? $currentLive['created_at'], 'g:i A, l, F j, Y') ?></span>
                  <span class="mx-2">&bull;</span>
                  <span><i class="bi bi-person-badge me-1"></i> Minister: Rev. Ogboo Akujuobi, PhD</span>
                </div>
              </div>

              <!-- Native App Quick Links -->
              <div class="d-flex gap-2 flex-wrap">
                <?php if (!empty($currentLive['youtube_url']) || $currentLive['platform'] !== 'facebook'): ?>
                  <a href="<?= h($currentLive['youtube_url'] ?: $ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-box-arrow-up-right me-1"></i> YouTube App
                  </a>
                <?php endif; ?>
                <?php if (!empty($currentLive['facebook_url']) || $currentLive['platform'] !== 'youtube'): ?>
                  <a href="<?= h($currentLive['facebook_url'] ?: $fbChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Facebook App
                  </a>
                <?php endif; ?>
              </div>
            </div>

            <?php if (!empty($currentLive['description'])): ?>
              <hr class="my-3">
              <div class="stream-description text-secondary">
                <?= nl2br(h($currentLive['description'])) ?>
              </div>
            <?php endif; ?>

            <!-- Action Buttons for Viewers -->
            <hr class="my-4">
            <div class="row g-3 align-items-center">
              <div class="col-md-7">
                <div class="d-flex gap-2 flex-wrap">
                  <a href="<?= h(url('counseling.php#request')) ?>" class="btn btn-hmi-red btn-sm">
                    <i class="bi bi-chat-heart me-1"></i> Request Prayer / Counseling
                  </a>
                  <a href="<?= h(url('shop.php')) ?>" class="btn btn-hmi-outline btn-sm">
                    <i class="bi bi-book me-1"></i> Ministry E-Books
                  </a>
                </div>
              </div>
              <div class="col-md-5 text-md-end">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="copyStreamLink()">
                  <i class="bi bi-share me-1"></i> Share Stream
                </button>
                <span id="copyToast" class="badge bg-success ms-2 d-none">Link Copied!</span>
              </div>
            </div>
          </div>

        </div>
      </div>

    <?php elseif ($nextScheduled): ?>
      <!-- 🟡 UPCOMING / SCHEDULED STREAM -->
      <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
          <div class="live-countdown-box shadow-sm mb-5">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase mb-2">Upcoming Live Broadcast</span>
            <h2 class="h3 mb-2" style="font-family: var(--font-display);"><?= h($nextScheduled['title']) ?></h2>
            
            <?php if (!empty($nextScheduled['scheduled_start'])): ?>
              <p class="text-muted mb-4">
                <i class="bi bi-calendar-check text-gold me-1"></i> <?= fmt_date($nextScheduled['scheduled_start'], 'l, F j, Y \a\t g:i A') ?>
                <span class="mx-2">&bull;</span>
                <i class="bi bi-broadcast text-danger me-1"></i> <?= h(livestream_platform_label($nextScheduled['platform'])) ?>
              </p>

              <!-- Real-time Countdown -->
              <div class="countdown-digits" id="liveCountdown" data-target="<?= h(date('c', strtotime($nextScheduled['scheduled_start']))) ?>">
                <div class="countdown-item"><div class="num" id="cdDays">00</div><div class="label">Days</div></div>
                <div class="countdown-item"><div class="num" id="cdHours">00</div><div class="label">Hours</div></div>
                <div class="countdown-item"><div class="num" id="cdMinutes">00</div><div class="label">Mins</div></div>
                <div class="countdown-item"><div class="num" id="cdSeconds">00</div><div class="label">Secs</div></div>
              </div>
            <?php endif; ?>

            <?php if (!empty($nextScheduled['description'])): ?>
              <p class="text-muted small mx-auto mb-4" style="max-width: 600px;">
                <?= nl2br(h($nextScheduled['description'])) ?>
              </p>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
              <a href="<?= h($ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-danger">
                <i class="bi bi-bell me-1"></i> Subscribe on YouTube
              </a>
              <a href="<?= h($fbChannel) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                <i class="bi bi-hand-thumbs-up me-1"></i> Follow on Facebook
              </a>
            </div>
          </div>
        </div>
      </div>

    <?php else: ?>
      <!-- ⚪ OFFLINE STATE -->
      <div class="row g-4 justify-content-center">
        <div class="col-lg-8 text-center py-5">
          <div class="mb-4">
            <span class="avatar-circle mx-auto" style="width: 80px; height: 80px; font-size: 2.5rem; background: var(--cream-200); color: var(--forest-800);">
              <i class="bi bi-camera-video-off"></i>
            </span>
          </div>
          <h2 class="h3 mb-3" style="font-family: var(--font-display);">We are currently offline</h2>
          <p class="lead text-muted mx-auto mb-4" style="max-width: 580px;">
            Healing My-Marriage International broadcasts regular marriage enrichment sessions, family teachings, and prayer sessions. Follow our channels to receive notifications the moment we go live.
          </p>

          <div class="d-flex justify-content-center gap-3 flex-wrap mb-5">
            <a href="<?= h($ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-outline-danger btn-lg">
              <i class="bi bi-youtube me-2"></i> Visit YouTube Channel
            </a>
            <a href="<?= h($fbChannel) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-lg">
              <i class="bi bi-facebook me-2"></i> Visit Facebook Page
            </a>
          </div>

          <!-- Broadcasting Schedule Card -->
          <div class="card border bg-light rounded-4 p-4 text-start mx-auto" style="max-width: 650px;">
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar3 text-gold me-2"></i> Regular Ministry Broadcast Schedule</h5>
            <div class="d-flex flex-column gap-2 small text-muted">
              <div><strong class="text-dark">Sunday Worship & Family Impartation:</strong> 8:00 AM &ndash; 11:30 AM (WAT)</div>
              <div><strong class="text-dark">Weekly Marriage Clinic & Teaching:</strong> Announced via Social Media & Email</div>
              <div><strong class="text-dark">Special Conferences & Retreats:</strong> See our <a href="<?= h(url('events.php')) ?>">Events Calendar</a></div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <!-- RECENT BROADCASTS / PAST SESSIONS -->
    <?php if (!empty($recentStreams)): ?>
      <div class="mt-5 pt-4 border-top">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <div>
            <p class="eyebrow mb-1">Archive</p>
            <h3 class="h4 mb-0" style="font-family: var(--font-display);">Recent & Past Broadcasts</h3>
          </div>
          <a href="<?= h($ytChannel) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-forest">
            Full Sermon Library <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>

        <div class="row g-4">
          <?php foreach ($recentStreams as $s): ?>
            <div class="col-md-6 col-lg-4">
              <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="p-4 d-flex flex-column h-100">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-light text-muted border small"><?= h(livestream_platform_label($s['platform'])) ?></span>
                    <span class="small text-muted"><?= fmt_date($s['started_at'] ?? $s['created_at'], 'M j, Y') ?></span>
                  </div>
                  <h4 class="h6 fw-bold mb-2"><?= h($s['title']) ?></h4>
                  <?php if (!empty($s['description'])): ?>
                    <p class="small text-muted mb-3 flex-grow-1"><?= h(mb_strimwidth($s['description'], 0, 110, '...')) ?></p>
                  <?php endif; ?>
                  
                  <div class="pt-2 border-top mt-auto d-flex justify-content-between align-items-center">
                    <?php if (!empty($s['youtube_url'])): ?>
                      <a href="<?= h($s['youtube_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-youtube me-1"></i> Watch Replay
                      </a>
                    <?php elseif (!empty($s['facebook_url'])): ?>
                      <a href="<?= h($s['facebook_url']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-facebook me-1"></i> Watch Replay
                      </a>
                    <?php else: ?>
                      <span class="small text-muted">Archived Broadcast</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<script>
function copyStreamLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const toast = document.getElementById('copyToast');
    if (toast) {
      toast.classList.remove('d-none');
      setTimeout(() => toast.classList.add('d-none'), 2500);
    }
  });
}

// Countdown handler for scheduled broadcasts
document.addEventListener('DOMContentLoaded', function() {
  const cd = document.getElementById('liveCountdown');
  if (!cd) return;

  const targetDate = new Date(cd.getAttribute('data-target')).getTime();
  if (isNaN(targetDate)) return;

  function updateTimer() {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance < 0) {
      document.getElementById('cdDays').textContent = '00';
      document.getElementById('cdHours').textContent = '00';
      document.getElementById('cdMinutes').textContent = '00';
      document.getElementById('cdSeconds').textContent = '00';
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById('cdDays').textContent = String(days).padStart(2, '0');
    document.getElementById('cdHours').textContent = String(hours).padStart(2, '0');
    document.getElementById('cdMinutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('cdSeconds').textContent = String(seconds).padStart(2, '0');
  }

  updateTimer();
  setInterval(updateTimer, 1000);
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
