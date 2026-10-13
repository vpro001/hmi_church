<?php
/**
 * Shared functions - Database-backed version
 * Healing My-Marriage International
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/* ---------- Output helpers ---------- */

function h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(): string
{
    $settings = get_public_settings();
    if (!empty($settings['site_base_url'])) {
        return rtrim($settings['site_base_url'], '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Get the site root by removing /admin, /actions, etc. from the script path
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    // Strip trailing /admin and /actions segments (handles /admin/actions too)
    while (in_array(basename($dir), ['admin', 'actions'])) {
        $dir = dirname($dir);
    }
    return $scheme . '://' . $host . $dir;
}

function url(string $path = ''): string
{
    // Use the base_url function which properly handles admin/actions context
    $base = base_url();
    return $base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    $file = __DIR__ . '/../assets/' . ltrim($path, '/');
    $v    = is_file($file) ? filemtime($file) : time();
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function tel_link(string $raw): string
{
    return 'tel:' . preg_replace('/[^+\d]/', '', $raw);
}

function money(int $n): string
{
    $symbol = get_setting('currency_symbol', '₦');
    return $symbol . number_format($n, 0, '.', ',');
}

function fmt_date(string $iso, string $format = 'j F Y'): string
{
    $t = strtotime($iso);
    return $t ? date($format, $t) : '';
}

/* ---------- Database Content Functions ---------- */

function get_products(): array
{
    return Database::fetchAll(
        "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.sort_order, p.title"
    );
}

function get_product_by_slug(string $slug): ?array
{
    return Database::fetchOne(
        "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.is_active = 1",
        [$slug]
    );
}

function get_product_by_id(int $id): ?array
{
    return Database::fetchOne(
        "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.is_active = 1",
        [$id]
    );
}

function get_featured_products(int $limit = 4): array
{
    return Database::fetchAll(
        "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.sort_order LIMIT ?",
        [$limit]
    );
}

function get_categories(): array
{
    return Database::fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name");
}

function get_posts(bool $publishedOnly = true): array
{
    $sql = "SELECT * FROM posts";
    $params = [];
    if ($publishedOnly) {
        $sql .= " WHERE status = 'published' AND (published_at IS NULL OR published_at <= NOW())";
    }
    $sql .= " ORDER BY published_at DESC, created_at DESC";
    return Database::fetchAll($sql, $params);
}

function get_post_by_slug(string $slug): ?array
{
    return Database::fetchOne(
        "SELECT * FROM posts WHERE slug = ? AND status = 'published' AND (published_at IS NULL OR published_at <= NOW())",
        [$slug]
    );
}

function get_recent_posts(int $limit = 3, int $excludeId = 0): array
{
    $sql = "SELECT * FROM posts WHERE status = 'published' AND (published_at IS NULL OR published_at <= NOW())";
    $params = [];
    if ($excludeId) {
        $sql .= " AND id != ?";
        $params[] = $excludeId;
    }
    $sql .= " ORDER BY published_at DESC LIMIT ?";
    $params[] = $limit;
    return Database::fetchAll($sql, $params);
}

function get_events(bool $upcomingOnly = false): array
{
    $sql = "SELECT * FROM events WHERE status = 'published'";
    $params = [];
    if ($upcomingOnly) {
        $sql .= " AND start_datetime > NOW()";
    }
    $sql .= " ORDER BY start_datetime ASC";
    return Database::fetchAll($sql, $params);
}

function get_upcoming_events(int $limit = 3): array
{
    return Database::fetchAll(
        "SELECT * FROM events WHERE status = 'published' AND start_datetime > NOW() ORDER BY start_datetime ASC LIMIT ?",
        [$limit]
    );
}

function get_event_by_slug(string $slug): ?array
{
    return Database::fetchOne(
        "SELECT * FROM events WHERE slug = ? AND status = 'published'",
        [$slug]
    );
}

function get_page_by_slug(string $slug): ?array
{
    return Database::fetchOne(
        "SELECT * FROM pages WHERE slug = ? AND is_active = 1",
        [$slug]
    );
}

function get_nav_pages(): array
{
    return Database::fetchAll(
        "SELECT * FROM pages WHERE is_active = 1 AND show_in_nav = 1 ORDER BY nav_order, title"
    );
}

/* ---------- CSRF & Forms ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">'
         . '<div class="hp-field" aria-hidden="true"><label for="website_url">Leave this empty</label>'
         . '<input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off"></div>';
}

function csrf_valid(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

function clean_text(?string $v, int $max = 500): string
{
    $v = trim((string)$v);
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v);
    return mb_substr($v, 0, $max);
}

function clean_line(?string $v, int $max = 150): string
{
    return preg_replace('/[\r\n]+/', ' ', clean_text($v, $max));
}

function too_fast_or_spam(): bool
{
    if (!empty($_POST['website_url'])) {
        return true;
    }
    $last = $_SESSION['last_submit'] ?? 0;
    return (time() - $last) < 8;
}

function flash(string $type, string $message, array $old = [], array $errors = []): void
{
    $_SESSION['flash'] = compact('type', 'message', 'old', 'errors');
}

function take_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function old(?array $flash = null, string $key = '', string $default = ''): string
{
    return h($flash['old'][$key] ?? $default);
}

function field_invalid(?array $flash, string $key): string
{
    return !empty($flash['errors'][$key]) ? ' is-invalid' : '';
}

function field_error(?array $flash, string $key): string
{
    $msg = $flash['errors'][$key] ?? '';
    return $msg !== '' ? '<div class="invalid-feedback">' . h($msg) . '</div>' : '';
}

/* ---------- Submissions (Database) ---------- */

function save_submission(string $type, array $data): bool
{
    $ipHash = substr(hash('sha256', (($_SERVER['REMOTE_ADDR'] ?? '') . 'hmi')), 0, 16);
    return Database::execute(
        "INSERT INTO submissions (type, name, email, phone, subject, message, metadata, ip_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $type,
            $data['name'] ?? '',
            $data['email'] ?? '',
            $data['phone'] ?? '',
            $data['subject'] ?? '',
            $data['message'] ?? '',
            isset($data['metadata']) ? json_encode($data['metadata']) : null,
            $ipHash
        ]
    );
}

function send_notification(string $subject, string $body, string $replyTo = ''): void
{
    $settings = get_public_settings();
    if (empty($settings['mail_enabled'])) return;
    
    $headers = [
        'From: ' . ($settings['site_short_name'] ?? 'HMI') . ' <' . ($settings['mail_from'] ?? 'no-reply@healingmymarriage.org') . '>',
        'Content-Type: text/plain; charset=UTF-8',
    ];
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    @mail($settings['mail_to'] ?? 'info@healingmymarriage.org', '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
}

function redirect(string $path, int $code = 303): never
{
    header('Location: ' . url($path), true, $code);
    exit;
}

function redirect_back(string $fallback, string $anchor = ''): never
{
    $to = $fallback;
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($ref !== '' && parse_url($ref, PHP_URL_HOST) === parse_url('//' . $host, PHP_URL_HOST)) {
        $to = strtok($ref, '#');
    }
    header('Location: ' . $to . $anchor, true, 303);
    exit;
}

/* ---------- Order Functions ---------- */

function create_order(array $data): int|false
{
    $ref = 'HMI-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    
    $orderId = Database::insert(
        "INSERT INTO orders (order_ref, user_id, status, customer_name, customer_email, customer_phone, customer_address, subtotal, discount, total, notes, ip_address, user_agent) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        [
            $ref,
            $data['user_id'] ?? null,
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address'] ?? '',
            $data['subtotal'],
            $data['discount'] ?? 0,
            $data['total'],
            $data['notes'] ?? '',
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]
    );
    
    if ($orderId && !empty($data['items'])) {
        foreach ($data['items'] as $item) {
            Database::execute(
                "INSERT INTO order_items (order_id, product_id, product_title, product_slug, price, qty, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$orderId, $item['product_id'] ?? null, $item['title'], $item['slug'], $item['price'], $item['qty'], $item['subtotal']]
            );
        }
    }
    
    return $orderId ? ['id' => $orderId, 'ref' => $ref] : false;
}

function get_order_by_ref(string $ref): ?array
{
    return Database::fetchOne("SELECT * FROM orders WHERE order_ref = ?", [$ref]);
}

function get_user_orders(int $userId): array
{
    return Database::fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
}

/* ---------- Auth Helpers ---------- */

function is_customer(): bool
{
    return !empty($_SESSION['customer_id']);
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

/* ---------- Legacy JSON fallback (for config.php constants) ---------- */

function load_json(string $name): array
{
    $file = __DIR__ . '/../data/' . $name . '.json';
    if (!is_file($file)) return [];
    $data = json_decode((string)file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

// Backwards compatibility - use DB versions
function get_products_json(): array { return get_products(); }
function get_posts_json(): array { return get_posts(); }
function get_events_json(): array { return get_events(); }