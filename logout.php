<?php
/**
 * logout.php
 * -------------------------------------------------------------
 * Memproses Logout Pengguna.
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/includes/auth.php';

// Hapus semua data session
$_SESSION = [];

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Mulai session baru hanya untuk set flash message
session_start();
$_SESSION['flash_message'] = [
    'type' => 'success',
    'text' => 'Anda telah berhasil logout.'
];

// Redirect ke halaman depan
header("Location: /galeri-desain-grafis/");
exit;
