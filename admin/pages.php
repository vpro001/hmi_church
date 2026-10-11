<?php
/**
 * Admin Pages Management (CMS)
 */

$page_title = 'Pages';
$currentPage = 'pages';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$pageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_page'])) {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        $slug = trim($_POST['slug'] ?? '');
        if (!$slug) $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $_POST['title']));
        
        $data = [
            'slug' => $slug,
            'title' => trim($_POST['title'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'template' => $_POST['template'] ?? 'default',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'show_in_nav' => isset($_POST['show_in_nav']) ? 1 : 0,
            'nav_order' => (int)($_POST['nav_order'] ?? 0),
        ];
        
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadDir = __DIR__ . '/../../assets/img/pages/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            $filename = $slug . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadDir . $filename)) {
                $data['featured_image'] = 'assets/img/pages/' . $filename;
            }
        } elseif (!empty($_POST['existing_image'])) {
            $data['featured_image'] = $_POST['existing_image'];
        }
        
        if ($action === 'edit' && $pageId) {
            Database::execute(
                "UPDATE pages SET slug=?, title=?, content=?, excerpt=?, featured_image=?, meta_title=?, meta_description=?, template=?, is_active=?, show_in_nav=?, nav_order=? WHERE id=?",
                [$data['slug'], $data['title'], $data['content'], $data['excerpt'], $data['featured_image'] ?? '', $data['meta_title'], $data['meta_description'], $data['template'], $data['is_active'], $data['show_in_nav'], $data['nav_order'], $pageId]
            );
            log_activity($admin['id'], 'admin', 'page_update', "Updated page: {$data['title']}");
            flash('success', 'Page updated.');
        } else {
            Database::execute(
                "INSERT INTO pages (slug, title, content, excerpt, featured_image, meta_title, meta_description, template, is_active, show_in_nav, nav_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$data['slug'], $data['title'], $data['content'], $data['excerpt'], $data['featured_image'] ?? '', $data['meta_title'], $data['meta_description'], $data['template'], $data['is_active'], $data['show_in_nav'], $data['nav_order']]
            );
            log_activity($admin['id'], 'admin', 'page_create', "Created page: {$data['title']}");
            flash('success', 'Page created.');
        }
        redirect('admin/pages.php');
    }
}

if ($action === 'delete' && $pageId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        // Prevent deleting essential pages
        $p = Database::fetchOne("SELECT slug FROM pages WHERE id = ?", [$pageId]);
        if (in_array($p['slug'] ?? '', ['privacy', 'about', 'contact'])) {
            flash('danger', 'Cannot delete essential system pages.');
        } else {
            Database::execute("DELETE FROM pages WHERE id = ?", [$pageId]);
            log_activity($admin['id'], 'admin', 'page_delete', "Deleted page ID: $pageId");
            flash('success', 'Page deleted.');
        }
    }
    redirect('admin/pages.php');
}

$page = null;
if (($action === 'edit' || $action === 'view') && $pageId) {
    $page = Database::fetchOne("SELECT * FROM pages WHERE id = ?", [$pageId]);
    if (!$page) { flash('danger', 'Page not found.'); redirect('admin/pages.php'); }
}

$pages = Database::fetchAll("SELECT * FROM pages ORDER BY nav_order, title");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0"><?= $action === 'list' ? 'Pages' : ($action === 'edit' ? 'Edit Page' : 'Add Page') ?></h2>
        <p class="text-muted mb-0">Manage static pages (About, Contact, Privacy, etc.)</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="<?= h(url('admin/pages.php?action=add')) ?>" class="btn btn-forest"><i class="bi bi-plus-lg me-2"></i> Add Page</a>
    <?php else: ?>
    <a href="<?= h(url('admin/pages.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($pages): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Template</th>
                        <th>Status</th>
                        <th>In Nav</th>
                        <th>Order</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td><strong><?= h($p['title']) ?></strong></td>
                        <td><code><?= h($p['slug']) ?></code></td>
                        <td><?= h($p['template']) ?></td>
                        <td><span class="badge bg-<?= $p['is_active'] ? 'published' : 'draft' ?>"><?= $p['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                        <td><?= $p['show_in_nav'] ? '<i class="bi bi-check text-success"></i>' : '<i class="bi bi-x text-muted"></i>' ?></td>
                        <td><?= (int)$p['nav_order'] ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= h(url('admin/pages.php?action=edit&id=' . $p['id'])) ?>" class="btn btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <a href="<?= h(url('admin/pages.php?action=view&id=' . $p['id'])) ?>" class="btn btn-outline-secondary" target="_blank"><i class="bi bi-eye"></i></a>
                                <?php if (!in_array($p['slug'], ['privacy', 'about', 'contact'])): ?>
                                <button class="btn btn-outline-danger" data-confirm="Delete '<?= h($p['title']) ?>'?" onclick="deleteItem(<?= $p['id'] ?>, 'pages')"><i class="bi bi-trash"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-file-text"></i><h5>No pages yet</h5><a href="<?= h(url('admin/pages.php?action=add')) ?>" class="btn btn-forest mt-3">Create First Page</a></div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="save_page" value="1">
            <?php if ($page): ?><input type="hidden" name="existing_image" value="<?= h($page['featured_image']) ?>"><?php endif; ?>
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input class="form-control" name="title" required value="<?= h($page['title'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                    <input class="form-control" name="slug" required value="<?= h($page['slug'] ?? '') ?>" pattern="[a-z0-9-]+">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Template</label>
                    <select class="form-select" name="template">
                        <option value="default" <?= ($page['template'] ?? 'default') === 'default' ? 'selected' : '' ?>>Default</option>
                        <option value="fullwidth" <?= ($page['template'] ?? '') === 'fullwidth' ? 'selected' : '' ?>>Full Width</option>
                        <option value="landing" <?= ($page['template'] ?? '') === 'landing' ? 'selected' : '' ?>>Landing Page</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nav Order</label>
                    <input class="form-control" type="number" name="nav_order" value="<?= (int)($page['nav_order'] ?? 0) ?>">
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($page['is_active']) || $page['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active / Visible</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="show_in_nav" id="show_in_nav" <?= !empty($page['show_in_nav']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="show_in_nav">Show in Navigation</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Excerpt</label>
                    <textarea class="form-control" name="excerpt" rows="2"><?= h($page['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Content (HTML)</label>
                    <textarea class="form-control" name="content" rows="10"><?= h($page['content'] ?? '') ?></textarea>
                    <small class="text-muted">Full HTML allowed. Use Bootstrap 5 classes.</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Featured Image</label>
                    <?php if (!empty($page['featured_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= h(url($page['featured_image'])) ?>" alt="" style="max-height: 150px; border-radius: 8px;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image">
                            <label class="form-check-label" for="remove_image">Remove current image</label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <input class="form-control" type="file" name="featured_image" accept="image/*">
                </div>
                
                <div class="col-12">
                    <hr>
                    <h6>SEO</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input class="form-control" name="meta_title" value="<?= h($page['meta_title'] ?? '') ?>" maxlength="60">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <input class="form-control" name="meta_description" value="<?= h($page['meta_description'] ?? '') ?>" maxlength="160">
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-forest" type="submit"><i class="bi bi-save me-2"></i> Save Page</button>
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