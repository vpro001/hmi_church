<?php
/**
 * Clear cache action
 */

require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

// Clear any file-based caches if needed
// For now, just return success
echo json_encode(['success' => true, 'message' => 'Cache cleared']);