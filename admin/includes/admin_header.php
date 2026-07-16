<?php
/**
 * admin/includes/admin_header.php
 * -------------------------------------------------------------
 * Layout header global untuk Admin Panel Dashboard.
 * DESIGN.md aligned: Pinterest-style minimal nav, 48px buttons,
 * 16px border-radius, proper typography weights.
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../koneksi.php';
require_once __DIR__ . '/../../includes/functions.php';

// Proteksi halaman admin
requireAdmin();

$pageTitle = isset($pageTitle) ? $pageTitle . " - Dashboard Admin" : "Dashboard Admin";
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
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= getBaseUrl(); ?>assets/css/style.css">
    <style>
        /* Admin-specific overrides — keeps the panel visually distinct from the public frontend */
        .admin-navbar {
            background-color: var(--deep-violet);
            height: 80px;
            padding: 0 24px;
            box-shadow: 0px 1px 0px rgba(0, 0, 0, 0.15);
        }
        .admin-navbar .navbar-brand {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-navbar .nav-link {
            color: rgba(255,255,255,0.75) !important;
            font-size: 12px;
            font-weight: 400;
            padding: 6px 14px !important;
            border-radius: 999px;
            height: 48px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease-in-out;
        }
        .admin-navbar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: #ffffff !important;
        }
        .admin-navbar .nav-link.active {
            background-color: rgba(255,255,255,0.15);
            color: #ffffff !important;
            font-weight: 700;
        }
        .admin-navbar .btn-admin-action {
            font-size: 12px;
            font-weight: 400;
            padding: 6px 14px;
            border-radius: 16px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease-in-out;
        }
        .admin-navbar .btn-view-site {
            background-color: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #ffffff;
        }
        .admin-navbar .btn-view-site:hover {
            background-color: rgba(255,255,255,0.2);
            color: #ffffff;
        }
        .admin-navbar .btn-logout {
            background-color: var(--brand-red);
            border: none;
            color: var(--true-white);
        }
        .admin-navbar .btn-logout:hover {
            filter: brightness(0.95);
        }
        .admin-user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 999px;
            color: rgba(255,255,255,0.85);
            font-size: 12px;
            font-weight: 400;
        }
        @media (max-width: 991.98px) {
            .admin-navbar {
                height: auto;
                padding: 12px 24px;
            }
            .admin-navbar .nav-link {
                height: auto;
                padding: 10px 14px !important;
            }
            .admin-user-pill {
                margin-top: 12px;
                justify-content: flex-start;
            }
            .admin-navbar .d-flex.gap-2 {
                flex-direction: column;
                align-items: stretch !important;
            }
        }
    </style>
</head>
<body style="background-color: var(--ice-white);">

    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg admin-navbar sticky-top">
        <div class="container-fluid px-0 px-md-3">
            <!-- Brand -->
            <a class="navbar-brand text-white fw-extrabold d-flex align-items-center gap-2" href="<?= getBaseUrl(); ?>admin/dashboard.php">
                <i class="ri-shield-user-fill" style="color: var(--brand-red);"></i>
                <span>Admin Panel</span>
            </a>

            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminNavbar" aria-controls="adminNavbar">
                <i class="ri-menu-line text-white fs-2"></i>
            </button>

            <div class="offcanvas-lg offcanvas-end" tabindex="-1" id="adminNavbar" aria-labelledby="adminNavbarLabel" style="background-color: var(--deep-violet);">
                <div class="offcanvas-header border-bottom border-light border-opacity-10 d-lg-none">
                    <h5 class="offcanvas-title text-white fw-bold d-flex align-items-center gap-2" id="adminNavbarLabel">
                        <i class="ri-shield-user-fill" style="color: var(--brand-red);"></i> Admin Panel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <!-- Main Nav Links -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center gap-1">
                        <li class="nav-item">
                            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="<?= getBaseUrl(); ?>admin/dashboard.php">
                                <i class="ri-dashboard-3-fill"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'assets.php') ? 'active' : ''; ?>" href="<?= getBaseUrl(); ?>admin/assets.php">
                                <i class="ri-image-fill"></i> Karya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'categories.php') ? 'active' : ''; ?>" href="<?= getBaseUrl(); ?>admin/categories.php">
                                <i class="ri-price-tag-3-fill"></i> Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>" href="<?= getBaseUrl(); ?>admin/users.php">
                                <i class="ri-group-fill"></i> Pengguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'comments.php') ? 'active' : ''; ?>" href="<?= getBaseUrl(); ?>admin/comments.php">
                                <i class="ri-chat-3-fill"></i> Komentar
                            </a>
                        </li>
                    </ul>

                    <!-- Right-side Actions -->
                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-center my-2 my-lg-0">
                        <span class="admin-user-pill d-none d-lg-flex">
                            <i class="ri-user-3-fill"></i>
                            <?= e($_SESSION['full_name'] ?? $_SESSION['username']); ?>
                        </span>
                        <a href="<?= getBaseUrl(); ?>" class="btn-admin-action btn-view-site text-decoration-none">
                            <i class="ri-global-line"></i> Lihat Situs
                        </a>
                        <a href="<?= getBaseUrl(); ?>logout.php" class="btn-admin-action btn-logout text-decoration-none">
                            <i class="ri-logout-box-line"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container my-4 flex-grow-1">

        <!-- Admin Global Flash Messages Notification -->
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


