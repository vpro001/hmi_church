<?php
/**
 * Admin Products Management
 */

$page_title = 'Products';
$currentPage = 'products';
require_once __DIR__ . '/includes/header.php';

// Handle actions
$action = $_GET['action'] ?? 'list';
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'slug' => trim($_POST['slug'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'price' => (int)($_POST['price'] ?? 0),
            'sale_price' => $_POST['sale_price'] !== '' ? (int)$_POST['sale_price'] : null,
            'format' => trim($_POST['format'] ?? 'E-book (digital)'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
        ];
        
        // Handle cover image upload
        if (!empty($_FILES['cover_image']['name'])) {
            $uploadDir = __DIR__ . '/../../assets/img/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
            $filename = $data['slug'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $filename)) {
                $data['cover_image'] = 'assets/img/products/' . $filename;
            }
        } elseif (!empty($_POST['existing_cover'])) {
            $data['cover_image'] = $_POST['existing_cover'];
        }
        
        if ($action === 'edit' && $productId) {
            $sql = "UPDATE products SET category_id=?, slug=?, title=?, description=?, short_description=?, price=?, sale_price=?, cover_image=?, format=?, is_featured=?, is_active=?, sort_order=?, meta_title=?, meta_description=? WHERE id=?";
            $params = array_values($data);
            $params[] = $productId;
            Database::execute($sql, $params);
            log_activity($admin['id'], 'admin', 'product_update', "Updated product: {$data['title']}");
            flash('success', 'Product updated successfully.');
        } else {
            $sql = "INSERT INTO products (category_id, slug, title, description, short_description, price, sale_price, cover_image, format, is_featured, is_active, sort_order, meta_title, meta_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            Database::execute($sql, array_values($data));
            log_activity($admin['id'], 'admin', 'product_create', "Created product: {$data['title']}");
            flash('success', 'Product created successfully.');
        }
        redirect('admin/products.php');
    }
}

if ($action === 'delete' && $productId && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        Database::execute("DELETE FROM products WHERE id = ?", [$productId]);
        log_activity($admin['id'], 'admin', 'product_delete', "Deleted product ID: $productId");
        flash('success', 'Product deleted.');
    }
    redirect('admin/products.php');
}

// Get data
$categories = Database::fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name");
$product = null;
if ($action === 'edit' && $productId) {
    $product = Database::fetchOne("SELECT * FROM products WHERE id = ?", [$productId]);
    if (!$product) {
        flash('danger', 'Product not found.');
        redirect('admin/products.php');
    }
}

