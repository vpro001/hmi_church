<?php
/**
 * Database connection using MySQLi
 * Healing My-Marriage International
 */

class Database {
    private static ?mysqli $conn = null;
    
    private function __construct() {}
    
    public static function getConnection(): mysqli {
        if (self::$conn === null) {
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $db   = 'hmi_website';
            $socket = '/opt/lampp/var/mysql/mysql.sock';
            
            self::$conn = new mysqli($host, $user, $pass, $db, 3306, $socket);
            
            if (self::$conn->connect_error) {
                error_log('MySQLi Connection Error: ' . self::$conn->connect_error);
                die('Database connection failed. Please check configuration.');
            }
            
            self::$conn->set_charset('utf8mb4');
            self::$conn->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
            // self::$conn->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
        }
        return self::$conn;
    }
    
    public static function close(): void {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }
    
    // Prepared statement helper
    public static function query(string $sql, array $params = []): mysqli_result|bool {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        if ($params) {
            $types = self::getTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public static function execute(string $sql, array $params = []): bool {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        if ($params) {
            $types = self::getTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }
    
    public static function insert(string $sql, array $params = []): int|false {
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);
        if ($params) {
            $types = self::getTypes($params);
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->insert_id;
    }
    
    public static function fetchOne(string $sql, array $params = []): ?array {
        $res = self::query($sql, $params);
        return $res ? $res->fetch_assoc() : null;
    }
    
    public static function fetchAll(string $sql, array $params = []): array {
        $res = self::query($sql, $params);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private static function getTypes(array $params): string {
        $types = '';
        foreach ($params as $v) {
            if (is_int($v)) $types .= 'i';
            elseif (is_float($v)) $types .= 'd';
            elseif (is_bool($v)) $types .= 'i';
            else $types .= 's';
        }
        return $types;
    }
}

/**
 * Initialize database tables if they don't exist
 */
function init_database(): void {
    $conn = Database::getConnection();
    
    $tables = [
        // Admin users
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(150) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role ENUM('superadmin','admin','editor') DEFAULT 'admin',
            last_login DATETIME NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Regular users (customers)
        "CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(150) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(30) DEFAULT '',
            address TEXT DEFAULT '',
            is_verified TINYINT(1) DEFAULT 0,
            verification_token VARCHAR(64) DEFAULT NULL,
            reset_token VARCHAR(64) DEFAULT NULL,
            reset_token_expires DATETIME DEFAULT NULL,
            last_login DATETIME DEFAULT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_verification (verification_token),
            INDEX idx_reset (reset_token)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Product categories
        "CREATE TABLE IF NOT EXISTS categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT DEFAULT '',
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Products (e-books)
        "CREATE TABLE IF NOT EXISTS products (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            category_id INT UNSIGNED DEFAULT NULL,
            slug VARCHAR(150) NOT NULL UNIQUE,
            title VARCHAR(200) NOT NULL,
            description TEXT DEFAULT '',
            short_description TEXT DEFAULT '',
            price INT UNSIGNED NOT NULL DEFAULT 0,
            sale_price INT UNSIGNED DEFAULT NULL,
            cover_image VARCHAR(255) DEFAULT '',
            gallery_images JSON DEFAULT NULL,
            format VARCHAR(50) DEFAULT 'E-book (digital)',
            is_featured TINYINT(1) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            sort_order INT DEFAULT 0,
            meta_title VARCHAR(200) DEFAULT '',
            meta_description VARCHAR(300) DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            INDEX idx_slug (slug),
            INDEX idx_active (is_active),
            INDEX idx_featured (is_featured),
            INDEX idx_category (category_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Orders
        "CREATE TABLE IF NOT EXISTS orders (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED DEFAULT NULL,
            order_ref VARCHAR(30) NOT NULL UNIQUE,
            status ENUM('pending','paid','processing','completed','cancelled','refunded') DEFAULT 'pending',
            payment_method VARCHAR(50) DEFAULT '',
            payment_reference VARCHAR(100) DEFAULT '',
            subtotal INT UNSIGNED NOT NULL DEFAULT 0,
            discount INT UNSIGNED DEFAULT 0,
            total INT UNSIGNED NOT NULL DEFAULT 0,
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(150) NOT NULL,
            customer_phone VARCHAR(30) NOT NULL,
            customer_address TEXT DEFAULT '',
            notes TEXT DEFAULT '',
            admin_notes TEXT DEFAULT '',
            ip_address VARCHAR(45) DEFAULT '',
            user_agent TEXT DEFAULT '',
            paid_at DATETIME DEFAULT NULL,
            completed_at DATETIME DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_ref (order_ref),
            INDEX idx_user (user_id),
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Order items
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED DEFAULT NULL,
            product_title VARCHAR(200) NOT NULL,
            product_slug VARCHAR(150) NOT NULL,
            price INT UNSIGNED NOT NULL,
            qty TINYINT UNSIGNED NOT NULL DEFAULT 1,
            subtotal INT UNSIGNED NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
            INDEX idx_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Contact/Counseling submissions
        "CREATE TABLE IF NOT EXISTS submissions (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            type ENUM('contact','counseling','newsletter','order') NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            phone VARCHAR(30) DEFAULT '',
            subject VARCHAR(200) DEFAULT '',
            message TEXT DEFAULT '',
            metadata JSON DEFAULT NULL,
            ip_hash VARCHAR(32) DEFAULT '',
            is_read TINYINT(1) DEFAULT 0,
            is_replied TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_type (type),
            INDEX idx_email (email),
            INDEX idx_read (is_read),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Site settings
        "CREATE TABLE IF NOT EXISTS site_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value LONGTEXT DEFAULT NULL,
            setting_type ENUM('string','text','json','int','bool','image') DEFAULT 'string',
            description TEXT DEFAULT '',
            is_public TINYINT(1) DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_key (setting_key),
            INDEX idx_public (is_public)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Pages content (for CMS)
        "CREATE TABLE IF NOT EXISTS pages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(150) NOT NULL UNIQUE,
            title VARCHAR(200) NOT NULL,
            content LONGTEXT DEFAULT '',
            excerpt TEXT DEFAULT '',
            meta_title VARCHAR(200) DEFAULT '',
            meta_description VARCHAR(300) DEFAULT '',
            featured_image VARCHAR(255) DEFAULT '',
            template VARCHAR(50) DEFAULT 'default',
            is_active TINYINT(1) DEFAULT 1,
            show_in_nav TINYINT(1) DEFAULT 0,
            nav_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Blog posts
        "CREATE TABLE IF NOT EXISTS posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(150) NOT NULL UNIQUE,
            title VARCHAR(200) NOT NULL,
            excerpt TEXT DEFAULT '',
            content LONGTEXT DEFAULT '',
            featured_image VARCHAR(255) DEFAULT '',
            category VARCHAR(100) DEFAULT '',
            author_id INT UNSIGNED DEFAULT NULL,
            status ENUM('draft','published','archived') DEFAULT 'draft',
            published_at DATETIME DEFAULT NULL,
            meta_title VARCHAR(200) DEFAULT '',
            meta_description VARCHAR(300) DEFAULT '',
            view_count INT UNSIGNED DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL,
            INDEX idx_slug (slug),
            INDEX idx_status (status),
            INDEX idx_published (published_at),
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Events
        "CREATE TABLE IF NOT EXISTS events (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(150) NOT NULL UNIQUE,
            title VARCHAR(200) NOT NULL,
            summary TEXT DEFAULT '',
            description LONGTEXT DEFAULT '',
            featured_image VARCHAR(255) DEFAULT '',
            category VARCHAR(100) DEFAULT '',
            location VARCHAR(200) DEFAULT '',
            start_datetime DATETIME NOT NULL,
            end_datetime DATETIME DEFAULT NULL,
            registration_required TINYINT(1) DEFAULT 0,
            registration_link VARCHAR(255) DEFAULT '',
            max_attendees INT UNSIGNED DEFAULT NULL,
            current_attendees INT UNSIGNED DEFAULT 0,
            status ENUM('draft','published','cancelled','completed') DEFAULT 'draft',
            meta_title VARCHAR(200) DEFAULT '',
            meta_description VARCHAR(300) DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_slug (slug),
            INDEX idx_status (status),
            INDEX idx_start (start_datetime),
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Activity log
        "CREATE TABLE IF NOT EXISTS activity_log (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED DEFAULT NULL,
            user_type ENUM('admin','customer') DEFAULT 'admin',
            action VARCHAR(100) NOT NULL,
            description TEXT DEFAULT '',
            ip_address VARCHAR(45) DEFAULT '',
            user_agent TEXT DEFAULT '',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE SET NULL,
            INDEX idx_user (user_id, user_type),
            INDEX idx_action (action),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    foreach ($tables as $sql) {
        try {
            $conn->query($sql);
        } catch (mysqli_sql_exception $e) {
            error_log('Table creation error: ' . $e->getMessage());
        }
    }
}

/**
 * Get a site setting value
 */
function get_setting(string $key, $default = null) {
    $row = Database::fetchOne("SELECT setting_value, setting_type FROM site_settings WHERE setting_key = ?", [$key]);
    if (!$row) return $default;
    
    $val = $row['setting_value'];
    switch ($row['setting_type']) {
        case 'json': return json_decode($val, true) ?? $default;
        case 'int': return (int)$val;
        case 'bool': return (bool)$val;
        default: return $val;
    }
}

/**
 * Set a site setting
 */
function set_setting(string $key, $value, string $type = 'string', string $description = '', bool $isPublic = false): bool {
    $jsonVal = in_array($type, ['json']) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string)$value;
    return Database::execute(
        "INSERT INTO site_settings (setting_key, setting_value, setting_type, description, is_public) VALUES (?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_type = VALUES(setting_type), description = VALUES(description), is_public = VALUES(is_public)",
        [$key, $jsonVal, $type, $description, $isPublic ? 1 : 0]
    );
}

/**
 * Get all public settings for frontend
 */
function get_public_settings(): array {
    $rows = Database::fetchAll("SELECT setting_key, setting_value, setting_type FROM site_settings WHERE is_public = 1");
    $settings = [];
    foreach ($rows as $row) {
        $val = $row['setting_value'];
        switch ($row['setting_type']) {
            case 'json': $settings[$row['setting_key']] = json_decode($val, true) ?? []; break;
            case 'int': $settings[$row['setting_key']] = (int)$val; break;
            case 'bool': $settings[$row['setting_key']] = (bool)$val; break;
            default: $settings[$row['setting_key']] = $val;
        }
    }
    return $settings;
}