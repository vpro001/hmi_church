<?php
/**
 * Product sort order update
 */

require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$csrf = $_POST['csrf'] ?? '';
if (!hash_equals($_SESSION['csrf'] ?? '', $csrf)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$order = (int)($_POST['order'] ?? 0);

if ($id > 0) {
    Database::execute("UPDATE products SET sort_order = ? WHERE id = ?", [$order, $id]);
    echo json_encode(['success' => true]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
}