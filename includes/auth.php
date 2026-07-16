<?php
/**
 * includes/auth.php
 * -------------------------------------------------------------
 * Manajemen session, hak akses role, dan proteksi CSRF.
 * -------------------------------------------------------------
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah pengguna sudah login.
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek apakah pengguna login sebagai Admin.
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Paksa pengguna untuk login (halaman User).
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'text' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'
        ];
        header("Location: " . getBaseUrl() . "login.php");
        exit;
    }
}

/**
 * Paksa pengguna untuk login sebagai Admin (halaman Admin).
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'text' => 'Akses ditolak! Halaman ini hanya untuk Administrator.'
        ];
        header("Location: " . getBaseUrl() . "login.php");
        exit;
    }
}

/**
 * Dapatkan URL dasar aplikasi secara dinamis.
 * @return string
 */
function getBaseUrl() {
    // Jalankan secara lokal di subfolder galeri-desain-grafis
    return "/galeri-desain-grafis/";
}

/**
 * Generate CSRF Token untuk form keamanan.
 * @return string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF Token dari input form.
 * @param string $token
 * @return bool
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}
