<?php
/**
 * Database initialization script
 * Run once to set up tables and seed initial data
 */

require_once __DIR__ . '/includes/db.php';

echo "Initializing database...\n";

init_database();

$conn = Database::getConnection();

// Check if admin exists
$admin = Database::fetchOne("SELECT id FROM admin_users WHERE username = 'admin'");
if (!$admin) {
    $password = 'admin123';
    Database::execute(
        "INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, 'superadmin')",
        ['admin', 'admin@healingmymarriage.org', $password, 'System Administrator']
    );
    echo "Created default admin user (admin / admin123)\n";
}

// Create default category
$cat = Database::fetchOne("SELECT id FROM categories WHERE slug = 'ebooks'");
if (!$cat) {
    Database::execute(
        "INSERT INTO categories (name, slug, description, sort_order) VALUES (?, ?, ?, ?)",
        ['Christian E-books', 'ebooks', 'Christian e-books on marriage, parenting and family life', 1]
    );
    echo "Created default category\n";
}

// Seed products from JSON if table empty
$count = Database::fetchOne("SELECT COUNT(*) as c FROM products");
if ($count && $count['c'] == 0) {
    $products = json_decode(file_get_contents(__DIR__ . '/data/products.json'), true);
    $catId = Database::fetchOne("SELECT id FROM categories WHERE slug = 'ebooks'")['id'];
    
    $imageMap = [
        'foundation-for-living' => 'assets/img/live/book-foundation-for-living.jpg',
        'i-want-to-marry' => 'assets/img/live/book-i-want-to-marry.jpg',
        'love-portion' => 'assets/img/live/book-love-portion.jpg',
        'the-logics-of-parenting' => 'assets/img/live/book-the-logics-of-parenting.gif',
        'the-understanding-of-marriage' => 'assets/img/live/book-the-understanding-of-marriage.jpg',
    ];
    
    foreach ($products as $i => $p) {
        Database::execute(
            "INSERT INTO products (category_id, slug, title, description, short_description, price, cover_image, format, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)",
            [
                $catId,
                $p['id'],
                $p['title'],
                $p['description'] ?: "A Christian e-book from Healing My-Marriage International Ministry.",
                $p['description'] ?: "A Christian e-book on marriage and family life.",
                $p['price'],
                $imageMap[$p['id']] ?? '',
                $p['format'],
                $i + 1
            ]
        );
    }
    echo "Seeded " . count($products) . " products\n";
}

// Seed site settings
$defaults = [
    ['site_name', 'Healing My-Marriage International Ministry, Inc.', 'string', 'Site name', 1],
    ['site_short_name', 'Healing My-Marriage', 'string', 'Short site name', 1],
    ['site_tagline', 'Reposition. Refix. Rekindle.', 'string', 'Site tagline', 1],
    ['site_email', 'info@healingmymarriage.org', 'string', 'Contact email', 1],
    ['site_phones', json_encode(['+234 704 222 6633', '+234 818 179 8886']), 'json', 'Phone numbers', 1],
    ['site_phones_raw', json_encode(['+2347042226633', '+2348181798886']), 'json', 'Raw phone numbers', 0],
    ['site_address', 'Mercyland Central / Diamond Estate, Rivers State, Nigeria', 'string', 'Physical address', 1],
    ['site_hours', 'Monday to Saturday, 8:00 am to 5:00 pm', 'string', 'Business hours', 1],
    ['facebook_url', 'https://www.facebook.com/profile.php?id=61565801562860', 'string', 'Facebook URL', 1],
    ['youtube_url', 'https://youtube.com/@healingmymarriageinternational', 'string', 'YouTube URL', 1],
    ['ga_id', '', 'string', 'Google Analytics GA4 ID', 0],
    ['mail_enabled', '0', 'bool', 'Enable email notifications', 0],
    ['mail_to', 'info@healingmymarriage.org', 'string', 'Notification recipient', 0],
    ['mail_from', 'no-reply@healingmymarriage.org', 'string', 'From email address', 0],
    ['currency_symbol', '₦', 'string', 'Currency symbol', 1],
    ['currency_code', 'NGN', 'string', 'Currency code', 1],
    ['paystack_enabled', '1', 'bool', 'Enable Paystack payment gateway', 1],
    ['paystack_mode', 'test', 'string', 'Paystack mode (test or live)', 1],
    ['paystack_public_key', '', 'string', 'Paystack Public Key (pk_test_... or pk_live_...)', 1],
    ['paystack_secret_key', '', 'string', 'Paystack Secret Key (sk_test_... or sk_live_...)', 0],
    ['paystack_test_simulator', '1', 'bool', 'Allow simulated test payment when API keys are not yet configured', 0],
];

foreach ($defaults as $d) {
    Database::execute(
        "INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_type, description, is_public) VALUES (?, ?, ?, ?, ?)",
        $d
    );
}
echo "Seeded site settings\n";

// Create sample pages
$pages = [
    ['privacy', 'Privacy Policy', 'privacy', 'Our privacy policy and terms of use.', '', '', '', 'default', 1, 0, 0],
    ['about', 'About Us', 'about', 'Learn about our ministry, vision, mission, and leadership.', '', '', '', 'default', 1, 1, 10],
    ['contact', 'Contact Us', 'contact', 'Get in touch with us for counseling, inquiries, or prayer requests.', '', '', '', 'default', 1, 1, 20],
];

foreach ($pages as $p) {
    Database::execute(
        "INSERT IGNORE INTO pages (slug, title, content, excerpt, meta_title, meta_description, featured_image, template, is_active, show_in_nav, nav_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
        $p
    );
}
echo "Seeded pages\n";

echo "Database initialization complete!\n";