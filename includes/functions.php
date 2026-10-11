<?php
require_once __DIR__ . '/config.php';

/* ---------- Output helpers ---------- */

function h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_url(): string
{
    if (SITE['base_url'] !== '') {
        return rtrim(SITE['base_url'], '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    // actions/ lives one level down
    $dir    = preg_replace('#/actions$#', '', $dir);
    return $scheme . '://' . $host . $dir;
}

/** Root-relative path helper that works from the site root and from actions/. */
function url(string $path = ''): string
{
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $dir = preg_replace('#/actions$#', '', $dir);
    return $dir . '/' . ltrim($path, '/');
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

/* ---------- Content (JSON) ---------- */

function load_json(string $name): array
{
    $file = __DIR__ . '/../data/' . $name . '.json';
    if (!is_file($file)) {
        return [];
    }
    $data = json_decode((string)file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function get_posts(): array
{
    $posts = load_json('posts');
    usort($posts, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
    return $posts;
}

function get_events(bool $upcomingOnly = false): array
{
    $events = load_json('events');
    usort($events, fn($a, $b) => strcmp($a['start'] ?? '', $b['start'] ?? ''));
    if ($upcomingOnly) {
        $now = date('Y-m-d');
        $events = array_values(array_filter($events, fn($e) => substr($e['start'] ?? '', 0, 10) >= $now));
    }
    return $events;
}

function get_products(): array
{
    return load_json('products');
}

function money(int|float $n): string
{
    return '₦' . number_format($n, 0, '.', ',');
}

function fmt_date(string $iso, string $format = 'j F Y'): string
{
    $t = strtotime($iso);
    return $t ? date($format, $t) : '';
}

/* ---------- Forms: CSRF, validation, storage, mail ---------- */

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
        return true; // honeypot filled
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

function save_submission(string $type, array $data): bool
{
    $dir = __DIR__ . '/../data/submissions';
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        return false;
    }
    $row = ['time' => date('c'), 'type' => $type, 'ip_hash' => substr(hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . 'hmi'), 0, 16)] + $data;
    $line = json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    return file_put_contents($dir . '/' . $type . '.jsonl', $line, FILE_APPEND | LOCK_EX) !== false;
}

function send_notification(string $subject, string $body, string $replyTo = ''): void
{
    if (!SITE['mail_enabled']) {
        return;
    }
    $headers = [
        'From: ' . SITE['short_name'] . ' <' . SITE['mail_from'] . '>',
        'Content-Type: text/plain; charset=UTF-8',
    ];
    if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    @mail(SITE['mail_to'], '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
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
