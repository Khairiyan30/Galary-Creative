<?php
/**
 * admin/dashboard.php
 * -------------------------------------------------------------
 * Dashboard Admin Panel - Ringkasan Statistik Dasar.
 * -------------------------------------------------------------
 */
$pageTitle = "Dashboard Utama";
require_once __DIR__ . '/includes/admin_header.php';

// Ambil data statistik dari database
$totalAssets = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalComments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$totalLikes = $pdo->query("SELECT COUNT(*) FROM likes")->fetchColumn();

// Ambil 5 komentar terbaru
$recentCommentsStmt = $pdo->query("
    SELECT c.*, u.username, a.title as asset_title
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN assets a ON c.asset_id = a.id
    ORDER BY c.created_at DESC
    LIMIT 5
");
$recentComments = $recentCommentsStmt->fetchAll();

// Ambil 5 pengguna terbaru terdaftar
$recentUsersStmt = $pdo->query("
    SELECT * FROM users
    ORDER BY created_at DESC
    LIMIT 5
");
$recentUsers = $recentUsersStmt->fetchAll();
?>

<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="glass-panel p-4" style="background: linear-gradient(135deg, rgba(62, 54, 54, 0.05), rgba(215, 35, 35, 0.08));">
            <h2 class="fw-extrabold mb-1" style="color: var(--deep-violet);">Selamat Datang, <?= e($_SESSION['full_name']); ?>!</h2>
            <p class="text-muted mb-0">Kelola konten galeri, moderasi interaksi, dan pantau perkembangan komunitas kreatif Anda dari satu panel kontrol.</p>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-title">Total Karya</div>
            <div class="stat-card-value"><?= $totalAssets; ?></div>
            <i class="ri-image-fill stat-card-icon"></i>
            <a href="assets.php" class="small text-decoration-none mt-2 d-inline-block fw-semibold text-primary">Kelola Karya &rarr;</a>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-title">Kategori</div>
            <div class="stat-card-value"><?= $totalCategories; ?></div>
            <i class="ri-price-tag-3-fill stat-card-icon"></i>
            <a href="categories.php" class="small text-decoration-none mt-2 d-inline-block fw-semibold text-primary">Kelola Kategori &rarr;</a>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-title">Pengguna</div>
            <div class="stat-card-value"><?= $totalUsers; ?></div>
            <i class="ri-group-fill stat-card-icon"></i>
            <a href="users.php" class="small text-decoration-none mt-2 d-inline-block fw-semibold" style="color: var(--brand-red);">Kelola Pengguna &rarr;</a>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-title">Komentar</div>
            <div class="stat-card-value"><?= $totalComments; ?></div>
            <i class="ri-chat-3-fill stat-card-icon"></i>
            <a href="comments.php" class="small text-decoration-none mt-2 d-inline-block fw-semibold" style="color: var(--brand-red);">Kelola Komentar &rarr;</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- List Komentar Terbaru -->
    <div class="col-lg-7">
        <div class="glass-panel p-4">
            <h4 class="fw-bold mb-3" style="color: var(--deep-violet);">
                <i class="ri-chat-3-fill me-2" style="color: var(--brand-red);"></i>Komentar Terbaru
            </h4>
            
            <?php if (count($recentComments) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Kreator</th>
                                <th>Karya</th>
                                <th>Komentar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentComments as $comment): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold">@<?= e($comment['username']); ?></span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 120px;" title="<?= e($comment['asset_title']); ?>">
                                            <?= e($comment['asset_title']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="<?= e($comment['comment']); ?>">
                                            <?= e($comment['comment']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <form action="comments.php" method="POST" onsubmit="return confirm('Hapus komentar ini secara permanen?');">
                                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $comment['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm rounded-3">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-2">
                    <a href="comments.php" class="btn btn-sm btn-primary-custom">Semua Komentar &rarr;</a>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Belum ada komentar terbaru.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- List Pengguna Baru -->
    <div class="col-lg-5">
        <div class="glass-panel p-4">
            <h4 class="fw-bold mb-3" style="color: var(--deep-violet);">
                <i class="ri-group-fill me-2" style="color: var(--brand-red);"></i>Pengguna Baru
            </h4>
            
            <?php if (count($recentUsers) > 0): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recentUsers as $usr): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-light py-3 px-0">
                            <div class="d-flex align-items-center gap-3">
                                <?php if (!empty($usr['avatar'])): ?>
                                    <img src="<?= getImageUrl($usr['avatar']); ?>" alt="User Avatar" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= getDefaultAvatar($usr['full_name'] ?? $usr['username']); ?>" alt="User Avatar" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= e($usr['full_name'] ?? $usr['username']); ?></h6>
                                    <span class="text-muted small">@<?= e($usr['username']); ?> &bull; <?= e($usr['role']); ?></span>
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-<?= $usr['status'] === 'active' ? 'dark' : 'secondary'; ?> px-2 py-1">
                                <?= e($usr['status']); ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="text-end mt-3">
                    <a href="users.php" class="btn btn-sm btn-primary-custom">Semua Pengguna &rarr;</a>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Belum ada pengguna terdaftar.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/admin_footer.php';
?>