$products = Database::fetchAll("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    ORDER BY p.sort_order, p.title
");
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="h3 mb-0">Products</h2>
        <p class="text-muted mb-0">Manage e-books and digital products</p>
    </div>
    <a href="<?= h(url('admin/products.php?action=add')) ?>" class="btn btn-forest"><i class="bi bi-plus-lg me-2"></i> Add Product</a>
</div>

<?php if ($action === 'list'): ?>
<div class="card">
    <div class="card-body p-0">
        <?php if ($products): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;">Cover</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th style="width: 120px;">Price</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 80px;">Order</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <?php if ($p['cover_image']): ?>
                            <img src="<?= h(url($p['cover_image'])) ?>" alt="" class="product-img-thumb">
                            <?php else: ?>
                            <div class="product-img-thumb bg-light d-flex align-items-center justify-content-center text-muted small">No image</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= h($p['title']) ?></strong>
                            <br><small class="text-muted"><?= h($p['slug']) ?></small>
                            <?php if ($p['is_featured']): ?><span class="badge bg-warning text-dark ms-2">Featured</span><?php endif; ?>
                        </td>
                        <td><?= h($p['category_name'] ?? '—') ?></td>
                        <td>
                            <?= money($p['price']) ?>
                            <?php if ($p['sale_price']): ?>
                            <br><small class="text-decoration-line-through text-muted"><?= money($p['price']) ?></small>
                            <br><strong class="text-success"><?= money($p['sale_price']) ?></strong>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['is_active']): ?>
                            <span class="badge bg-published">Active</span>
                            <?php else: ?>
                            <span class="badge bg-draft">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="number" class="form-control form-control-sm" value="<?= (int)$p['sort_order'] ?>" data-sort-order="<?= $p['id'] ?>" style="width: 70px;">
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?= h(url('admin/products.php?action=edit&id=' . $p['id'])) ?>" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-outline-danger" data-confirm="Delete '<?= h($p['title']) ?>'?" onclick="deleteProduct(<?= $p['id'] ?>, this)"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state p-5"><i class="bi bi-book"></i><h5>No products yet</h5><p class="mb-3">Add your first e-book to get started.</p><a href="<?= h(url('admin/products.php?action=add')) ?>" class="btn btn-forest">Add Product</a></div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- Add/Edit Form -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?= $action === 'edit' ? 'Edit Product' : 'Add New Product' ?></h5>
        <a href="<?= h(url('admin/products.php')) ?>" class="btn btn-sm btn-outline-secondary">Cancel</a>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="save_product" value="1">
            <?php if ($product): ?><input type="hidden" name="existing_cover" value="<?= h($product['cover_image']) ?>"><?php endif; ?>
            
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input class="form-control" name="title" required value="<?= h($product['title'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slug <span class="text-danger">*</span></label>
                    <input class="form-control" name="slug" required value="<?= h($product['slug'] ?? '') ?>" pattern="[a-z0-9-]+" title="Lowercase letters, numbers, and hyphens only">
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">— Select —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($product['category_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= h($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Format</label>
                    <input class="form-control" name="format" value="<?= h($product['format'] ?? 'E-book (digital)') ?>">
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Price (₦) <span class="text-danger">*</span></label>
                    <input class="form-control" type="number" name="price" min="0" step="500" required value="<?= (int)($product['price'] ?? 0) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sale Price (₦) <small class="text-muted">(optional)</small></label>
                    <input class="form-control" type="number" name="sale_price" min="0" step="500" value="<?= $product['sale_price'] ?? '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort Order</label>
                    <input class="form-control" type="number" name="sort_order" value="<?= (int)($product['sort_order'] ?? 0) ?>">
                </div>
                
                <div class="col-12">
                    <label class="form-label">Short Description</label>
                    <textarea class="form-control" name="short_description" rows="2"><?= h($product['short_description'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Full Description</label>
                    <textarea class="form-control" name="description" rows="5"><?= h($product['description'] ?? '') ?></textarea>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Cover Image</label>
                    <?php if (!empty($product['cover_image'])): ?>
                    <div class="mb-2">
                        <img src="<?= h(url($product['cover_image'])) ?>" alt="" style="max-height: 150px; border-radius: 8px;">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_cover" id="remove_cover">
                            <label class="form-check-label" for="remove_cover">Remove current image</label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <input class="form-control" type="file" name="cover_image" accept="image/*" data-preview="coverPreview">
                    <small class="text-muted">Recommended: 600x800px, JPG/PNG/GIF</small>
                    <img id="coverPreview" src="" alt="Preview" style="max-height: 150px; border-radius: 8px; margin-top: 0.5rem; display: none;">
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?= !empty($product['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= !isset($product['is_active']) || $product['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active / Visible</label>
                    </div>
                </div>
                
                <div class="col-12">
                    <hr>
                    <h6>SEO</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Title</label>
                    <input class="form-control" name="meta_title" value="<?= h($product['meta_title'] ?? '') ?>" maxlength="60">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Meta Description</label>
                    <input class="form-control" name="meta_description" value="<?= h($product['meta_description'] ?? '') ?>" maxlength="160">
                </div>
                
                <div class="col-12">
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-forest" type="submit"><i class="bi bi-save me-2"></i> Save Product</button>
                        <a href="<?= h(url('admin/products.php')) ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function deleteProduct(id, btn) {
    if (!confirm("Delete this product?")) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= h(url('admin/products.php?action=delete&id=')) ?>' + id;
    form.innerHTML = '<?= csrf_field() ?>';
    document.body.appendChild(form);
    form.submit();
}

// Auto-save sort order
document.querySelectorAll('[data-sort-order]').forEach(input => {
    let timer;
    input.addEventListener('change', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fetch('<?= h(url('admin/actions/product_sort.php')) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'csrf=' + encodeURIComponent('<?= csrf_token() ?>') + '&id=' + this.dataset.sortOrder + '&order=' + this.value
            });
        }, 500);
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>