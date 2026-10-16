<?php
/**
 * Admin Posts Management
 */

$page_title = 'Posts';
$currentPage = 'posts';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_post'])) {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        $slug = trim($_POST['slug'] ?? '');
        if (!$slug) $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $_POST['title']));
        
        $data = [
            'slug' => $slug,
            'title' => trim($_POST['title'] ?? ''),
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'category' => trim($_POST['category'] ?? ''),
            'status' => $_POST['status'] ?? 'draft',
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
        ];
        
        // Handle featured image
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadDir = dirname(__DIR__) . '/assets/img/posts/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                @chmod($uploadDir, 0777);
            }
            $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
            $filename = $slug . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadDir . $filename)) {
                @chmod($uploadDir . $filename, 0666);
                $data['featured_image'] = 'assets/img/posts/' . $filename;
            }
        } elseif (!empty($_POST['existing_image'])) {
            $data['featured_image'] = $_POST['existing_image'];
        }
        
        if ($action === 'edit' && $postId) {
            $published = $data['status'] === 'published' ? ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;
            Database::execute(
                "UPDATE posts SET slug=?, title=?, excerpt=?, content=?, featured_image=?, category=?, status=?, published_at=?, meta_title=?, meta_description=? WHERE id=?",
                [$data['slug'], $data['title'], $data['excerpt'], $data['content'], $data['featured_image'] ?? '', $data['category'], $data['status'], $published, $data['meta_title'], $data['meta_description'], $postId]
            );
            log_activity($admin['id'], 'admin', 'post_update', "Updated post: {$data['title']}");
            flash('success', 'Post updated.');
        } else {
            $published = $data['status'] === 'published' ? date('Y-m-d H:i:s') : null;
            Database::execute(
                "INSERT INTO posts (slug, title, excerpt, content, featured_image, category, status, published_at, meta_title, meta_description, author_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$data['slug'], $data['title'], $data['excerpt'], $data['content'], $data['featured_image'] ?? '', $data['category'], $data['status'], $published, $data['meta_title'], $data['meta_description'], $admin['id']]
            );
            log_activity($admin['id'], 'admin', 'post_create', "Created post: {$data['title']}");
            flash('success', 'Post created.');
        }
        redirect('admin/posts.php');
    }
}

if ($action === 'delete' && $postId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        Database::execute("DELETE FROM posts WHERE id = ?", [$postId]);
        log_activity($admin['id'], 'admin', 'post_delete', "Deleted post ID: $postId");
        flash('success', 'Post deleted.');
    }
    redirect('admin/posts.php');
}

$post = null;
if (($action === 'edit' || $action === 'view') && $postId) {
    $post = Database::fetchOne("SELECT * FROM posts WHERE id = ?", [$postId]);
    if (!$post) { flash('danger', 'Post not found.'); redirect('admin/posts.php'); }
}

$posts = Database::fetchAll("SELECT * FROM posts ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0"><?= $action === 'list' ? 'Posts' : ($action === 'edit' ? 'Edit Post' : 'Add Post') ?></h2>
        <p class="text-muted mb-0">Manage blog posts and articles</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="<?= h(url('admin/posts.php?action=add')) ?>" class="btn btn-forest"><i class="bi bi-plus-lg me-2"></i> Add Post</a>
    <?php else: ?>
    <a href="<?= h(url('admin/posts.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
    <?php endif; ?>
</div>

<?php if ($action === 'list'): ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($posts): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Published</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $p): ?>
                    <tr>
                        <td>
                            <?php if ($p['featured_image']): ?>
                            <img src="<?= h(url($p['featured_image'])) ?>" alt="" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 40px; border-radius: 4px;"><small class="text-muted">No img</small></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= h($p['title']) ?></strong>
                            <br><small class="text-muted"><?= h($p['slug']) ?></small>
                        </td>
                        <td><?= h($p['category'] ?: '—') ?></td>
                        <td>
                            <span class="badge bg-<?= $p['status'] === 'published' ? 'published' : ($p['status'] === 'draft' ? 'draft' : 'archived') ?>">
                                <?= ucfirst($p['status']) ?>
                            </span>
                        </td>
                        <td><?= h($p['author_id'] ? 'Admin' : '—') ?></td>
                        <td><?= $p['published_at'] ? fmt_date($p['published_at'], 'M j, Y') : '—' ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= h(url('admin/posts.php?action=edit&id=' . $p['id'])) ?>" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="<?= h(url('admin/posts.php?action=view&id=' . $p['id'])) ?>" class="btn btn-outline-secondary" title="View" target="_blank"><i class="bi bi-eye"></i></a>
                                <button class="btn btn-outline-danger" data-confirm="Delete '<?= h($p['title']) ?>'?" onclick="deleteItem(<?= $p['id'] ?>, 'posts')"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-journal-text"></i><h5>No posts yet</h5><a href="<?= h(url('admin/posts.php?action=add')) ?>" class="btn btn-forest mt-3">Create First Post</a></div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="save_post" value="1">
            <?php if ($post): ?><input type="hidden" name="existing_image" value="<?= h($post['featured_image']) ?>"><?php endif; ?>
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input class="form-control" name="title" required value="<?= h($post['title'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                    <input class="form-control" name="slug" required value="<?= h($post['slug'] ?? '') ?>" pattern="[a-z0-9-]+">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <input class="form-control" name="category" value="<?= h($post['category'] ?? '') ?>" list="categories">
                    <datalist id="categories">
                        <option>Counseling</option>
                        <option>Teaching</option>
                        <option>Prayer</option>
                        <option>Events</option>
                        <option>Outreach</option>
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= ($post['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Excerpt</label>
                    <textarea class="form-control" name="excerpt" rows="2"><?= h($post['excerpt'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea class="form-control" name="content" rows="10"><?= h($post['content'] ?? '') ?></textarea>
                    <small class="text-muted">Plain text - each paragraph on a new line will be wrapped in <p> tags</small>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Featured Image</label>
                    <?php if (!empty($post['featured_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= h(url($post['featured_image'])) ?>" alt="" style="max-height: 150px; border-radius: 8px;">
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
                    <input class="form-control" name="meta_title" value="<?= h($post['meta_title'] ?? '') ?>" maxlength="60">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <input class="form-control" name="meta_description" value="<?= h($post['meta_description'] ?? '') ?>" maxlength="160">
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-forest" type="submit"><i class="bi bi-save me-2"></i> Save Post</button>
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