<?php
/**
 * Admin Events Management
 */

$page_title = 'Events';
$currentPage = 'events';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        $slug = trim($_POST['slug'] ?? '');
        if (!$slug) $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $_POST['title']));
        
        $data = [
            'slug' => $slug,
            'title' => trim($_POST['title'] ?? ''),
            'summary' => trim($_POST['summary'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'program_details' => trim($_POST['program_details'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'start_datetime' => $_POST['start_datetime'] ?? '',
            'end_datetime' => $_POST['end_datetime'] ?: null,
            'registration_required' => isset($_POST['registration_required']) ? 1 : 0,
            'registration_link' => trim($_POST['registration_link'] ?? ''),
            'max_attendees' => $_POST['max_attendees'] !== '' ? (int)$_POST['max_attendees'] : null,
            'status' => $_POST['status'] ?? 'draft',
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'featured_image' => trim($_POST['existing_image'] ?? ''),
            'program_image' => trim($_POST['existing_program_image'] ?? ''),
        ];
        
        $uploadDir = dirname(__DIR__) . '/uploads/events/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            @chmod($uploadDir, 0777);
        }
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $uploadError = null;
        
        // Main Event Flyer Upload
        if (!empty($_POST['remove_image'])) {
            $data['featured_image'] = '';
        } elseif (!empty($_FILES['featured_image']['name'])) {
            if ($_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
                $uploadError = 'Error uploading event flyer (error code ' . $_FILES['featured_image']['error'] . ').';
            } else {
                $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    $uploadError = 'Invalid event flyer format. Allowed: ' . implode(', ', $allowed);
                } else {
                    $filename = $slug . '_flyer_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadDir . $filename)) {
                        @chmod($uploadDir . $filename, 0666);
                        $data['featured_image'] = 'uploads/events/' . $filename;
                    } else {
                        $uploadError = 'Could not save flyer file. Please ensure uploads directory is writable.';
                    }
                }
            }
        }
        
        // Program Schedule Image Upload
        if (!empty($_POST['remove_program_image'])) {
            $data['program_image'] = '';
        } elseif (!empty($_FILES['program_image']['name'])) {
            if ($_FILES['program_image']['error'] !== UPLOAD_ERR_OK) {
                $uploadError = ($uploadError ? $uploadError . ' ' : '') . 'Error uploading program schedule image.';
            } else {
                $ext = strtolower(pathinfo($_FILES['program_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    $uploadError = ($uploadError ? $uploadError . ' ' : '') . 'Invalid program graphic format.';
                } else {
                    $filename = $slug . '_program_' . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES['program_image']['tmp_name'], $uploadDir . $filename)) {
                        @chmod($uploadDir . $filename, 0666);
                        $data['program_image'] = 'uploads/events/' . $filename;
                    } else {
                        $uploadError = ($uploadError ? $uploadError . ' ' : '') . 'Could not save program image.';
                    }
                }
            }
        }
        
        if ($action === 'edit' && $eventId) {
            Database::execute(
                "UPDATE events SET slug=?, title=?, summary=?, description=?, program_details=?, featured_image=?, program_image=?, category=?, location=?, start_datetime=?, end_datetime=?, registration_required=?, registration_link=?, max_attendees=?, status=?, meta_title=?, meta_description=? WHERE id=?",
                [$data['slug'], $data['title'], $data['summary'], $data['description'], $data['program_details'], $data['featured_image'], $data['program_image'], $data['category'], $data['location'], $data['start_datetime'], $data['end_datetime'], $data['registration_required'], $data['registration_link'], $data['max_attendees'], $data['status'], $data['meta_title'], $data['meta_description'], $eventId]
            );
            log_activity($admin['id'], 'admin', 'event_update', "Updated event: {$data['title']}");
            if ($uploadError) {
                flash('warning', 'Event updated, but flyer upload had an issue: ' . $uploadError);
            } else {
                flash('success', 'Event updated successfully.');
            }
        } else {
            Database::execute(
                "INSERT INTO events (slug, title, summary, description, program_details, featured_image, program_image, category, location, start_datetime, end_datetime, registration_required, registration_link, max_attendees, status, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$data['slug'], $data['title'], $data['summary'], $data['description'], $data['program_details'], $data['featured_image'], $data['program_image'], $data['category'], $data['location'], $data['start_datetime'], $data['end_datetime'], $data['registration_required'], $data['registration_link'], $data['max_attendees'], $data['status'], $data['meta_title'], $data['meta_description']]
            );
            log_activity($admin['id'], 'admin', 'event_create', "Created event: {$data['title']}");
            if ($uploadError) {
                flash('warning', 'Event created, but flyer upload had an issue: ' . $uploadError);
            } else {
                flash('success', 'Event created successfully.');
            }
        }
        redirect('admin/events.php');
    }
}

if ($action === 'delete' && $eventId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        Database::execute("DELETE FROM events WHERE id = ?", [$eventId]);
        log_activity($admin['id'], 'admin', 'event_delete', "Deleted event ID: $eventId");
        flash('success', 'Event deleted.');
    }
    redirect('admin/events.php');
}

