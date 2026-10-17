<?php
/**
 * Admin Live Streams Management
 * Supports Facebook, YouTube, or Simulcast (Both)
 */

$page_title = 'Live Streams';
$currentPage = 'livestreams';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$streamId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Quick "Go Live"
if ($action === 'golive' && $streamId && (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST')) {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        $st = Database::fetchOne("SELECT * FROM livestreams WHERE id = ?", [$streamId]);
        if ($st) {
            // End any other currently live streams
            Database::execute("UPDATE livestreams SET is_active = 0, status = 'ended', ended_at = NOW() WHERE status = 'live'");
            // Set this stream to live
            Database::execute("UPDATE livestreams SET status = 'live', is_active = 1, started_at = NOW(), ended_at = NULL WHERE id = ?", [$streamId]);
            log_activity($admin['id'], 'admin', 'livestream_start', "Started live stream: {$st['title']} ({$st['platform']})");
            flash('success', "Broadcast is now LIVE on {$st['platform']}!");
        } else {
            flash('danger', 'Stream record not found.');
        }
    }
    redirect('admin/livestreams.php');
}

// Handle Quick "End Broadcast"
if ($action === 'endstream' && $streamId && (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST')) {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        $st = Database::fetchOne("SELECT * FROM livestreams WHERE id = ?", [$streamId]);
        if ($st) {
            Database::execute("UPDATE livestreams SET status = 'ended', is_active = 0, ended_at = NOW() WHERE id = ?", [$streamId]);
            log_activity($admin['id'], 'admin', 'livestream_end', "Ended live stream: {$st['title']}");
            flash('success', "Live broadcast ended successfully.");
        }
    }
    redirect('admin/livestreams.php');
}

