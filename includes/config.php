<?php
/**
 * Healing My-Marriage International Ministry, Inc.
 * Site configuration. Edit values here; nothing else needs touching for basics.
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

const SITE = [
    'name'        => 'Healing My-Marriage International Ministry, Inc.',
    'short_name'  => 'Healing My-Marriage',
    'abbr'        => 'HMI',
    'tagline'     => 'Reposition. Refix. Rekindle.',
    'description' => 'A faith-based marriage and family ministry in Rivers State, Nigeria. Biblical counseling, marriage enrichment, family support and leadership for ministers.',

    // Set to your live domain with no trailing slash, e.g. https://healingmymarriage.org
    // Leave '' to auto-detect from the current request.
    'base_url'    => '',

    // Contact details taken from the current website. Confirm before launch.
    'email'       => 'info@healingmymarriage.org',
    'phones'      => ['+234 704 222 6633', '+234 818 179 8886'],
    'phones_raw'  => ['+2347042226633', '+2348181798886'],
    'address'     => 'Mercyland Central / Diamond Estate, Rivers State, Nigeria',
    'hours'       => 'Monday to Saturday, 8:00 am to 5:00 pm',   // CONFIRM: old site lists two different schedules
    'maps_query'  => 'Mercyland Central Diamond Estate Rivers State Nigeria',

    'facebook'    => 'https://www.facebook.com/profile.php?id=61565801562860',
    'youtube'     => 'https://youtube.com/@healingmymarriageinternational',

    // Measurement. Paste a GA4 ID such as G-XXXXXXXXXX to switch analytics on.
    'ga_id'       => '',

    // Form delivery. Submissions are always saved to data/submissions/.
    // Set mail_enabled to true once the server can send mail.
    'mail_enabled' => false,
    'mail_to'      => 'info@healingmymarriage.org',
    'mail_from'    => 'no-reply@healingmymarriage.org',

    'built_by'    => '',
];

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