$event = null;
if (($action === 'edit' || $action === 'view') && $eventId) {
    $event = Database::fetchOne("SELECT * FROM events WHERE id = ?", [$eventId]);
    if (!$event) { flash('danger', 'Event not found.'); redirect('admin/events.php'); }
}

$events = Database::fetchAll("SELECT * FROM events ORDER BY start_datetime DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0"><?= $action === 'list' ? 'Events' : ($action === 'edit' ? 'Edit Event' : 'Add Event') ?></h2>
        <p class="text-muted mb-0">Manage workshops, retreats, and gatherings</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="<?= h(url('admin/events.php?action=add')) ?>" class="btn btn-forest"><i class="bi bi-plus-lg me-2"></i> Add Event</a>
    <?php else: ?>
    <a href="<?= h(url('admin/events.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($events): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 85px;">Flyer</th>
                        <th>Event & Details</th>
                        <th>Category</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $e): ?>
                    <tr>
                        <td>
                            <?php if (!empty($e['featured_image'])): ?>
                            <a href="<?= h(url($e['featured_image'])) ?>" target="_blank" title="View Flyer">
                                <img src="<?= h(url($e['featured_image'])) ?>" alt="" style="width: 65px; height: 46px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,.12);">
                            </a>
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 65px; height: 46px; border-radius: 4px; border: 1px dashed #ccc; font-size: 0.7rem;">No Flyer</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong class="d-block mb-1"><?= h($e['title']) ?></strong>
                            <div class="d-flex flex-wrap gap-1">
                                <?php if (!empty($e['featured_image'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-image text-primary me-1"></i>Flyer</span>
                                <?php endif; ?>
                                <?php if (!empty($e['program_details'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-list-task text-success me-1"></i>Program Schedule</span>
                                <?php endif; ?>
                                <?php if (!empty($e['program_image'])): ?>
                                    <span class="badge bg-light text-dark border"><i class="bi bi-card-image text-info me-1"></i>Program Image</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= h($e['category'] ?: '—') ?></td>
                        <td>
                            <?= fmt_date($e['start_datetime'], 'M j, Y g:i a') ?>
                            <?php if ($e['end_datetime']): ?><br><small class="text-muted">to <?= fmt_date($e['end_datetime'], 'g:i a') ?></small><?php endif; ?>
                        </td>
                        <td><?= h($e['location'] ?: '—') ?></td>
                        <td>
                            <span class="badge bg-<?= $e['status'] === 'published' ? 'published' : ($e['status'] === 'draft' ? 'draft' : ($e['status'] === 'completed' ? 'completed' : 'cancelled')) ?>">
                                <?= ucfirst($e['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= h(url('admin/events.php?action=edit&id=' . $e['id'])) ?>" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="<?= h(url('event.php?slug=' . $e['slug'])) ?>" class="btn btn-outline-secondary" target="_blank" title="View Public Page"><i class="bi bi-eye"></i></a>
                                <button class="btn btn-outline-danger" data-confirm="Delete '<?= h($e['title']) ?>'?" onclick="deleteItem(<?= $e['id'] ?>, 'events')" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-calendar-event"></i><h5>No events yet</h5><a href="<?= h(url('admin/events.php?action=add')) ?>" class="btn btn-forest mt-3">Create First Event</a></div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="save_event" value="1">
            <?php if ($event): ?>
                <input type="hidden" name="existing_image" value="<?= h($event['featured_image'] ?? '') ?>">
                <input type="hidden" name="existing_program_image" value="<?= h($event['program_image'] ?? '') ?>">
            <?php endif; ?>
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input class="form-control" name="title" required value="<?= h($event['title'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                    <input class="form-control" name="slug" required value="<?= h($event['slug'] ?? '') ?>" pattern="[a-z0-9-]+">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <input class="form-control" name="category" value="<?= h($event['category'] ?? '') ?>" list="eventCats">
                    <datalist id="eventCats">
                        <option>Workshop</option>
                        <option>Retreat</option>
                        <option>Prayer</option>
                        <option>Seminar</option>
                        <option>Conference</option>
                        <option>Campaign</option>
                        <option>Meeting</option>
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="draft" <?= ($event['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($event['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="completed" <?= ($event['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= ($event['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                    <input class="form-control" type="datetime-local" name="start_datetime" required value="<?= !empty($event['start_datetime']) ? date('Y-m-d\TH:i', strtotime($event['start_datetime'])) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date & Time</label>
                    <input class="form-control" type="datetime-local" name="end_datetime" value="<?= !empty($event['end_datetime']) ? date('Y-m-d\TH:i', strtotime($event['end_datetime'])) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input class="form-control" name="location" value="<?= h($event['location'] ?? 'Mercyland Central / Diamond Estate, Rivers State') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Max Attendees (optional)</label>
                    <input class="form-control" type="number" name="max_attendees" min="1" value="<?= $event['max_attendees'] ?? '' ?>">
                </div>
                
                <div class="col-12">
                    <label class="form-label">Short Summary</label>
                    <textarea class="form-control" name="summary" rows="2" placeholder="Brief 1-2 sentence overview for cards and social previews"><?= h($event['summary'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Full Description (About Event)</label>
                    <textarea class="form-control" name="description" rows="4" placeholder="Detailed description of the event, teaching themes, who should attend..."><?= h($event['description'] ?? '') ?></textarea>
                </div>

                <!-- Program Details & Schedule -->
                <div class="col-12">
                    <div class="card bg-light border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-2">
                            <label class="form-label fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-forest me-1"></i> Program Details & Schedule</label>
                            <span class="badge bg-white text-muted border">Renders as an interactive timeline on the public page</span>
                        </div>
                        <p class="small text-muted mb-2">Enter the program itinerary, session schedule, or order of service. Lines starting with times (e.g. <code>09:00 AM - Registration</code> or <code>10:30 AM: Session 1</code>) will automatically display as visual schedule milestones.</p>
                        <textarea class="form-control font-monospace" name="program_details" rows="7" placeholder="09:00 AM - Arrival, Registration & Welcome&#10;09:30 AM - Opening Worship & Intercessory Prayer&#10;10:00 AM - Keynote Address: Critical Marriage Evaluation (Rev. Ogboo Akujuobi, PhD)&#10;11:30 AM - Breakout Sessions & Discussions&#10;12:30 PM - Interactive Q&A&#10;01:30 PM - Healing Prayers & Impartation for Families&#10;02:00 PM - Refreshments & Free Pastoral Counseling"><?= h($event['program_details'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <!-- Media / Flyer Upload Section -->
                <div class="col-12">
                    <hr>
                    <h6 class="fw-bold mb-1"><i class="bi bi-images me-1 text-gold"></i> Event Flyer & Program Images</h6>
                    <p class="text-muted small">Upload high-quality event flyer and program graphics. Files are stored directly in <code>uploads/events/</code>.</p>
                </div>
                
                <div class="col-md-6">
                    <div class="card p-3 h-100 border">
                        <label class="form-label fw-bold text-dark"><i class="bi bi-image text-danger me-1"></i> Main Event Flyer (Picture of the Flyer)</label>
                        <p class="small text-muted mb-2">The official event promotional flyer poster. Displayed prominently on the event card, detail page, and hero showcase.</p>
                        <?php if (!empty($event['featured_image'])): ?>
                        <div class="mb-3 p-2 bg-light rounded text-center">
                            <img src="<?= h(url($event['featured_image'])) ?>" alt="Event Flyer" style="max-height: 180px; width: auto; max-width: 100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,.15);" class="mb-2">
                            <div class="d-flex justify-content-center gap-3 align-items-center">
                                <a href="<?= h(url($event['featured_image'])) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i> View Full Flyer</a>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                                    <label class="form-check-label text-danger small" for="remove_image">Remove flyer</label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <input class="form-control" type="file" name="featured_image" accept="image/*">
                        <small class="text-muted mt-1">Accepts JPG, PNG, WEBP, GIF. Saved to <code>uploads/events/</code>.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card p-3 h-100 border">
                        <label class="form-label fw-bold text-dark"><i class="bi bi-card-image text-teal me-1"></i> Program Schedule Image / Order of Service (Optional)</label>
                        <p class="small text-muted mb-2">Optional graphic poster showing the order of service or program schedule booklet.</p>
                        <?php if (!empty($event['program_image'])): ?>
                        <div class="mb-3 p-2 bg-light rounded text-center">
                            <img src="<?= h(url($event['program_image'])) ?>" alt="Program Graphic" style="max-height: 180px; width: auto; max-width: 100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,.15);" class="mb-2">
                            <div class="d-flex justify-content-center gap-3 align-items-center">
                                <a href="<?= h(url($event['program_image'])) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i> View Full Image</a>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_program_image" id="remove_program_image">
                                    <label class="form-check-label text-danger small" for="remove_program_image">Remove image</label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <input class="form-control" type="file" name="program_image" accept="image/*">
                        <small class="text-muted mt-1">Accepts JPG, PNG, WEBP, GIF. Saved to <code>uploads/events/</code>.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="registration_required" id="reg_required" <?= !empty($event['registration_required']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="reg_required">Registration Required</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Registration Link (External)</label>
                    <input class="form-control" name="registration_link" value="<?= h($event['registration_link'] ?? '') ?>" placeholder="https://...">
                    <small class="text-muted">If empty, visitors will register interest via the ministry contact form.</small>
                </div>
                
                <div class="col-12">
                    <hr>
                    <h6>SEO</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input class="form-control" name="meta_title" value="<?= h($event['meta_title'] ?? '') ?>" maxlength="60">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <input class="form-control" name="meta_description" value="<?= h($event['meta_description'] ?? '') ?>" maxlength="160">
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-forest" type="submit"><i class="bi bi-save me-2"></i> Save Event</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function deleteItem(id, type) {
    if (!confirm("Delete this item?")) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= h(url('admin/')) ?>' + type + '.php?action=delete&id=' + id;
    form.innerHTML = '<?= csrf_field() ?>';
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>