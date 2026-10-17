<?php
/**
 * Admin stats API endpoint
 */

require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

try {
    $stats = [
        'products' => (int)(Database::fetchOne("SELECT COUNT(*) as c FROM products WHERE is_active = 1")['c'] ?? 0),
        'pendingOrders' => (int)(Database::fetchOne("SELECT COUNT(*) as c FROM orders WHERE status = 'pending'")['c'] ?? 0),
        'unreadSubmissions' => (int)(Database::fetchOne("SELECT COUNT(*) as c FROM submissions WHERE is_read = 0")['c'] ?? 0),
        'isLive' => (int)(Database::fetchOne("SELECT COUNT(*) as c FROM livestreams WHERE status = 'live' AND is_active = 1")['c'] ?? 0),
    ];
    echo json_encode($stats);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}