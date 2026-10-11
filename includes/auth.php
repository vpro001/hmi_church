<?php
/**
 * Authentication and session management
 * Healing My-Marriage International
 */

require_once __DIR__ . '/db.php';

/* ---------- Admin Authentication ---------- */

function admin_login(string $username, string $password): array|false {
    $admin = Database::fetchOne("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1", [$username, $username]);
    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }
    
    // Update last login
    Database::execute("UPDATE admin_users SET last_login = NOW() WHERE id = ?", [$admin['id']]);
    
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_role'] = $admin['role'];
    $_SESSION['admin_name'] = $admin['full_name'];
    
    log_activity($admin['id'], 'admin', 'login', 'Admin logged in');
    
    return $admin;
}

function admin_logout(): void {
    if (!empty($_SESSION['admin_id'])) {
        log_activity($_SESSION['admin_id'], 'admin', 'logout', 'Admin logged out');
    }
    session_destroy();
    session_start();
}

function admin_check(): bool {
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_role']);
}

function admin_require_role(string ...$roles): void {
    if (!admin_check() || !in_array($_SESSION['admin_role'], $roles)) {
        header('Location: ' . url('admin/login.php'), true, 303);
        exit;
    }
}

function admin_user(): array|false {
    if (!admin_check()) return false;
    return Database::fetchOne("SELECT id, username, email, full_name, role, last_login FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]);
}

/* ---------- Customer Authentication ---------- */

function customer_login(string $email, string $password): array|false {
    $user = Database::fetchOne("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    
    if (!$user['is_verified']) {
        return ['error' => 'unverified', 'message' => 'Please verify your email address before logging in.'];
    }
    
    Database::execute("UPDATE users SET last_login = NOW() WHERE id = ?", [$user['id']]);
    
    $_SESSION['customer_id'] = $user['id'];
    $_SESSION['customer_email'] = $user['email'];
    $_SESSION['customer_name'] = $user['full_name'];
    
    log_activity($user['id'], 'customer', 'login', 'Customer logged in');
    
    return $user;
}

function customer_register(array $data): array|false {
    // Check if email exists
    if (Database::fetchOne("SELECT id FROM users WHERE email = ?", [$data['email']])) {
        return ['error' => 'exists', 'message' => 'An account with this email already exists.'];
    }
    
    $hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    
    $id = Database::insert(
        "INSERT INTO users (email, password_hash, full_name, phone, address, verification_token) VALUES (?, ?, ?, ?, ?, ?)",
        [$data['email'], $hash, $data['name'], $data['phone'] ?? '', $data['address'] ?? '', $token]
    );
    
    if (!$id) return false;
    
    // Send verification email (implement when mail is enabled)
    // send_verification_email($data['email'], $token);
    
    // Auto-verify for now (since mail not configured)
    Database::execute("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?", [$id]);
    
    log_activity($id, 'customer', 'register', 'Customer registered');
    
    return ['id' => $id, 'email' => $data['email'], 'name' => $data['name']];
}

function customer_logout(): void {
    if (!empty($_SESSION['customer_id'])) {
        log_activity($_SESSION['customer_id'], 'customer', 'logout', 'Customer logged out');
    }
    session_destroy();
    session_start();
}

function customer_check(): bool {
    return !empty($_SESSION['customer_id']);
}

function customer_user(): array|false {
    if (!customer_check()) return false;
    return Database::fetchOne("SELECT id, email, full_name, phone, address, is_verified, created_at FROM users WHERE id = ?", [$_SESSION['customer_id']]);
}

function customer_require_login(): void {
    if (!customer_check()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
        header('Location: ' . url('login.php'), true, 303);
        exit;
    }
}

/* ---------- Password Reset ---------- */

function request_password_reset(string $email): bool {
    $user = Database::fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    if (!$user) return true; // Don't reveal if email exists
    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    Database::execute("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?", [$token, $expires, $user['id']]);
    
    // send_reset_email($email, $token);
    
    return true;
}

function reset_password(string $token, string $password): array|false {
    $user = Database::fetchOne("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()", [$token]);
    if (!$user) return ['error' => 'invalid', 'message' => 'Invalid or expired reset token.'];
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    Database::execute("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?", [$hash, $user['id']]);
    
    return ['success' => true, 'message' => 'Password has been reset. You can now log in.'];
}

/* ---------- Activity Logging ---------- */

function log_activity(int $userId, string $userType, string $action, string $description = ''): void {
    Database::execute(
        "INSERT INTO activity_log (user_id, user_type, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)",
        [$userId, $userType, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
    );
}

/* ---------- CSRF ---------- */

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}

function csrf_valid(): bool {
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

/* ---------- Helpers ---------- */

function h(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string {
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $dir = preg_replace('#/actions$#', '', $dir);
    $dir = preg_replace('#/admin$#', '', $dir);
    return $dir . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    $file = __DIR__ . '/../assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : time();
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function base_url(): string {
    $settings = get_public_settings();
    if (!empty($settings['site_base_url'])) {
        return rtrim($settings['site_base_url'], '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $dir = preg_replace('#/(actions|admin)$#', '', $dir);
    return $scheme . '://' . $host . $dir;
}

function money(int $n): string {
    $symbol = get_setting('currency_symbol', '₦');
    return $symbol . number_format($n, 0, '.', ',');
}

function fmt_date(string $iso, string $format = 'j F Y'): string {
    $t = strtotime($iso);
    return $t ? date($format, $t) : '';
}

function redirect(string $path, int $code = 303): never {
    header('Location: ' . url($path), true, $code);
    exit;
}

function flash(string $type, string $message, array $old = [], array $errors = []): void {
    $_SESSION['flash'] = compact('type', 'message', 'old', 'errors');
}

function take_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function old(?array $flash = null, string $key = '', string $default = ''): string {
    return h($flash['old'][$key] ?? $default);
}

function is_admin(): bool {
    return admin_check();
}

function is_customer(): bool {
    return customer_check();
}