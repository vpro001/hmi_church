<?php
/**
 * Admin logout action
 */

require_once __DIR__ . '/../../includes/auth.php';

admin_logout();
redirect('admin/login.php');