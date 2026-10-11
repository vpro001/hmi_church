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
        ];
        
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadDir = __DIR__ . '/../../assets/img/events/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            $filename = $slug . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadDir . $filename)) {
                $data['featured_image'] = 'assets/img/events/' . $filename;
            }
        } elseif (!empty($_POST['existing_image'])) {
            $data['featured_image'] = $_POST['existing_image'];
        }
        
        if ($action === 'edit' && $eventId) {
            Database::execute(
                "UPDATE events SET slug=?, title=?, summary=?, description=?, featured_image=?, category=?, location=?, start_datetime=?, end_datetime=?, registration_required=?, registration_link=?, max_attendees=?, status=?, meta_title=?, meta_description=? WHERE id=?",
                [$data['slug'], $data['title'], $data['summary'], $data['description'], $data['featured_image'] ?? '', $data['category'], $data['location'], $data['start_datetime'], $data['end_datetime'], $data['registration_required'], $data['registration_link'], $data['max_attendees'], $data['status'], $data['meta_title'], $data['meta_description'], $eventId]
            );
            log_activity($admin['id'], 'admin', 'event_update', "Updated event: {$data['title']}");
            flash('success', 'Event updated.');
        } else {
            Database::execute(
                "INSERT INTO events (slug, title, summary, description, featured_image, category, location, start_datetime, end_datetime, registration_required, registration_link, max_attendees, status, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$data['slug'], $data['title'], $data['summary'], $data['description'], $data['featured_image'] ?? '', $data['category'], $data['location'], $data['start_datetime'], $data['end_datetime'], $data['registration_required'], $data['registration_link'], $data['max_attendees'], $data['status'], $data['meta_title'], $data['meta_description']]
            );
            log_activity($admin['id'], 'admin', 'event_create', "Created event: {$data['title']}");
            flash('success', 'Event created.');
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
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
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
                            <?php if ($e['featured_image']): ?>
                            <img src="<?= h(url($e['featured_image'])) ?>" alt="" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 40px; border-radius: 4px;"><small class="text-muted">No img</small></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= h($e['title']) ?></strong></td>
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
                                <a href="<?= h(url('admin/events.php?action=edit&id=' . $e['id'])) ?>" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <a href="<?= h(url('admin/events.php?action=view&id=' . $e['id'])) ?>" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-eye"></i></a>
                                <button class="btn btn-outline-danger" data-confirm="Delete '<?= h($e['title']) ?>'?" onclick="deleteItem(<?= $e['id'] ?>, 'events')"><i class="bi bi-trash"></i></button>
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
            <?php if ($event): ?><input type="hidden" name="existing_image" value="<?= h($event['featured_image']) ?>"><?php endif; ?>
            
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
                    <input class="form-control" type="datetime-local" name="start_datetime" required value="<?= $event['start_datetime'] ? date('Y-m-d\TH:i', strtotime($event['start_datetime'])) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">End Date & Time</label>
                    <input class="form-control" type="datetime-local" name="end_datetime" value="<?= $event['end_datetime'] ? date('Y-m-d\TH:i', strtotime($event['end_datetime'])) : '' ?>">
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
                    <textarea class="form-control" name="summary" rows="2"><?= h($event['summary'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Full Description</label>
                    <textarea class="form-control" name="description" rows="5"><?= h($event['description'] ?? '') ?></textarea>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="registration_required" id="reg_required" <?= !empty($event['registration_required']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="reg_required">Registration Required</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Registration Link</label>
                    <input class="form-control" name="registration_link" value="<?= h($event['registration_link'] ?? '') ?>" placeholder="https://...">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Featured Image</label>
                    <?php if (!empty($event['featured_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= h(url($event['featured_image'])) ?>" alt="" style="max-height: 150px; border-radius: 8px;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                            <label class="form-check-label" for="remove_image">Remove current image</label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <input class="form-control" type="file" name="featured_image" accept="image/*">
                    <small class="text-muted">Recommended: 800x450px</small>
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