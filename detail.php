<?php
/**
 * detail.php
 * -------------------------------------------------------------
 * Halaman Detail Karya (Gambar besar, Like, Komentar).
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cari asset di database
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.username, u.full_name, u.avatar as creator_avatar
    FROM assets a
    JOIN categories c ON a.category_id = c.id
    JOIN users u ON a.user_id = u.id
    WHERE a.id = :id AND a.status = 'published'
");
$stmt->execute(['id' => $id]);
$asset = $stmt->fetch();

if (!$asset) {
    $_SESSION['flash_message'] = [
        'type' => 'danger',
        'text' => 'Karya tidak ditemukan atau belum dipublikasikan.'
    ];
    header("Location: " . getBaseUrl());
    exit;
}

$pageTitle = $asset['title'];

// Proses Aksi Form (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Verifikasi CSRF Token
    if (!validateCSRFToken($csrfToken)) {
        $_SESSION['flash_message'] = [
            'type' => 'danger',
            'text' => 'Token keamanan tidak valid. Silakan coba kembali.'
        ];
    } else {
        $userId = $_SESSION['user_id'];

        if ($action === 'like') {
            // Cek apakah user sudah menyukai karya ini
            if (hasUserLiked($id, $userId)) {
                // Delete like (Unlike)
                $likeStmt = $pdo->prepare("DELETE FROM likes WHERE asset_id = :asset_id AND user_id = :user_id");
                $likeStmt->execute(['asset_id' => $id, 'user_id' => $userId]);
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Batal menyukai karya ini.'];
            } else {
                // Add like
                try {
                    $likeStmt = $pdo->prepare("INSERT INTO likes (asset_id, user_id) VALUES (:asset_id, :user_id)");
                    $likeStmt->execute(['asset_id' => $id, 'user_id' => $userId]);
                    $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Menyukai karya ini! Terima kasih atas apresiasinya.'];
                } catch (PDOException $e) {
                    // Ignored since unique key handles duplicates
                }
            }
            header("Location: detail.php?id=" . $id);
            exit;
        }

        if ($action === 'add_comment') {
            $commentText = trim($_POST['comment'] ?? '');
            if (empty($commentText)) {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Komentar tidak boleh kosong.'];
            } else {
                $commentStmt = $pdo->prepare("INSERT INTO comments (asset_id, user_id, comment) VALUES (:asset_id, :user_id, :comment)");
                $commentStmt->execute([
                    'asset_id' => $id,
                    'user_id' => $userId,
                    'comment' => $commentText
                ]);
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Komentar berhasil dikirim!'];
            }
            header("Location: detail.php?id=" . $id);
            exit;
        }

        if ($action === 'edit_comment') {
            $commentId = (int)($_POST['comment_id'] ?? 0);
            $commentText = trim($_POST['comment'] ?? '');

            // Validasi kepemilikan komentar (hanya pemilik yang bisa mengedit)
            $checkStmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :id");
            $checkStmt->execute(['id' => $commentId]);
            $commentOwner = $checkStmt->fetchColumn();

            if ($commentOwner == $userId) {
                if (empty($commentText)) {
                    $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Komentar tidak boleh kosong.'];
                } else {
                    $updateStmt = $pdo->prepare("UPDATE comments SET comment = :comment WHERE id = :id");
                    $updateStmt->execute([
                        'comment' => $commentText,
                        'id' => $commentId
                    ]);
                    $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Komentar berhasil diperbarui.'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Akses ditolak! Anda hanya dapat mengubah komentar Anda sendiri.'];
            }
            header("Location: detail.php?id=" . $id);
            exit;
        }

        if ($action === 'delete_comment') {
            $commentId = (int)($_POST['comment_id'] ?? 0);

            // Cek kepemilikan (Pemilik komentar ATAU admin)
            $checkStmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :id");
            $checkStmt->execute(['id' => $commentId]);
            $commentOwner = $checkStmt->fetchColumn();

            if ($commentOwner == $userId || isAdmin()) {
                $deleteStmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
                $deleteStmt->execute(['id' => $commentId]);
                $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Komentar berhasil dihapus.'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Akses ditolak! Anda tidak berwenang menghapus komentar ini.'];
            }
            header("Location: detail.php?id=" . $id);
            exit;
        }
    }
}

// Ambil semua komentar untuk asset ini
$commentsStmt = $pdo->prepare("
    SELECT c.*, u.username, u.full_name, u.avatar
    FROM comments c
    JOIN users u ON c.user_id = u.id
    WHERE c.asset_id = :asset_id
    ORDER BY c.created_at ASC
");
$commentsStmt->execute(['asset_id' => $id]);
$comments = $commentsStmt->fetchAll();

// Likes and check if user liked
$likesCount = getLikeCount($id);
$liked = isLoggedIn() ? hasUserLiked($id, $_SESSION['user_id']) : false;

require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4">
    <!-- Back to gallery link -->
    <div class="col-12">
        <a href="<?= getBaseUrl(); ?>" class="text-decoration-none fw-bold" style="color: var(--vivid-purple);">
            <i class="ri-arrow-left-line"></i> Kembali ke Galeri
        </a>
    </div>

    <!-- Image Column -->
    <div class="col-lg-7">
        <div class="detail-img-container text-center">
            <img src="<?= getImageUrl($asset['image_path']); ?>" alt="<?= e($asset['title']); ?>" class="detail-img img-fluid">
        </div>
    </div>

    <!-- Metadata & Interactions Column -->
    <div class="col-lg-5">
        <div class="glass-panel p-4 mb-4">
            <div class="mb-3">
                <span class="badge-category"><?= e($asset['category_name']); ?></span>
            </div>
            <h1 class="h1-display mb-3"><?= e($asset['title']); ?></h1>
            
            <!-- Creator Info -->
            <div class="d-flex align-items-center gap-3 my-3 py-3 border-top border-bottom">
                <?php if (!empty($asset['creator_avatar'])): ?>
                    <img src="<?= getImageUrl($asset['creator_avatar']); ?>" alt="Creator Avatar" class="rounded-circle border border-2" style="width: 48px; height: 48px; object-fit: cover;">
                <?php else: ?>
                    <img src="<?= getDefaultAvatar($asset['full_name'] ?? $asset['username']); ?>" alt="Creator Avatar" class="rounded-circle border border-2" style="width: 48px; height: 48px; object-fit: cover;">
                <?php endif; ?>
                <div>
                    <h2 class="h2-title mb-0" style="font-size: 14px;"><?= e($asset['full_name'] ?? $asset['username']); ?></h2>
                    <span class="caption-text text-muted">@<?= e($asset['username']); ?> &bull; Kreator Aset</span>
                </div>
            </div>

            <div class="mb-4">
                <h2 class="h2-title mb-2" style="font-size: 12px; opacity: 0.8;">Deskripsi Karya</h2>
                <p class="caption-text text-muted" style="line-height: 1.6; white-space: pre-line;"><?= e($asset['description'] ?? 'Tidak ada deskripsi untuk karya ini.'); ?></p>
                <div class="caption-text text-muted mt-2">
                    <i class="fa-regular fa-clock me-1"></i> Diposting pada: <?= date('d M Y, H:i', strtotime($asset['created_at'])); ?>
                </div>
            </div>

            <!-- Like Interaction Section -->
            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                <div class="fs-5 fw-bold" style="color: var(--deep-violet);">
                    <i class="ri-heart-3-fill text-danger me-1"></i> <span id="like-count-display"><?= $likesCount; ?></span> Apresiasi
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <form action="detail.php?id=<?= $id; ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="like">
                        <button type="submit" class="btn <?= $liked ? 'btn-accent-custom' : 'btn-outline-custom'; ?> d-inline-flex align-items-center gap-2 px-4 py-2">
                            <i class="<?= $liked ? 'ri-heart-3-fill' : 'ri-heart-3-line'; ?>"></i>
                            <?= $liked ? 'Suka' : 'Beri Like'; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?= getBaseUrl(); ?>login.php" class="btn btn-outline-custom d-inline-flex align-items-center gap-2 px-4 py-2">
                        <i class="ri-heart-3-line"></i> Login untuk Like
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Comments Thread Section -->
    <div class="col-12 col-lg-8 mt-5" id="komentar">
        <h2 class="h1-display mb-4">
            <i class="ri-chat-3-fill me-2" style="color: var(--vivid-purple);"></i>Diskusi Komunitas
        </h2>

        <!-- Comment List -->
        <div class="mb-4">
            <?php if (count($comments) > 0): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-card shadow-sm" id="comment-container-<?= $comment['id']; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($comment['avatar'])): ?>
                                    <img src="<?= getImageUrl($comment['avatar']); ?>" alt="User Avatar" class="comment-avatar">
                                <?php else: ?>
                                    <img src="<?= getDefaultAvatar($comment['full_name'] ?? $comment['username']); ?>" alt="User Avatar" class="comment-avatar">
                                <?php endif; ?>
                                <div>
                                    <h6 class="comment-author mb-0"><?= e($comment['full_name'] ?? $comment['username']); ?> <span class="text-muted fw-normal">@<?= e($comment['username']); ?></span></h6>
                                    <span class="comment-meta"><?= date('d M Y, H:i', strtotime($comment['created_at'])); ?></span>
                                </div>
                            </div>
                            
                            <!-- Comment Dropdown Actions (for comment owner or admin) -->
                            <?php if (isLoggedIn() && ($_SESSION['user_id'] == $comment['user_id'] || isAdmin())): ?>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <?php if ($_SESSION['user_id'] == $comment['user_id']): ?>
                                            <li>
                                                <button class="dropdown-item" onclick="toggleEditComment(<?= $comment['id']; ?>)">
                                                    <i class="fa-solid fa-pen text-warning me-2"></i> Edit
                                                </button>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <form action="detail.php?id=<?= $id; ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete_comment">
                                                <input type="hidden" name="comment_id" value="<?= $comment['id']; ?>">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="ri-delete-bin-fill me-2"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Comment display body -->
                        <div class="comment-text" id="comment-text-<?= $comment['id']; ?>">
                            <?= e($comment['comment']); ?>
                        </div>

                        <!-- Comment edit inline form -->
                        <?php if (isLoggedIn() && $_SESSION['user_id'] == $comment['user_id']): ?>
                            <form action="detail.php?id=<?= $id; ?>" method="POST" id="edit-form-<?= $comment['id']; ?>" class="d-none mt-3">
                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="edit_comment">
                                <input type="hidden" name="comment_id" value="<?= $comment['id']; ?>">
                                <div class="mb-2">
                                    <textarea name="comment" class="form-control form-control-custom" rows="2" required><?= e($comment['comment']); ?></textarea>
                                </div>
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3" onclick="toggleEditComment(<?= $comment['id']; ?>)">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary-custom rounded-3 px-3">Simpan</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 my-2 glass-panel text-muted">
                    <i class="fa-regular fa-comments fs-1 mb-2 d-block" style="opacity: 0.5;"></i>
                    Belum ada diskusi untuk karya ini. Jadilah yang pertama memberikan masukan!
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Comment Form -->
        <div class="glass-panel p-4 mb-5">
            <h5 class="fw-bold mb-3" style="color: var(--deep-violet);">Tinggalkan Komentar</h5>
            <?php if (isLoggedIn()): ?>
                <form action="detail.php?id=<?= $id; ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add_comment">
                    <div class="mb-3">
                        <textarea class="form-control form-control-custom" name="comment" rows="4" placeholder="Tulis komentar/masukan konstruktif Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary-custom px-4 py-2">
                        <i class="ri-send-plane-fill me-1"></i> Kirim Komentar
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center py-3">
                    <p class="text-muted">Anda harus login terlebih dahulu untuk dapat berkomentar.</p>
                    <a href="<?= getBaseUrl(); ?>login.php" class="btn btn-primary-custom px-4 py-2">
                        <i class="ri-login-box-line me-1"></i> Login Sekarang
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
/**
 * Toggle display mode edit komentar inline
 */
function toggleEditComment(commentId) {
    const textDiv = document.getElementById('comment-text-' + commentId);
    const form = document.getElementById('edit-form-' + commentId);
    
    if (textDiv.classList.contains('d-none')) {
        textDiv.classList.remove('d-none');
        form.classList.add('d-none');
    } else {
        textDiv.classList.add('d-none');
        form.classList.remove('d-none');
    }
}
</script>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>


