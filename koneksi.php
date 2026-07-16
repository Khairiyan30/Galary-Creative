<?php
/**
 * koneksi.php
 * -------------------------------------------------------------
 * File koneksi database untuk lingkungan lokal XAMPP.
 * Menggunakan PDO agar mendukung prepared statement (aman dari SQL Injection).
 *
 * Asumsi default XAMPP:
 * - Host   : localhost
 * - User   : root
 * - Pass   : (kosong)
 * - DB     : galeri_kreatif (lihat database.sql)
 *
 * Letakkan project ini di: C:\xampp\htdocs\galeri-kreatif\
 * lalu akses melalui: http://localhost/galeri-kreatif/
 * -------------------------------------------------------------
 */

// Aktifkan pelaporan error saat development (nonaktifkan/matikan saat "produksi")
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Konfigurasi koneksi (sesuaikan jika kredensial XAMPP kamu berbeda)
define('DB_HOST', 'localhost');
define('DB_NAME', 'galeri_kreatif');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,       // lempar exception jika query gagal
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,             // hasil query berupa array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                        // gunakan prepared statement native MySQL
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Hentikan eksekusi dan tampilkan pesan error yang jelas jika koneksi gagal
    die(
        "Koneksi database gagal. Pastikan:\n" .
        "1. Service Apache & MySQL di XAMPP Control Panel sudah 'Start'.\n" .
        "2. Database '" . DB_NAME . "' sudah dibuat (import database.sql lewat phpMyAdmin).\n" .
        "3. Konfigurasi DB_USER/DB_PASS di koneksi.php sesuai dengan MySQL kamu.\n\n" .
        "Detail error: " . $e->getMessage()
    );
}

/**
 * Contoh pemakaian di file lain:
 *
 * require_once 'includes/koneksi.php';
 *
 * $stmt = $pdo->prepare("SELECT * FROM assets WHERE category_id = :category_id");
 * $stmt->execute(['category_id' => $categoryId]);
 * $assets = $stmt->fetchAll();
 */
