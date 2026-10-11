<?php
/**
 * Healing My-Marriage International Ministry, Inc.
 * Site configuration - loads from database with defaults
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

date_default_timezone_set('Africa/Lagos');

require_once __DIR__ . '/db.php';

// Load settings from database (with defaults as fallback)
function get_site_config(): array {
    static $config = null;
    if ($config !== null) return $config;
    
    $defaults = [
        'site_name' => 'Healing My-Marriage International Ministry, Inc.',
        'site_short_name' => 'Healing My-Marriage',
        'site_tagline' => 'Reposition. Refix. Rekindle.',
        'site_description' => 'A faith-based marriage and family ministry in Rivers State, Nigeria. Biblical counseling, marriage enrichment, family support and leadership for ministers.',
        'site_base_url' => '',
        'site_email' => 'info@healingmymarriage.org',
        'site_phones' => ['+234 704 222 6633', '+234 818 179 8886'],
        'site_phones_raw' => ['+2347042226633', '+2348181798886'],
        'site_address' => 'Mercyland Central / Diamond Estate, Rivers State, Nigeria',
        'site_hours' => 'Monday to Saturday, 8:00 am to 5:00 pm',
        'site_maps_query' => 'Mercyland Central Diamond Estate Rivers State Nigeria',
        'facebook_url' => 'https://www.facebook.com/profile.php?id=61565801562860',
        'youtube_url' => 'https://youtube.com/@healingmymarriageinternational',
        'ga_id' => '',
        'mail_enabled' => false,
        'mail_to' => 'info@healingmymarriage.org',
        'mail_from' => 'no-reply@healingmymarriage.org',
        'currency_symbol' => '₦',
        'currency_code' => 'NGN',
    ];
    
    try {
        $dbSettings = get_public_settings();
        foreach ($dbSettings as $key => $value) {
            if (array_key_exists($key, $defaults)) {
                $defaults[$key] = $value;
            }
        }
    } catch (Exception $e) {
        // DB not ready, use defaults
    }
    
    $config = $defaults;
    return $config;
}

$siteConfig = get_site_config();

const SITE = [
    'name'        => 'Healing My-Marriage International Ministry, Inc.',
    'short_name'  => 'Healing My-Marriage',
    'abbr'        => 'HMI',
    'tagline'     => 'Reposition. Refix. Rekindle.',
    'description' => 'A faith-based marriage and family ministry in Rivers State, Nigeria. Biblical counseling, marriage enrichment, family support and leadership for ministers.',
    'base_url'    => '',
    'email'       => 'info@healingmymarriage.org',
    'phones'      => ['+234 704 222 6633', '+234 818 179 8886'],
    'phones_raw'  => ['+2347042226633', '+2348181798886'],
    'address'     => 'Mercyland Central / Diamond Estate, Rivers State, Nigeria',
    'hours'       => 'Monday to Saturday, 8:00 am to 5:00 pm',
    'maps_query'  => 'Mercyland Central Diamond Estate Rivers State Nigeria',
    'facebook'    => 'https://www.facebook.com/profile.php?id=61565801562860',
    'youtube'     => 'https://youtube.com/@healingmymarriageinternational',
    'ga_id'       => '',
    'mail_enabled' => false,
    'mail_to'      => 'info@healingmymarriage.org',
    'mail_from'    => 'no-reply@healingmymarriage.org',
    'built_by'    => '',
];

// Override constants with database values where available
if ($siteConfig) {
    // We can't override constants, so functions will use get_setting() instead
}

/** Primary navigation. 'key' is matched against $page_key set on each page. */
const NAV = [
    ['key' => 'home',       'label' => 'Home',       'href' => 'index.php'],
    ['key' => 'about',      'label' => 'About',      'href' => 'about.php'],
    ['key' => 'counseling', 'label' => 'Counseling', 'href' => 'counseling.php'],
    ['key' => 'programs',   'label' => 'Programs',   'href' => 'programs.php'],
    ['key' => 'events',     'label' => 'Events',     'href' => 'events.php'],
    ['key' => 'resources',  'label' => 'News & Updates', 'href' => 'resources.php'],
    ['key' => 'shop',       'label' => 'Shop',       'href' => 'shop.php'],
    ['key' => 'contact',    'label' => 'Contact',    'href' => 'contact.php'],
];

// Helper functions for templates to use database settings
function site_setting(string $key, $default = null) {
    $settings = get_site_config();
    return $settings[$key] ?? $default;
}

function site_phones(): array { return site_setting('site_phones', ['+234 704 222 6633', '+234 818 179 8886']); }
function site_phones_raw(): array { return site_setting('site_phones_raw', ['+2347042226633', '+2348181798886']); }
function site_email(): string { return site_setting('site_email', 'info@healingmymarriage.org'); }
function site_address(): string { return site_setting('site_address', 'Mercyland Central / Diamond Estate, Rivers State, Nigeria'); }
function site_hours(): string { return site_setting('site_hours', 'Monday to Saturday, 8:00 am to 5:00 pm'); }
function facebook_url(): string { return site_setting('facebook_url', 'https://www.facebook.com/profile.php?id=61565801562860'); }
function youtube_url(): string { return site_setting('youtube_url', 'https://youtube.com/@healingmymarriageinternational'); }
function ga_id(): string { return site_setting('ga_id', ''); }
function mail_enabled(): bool { return (bool)site_setting('mail_enabled', false); }
function mail_to(): string { return site_setting('mail_to', 'info@healingmymarriage.org'); }
function mail_from(): string { return site_setting('mail_from', 'no-reply@healingmymarriage.org'); }
function currency_symbol(): string { return site_setting('currency_symbol', '₦'); }
function currency_code(): string { return site_setting('currency_code', 'NGN'); }