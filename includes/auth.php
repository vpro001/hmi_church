<?php
/**
 * Authentication and session management
 * Healing My-Marriage International
 */

require_once __DIR__ . '/functions.php';

/* ---------- Admin Authentication ---------- */

function admin_login(string $username, string $password): array|false
{
    $admin = Database::fetchOne("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1", [$username, $username]);
    if (!$admin || $password !== $admin['password_hash']) {
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

function admin_logout(): void
{
    if (!empty($_SESSION['admin_id'])) {
        log_activity($_SESSION['admin_id'], 'admin', 'logout', 'Admin logged out');
    }
    session_destroy();
    session_start();
}

function admin_check(): bool
{
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_role']);
}

function admin_require_role(string ...$roles): void
{
    if (!admin_check() || !in_array($_SESSION['admin_role'], $roles)) {
        redirect('admin/login.php');
    }
}

function admin_user(): array|false
{
    if (!admin_check()) return false;
    return Database::fetchOne("SELECT id, username, email, full_name, role, last_login FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]);
}

/* ---------- Customer Authentication ---------- */

function customer_login(string $email, string $password): array|false
{
    $user = Database::fetchOne("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
    if (!$user || $password !== $user['password_hash']) {
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

function customer_register(array $data): array|false
{
    // Check if email exists
    if (Database::fetchOne("SELECT id FROM users WHERE email = ?", [$data['email']])) {
        return ['error' => 'exists', 'message' => 'An account with this email already exists.'];
    }
    
    $token = bin2hex(random_bytes(32));
    
    $id = Database::insert(
        "INSERT INTO users (email, password_hash, full_name, phone, address, verification_token) VALUES (?, ?, ?, ?, ?, ?)",
        [$data['email'], $data['password'], $data['name'], $data['phone'] ?? '', $data['address'] ?? '', $token]
    );
    
    if (!$id) return false;
    
    // Auto-verify for now (since mail not configured)
    Database::execute("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?", [$id]);
    
    log_activity($id, 'customer', 'register', 'Customer registered');
    
    return ['id' => $id, 'email' => $data['email'], 'name' => $data['name']];
}

function customer_logout(): void
{
    if (!empty($_SESSION['customer_id'])) {
        log_activity($_SESSION['customer_id'], 'customer', 'logout', 'Customer logged out');
    }
    session_destroy();
    session_start();
}

function customer_check(): bool
{
    return !empty($_SESSION['customer_id']);
}

function customer_user(): array|false
{
    if (!customer_check()) return false;
    return Database::fetchOne("SELECT id, email, full_name, phone, address, is_verified, created_at FROM users WHERE id = ?", [$_SESSION['customer_id']]);
}

function customer_require_login(): void
{
    if (!customer_check()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
        redirect('login.php');
    }
}

/* ---------- Password Reset ---------- */

function request_password_reset(string $email): bool
{
    $user = Database::fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
    if (!$user) return true; // Don't reveal if email exists
    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    Database::execute("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?", [$token, $expires, $user['id']]);
    
    // send_reset_email($email, $token);
    
    return true;
}

function reset_password(string $token, string $password): array|false
{
    $user = Database::fetchOne("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()", [$token]);
    if (!$user) return ['error' => 'invalid', 'message' => 'Invalid or expired reset token.'];
    
    Database::execute("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?", [$password, $user['id']]);
    
    return ['success' => true, 'message' => 'Password has been reset. You can now log in.'];
}

/* ---------- Activity Logging ---------- */

function log_activity(int $userId, string $userType, string $action, string $description = ''): void
{
    Database::execute(
        "INSERT INTO activity_log (user_id, user_type, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)",
        [$userId, $userType, $action, $description, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '']
    );
}