<?php
/**
 * includes/functions.php
 * -------------------------------------------------------------
 * Kumpulan fungsi pembantu (helper) untuk aplikasi.
 * -------------------------------------------------------------
 */

/**
 * Escaping string untuk mencegah XSS (Cross-Site Scripting).
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Membuat slug ramah URL dari string.
 * @param string $text
 * @return string
 */
function generateSlug($text) {
    // Ganti karakter non-alfanumerik dengan -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterasi
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Hapus karakter yang tidak diinginkan
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Hilangkan duplikasi -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

/**
 * Upload gambar dengan validasi tipe data dan ukuran file.
 * @param array $file File dari $_FILES['name']
 * @param string $targetDir Direktori tujuan upload relatif ke file ini atau absolute
 * @param int $maxSize Maksimal ukuran file dalam byte (default 2MB)
 * @return string|bool Mengembalikan path gambar relatif (misal: 'uploads/nama_file.png') jika sukses, false jika gagal.
 */
function uploadImage($file, $targetDir, $maxSize = 2097152) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $fileName = basename($file['name']);
    $fileSize = $file['size'];
    $fileTmpName = $file['tmp_name'];
    $fileType = wp_mime_type($fileTmpName);

    // Daftar mime type gambar yang diperbolehkan
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // Validasi tipe file
    if (!in_array($fileType, $allowedMimeTypes)) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'text' => 'Tipe file tidak valid. Hanya JPG, PNG, GIF, dan WEBP yang diperbolehkan.'
        ];
        return false;
    }

    // Validasi ukuran file
    if ($fileSize > $maxSize) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'text' => 'Ukuran file terlalu besar. Maksimal 2MB.'
        ];
        return false;
    }

    // Ekstensi file asli
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    // Ganti nama file agar unik guna menghindari konflik penamaan
    $newFileName = uniqid('img_', true) . '.' . $ext;
    
    // Pastikan folder target ada
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $destination = rtrim($targetDir, '/') . '/' . $newFileName;

    if (move_uploaded_file($fileTmpName, $destination)) {
        // Return path relatif untuk disimpan di database
        return 'uploads/' . $newFileName;
    }

    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Gagal memindahkan file ke direktori tujuan.'
    ];
    return false;
}

/**
 * Mendapatkan Mime Type secara aman menggunakan Fileinfo.
 * @param string $filename
 * @return string
 */
function wp_mime_type($filename) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $filename);
    finfo_close($finfo);
    return $mime;
}

/**
 * Cek apakah user tertentu menyukai asset tertentu.
 * @param int $assetId
 * @param int $userId
 * @return bool
 */
function hasUserLiked($assetId, $userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT 1 FROM likes WHERE asset_id = :asset_id AND user_id = :user_id");
    $stmt->execute([
        'asset_id' => $assetId,
        'user_id' => $userId
    ]);
    return (bool) $stmt->fetch();
}

/**
 * Hitung jumlah likes pada suatu karya.
 * @param int $assetId
 * @return int
 */
function getLikeCount($assetId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE asset_id = :asset_id");
    $stmt->execute(['asset_id' => $assetId]);
    return (int) $stmt->fetchColumn();
}

/**
 * Hitung jumlah komentar pada suatu karya.
 * @param int $assetId
 * @return int
 */
function getCommentCount($assetId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE asset_id = :asset_id");
    $stmt->execute(['asset_id' => $assetId]);
    return (int) $stmt->fetchColumn();
}

/**
 * Mengambil URL gambar lengkap dengan subfolder assets/.
 * @param string|null $path Path dari database (misal: 'uploads/nama_file.png')
 * @return string
 */
function getImageUrl($path) {
    if (empty($path)) {
        return '';
    }
    // Jika path sudah memiliki prefiks 'assets/', langsung kembalikan dengan base URL
    if (strpos($path, 'assets/') === 0) {
        return getBaseUrl() . $path;
    }
    return getBaseUrl() . 'assets/' . $path;
}

/**
 * Menghasilkan avatar default berdasarkan inisial nama.
 * (Pustaka lasserafn/php-initial-avatar-generator menyebabkan Fatal Error di PHP 8+, 
 * sehingga kita gunakan native SVG generator yang identik).
 * @param string $name Nama pengguna (full_name atau username)
 * @return string Data URL base64 gambar avatar
 */
function getDefaultAvatar($name) {
    // Generate native SVG avatar fallback
    $initial = e(strtoupper(substr($name ?? 'U', 0, 1)));
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" fill="#3E3636" rx="32"/><text x="50%" y="50%" dominant-baseline="central" text-anchor="middle" font-family="Arial, sans-serif" font-weight="bold" font-size="28" fill="#F5EDED">'.$initial.'</text></svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}
