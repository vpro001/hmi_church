<?php
/**
 * Admin panel - Login page
 */

require_once __DIR__ . '/../includes/auth.php';

if (admin_check()) {
    redirect('admin/index.php');
}

$flash = take_flash();
$page_title = 'Admin Login';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($page_title) ?> | Healing My-Marriage Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap/bootstrap.min.css')) ?>" rel="stylesheet">
<link href="<?= h(asset('vendor/bootstrap-icons/bootstrap-icons.min.css')) ?>" rel="stylesheet">
<style>
:root { --forest-700: #1f4733; --forest-600: #265f40; --green: #2e8b57; --red: #c0392b; }
body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5f8f6 0%, #e8f0ec 100%); font-family: 'Source Sans 3', sans-serif; padding: 2rem; }
.login-card { background: #fff; border-radius: 16px; box-shadow: 0 10px 40px rgba(31,71,51,0.12); padding: 3rem; width: 100%; max-width: 420px; }
.login-header { text-align: center; margin-bottom: 2rem; }
.login-header img { height: 80px; margin-bottom: 1rem; }
.login-header h1 { font-family: 'Bricolage Grotesque', sans-serif; color: var(--forest-700); font-weight: 700; font-size: 1.75rem; margin: 0; }
.login-header p { color: #666; margin: .5rem 0 0; }
.form-control { border-radius: 8px; padding: .875rem 1rem; border: 1px solid #d1d9d5; }
.form-control:focus { border-color: var(--forest-600); box-shadow: 0 0 0 3px rgba(31,71,51,0.15); }
.btn-hmi { background: var(--forest-700); border: none; color: #fff; padding: .875rem; font-weight: 600; border-radius: 8px; width: 100%; }
.btn-hmi:hover { background: var(--forest-600); }
.alert { border-radius: 8px; border: none; }
</style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <img src="<?= h(asset('img/logo.jpeg')) ?>" alt="Healing My-Marriage International">
        <h1>Admin Panel</h1>
        <p>Sign in to manage the ministry website</p>
    </div>
    
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <?= h($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <form method="post" action="<?= h(url('admin/actions/login.php')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label fw-medium" for="username">Username or Email</label>
            <input class="form-control" type="text" id="username" name="username" required autocomplete="username" value="<?= old($flash, 'username') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium" for="password">Password</label>
            <input class="form-control" type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button class="btn btn-hmi mt-3" type="submit"><i class="bi bi-box-arrow-in-right me-2"></i> Sign In</button>
    </form>
    
    <div class="text-center mt-4">
        <a href="<?= h(url('index.php')) ?>" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Back to website</a>
    </div>
</div>
<script src="<?= h(asset('vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
</body>
</html>