// Handle Save Stream (Add or Edit)
if ((($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && isset($_POST['save_stream'])) {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        $title = trim($_POST['title'] ?? '');
        $platform = $_POST['platform'] ?? 'both';
        if (!in_array($platform, ['youtube', 'facebook', 'both'], true)) {
            $platform = 'both';
        }
        $status = $_POST['status'] ?? 'offline';
        if (!in_array($status, ['live', 'scheduled', 'ended', 'offline'], true)) {
            $status = 'offline';
        }

        $youtubeUrl = trim($_POST['youtube_url'] ?? '');
        $youtubeVideoId = extract_youtube_id($youtubeUrl);
        $facebookUrl = trim($_POST['facebook_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $scheduledStart = !empty($_POST['scheduled_start']) ? $_POST['scheduled_start'] : null;

        $isActive = ($status === 'live' || $status === 'scheduled') ? 1 : 0;

        if (empty($title)) {
            flash('danger', 'Stream title is required.');
            redirect('admin/livestreams.php?action=' . ($streamId ? 'edit&id=' . $streamId : 'add'));
        }

        // If setting this stream to LIVE, deactivate any other stream
        if ($status === 'live') {
            Database::execute("UPDATE livestreams SET is_active = 0, status = 'ended', ended_at = NOW() WHERE status = 'live'");
        }

        if ($action === 'edit' && $streamId) {
            $startedAtUpdate = ($status === 'live') ? "started_at = IFNULL(started_at, NOW()), ended_at = NULL," : "";
            $endedAtUpdate = ($status === 'ended') ? "ended_at = IFNULL(ended_at, NOW())," : "";

            Database::execute(
                "UPDATE livestreams SET title=?, description=?, platform=?, status=?, youtube_url=?, youtube_video_id=?, facebook_url=?, scheduled_start=?, {$startedAtUpdate} {$endedAtUpdate} is_active=? WHERE id=?",
                [$title, $description, $platform, $status, $youtubeUrl, $youtubeVideoId, $facebookUrl, $scheduledStart, $isActive, $streamId]
            );
            log_activity($admin['id'], 'admin', 'livestream_update', "Updated live stream: {$title}");
            flash('success', 'Live stream updated successfully.');
        } else {
            $startedAt = ($status === 'live') ? date('Y-m-d H:i:s') : null;
            Database::execute(
                "INSERT INTO livestreams (title, description, platform, status, youtube_url, youtube_video_id, facebook_url, scheduled_start, started_at, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$title, $description, $platform, $status, $youtubeUrl, $youtubeVideoId, $facebookUrl, $scheduledStart, $startedAt, $isActive]
            );
            $newId = Database::lastInsertId();
            log_activity($admin['id'], 'admin', 'livestream_create', "Created live stream: {$title}");
            flash('success', $status === 'live' ? 'Broadcast is now LIVE!' : 'Live stream created successfully.');
        }
        redirect('admin/livestreams.php');
    }
}

// Handle Delete Stream
if ($action === 'delete' && $streamId && (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST')) {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        $st = Database::fetchOne("SELECT title FROM livestreams WHERE id = ?", [$streamId]);
        Database::execute("DELETE FROM livestreams WHERE id = ?", [$streamId]);
        log_activity($admin['id'], 'admin', 'livestream_delete', "Deleted live stream: " . ($st['title'] ?? "#$streamId"));
        flash('success', 'Live stream deleted.');
    }
    redirect('admin/livestreams.php');
}

// Fetch single stream for editing
$stream = null;
if (($action === 'edit') && $streamId) {
    $stream = Database::fetchOne("SELECT * FROM livestreams WHERE id = ?", [$streamId]);
    if (!$stream) {
        flash('danger', 'Stream record not found.');
        redirect('admin/livestreams.php');
    }
}

// Fetch current active / live stream
$currentLiveStream = Database::fetchOne("SELECT * FROM livestreams WHERE status = 'live' AND is_active = 1 LIMIT 1");
// Fetch next scheduled stream
$nextScheduledStream = Database::fetchOne("SELECT * FROM livestreams WHERE status = 'scheduled' AND is_active = 1 ORDER BY scheduled_start ASC LIMIT 1");

// Fetch all streams for table
$allStreams = Database::fetchAll("SELECT * FROM livestreams ORDER BY created_at DESC LIMIT 100");

$fbDefault = site_setting('facebook_url', 'https://www.facebook.com/profile.php?id=61565801562860');
$ytDefault = site_setting('youtube_url', 'https://youtube.com/@healingmymarriageinternational');
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0" style="font-family: var(--font-display);">Live Streams Management</h2>
        <p class="text-muted mb-0">Broadcast live video to Facebook, YouTube, or simulcast on both platforms simultaneously</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= h(url('live.php')) ?>" target="_blank" class="btn btn-outline-forest">
            <i class="bi bi-box-arrow-up-right me-1"></i> Public Live Page
        </a>
        <?php if ($action === 'list'): ?>
            <a href="<?= h(url('admin/livestreams.php?action=add')) ?>" class="btn btn-forest">
                <i class="bi bi-plus-lg me-1"></i> New Live Stream
            </a>
        <?php else: ?>
            <a href="<?= h(url('admin/livestreams.php')) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Stream List
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($action === 'list'): ?>

    <!-- ======================================================================
         CURRENT BROADCAST STATUS CONTROLLER
         ====================================================================== -->
    <div class="mb-4">
        <?php if ($currentLiveStream): ?>
            <!-- 🔴 ACTIVE LIVE BROADCAST CARD -->
            <div class="live-control-card is-live">
                <div class="live-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-white text-danger fw-bold px-3 py-2 rounded-pill shadow-sm">
                            <span class="live-dot-pulse me-1"></span> ON AIR &mdash; LIVE NOW
                        </span>
                        <span class="platform-pill text-white">
                            <i class="bi bi-broadcast"></i> <?= h(livestream_platform_label($currentLiveStream['platform'])) ?>
                        </span>
                    </div>

                    <form method="post" action="<?= h(url('admin/livestreams.php?action=endstream&id=' . $currentLiveStream['id'])) ?>" class="d-inline" onsubmit="return confirm('End this live broadcast now?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-light text-danger fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-stop-circle-fill me-1"></i> End Live Broadcast
                        </button>
                    </form>
                </div>
                <div class="p-4 bg-white">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-8">
                            <h3 class="h4 mb-2 fw-bold text-dark"><?= h($currentLiveStream['title']) ?></h3>
                            <?php if (!empty($currentLiveStream['description'])): ?>
                                <p class="text-muted small mb-3"><?= nl2br(h($currentLiveStream['description'])) ?></p>
                            <?php endif; ?>
                            <div class="d-flex flex-wrap gap-4 text-muted small">
                                <div><i class="bi bi-clock me-1 text-gold"></i> Started: <?= fmt_date($currentLiveStream['started_at'], 'g:i A, M j, Y') ?></div>
                                <?php if (!empty($currentLiveStream['youtube_url'])): ?>
                                    <div><i class="bi bi-youtube text-danger me-1"></i> <a href="<?= h($currentLiveStream['youtube_url']) ?>" target="_blank" class="text-decoration-none">YouTube Feed</a></div>
                                <?php endif; ?>
                                <?php if (!empty($currentLiveStream['facebook_url'])): ?>
                                    <div><i class="bi bi-facebook text-primary me-1"></i> <a href="<?= h($currentLiveStream['facebook_url']) ?>" target="_blank" class="text-decoration-none">Facebook Feed</a></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="<?= h(url('live.php')) ?>" target="_blank" class="btn btn-outline-forest btn-sm me-2">
                                <i class="bi bi-eye me-1"></i> View Live as Visitor
                            </a>
                            <a href="<?= h(url('admin/livestreams.php?action=edit&id=' . $currentLiveStream['id'])) ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil me-1"></i> Edit Stream
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($nextScheduledStream): ?>
            <!-- 🟡 UPCOMING SCHEDULED STREAM CARD -->
            <div class="live-control-card is-scheduled">
                <div class="live-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-dark text-warning fw-bold px-3 py-2 rounded-pill">
                            <i class="bi bi-calendar-event me-1"></i> UPCOMING SCHEDULED BROADCAST
                        </span>
                        <span class="platform-pill text-dark border border-dark">
                            <?= h(livestream_platform_label($nextScheduledStream['platform'])) ?>
                        </span>
                    </div>

                    <form method="post" action="<?= h(url('admin/livestreams.php?action=golive&id=' . $nextScheduledStream['id'])) ?>" class="d-inline" onsubmit="return confirm('Launch this scheduled broadcast LIVE right now?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-forest fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-broadcast me-1"></i> Go Live Now
                        </button>
                    </form>
                </div>
                <div class="p-4 bg-white">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-8">
                            <h3 class="h4 mb-2 fw-bold text-dark"><?= h($nextScheduledStream['title']) ?></h3>
                            <div class="text-muted small">
                                <i class="bi bi-calendar3 text-gold me-1"></i> Scheduled for: <strong><?= fmt_date($nextScheduledStream['scheduled_start'], 'l, F j, Y \a\t g:i A') ?></strong>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <a href="<?= h(url('admin/livestreams.php?action=edit&id=' . $nextScheduledStream['id'])) ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil me-1"></i> Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ⚪ OFFLINE / STANDBY CONTROLLER CARD -->
            <div class="live-control-card is-offline">
                <div class="live-header">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-secondary text-white px-3 py-2 rounded-pill">
                            <i class="bi bi-circle-fill me-1" style="font-size:0.6rem;"></i> STANDBY / OFFLINE
                        </span>
                        <span class="text-white-50 small">No stream is currently broadcasting</span>
                    </div>
                    <a href="<?= h(url('admin/livestreams.php?action=add')) ?>" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm text-dark">
                        <i class="bi bi-broadcast me-1"></i> Launch Instant Live Broadcast
                    </a>
                </div>
                <div class="p-4 bg-white">
                    <p class="text-muted mb-0 small">
                        Ready to broadcast? Click <strong>Launch Instant Live Broadcast</strong> to enter your YouTube and/or Facebook live links, select the platform (YouTube, Facebook, or Both), and start streaming immediately to your audience.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ======================================================================
         STREAM ARCHIVE & MANAGEMENT TABLE
         ====================================================================== -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-collection-play me-2 text-gold"></i> All Stream Records</h5>
            <span class="badge bg-light text-dark border"><?= count($allStreams) ?> Total</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($allStreams)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Title & Notes</th>
                                <th>Platform</th>
                                <th>Status</th>
                                <th>Date / Schedule</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allStreams as $s): ?>
                                <tr>
                                    <td class="text-muted small"><?= $s['id'] ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= h($s['title']) ?></div>
                                        <?php if (!empty($s['description'])): ?>
                                            <small class="text-muted d-block text-truncate" style="max-width: 280px;"><?= h($s['description']) ?></small>
                                        <?php endif; ?>
                                        <div class="small mt-1 d-flex gap-2">
                                            <?php if (!empty($s['youtube_url'])): ?>
                                                <a href="<?= h($s['youtube_url']) ?>" target="_blank" class="text-danger text-decoration-none small">
                                                    <i class="bi bi-youtube"></i> YouTube
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($s['facebook_url'])): ?>
                                                <a href="<?= h($s['facebook_url']) ?>" target="_blank" class="text-primary text-decoration-none small">
                                                    <i class="bi bi-facebook"></i> Facebook
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($s['platform'] === 'youtube'): ?>
                                            <span class="badge platform-badge-youtube"><i class="bi bi-youtube me-1"></i> YouTube</span>
                                        <?php elseif ($s['platform'] === 'facebook'): ?>
                                            <span class="badge platform-badge-facebook"><i class="bi bi-facebook me-1"></i> Facebook</span>
                                        <?php else: ?>
                                            <span class="badge platform-badge-both"><i class="bi bi-broadcast me-1"></i> YouTube & FB</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($s['status'] === 'live'): ?>
                                            <span class="badge bg-danger pulse-live"><span class="live-dot-pulse me-1"></span> LIVE</span>
                                        <?php elseif ($s['status'] === 'scheduled'): ?>
                                            <span class="badge bg-warning text-dark"><i class="bi bi-calendar-event me-1"></i> Scheduled</span>
                                        <?php elseif ($s['status'] === 'ended'): ?>
                                            <span class="badge bg-secondary">Ended</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border">Offline</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="small text-muted">
                                        <?php if ($s['status'] === 'scheduled' && !empty($s['scheduled_start'])): ?>
                                            <div><strong>Starts:</strong> <?= fmt_date($s['scheduled_start'], 'M j, Y g:i A') ?></div>
                                        <?php elseif (!empty($s['started_at'])): ?>
                                            <div><strong>Started:</strong> <?= fmt_date($s['started_at'], 'M j, Y g:i A') ?></div>
                                            <?php if (!empty($s['ended_at'])): ?>
                                                <div><small>Ended: <?= fmt_date($s['ended_at'], 'g:i A') ?></small></div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div>Created: <?= fmt_date($s['created_at'], 'M j, Y') ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1 align-items-center">
                                            <?php if ($s['status'] !== 'live'): ?>
                                                <form method="post" action="<?= h(url('admin/livestreams.php?action=golive&id=' . $s['id'])) ?>" class="d-inline" onsubmit="return confirm('Launch this broadcast LIVE right now?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Go Live With This Stream">
                                                        <i class="bi bi-broadcast"></i> Go Live
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="<?= h(url('admin/livestreams.php?action=endstream&id=' . $s['id'])) ?>" class="d-inline" onsubmit="return confirm('End this live broadcast now?');">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger" title="End Broadcast">
                                                        <i class="bi bi-stop-circle"></i> End
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <a href="<?= h(url('admin/livestreams.php?action=edit&id=' . $s['id'])) ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <form method="post" action="<?= h(url('admin/livestreams.php?action=delete&id=' . $s['id'])) ?>" class="d-inline" onsubmit="return confirm('Delete this stream record?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-broadcast fs-1 text-muted mb-2 d-block"></i>
                    <h5 class="text-dark">No live streams yet</h5>
                    <p class="text-muted small mb-3">Create your first live broadcast record to stream on YouTube, Facebook, or both.</p>
                    <a href="<?= h(url('admin/livestreams.php?action=add')) ?>" class="btn btn-forest btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create First Stream
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ======================================================================
         MINISTRY CHANNELS REFERENCE CARD
         ====================================================================== -->
    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <h5 class="fw-bold mb-1"><i class="bi bi-link-45deg text-gold me-1"></i> Ministry Social Channels Defaults</h5>
                    <p class="text-muted small mb-0">These default channels are configured in Site Settings and linked automatically if no custom stream URL is entered:</p>
                    <div class="d-flex flex-wrap gap-3 mt-2 small">
                        <div><strong class="text-danger"><i class="bi bi-youtube"></i> YouTube:</strong> <code><?= h($ytDefault) ?></code></div>
                        <div><strong class="text-primary"><i class="bi bi-facebook"></i> Facebook:</strong> <code><?= h($fbDefault) ?></code></div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?= h(url('admin/settings.php?tab=general')) ?>" class="btn btn-sm btn-outline-forest">
                        <i class="bi bi-gear me-1"></i> Change in Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>

    <!-- ======================================================================
         ADD / EDIT LIVE STREAM FORM
         ====================================================================== -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <?= $action === 'edit' ? '<i class="bi bi-pencil-square text-gold me-2"></i> Edit Live Stream' : '<i class="bi bi-plus-circle text-gold me-2"></i> New Live Stream Broadcast' ?>
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="post" class="needs-validation" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="save_stream" value="1">

                <div class="row g-4">
                    <!-- Stream Title -->
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Broadcast Title <span class="text-danger">*</span></label>
                        <input class="form-control" name="title" required value="<?= h($stream['title'] ?? '') ?>" placeholder="e.g. Sunday Family Impartation & Healing Service">
                        <small class="text-muted">Descriptive title shown in the video header and browser notification.</small>
                    </div>

                    <!-- Platform Selection -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Broadcast Platform <span class="text-danger">*</span></label>
                        <select class="form-select" name="platform" id="platformSelect" onchange="togglePlatformInputs()">
                            <option value="both" <?= ($stream['platform'] ?? 'both') === 'both' ? 'selected' : '' ?>>🟣 Both (YouTube & Facebook)</option>
                            <option value="youtube" <?= ($stream['platform'] ?? '') === 'youtube' ? 'selected' : '' ?>>🔴 YouTube Live Only</option>
                            <option value="facebook" <?= ($stream['platform'] ?? '') === 'facebook' ? 'selected' : '' ?>>🔵 Facebook Live Only</option>
                        </select>
                        <small class="text-muted">Choose where viewers will be watching.</small>
                    </div>

                    <!-- Status -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stream Status</label>
                        <select class="form-select" name="status" id="statusSelect">
                            <option value="live" <?= ($stream['status'] ?? '') === 'live' ? 'selected' : '' ?>>🔴 LIVE NOW (Goes on air immediately)</option>
                            <option value="scheduled" <?= ($stream['status'] ?? ($action === 'add' ? 'scheduled' : '')) === 'scheduled' ? 'selected' : '' ?>>🟡 Scheduled (Upcoming Broadcast)</option>
                            <option value="ended" <?= ($stream['status'] ?? '') === 'ended' ? 'selected' : '' ?>>⚪ Ended (Save to past archives)</option>
                            <option value="offline" <?= ($stream['status'] ?? '') === 'offline' ? 'selected' : '' ?>>Draft / Offline</option>
                        </select>
                        <small class="text-muted">Selecting <strong>LIVE NOW</strong> will set the broadcast live on the website.</small>
                    </div>

                    <!-- Scheduled Start Date/Time -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Scheduled Date & Time (Optional)</label>
                        <input class="form-control" type="datetime-local" name="scheduled_start" value="<?= !empty($stream['scheduled_start']) ? date('Y-m-d\TH:i', strtotime($stream['scheduled_start'])) : '' ?>">
                        <small class="text-muted">If scheduled, a live countdown timer displays on the public page.</small>
                    </div>

                    <!-- YouTube URL / ID Input -->
                    <div class="col-md-6" id="ytGroup">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-danger">
                                <i class="bi bi-youtube me-1"></i> YouTube Live Stream URL or Video ID
                            </label>
                            <input class="form-control mb-2" name="youtube_url" value="<?= h($stream['youtube_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=... or Video ID">
                            <small class="text-muted d-block">
                                Accepts full link (<code>youtube.com/watch?v=XYZ</code>), short link (<code>youtu.be/XYZ</code>), or direct 11-char Video ID.
                            </small>
                            <small class="text-muted">Default channel: <code><?= h($ytDefault) ?></code></small>
                        </div>
                    </div>

                    <!-- Facebook URL Input -->
                    <div class="col-md-6" id="fbGroup">
                        <div class="p-3 border rounded-3 bg-light">
                            <label class="form-label fw-bold text-primary">
                                <i class="bi bi-facebook me-1"></i> Facebook Live URL / Video Link
                            </label>
                            <input class="form-control mb-2" name="facebook_url" value="<?= h($stream['facebook_url'] ?? '') ?>" placeholder="https://www.facebook.com/.../videos/... or Live Link">
                            <small class="text-muted d-block">
                                Paste your Facebook Live link, video URL, or embedded player link.
                            </small>
                            <small class="text-muted">Default page: <code><?= h($fbDefault) ?></code></small>
                        </div>
                    </div>

                    <!-- Description / Sermon Notes -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Broadcast Summary / Sermon Notes (Optional)</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Brief outline of the teaching, scripture references, or prayer theme for this session..."><?= h($stream['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                    <a href="<?= h(url('admin/livestreams.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-forest px-4 py-2">
                        <i class="bi bi-check-lg me-1"></i> <?= $action === 'edit' ? 'Update Live Stream' : 'Save Live Stream' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function togglePlatformInputs() {
        const val = document.getElementById('platformSelect').value;
        const ytGroup = document.getElementById('ytGroup');
        const fbGroup = document.getElementById('fbGroup');
        if (val === 'youtube') {
            ytGroup.style.display = 'block';
            fbGroup.style.display = 'none';
        } else if (val === 'facebook') {
            ytGroup.style.display = 'none';
            fbGroup.style.display = 'block';
        } else {
            ytGroup.style.display = 'block';
            fbGroup.style.display = 'block';
        }
    }
    document.addEventListener('DOMContentLoaded', togglePlatformInputs);
    </script>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
