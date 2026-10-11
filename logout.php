<?php
/**
 * Customer logout
 */

require_once __DIR__ . '/../includes/auth.php';

customer_logout();
redirect('login.php');