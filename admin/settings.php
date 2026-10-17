<?php
/**
 * Admin Settings
 */

$page_title = 'Settings';
$currentPage = 'settings';
require_once __DIR__ . '/../includes/auth.php';
admin_require_role('superadmin', 'admin');
require_once __DIR__ . '/includes/header.php';

$tab = $_GET['tab'] ?? 'general';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    if (!csrf_valid()) {
        flash('danger', 'Invalid CSRF token.');
    } else {
        $currentTab = $_POST['tab'] ?? $tab;

        if ($currentTab === 'payment') {
            $paystackEnabled   = isset($_POST['paystack_enabled']) ? '1' : '0';
            $paystackMode      = in_array($_POST['paystack_mode'] ?? '', ['test', 'live']) ? $_POST['paystack_mode'] : 'test';
            $paystackPublicKey = trim($_POST['paystack_public_key'] ?? '');
            $paystackSecretKey = trim($_POST['paystack_secret_key'] ?? '');
            $paystackSimulator = isset($_POST['paystack_test_simulator']) ? '1' : '0';

            set_setting('paystack_enabled', $paystackEnabled, 'bool', 'Enable Paystack payment gateway', true);
            set_setting('paystack_mode', $paystackMode, 'string', 'Paystack mode (test or live)', true);
            set_setting('paystack_public_key', $paystackPublicKey, 'string', 'Paystack Public Key', true);
            set_setting('paystack_secret_key', $paystackSecretKey, 'string', 'Paystack Secret Key', false);
            set_setting('paystack_test_simulator', $paystackSimulator, 'bool', 'Allow simulated test payment', false);

            log_activity($admin['id'], 'admin', 'settings_update', "Updated Paystack payment gateway settings");
            flash('success', 'Payment settings saved successfully.');
            redirect('admin/settings.php?tab=payment');
        } else {
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
            
            log_activity($admin['id'], 'admin', 'settings_update', "Updated " . htmlspecialchars($currentTab) . " settings");
            flash('success', 'Settings saved.');
            redirect('admin/settings.php?tab=' . urlencode($currentTab));
        }
    }
}

$settings = get_public_settings();
$paystackSecret = get_setting('paystack_secret_key', '');
$paystackPublic = get_setting('paystack_public_key', '');
$paystackEnabled = (bool)get_setting('paystack_enabled', true);
$paystackMode = get_setting('paystack_mode', 'test');
$paystackSimulator = (bool)get_setting('paystack_test_simulator', true);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0">Settings</h2>
        <p class="text-muted mb-0">Configure site settings, payment gateway, and preferences</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link <?= $tab === 'general' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=general')) ?>">General</a></li>
            <li class="nav-item"><a class="nav-link <?= $tab === 'payment' ? 'active' : '' ?>" href="<?= h(url('admin/settings.php?tab=payment')) ?>"><i class="bi bi-credit-card me-1"></i> Payment (Paystack)</a></li>
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
            <input type="hidden" name="tab" value="<?= h($tab) ?>">
            
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

            <?php elseif ($tab === 'payment'): ?>
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between p-3 rounded bg-light border">
                        <div>
                            <h5 class="mb-1"><i class="bi bi-shield-check text-primary me-2"></i>Paystack Gateway Status</h5>
                            <p class="text-muted small mb-0">Enable or disable Paystack for shop purchases and e-book checkouts.</p>
                        </div>
                        <div class="form-check form-switch fs-5 m-0">
                            <input class="form-check-input" type="checkbox" name="paystack_enabled" id="paystack_enabled" <?= $paystackEnabled ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="paystack_mode">Environment Mode</label>
                    <select class="form-select" name="paystack_mode" id="paystack_mode">
                        <option value="test" <?= $paystackMode === 'test' ? 'selected' : '' ?>>Test Mode (Sandbox)</option>
                        <option value="live" <?= $paystackMode === 'live' ? 'selected' : '' ?>>Live Mode (Production)</option>
                    </select>
                    <small class="text-muted">Use Test Mode for testing with Paystack test cards.</small>
                </div>

                <div class="col-md-6">
                    <div class="form-check mt-md-4 pt-md-2">
                        <input class="form-check-input" type="checkbox" name="paystack_test_simulator" id="paystack_test_simulator" <?= $paystackSimulator ? 'checked' : '' ?>>
                        <label class="form-check-label" for="paystack_test_simulator">
                            <strong>Enable Local Test Sandbox Simulator</strong>
                        </label>
                        <small class="text-muted d-block">Allows completing test purchases when Paystack API keys are not yet configured.</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="paystack_public_key">Paystack Public Key</label>
                    <input class="form-control font-monospace" name="paystack_public_key" id="paystack_public_key" value="<?= h($paystackPublic) ?>" placeholder="pk_test_... or pk_live_...">
                    <small class="text-muted">Found on your Paystack Dashboard under Settings &rarr; API Keys & Webhooks.</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" for="paystack_secret_key">Paystack Secret Key</label>
                    <div class="input-group">
                        <input class="form-control font-monospace" type="password" name="paystack_secret_key" id="paystack_secret_key" value="<?= h($paystackSecret) ?>" placeholder="sk_test_... or sk_live_...">
                        <button class="btn btn-outline-secondary" type="button" onclick="toggleSecretVisibility()"><i class="bi bi-eye" id="secretEye"></i></button>
                    </div>
                    <small class="text-muted">Never share this key publicly. Kept secure and private.</small>
                </div>

                <div class="col-12">
                    <div class="card bg-light border">
                        <div class="card-body">
                            <h6 class="card-title fw-bold"><i class="bi bi-link-45deg me-1"></i> Paystack Webhook & Callback Configuration</h6>
                            <p class="small text-muted mb-3">Copy these URLs into your <strong>Paystack Dashboard &rarr; Settings &rarr; API Keys & Webhooks</strong>:</p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Webhook URL</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-white font-monospace" readonly value="<?= h(url('actions/paystack_webhook.php')) ?>" id="webhookUrlInput">
                                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('webhookUrlInput').value); alert('Webhook URL copied!');">Copy</button>
                                    </div>
                                    <small class="text-muted">Handles background payment notifications (e.g. charge.success).</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small text-muted mb-1">Callback URL</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-white font-monospace" readonly value="<?= h(url('actions/paystack_callback.php')) ?>" id="callbackUrlInput">
                                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('callbackUrlInput').value); alert('Callback URL copied!');">Copy</button>
                                    </div>
                                    <small class="text-muted">Where customers are returned after completing payment.</small>
                                </div>
                            </div>
                        </div>
                    </div>
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
function toggleSecretVisibility() {
    var input = document.getElementById('paystack_secret_key');
    var eye = document.getElementById('secretEye');
    if (input.type === 'password') {
        input.type = 'text';
        eye.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        eye.className = 'bi bi-eye';
    }
}

function clearCache() {
    if (confirm('Clear all caches?')) {
        fetch('<?= h(url('admin/actions/clear_cache.php')) ?>', { method: 'POST' })
            .then(r => r.json())
            .then(d => alert(d.message || 'Cache cleared'));
    }
}
</script>