<?php
/**
 * profile.php
 * -------------------------------------------------------------
 * Halaman Profil Pengguna (Melihat Like & Komentar, Mengelola Karya & Foto Profil).
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

// Proteksi halaman, paksa login
requireLogin();

$userId = $_SESSION['user_id'];
$error = '';

// Handle POST request (Ganti avatar, upload karya, hapus karya)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrfToken)) {
        $error = 'Token keamanan tidak valid. Silakan coba kembali.';
    } else {
        if ($action === 'update_avatar') {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $uploadedPath = uploadImage($_FILES['avatar'], __DIR__ . '/assets/uploads');
                if ($uploadedPath !== false) {
                    // Ambil avatar lama
                    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = :id");
                    $stmt->execute(['id' => $userId]);
                    $oldAvatar = $stmt->fetchColumn();

                    // Update avatar di database
                    $updateStmt = $pdo->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
                    $updateStmt->execute([
                        'avatar' => $uploadedPath,
                        'id' => $userId
                    ]);

                    // Hapus file avatar lama jika ada
                    if (!empty($oldAvatar)) {
                        $oldFileAbsolute = __DIR__ . '/assets/' . $oldAvatar;
                        if (file_exists($oldFileAbsolute)) {
                            unlink($oldFileAbsolute);
                        }
                    }

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Foto profil berhasil diperbarui!'
                    ];
                    header("Location: profile.php");
                    exit;
                } else {
                    if (isset($_SESSION['flash_message'])) {
                        $error = $_SESSION['flash_message']['text'];
                        unset($_SESSION['flash_message']);
                    } else {
                        $error = 'Gagal mengunggah foto profil.';
                    }
                }
            } else {
                $error = 'Silakan pilih file foto terlebih dahulu.';
            }
        }

        if ($action === 'delete_avatar') {
            // Ambil avatar lama
            $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            $oldAvatar = $stmt->fetchColumn();

            if (!empty($oldAvatar)) {
                $oldFileAbsolute = __DIR__ . '/assets/' . $oldAvatar;
                if (file_exists($oldFileAbsolute)) {
                    unlink($oldFileAbsolute);
                }

                $updateStmt = $pdo->prepare("UPDATE users SET avatar = NULL WHERE id = :id");
                $updateStmt->execute(['id' => $userId]);

                $_SESSION['avatar'] = null;

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Foto profil berhasil dihapus dan dikembalikan ke avatar bawaan.'
                ];
                header("Location: profile.php");
                exit;
            } else {
                $error = 'Anda tidak memiliki foto profil khusus.';
            }
        }

        if ($action === 'update_name') {
            $fullName = trim($_POST['full_name'] ?? '');
            if (empty($fullName)) {
                $error = 'Nama lengkap tidak boleh kosong.';
            } else {
                $updateStmt = $pdo->prepare("UPDATE users SET full_name = :full_name WHERE id = :id");
                $updateStmt->execute([
                    'full_name' => $fullName,
                    'id' => $userId
                ]);

                // Perbarui sesi
                $_SESSION['full_name'] = $fullName;

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Nama lengkap berhasil diperbarui!'
                ];
                header("Location: profile.php");
                exit;
            }
        }

        if ($action === 'update_username') {
            $newUsername = trim($_POST['username'] ?? '');
            if (empty($newUsername) || !preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
                $error = 'Username tidak valid. Hanya gunakan huruf, angka, dan underscore.';
            } else {
                // Cek apakah username sudah dipakai orang lain
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
                $checkStmt->execute([
                    'username' => $newUsername,
                    'id' => $userId
                ]);
                
                if ($checkStmt->rowCount() > 0) {
                    $error = 'Username tersebut sudah digunakan oleh pengguna lain.';
                } else {
                    $updateStmt = $pdo->prepare("UPDATE users SET username = :username WHERE id = :id");
                    $updateStmt->execute([
                        'username' => $newUsername,
                        'id' => $userId
                    ]);

                    // Perbarui sesi
                    $_SESSION['username'] = $newUsername;

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Username berhasil diperbarui!'
                    ];
                    header("Location: profile.php");
                    exit;
                }
            }
        }

        if ($action === 'upload_asset') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categoryId = (int)($_POST['category_id'] ?? 0);
            $status = 'published';

            if (empty($title) || $categoryId <= 0) {
                $error = 'Judul dan Kategori wajib diisi.';
            } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'File gambar karya wajib diunggah.';
            } else {
                $uploadedPath = uploadImage($_FILES['image'], __DIR__ . '/assets/uploads');
                if ($uploadedPath !== false) {
                    $insertStmt = $pdo->prepare("
                        INSERT INTO assets (user_id, category_id, title, description, image_path, status) 
                        VALUES (:user_id, :category_id, :title, :description, :image_path, :status)
                    ");
                    $insertStmt->execute([
                        'user_id' => $userId,
                        'category_id' => $categoryId,
                        'title' => $title,
                        'description' => $description,
                        'image_path' => $uploadedPath,
                        'status' => $status
                    ]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Karya baru <strong>' . e($title) . '</strong> berhasil diunggah!'
                    ];
                    header("Location: profile.php");
                    exit;
                } else {
                    if (isset($_SESSION['flash_message'])) {
                        $error = $_SESSION['flash_message']['text'];
                        unset($_SESSION['flash_message']);
                    } else {
                        $error = 'Gagal mengunggah gambar karya.';
                    }
                }
            }
        }

        if ($action === 'delete_asset') {
            $assetId = (int)($_POST['asset_id'] ?? 0);

            // Validasi kepemilikan karya sebelum dihapus
            $stmt = $pdo->prepare("SELECT image_path FROM assets WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $assetId, 'user_id' => $userId]);
            $imagePath = $stmt->fetchColumn();

            if ($imagePath) {
                $fileAbsolute = __DIR__ . '/assets/' . $imagePath;
                if (file_exists($fileAbsolute)) {
                    unlink($fileAbsolute);
                }

                $deleteStmt = $pdo->prepare("DELETE FROM assets WHERE id = :id AND user_id = :user_id");
                $deleteStmt->execute(['id' => $assetId, 'user_id' => $userId]);

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Karya berhasil dihapus.'
                ];
                header("Location: profile.php");
                exit;
            } else {
                $error = 'Karya tidak ditemukan atau Anda tidak memiliki akses.';
            }
        }
    }
}

// Ambil info lengkap pengguna terkini dari DB
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$userStmt->execute(['id' => $userId]);
$user = $userStmt->fetch();

if (!$user) {
    header("Location: logout.php");
    exit;
}

// Ambil karya milik pengguna sendiri (uploaded assets)
$myAssetsStmt = $pdo->prepare("
    SELECT a.*, c.name as category_name
    FROM assets a
    JOIN categories c ON a.category_id = c.id
    WHERE a.user_id = :user_id
    ORDER BY a.created_at DESC
");
$myAssetsStmt->execute(['user_id' => $userId]);
$myAssets = $myAssetsStmt->fetchAll();

// Ambil karya yang disukai (liked assets)
$likedStmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, u.username as uploader_username
    FROM likes l
    JOIN assets a ON l.asset_id = a.id
    JOIN categories c ON a.category_id = c.id
    JOIN users u ON a.user_id = u.id
    WHERE l.user_id = :user_id AND a.status = 'published'
    ORDER BY l.created_at DESC
");
$likedStmt->execute(['user_id' => $userId]);
$likedAssets = $likedStmt->fetchAll();

// Ambil riwayat komentar pengguna
$commentStmt = $pdo->prepare("
    SELECT co.*, a.title as asset_title, a.id as asset_id
    FROM comments co
    JOIN assets a ON co.asset_id = a.id
    WHERE co.user_id = :user_id
    ORDER BY co.created_at DESC
");
$commentStmt->execute(['user_id' => $userId]);
$userComments = $commentStmt->fetchAll();

// Ambil semua kategori untuk form select upload karya
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll();

$pageTitle = "Profil Saya";
require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4 mb-5">
    <!-- Back link to homepage -->
    <div class="col-12">
        <a href="<?= getBaseUrl(); ?>index.php" class="text-decoration-none fw-bold" style="color: var(--vivid-purple);">
            <i class="ri-arrow-left-line"></i> Kembali ke Beranda / Galeri
        </a>
    </div>

    <!-- Sidebar Profil User -->
    <div class="col-lg-4">
        <div class="glass-panel p-4 text-center">
            <div class="mb-3 position-relative d-inline-block">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= getImageUrl($user['avatar']); ?>" alt="Profile Picture" class="avatar-profile-big">
                <?php else: ?>
                    <img src="<?= getDefaultAvatar($user['full_name'] ?? $user['username']); ?>" alt="Profile Picture" class="avatar-profile-big">
                <?php endif; ?>
                <span class="position-absolute badge rounded-pill bg-primary border border-2 border-white px-3 py-2" style="bottom: 0; right: 0; font-size: 0.75rem; text-transform: uppercase;">
                    <?= e($user['role']); ?>
                </span>
            </div>
            
            <h3 class="fw-extrabold mb-1" style="color: var(--deep-violet);"><?= e($user['full_name']); ?></h3>
            <p class="text-muted small mb-3">@<?= e($user['username']); ?></p>
            
            <div class="text-start py-3 border-top">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger p-2 small mb-3 text-center" style="border-radius: 8px; font-weight: 400;"><?= e($error); ?></div>
                <?php endif; ?>
                
                <div class="mb-2">
                    <span class="fw-bold text-muted small d-block">E-mail</span>
                    <span class="fw-semibold" style="color: var(--deep-violet);"><?= e($user['email']); ?></span>
                </div>
                <div class="mb-2">
                    <span class="fw-bold text-muted small d-block">Bergabung Sejak</span>
                    <span class="fw-semibold" style="color: var(--deep-violet);"><?= date('d F Y', strtotime($user['created_at'])); ?></span>
                </div>
                <div class="mb-3">
                    <span class="fw-bold text-muted small d-block">Status Akun</span>
                    <span class="badge bg-success rounded-pill px-2 py-1 small"><?= ucfirst(e($user['status'])); ?></span>
                </div>
                
                <div class="pt-3 border-top">
                    <span class="fw-bold text-muted small d-block mb-2"><i class="ri-image-edit-fill me-1"></i>Ganti Foto Profil</span>
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_avatar">
                        <div class="mb-2">
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control form-control-custom px-2 py-1" style="height: auto; font-size: 12px; font-weight: 400;" required>
                            <div class="text-muted mt-1" style="font-size: 10px; font-weight: 400;">Maks. 2MB (JPG, PNG, GIF, WEBP)</div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary-custom w-100 rounded-3 py-2" style="font-size: 12px; font-weight: 600; height: 38px;">
                            <i class="ri-camera-fill me-1"></i> Save
                        </button>
                    </form>
                    <?php if (!empty($user['avatar'])): ?>
                    <form action="profile.php" method="POST" class="mt-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil ini?');">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="delete_avatar">
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-3 py-1" style="font-size: 12px; font-weight: 600;">
                            <i class="ri-delete-bin-fill me-1"></i> Hapus Foto
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                
                <div class="pt-3 border-top mt-3">
                    <span class="fw-bold text-muted small d-block mb-2"><i class="ri-user-fill-pen me-1"></i>Ganti Nama Lengkap</span>
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_name">
                        <div class="mb-2">
                            <input type="text" name="full_name" class="form-control form-control-custom px-2 py-1" style="height: 38px; font-size: 12px; font-weight: 400;" value="<?= e($user['full_name']); ?>" required placeholder="Nama Lengkap">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary-custom w-100 rounded-3 py-2" style="font-size: 12px; font-weight: 600; height: 38px;">
                            <i class="ri-save-3-fill me-1"></i> Simpan Nama
                        </button>
                    </form>
                </div>
                
                <div class="pt-3 border-top mt-3">
                    <span class="fw-bold text-muted small d-block mb-2"><i class="ri-user-settings-fill me-1"></i>Ganti Username</span>
                    <form action="profile.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_username">
                        <div class="mb-2">
                            <input type="text" name="username" class="form-control form-control-custom px-2 py-1" style="height: 38px; font-size: 12px; font-weight: 400;" value="<?= e($user['username']); ?>" required placeholder="Username Baru">
                            <div class="text-muted mt-1" style="font-size: 10px; font-weight: 400;">Hanya huruf, angka & underscore.</div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-secondary w-100 rounded-3 py-2" style="font-size: 12px; font-weight: 600; height: 38px; border-color: var(--light-gray);">
                            <i class="ri-save-3-line me-1"></i> Simpan Username
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Utama: Aktivitas Liked & Comments -->
    <div class="col-lg-8">
        <div class="glass-panel p-4 h-100">
            <!-- Navigation Tabs -->
            <ul class="nav nav-pills mb-4 d-flex gap-2" id="profileActivityTab" role="tablist" style="background: rgba(0,0,0,0.03); padding: 0.5rem; border-radius: 12px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-3 fw-bold" id="my-assets-tab" data-bs-toggle="tab" data-bs-target="#my-assets-pane" type="button" role="tab" aria-controls="my-assets-pane" aria-selected="true">
                        <i class="ri-palette-fill me-1"></i> Karya Saya (<?= count($myAssets); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-3 fw-bold" id="liked-tab" data-bs-toggle="tab" data-bs-target="#liked-pane" type="button" role="tab" aria-controls="liked-pane" aria-selected="false">
                        <i class="ri-heart-3-fill me-1"></i> Disukai (<?= count($likedAssets); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-3 fw-bold" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments-pane" type="button" role="tab" aria-controls="comments-pane" aria-selected="false">
                        <i class="ri-chat-3-fill me-1"></i> Komentar (<?= count($userComments); ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="profileActivityTabContent">
                <!-- Tab Karya Saya -->
                <div class="tab-pane fade show active" id="my-assets-pane" role="tabpanel" aria-labelledby="my-assets-tab" tabindex="0">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="h2-title mb-0">Karya Desain Saya</h2>
                        <button class="btn btn-primary-custom d-flex align-items-center gap-2 px-3 py-2 rounded-3" style="font-size: 13px; font-weight: 600; height: 40px;" data-bs-toggle="modal" data-bs-target="#uploadAssetModal">
                            <i class="ri-add-line"></i> Unggah Karya Baru
                        </button>
                    </div>

                    <?php if (count($myAssets) > 0): ?>
                        <div class="pin-columns mb-3">
                            <?php foreach ($myAssets as $asset): ?>
                                <div class="pin-item">
                                    <div class="card-asset shadow-sm border border-light">
                                        <div class="card-asset-img-wrapper">
                                            <img src="<?= getImageUrl($asset['image_path']); ?>" alt="<?= e($asset['title']); ?>" class="card-asset-img">
                                            <div class="card-asset-overlay">
                                                <a href="detail.php?id=<?= $asset['id']; ?>" class="btn btn-accent-custom btn-sm w-100 fw-bold">
                                                    <i class="ri-eye-fill me-1"></i> Buka Detail
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-asset-body p-3">
                                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                                <span class="badge-category"><?= e($asset['category_name']); ?></span>
                                                <form action="profile.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karya ini?');" class="m-0">
                                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete_asset">
                                                    <input type="hidden" name="asset_id" value="<?= $asset['id']; ?>">
                                                    <button type="submit" class="btn btn-link text-danger p-0 m-0 border-0 fs-6" title="Hapus Karya">
                                                        <i class="ri-delete-bin-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <h5 class="card-asset-title mb-1">
                                                <a href="detail.php?id=<?= $asset['id']; ?>">
                                                    <?= e($asset['title']); ?>
                                                </a>
                                            </h5>
                                            <p class="caption-text text-muted mb-0" style="font-weight: 400;">Status: <span class="badge bg-secondary px-2 py-1 small"><?= ucfirst(e($asset['status'])); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted glass-panel border-dashed" style="border: 2px dashed rgba(73,16,139,0.15);">
                            <i class="ri-palette-fill fs-1 mb-2 d-block" style="opacity: 0.4;"></i>
                            Anda belum mengunggah karya apa pun.
                            <div class="mt-3">
                                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#uploadAssetModal">Mulai Unggah Karya Pertama Anda</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Liked Assets -->
                <div class="tab-pane fade" id="liked-pane" role="tabpanel" aria-labelledby="liked-tab" tabindex="0">
                    <h2 class="h2-title mb-3">Karya Yang Anda Sukai</h2>
                    
                    <?php if (count($likedAssets) > 0): ?>
                        <div class="pin-columns mb-3">
                            <?php foreach ($likedAssets as $asset): ?>
                                <div class="pin-item">
                                    <div class="card-asset shadow-sm border border-light">
                                        <div class="card-asset-img-wrapper">
                                            <img src="<?= getImageUrl($asset['image_path']); ?>" alt="<?= e($asset['title']); ?>" class="card-asset-img">
                                            <div class="card-asset-overlay">
                                                <a href="detail.php?id=<?= $asset['id']; ?>" class="btn btn-accent-custom btn-sm w-100 fw-bold">
                                                    <i class="ri-eye-fill me-1"></i> Buka Detail
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-asset-body p-3">
                                            <div class="mb-2">
                                                <span class="badge-category"><?= e($asset['category_name']); ?></span>
                                            </div>
                                            <h5 class="card-asset-title mb-1">
                                                <a href="detail.php?id=<?= $asset['id']; ?>">
                                                    <?= e($asset['title']); ?>
                                                </a>
                                            </h5>
                                            <p class="caption-text text-muted mb-0" style="font-weight: 400;">karya @<?= e($asset['uploader_username']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted glass-panel border-dashed" style="border: 2px dashed rgba(73,16,139,0.15);">
                            <i class="ri-heart-3-line fs-1 mb-2 d-block" style="opacity: 0.4;"></i>
                            Anda belum menyukai karya apa pun saat ini.
                            <div class="mt-2">
                                <a href="index.php" class="btn btn-primary-custom btn-sm">Jelajahi Galeri</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tab Comments History -->
                <div class="tab-pane fade" id="comments-pane" role="tabpanel" aria-labelledby="comments-tab" tabindex="0">
                    <h4 class="fw-bold mb-3" style="color: var(--deep-violet);">Riwayat Komentar Anda</h4>

                    <?php if (count($userComments) > 0): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($userComments as $comment): ?>
                                <div class="comment-card shadow-sm border border-light border-start-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold" style="color: var(--deep-violet);">
                                            Pada: <a href="detail.php?id=<?= $comment['asset_id']; ?>" class="text-decoration-none" style="color: var(--vivid-purple);"><?= e($comment['asset_title']); ?></a>
                                        </span>
                                        <span class="text-muted small"><?= date('d M Y, H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                    <p class="mb-0 text-muted italic">"<?= e($comment['comment']); ?>"</p>
                                    <div class="d-flex gap-2 justify-content-end mt-2">
                                        <a href="detail.php?id=<?= $comment['asset_id']; ?>" class="btn btn-link text-primary p-0 text-decoration-none btn-sm">
                                            <i class="ri-arrow-right-line"></i> Selengkapnya
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted glass-panel border-dashed" style="border: 2px dashed rgba(73,16,139,0.15);">
                            <i class="ri-chat-3-line fs-1 mb-2 d-block" style="opacity: 0.4;"></i>
                            Anda belum menulis komentar di karya apa pun.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Unggah Karya -->
<div class="modal fade" id="uploadAssetModal" tabindex="-1" aria-labelledby="uploadAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-extrabold" id="uploadAssetModalLabel" style="color: var(--deep-violet); font-size: 1.5rem;">
                    <i class="ri-upload-cloud-2-fill me-2" style="color: var(--vivid-purple);"></i>Unggah Karya Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="upload_asset">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label-custom">Judul Karya <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control form-control-custom" placeholder="Masukkan judul karya desain Anda" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label-custom">Kategori Karya <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-select form-control-custom" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>"><?= e($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label-custom">Deskripsi Karya</label>
                        <textarea name="description" id="description" class="form-control form-control-custom" style="height: 120px;" placeholder="Tuliskan detail, inspirasi, atau proses pembuatan karya Anda..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label for="image" class="form-label-custom">File Gambar Karya <span class="text-danger">*</span></label>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif,image/webp" class="form-control form-control-custom px-3 py-2" style="height: auto;" required>
                        <div class="text-muted mt-1 small" style="font-size: 11px; font-weight: 400;">Format gambar yang didukung: JPG, PNG, GIF, WEBP. Maksimal ukuran 2MB.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-outline-custom px-4 py-2" style="height: 48px; font-size: 14px; font-weight: 600; border-radius: 16px;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom px-4 py-2 flex-grow-1" style="height: 48px; font-size: 14px; font-weight: 600; border-radius: 16px;">
                        <i class="ri-send-plane-fill me-1"></i> Publikasikan Karya
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>


