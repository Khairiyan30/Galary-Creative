<?php
/**
 * admin/index.php
 * -------------------------------------------------------------
 * Entry point untuk folder /admin/ — redirect otomatis ke dashboard.
 * Mencegah directory listing tampil di browser.
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect ke dashboard (akan dicek auth di sana)
header("Location: " . getBaseUrl() . "admin/dashboard.php");
exit;
