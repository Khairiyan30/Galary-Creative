<?php
/**
 * login.php
 * -------------------------------------------------------------
 * Halaman Login Pengguna (User & Admin).
 * DESIGN.md aligned: clean card, 48px inputs & buttons, 16px radius.
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, redirect sesuai role
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: " . getBaseUrl() . "admin/dashboard.php");
    } else {
        header("Location: " . getBaseUrl() . "index.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!validateCSRFToken($csrfToken)) {
        $error = 'Token keamanan tidak valid. Silakan coba lagi.';
    } elseif (empty($identity) || empty($password)) {
        $error = 'Username/Email dan Password wajib diisi.';
    } else {
        // Cari pengguna berdasarkan username atau email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute([
            'username' => $identity,
            'email' => $identity
        ]);
        $user = $stmt->fetch();

        if ($user) {
            // Cek jika akun nonaktif
            if ($user['status'] !== 'active') {
                $error = 'Akun Anda telah dinonaktifkan oleh administrator.';
            } else {
                // Verifikasi password (termasuk handle dummy hash bawaan database.sql)
                $isValidPassword = false;

                // Handle dummy hashes khusus dari seed database
                if ($user['username'] === 'admin' && $user['password'] === '$2y$10$abcdefghijklmnopqrstuvexampleHashAdmin123456789012' && $password === 'admin123') {
                    $isValidPassword = true;
                } elseif ($user['username'] === 'rasya_art' && $user['password'] === '$2y$10$abcdefghijklmnopqrstuvexampleHashUser1234567890123' && $password === 'user123') {
                    $isValidPassword = true;
                } elseif ($user['username'] === 'dinapixel' && $user['password'] === '$2y$10$abcdefghijklmnopqrstuvexampleHashUser2234567890123' && $password === 'user123') {
                    $isValidPassword = true;
                } else {
                    // Pengecekan standard menggunakan bcrypt
                    $isValidPassword = password_verify($password, $user['password']);
                }

                if ($isValidPassword) {
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['avatar'] = $user['avatar'];
                    $_SESSION['role'] = $user['role'];

                    // Flash message sukses
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Selamat datang kembali, <strong>' . e($user['full_name'] ?? $user['username']) . '</strong>!'
                    ];

                    // Redirect sesuai role
                    if ($user['role'] === 'admin') {
                        header("Location: " . getBaseUrl() . "admin/dashboard.php");
                    } else {
                        header("Location: " . getBaseUrl() . "index.php");
                    }
                    exit;
                } else {
                    $error = 'Password yang Anda masukkan salah.';
                }
            }
        } else {
            $error = 'Username atau Email tidak ditemukan.';
        }
    }
}

// Generate token CSRF untuk form
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Galeri Kreatif</title>
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

            <!-- Brand Logo above card -->
            <a href="<?= getBaseUrl(); ?>" class="auth-page-brand">
                <i class="ri-palette-fill"></i>
                <span>Galeri Kreatif</span>
            </a>

            <div class="auth-card">
                <div class="auth-header">
                    <h1 style="font-size: 24px; font-weight: 700; line-height: 26.4px; margin-bottom: 4px;">Masuk ke Akun</h1>
                    <p style="font-size: 14px; font-weight: 400; color: rgba(255,255,255,0.9); margin: 0;">Beri apresiasi dan diskusikan karya-karya favorit Anda</p>
                </div>

                <div class="auth-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 py-2 px-3 mb-4" role="alert" style="font-size: 12px; font-weight: 400;">
                            <i class="ri-error-warning-fill flex-shrink-0"></i>
                            <div><?= e($error); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?= e($_SESSION['flash_message']['type']); ?> d-flex align-items-center gap-2 rounded-3 py-2 px-3 mb-4" role="alert" style="font-size: 12px; font-weight: 400;">
                            <i class="ri-checkbox-circle-fill flex-shrink-0"></i>
                            <div><?= $_SESSION['flash_message']['text']; ?></div>
                        </div>
                        <?php unset($_SESSION['flash_message']); ?>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken; ?>">

                        <div class="mb-3">
                            <label for="identity" class="form-label-custom">Username atau Email</label>
                            <input type="text" class="form-control form-control-custom" id="identity" name="identity"
                                placeholder="Masukkan username/email"
                                value="<?= isset($_POST['identity']) ? e($_POST['identity']) : ''; ?>"
                                required autocomplete="username">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label-custom">Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control form-control-custom pe-5" id="password" name="password"
                                    placeholder="Masukkan password"
                                    required autocomplete="current-password">
                                <i class="ri-eye-off-line position-absolute top-50 translate-middle-y end-0 me-3 text-muted" 
                                   id="togglePassword" style="cursor: pointer; font-size: 18px;" 
                                   onclick="togglePasswordVisibility('password', 'togglePassword')"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                            <i class="ri-login-box-line me-1"></i> Masuk Sekarang
                        </button>
                    </form>

                    <hr style="border-color: var(--light-gray); margin: 20px 0;">

                    <p class="text-center mb-0" style="font-size: 12px; font-weight: 400; color: var(--warm-gray);">
                        Belum punya akun?
                        <a href="register.php" class="text-decoration-none fw-bold" style="color: var(--vivid-purple);">Daftar di sini</a>
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



