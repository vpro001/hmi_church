<?php
/**
 * Admin Settings
 */

$page_title = 'Settings';
$currentPage = 'settings';
admin_require_role('superadmin', 'admin');
require_once __DIR__ . '/includes/header.php';

$tab = $_GET['tab'] ?? 'general';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!csrf_valid()) flash('danger', 'Invalid CSRF token.');
    else {
        $settings = [
            'site_name' => trim($_POST['site_name'] ?? ''),
            'site_short_name' => trim($_POST['site_short_name'] ?? ''),
            'site_tagline' => trim($_POST['site_tagline'] ?? ''),
            'site_email' => trim($_POST['site_email'] ?? ''),
            'site_phones' => json_encode(array_filter(array_map('trim', explode("\n", $_POST['site_phones'] ?? '')))),
            'site_phones_raw' => json_encode(array_filter(array_map('trim', explode("\n", $_POST['site_phones_raw'] ?? '')))),
            'site_address' => trim($_POST['site_address'] ?? ''),
            'site_hours' => trim($_POST['site_hours'] ?? ''),
            'facebook_url' => trim($_POST['facebook_url'] ?? ''),
            'youtube_url' => trim($_POST['youtube_url'] ?? ''),
            'ga_id' => trim($_POST['ga_id'] ?? ''),
            'mail_enabled' => isset($_POST['mail_enabled']) ? '1' : '0',
            'mail_to' => trim($_POST['mail_to'] ?? ''),
            'mail_from' => trim($_POST['mail_from'] ?? ''),
            'currency_symbol' => trim($_POST['currency_symbol'] ?? '₦'),
            'currency_code' => trim($_POST['currency_code'] ?? 'NGN'),
        ];
        
        foreach ($settings as $key => $value) {
            $type = in_array($key, ['site_phones', 'site_phones_raw', 'mail_enabled']) ? 
                ($key === 'mail_enabled' ? 'bool' : 'json') : 'string';
            set_setting($key, $value, $type, '', true);
        }
        
        log_activity($admin['id'], 'admin', 'settings_update', "Updated general settings");
        flash('success', 'Settings saved.');
        redirect('admin/settings.php?tab=general');
    }
}

$settings = get_public_settings();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0">Settings</h2>
        <p class="text-muted mb-0">Configure site settings and preferences</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link <?= $tab === 'general' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=general')) ?>">General</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'contact' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=contact')) ?>">Contact Info</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'social' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=social')) ?>">Social</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'email' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=email')) ?>">Email</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'advanced' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=advanced')) ?>">Advanced</a></li>
        </ul>
    </div>
    <div class="card-body">
        <form method="post" class="needs-validation" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="save_settings" value="1">
            
            <?php if ($tab === 'general'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Site Name</label>
                    <input class="form-control" name="site_name" value="<?= h($settings['site_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Short Name</label>
                    <input class="form-control" name="site_short_name" value="<?= h($settings['site_short_name'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Tagline</label>
                    <input class="form-control" name="site_tagline" value="<?= h($settings['site_tagline'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Currency Symbol</label>
                    <input class="form-control" name="currency_symbol" value="<?= h($settings['currency_symbol'] ?? '₦') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Currency Code</label>
                    <input class="form-control" name="currency_code" value="<?= h($settings['currency_code'] ?? 'NGN') ?>">
                </div>
            </div>
            
            <?php elseif ($tab === 'contact'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Contact Email</label>
                    <input class="form-control" type="email" name="site_email" value="<?= h($settings['site_email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phones (display format, one per line)</label>
                    <textarea class="form-control" name="site_phones" rows="3"><?= h(implode("\n", $settings['site_phones'] ?? [])) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phones (raw format for tel: links, one per line)</label>
                    <textarea class="form-control" name="site_phones_raw" rows="3"><?= h(implode("\n", $settings['site_phones_raw'] ?? [])) ?></textarea>
                    <small class="text-muted">E.g., +2347042226633</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" name="site_address" rows="2"><?= h($settings['site_address'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Business Hours</label>
                    <input class="form-control" name="site_hours" value="<?= h($settings['site_hours'] ?? '') ?>">
                </div>
            </div>
            
            <?php elseif ($tab === 'social'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Facebook URL</label>
                    <input class="form-control" name="facebook_url" value="<?= h($settings['facebook_url'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">YouTube URL</label>
                    <input class="form-control" name="youtube_url" value="<?= h($settings['youtube_url'] ?? '') ?>">
                </div>
            </div>
            
            <?php elseif ($tab === 'email'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="mail_enabled" id="mail_enabled" <?= !empty($settings['mail_enabled']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="mail_enabled">Enable Email Notifications</label>
                    </div>
                    <small class="text-muted d-block mt-2">Requires server mail configuration</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Notification Recipient</label>
                    <input class="form-control" type="email" name="mail_to" value="<?= h($settings['mail_to'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">From Email</label>
                    <input class="form-control" type="email" name="mail_from" value="<?= h($settings['mail_from'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>Note:</strong> Email sending requires a working PHP mail() configuration or SMTP setup.
                        For production, consider using a service like SendGrid, Mailgun, or SMTP.
                    </div>
                </div>
            </div>
            
            <?php elseif ($tab === 'advanced'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Google Analytics GA4 ID</label>
                    <input class="form-control" name="ga_id" value="<?= h($settings['ga_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
                </div>
            </div>
            <hr>
            <h6>Cache & Maintenance</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" type="button" onclick="clearCache()">Clear Cache</button>
            </div>
            <?php endif; ?>
            
            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-forest" type="submit"><i class="bi bi-save me-2"></i> Save Settings</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
function clearCache() {
    if (confirm('Clear all caches?')) {
        fetch('<?= h(url('admin/actions/clear_cache.php')) ?>', { method: 'POST' })
            .then(r => r.json())
            .then(d => alert(d.message || 'Cache cleared'));
    }
}
</script>