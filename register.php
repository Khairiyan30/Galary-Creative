<?php
/**
 * register.php
 * -------------------------------------------------------------
 * Halaman Registrasi Pengguna Baru.
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, redirect
if (isLoggedIn()) {
    header("Location: " . getBaseUrl() . "index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!validateCSRFToken($csrfToken)) {
        $error = 'Token keamanan tidak valid. Silakan coba lagi.';
    } elseif (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        $error = 'Semua field wajib diisi (kecuali Foto Profil).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($username) < 3 || strlen($username) > 30) {
        $error = 'Username harus berkisar antara 3 sampai 30 karakter.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal harus 6 karakter.';
    } else {
        // Cek keunikan username dan email
        $stmt = $pdo->prepare("SELECT 1 FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Username atau Email sudah terdaftar.';
        } else {
            // Handle upload avatar jika ada
            $avatarPath = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                // Upload gambar ke assets/uploads/
                $uploaded = uploadImage($_FILES['avatar'], __DIR__ . '/assets/uploads');
                if ($uploaded !== false) {
                    $avatarPath = $uploaded;
                } else {
                    // Jika upload gagal, pesan error sudah di-set di $_SESSION['flash_message'] oleh uploadImage()
                    if (isset($_SESSION['flash_message'])) {
                        $error = $_SESSION['flash_message']['text'];
                        unset($_SESSION['flash_message']);
                    } else {
                        $error = 'Gagal mengunggah foto profil.';
                    }
                }
            }

            // Jika tidak ada error upload
            if (empty($error)) {
                // Hash password menggunakan bcrypt
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Insert user baru ke database
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, full_name, avatar, role, status) 
                    VALUES (:username, :email, :password, :full_name, :avatar, 'user', 'active')
                ");

                try {
                    $stmt->execute([
                        'username'  => $username,
                        'email'     => $email,
                        'password'  => $hashedPassword,
                        'full_name' => $fullName,
                        'avatar'    => $avatarPath
                    ]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Pendaftaran berhasil! Silakan login menggunakan akun baru Anda.'
                    ];
                    header("Location: " . getBaseUrl() . "login.php");
                    exit;
                } catch (PDOException $e) {
                    $error = 'Gagal menyimpan data ke database: ' . $e->getMessage();
                }
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - Galeri Kreatif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= getBaseUrl(); ?>assets/css/style.css">
    <style>
        body {
            background-color: var(--off-white);
            background-image: radial-gradient(ellipse at 60% 20%, rgba(62, 54, 54, 0.06) 0%, transparent 60%),
                              radial-gradient(ellipse at 20% 80%, rgba(215, 35, 35, 0.07) 0%, transparent 60%);
            min-height: 100vh;
        }
        .auth-page-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 32px;
            text-decoration: none;
        }
        .auth-page-brand i {
            font-size: 28px;
            color: var(--vivid-purple);
        }
        .auth-page-brand span {
            font-size: 22px;
            font-weight: 700;
            color: var(--deep-violet);
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div style="width: 100%; max-width: 440px;">

            <!-- Brand above card -->
            <a href="<?= getBaseUrl(); ?>" class="auth-page-brand">
                <i class="ri-palette-fill"></i>
                <span>Galeri Kreatif</span>
            </a>

            <div class="auth-card">
            <div class="auth-header">
                <h1 style="font-size: 24px; font-weight: 700; line-height: 26.4px; margin-bottom: 4px;">Buat Akun Baru</h1>
                <p style="font-size: 14px; font-weight: 400; color: rgba(255,255,255,0.9); margin: 0;">Bergabunglah dengan komunitas kreator visual</p>
            </div>
            <div class="auth-body">
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 py-2 px-3 mb-4" role="alert" style="font-size: 12px; font-weight: 400;">
                        <i class="ri-error-warning-fill flex-shrink-0"></i>
                        <div><?= e($error); ?></div>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken; ?>">
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label-custom">Nama Lengkap</label>
                        <input type="text" class="form-control form-control-custom" id="full_name" name="full_name" placeholder="Contoh: Rasya Pratama" value="<?= isset($_POST['full_name']) ? e($_POST['full_name']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label-custom">Username</label>
                        <input type="text" class="form-control form-control-custom" id="username" name="username" placeholder="Masukkan username unik" value="<?= isset($_POST['username']) ? e($_POST['username']) : ''; ?>" required>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Username hanya huruf, angka, dan underscore.</div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label-custom">Email</label>
                        <input type="email" class="form-control form-control-custom" id="email" name="email" placeholder="Contoh: rasya@mail.com" value="<?= isset($_POST['email']) ? e($_POST['email']) : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label-custom">Password</label>
                        <div class="position-relative">
                            <input type="password" class="form-control form-control-custom pe-5" id="password" name="password" placeholder="Minimal 6 karakter" required>
                            <i class="ri-eye-off-line position-absolute top-50 translate-middle-y end-0 me-3 text-muted" 
                               id="togglePassword" style="cursor: pointer; font-size: 18px;" 
                               onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="avatar" class="form-label-custom">Foto Profil <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="file" class="form-control form-control-custom" id="avatar" name="avatar" accept="image/*">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Format JPG/PNG/WEBP. Maksimal 2MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                        <i class="ri-user-add-fill me-1"></i> Daftar Sekarang
                    </button>
                </form>

                <hr style="border-color: var(--light-gray); margin: 20px 0;">

                <p class="text-center mb-0" style="font-size: 12px; font-weight: 400; color: var(--warm-gray);">
                    Sudah memiliki akun?
                    <a href="login.php" class="text-decoration-none fw-bold" style="color: var(--vivid-purple);">Login di sini</a>
                </p>
            </div>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
                icon.style.color = 'var(--brand-red)';
            } else {
                input.type = 'password';
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
                icon.style.color = '';
            }
        }
    </script>
</body>
</html>



