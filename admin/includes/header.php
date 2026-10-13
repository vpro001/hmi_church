<?php
/**
 * Admin header
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

admin_require_role('superadmin', 'admin', 'editor');

$admin = admin_user();
$currentPage = $currentPage ?? '';
$settings = get_public_settings();

$siteName = $settings['site_short_name'] ?? 'Healing My-Marriage';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0f261b">
<title><?= h($page_title ?? 'Dashboard') ?> | <?= h($siteName) ?> Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap/bootstrap.min.css')) ?>" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>" rel="stylesheet">
<link href="<?= h(asset('admin/css/admin.css')) ?>" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="<?= h(url('admin/index.php')) ?>" class="sidebar-brand">
                <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="<?= h($siteName) ?>" class="brand-img">
                <span class="brand-text">
                    <b><?= h($siteName) ?></b>
                    <span>Admin Panel</span>
                </span>
            </a>
        </div>
        <nav class="sidebar-nav" aria-label="Admin navigation">
            <ul class="nav flex-column">
                <li class="nav-group-label" role="presentation">Overview</li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= h(url('admin/index.php')) ?>">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-group-label" role="presentation">Store</li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'products' ? 'active' : '' ?>" href="<?= h(url('admin/products.php')) ?>">
                        <i class="bi bi-book"></i> <span>Products</span>
                        <span class="badge bg-success ms-auto" id="productCount">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'orders' ? 'active' : '' ?>" href="<?= h(url('admin/orders.php')) ?>">
                        <i class="bi bi-cart-check"></i> <span>Orders</span>
                        <span class="badge bg-warning ms-auto" id="pendingOrders">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>" href="<?= h(url('admin/users.php')) ?>">
                        <i class="bi bi-people"></i> <span>Customers</span>
                    </a>
                </li>

                <li class="nav-group-label" role="presentation">Ministry</li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'submissions' ? 'active' : '' ?>" href="<?= h(url('admin/submissions.php')) ?>">
                        <i class="bi bi-envelope"></i> <span>Submissions</span>
                        <span class="badge bg-danger ms-auto" id="unreadSubmissions">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'posts' ? 'active' : '' ?>" href="<?= h(url('admin/posts.php')) ?>">
                        <i class="bi bi-journal-text"></i> <span>Posts</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'events' ? 'active' : '' ?>" href="<?= h(url('admin/events.php')) ?>">
                        <i class="bi bi-calendar-event"></i> <span>Events</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'pages' ? 'active' : '' ?>" href="<?= h(url('admin/pages.php')) ?>">
                        <i class="bi bi-file-text"></i> <span>Pages</span>
                    </a>
                </li>

                <?php if (in_array($admin['role'], ['superadmin', 'admin'])): ?>
                <li class="nav-group-label" role="presentation">System</li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php')) ?>">
                        <i class="bi bi-gear"></i> <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'admins' ? 'active' : '' ?>" href="<?= h(url('admin/admins.php')) ?>">
                        <i class="bi bi-person-badge"></i> <span>Admin Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'activity' ? 'active' : '' ?>" href="<?= h(url('admin/activity.php')) ?>">
                        <i class="bi bi-activity"></i> <span>Activity Log</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="admin-main">
        <!-- Top bar -->
        <header class="admin-topbar">
            <div class="d-flex align-items-center justify-content-between w-100 gap-3">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <button class="btn btn-link p-0 d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="topbar-titles min-w-0">
                        <span class="topbar-eyebrow"><?= h($siteName) ?></span>
                        <span class="topbar-title"><?= h($page_title ?? 'Dashboard') ?></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    <a href="<?= h(url('index.php')) ?>" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm" title="View website">
                        <i class="bi bi-box-arrow-up-right"></i> <span class="d-none d-md-inline">View site</span>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 d-flex align-items-center gap-2 dropdown-toggle text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar-circle"><?= h(strtoupper($admin['full_name'][0])) ?></span>
                            <span class="d-none d-md-inline text-start">
                                <span style="display:block; line-height:1.1; font-weight:600; color:var(--forest-900);"><?= h($admin['full_name']) ?></span>
                                <small style="color:var(--muted); text-transform:capitalize;"><?= h($admin['role']) ?></small>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header"><?= h($admin['full_name']) ?></h6></li>
                            <li><small class="dropdown-item-text text-muted"><?= h(ucfirst($admin['role'])) ?></small></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= h(url('admin/profile.php')) ?>"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?= h(url('admin/actions/logout.php')) ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <main class="admin-content">
            <?php $flash = take_flash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2" aria-hidden="true"></i><?= h($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
