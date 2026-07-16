<?php
/**
 * includes/header.php
 * -------------------------------------------------------------
 * Layout header global untuk bagian depan (User/Publik).
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/functions.php';

// Menentukan judul halaman dinamis
$pageTitle = isset($pageTitle) ? $pageTitle . " - Galeri Kreatif" : "Galeri Aset Kreatif & Desain Karakter";

$searchVal = trim($_GET['search'] ?? '');
$categorySlugVal = trim($_GET['category'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- RemixIcon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form[onsubmit*="return confirm"]');
        forms.forEach(form => {
            const match = form.getAttribute('onsubmit').match(/confirm\('([^']+)'\)/);
            if (match) {
                const message = match[1];
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D72323', // Brand Red
                        cancelButtonColor: '#3E3636', // Dark Gray
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        background: '#F5EDED', // Ice White
                        color: '#3E3636'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    });
    </script>
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="<?= getBaseUrl(); ?>assets/css/style.css">
</head>
<body>

    <!-- Header Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container-fluid px-md-4">
            <a class="navbar-brand me-3" href="<?= getBaseUrl(); ?>">
                <i class="ri-palette-fill"></i>
                <span class="d-none d-sm-inline">Galeri Kreatif</span>
            </a>
            
            <!-- Pinterest-style Search Form inside Navbar (Desktop) -->
            <div class="navbar-search-container d-none d-lg-block">
                <form action="<?= getBaseUrl(); ?>index.php" method="GET" class="search-pill-form">
                    <?php if (!empty($categorySlugVal)): ?>
                        <input type="hidden" name="category" value="<?= e($categorySlugVal); ?>">
                    <?php endif; ?>
                    <button type="submit" class="search-pill-btn me-2">
                        <i class="ri-search-line"></i>
                    </button>
                    <input type="text" name="search" class="search-pill-input" placeholder="Cari karya kreatif..." value="<?= e($searchVal); ?>">
                </form>
            </div>

            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ri-menu-line text-dark fs-2"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                </ul>
                
                <!-- Search Form (Mobile collapse) -->
                <div class="d-block d-lg-none my-3">
                    <form action="<?= getBaseUrl(); ?>index.php" method="GET" class="search-pill-form">
                        <?php if (!empty($categorySlugVal)): ?>
                            <input type="hidden" name="category" value="<?= e($categorySlugVal); ?>">
                        <?php endif; ?>
                        <button type="submit" class="search-pill-btn me-2">
                            <i class="ri-search-line"></i>
                        </button>
                        <input type="text" name="search" class="search-pill-input" placeholder="Cari karya kreatif..." value="<?= e($searchVal); ?>">
                    </form>
                </div>

                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-center">
                    <?php if (isLoggedIn()): ?>
                        <!-- Jika sudah login -->
                        <?php if (isAdmin()): ?>
                            <a href="<?= getBaseUrl(); ?>admin/dashboard.php" class="btn btn-secondary-custom px-3 me-2">
                                <i class="ri-dashboard-3-fill me-1"></i> Admin Panel
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?= getBaseUrl(); ?>profile.php" class="btn btn-primary-custom px-3 d-none d-md-flex align-items-center gap-1 me-2" style="font-size: 12px; font-weight: 600; height: 38px; border-radius: 999px;">
                            <i class="ri-add-line"></i> Unggah
                        </a>

                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-dark d-flex align-items-center gap-2 px-2 py-1" style="background-color: var(--off-white); border-radius: 999px;" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if (!empty($_SESSION['avatar'])): ?>
                                    <img src="<?= getImageUrl($_SESSION['avatar']); ?>" alt="Avatar" class="rounded-circle border border-2 border-white" style="width: 32px; height: 32px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= getDefaultAvatar($_SESSION['full_name'] ?? $_SESSION['username']); ?>" alt="Avatar" class="rounded-circle border border-2 border-white" style="width: 32px; height: 32px; object-fit: cover;">
                                <?php endif; ?>
                                <span class="fw-bold me-1"><?= e($_SESSION['full_name'] ?? $_SESSION['username']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end rounded-4 shadow border-0 mt-2">
                                <li>
                                    <a class="dropdown-item py-2" href="<?= getBaseUrl(); ?>profile.php">
                                        <i class="ri-user-fill me-2" style="color: var(--deep-violet);"></i> Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="<?= getBaseUrl(); ?>profile.php">
                                        <i class="ri-upload-cloud-2-fill me-2" style="color: var(--deep-violet);"></i> Unggah Karya
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 fw-bold" href="<?= getBaseUrl(); ?>logout.php" style="color: var(--vivid-purple);">
                                        <i class="ri-logout-box-line me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Jika belum login -->
                        <a href="<?= getBaseUrl(); ?>login.php" class="btn btn-secondary-custom px-3">
                            <i class="ri-login-box-line me-1"></i> Login
                        </a>
                        <a href="<?= getBaseUrl(); ?>register.php" class="btn btn-primary-custom px-3">
                            <i class="ri-user-fill-plus me-1"></i> Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div class="container my-4 flex-grow-1">
        
        <!-- Global Flash Messages Notification -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?= e($_SESSION['flash_message']['type']); ?> alert-dismissible fade show rounded-4 shadow-sm py-3 px-4 mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <?php if ($_SESSION['flash_message']['type'] === 'success'): ?>
                        <i class="ri-checkbox-circle-fill fs-4 text-success"></i>
                    <?php else: ?>
                        <i class="ri-error-warning-fill fs-4 text-danger"></i>
                    <?php endif; ?>
                    <div>
                        <?= $_SESSION['flash_message']['text']; ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>


