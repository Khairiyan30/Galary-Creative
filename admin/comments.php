<?php
/**
 * admin/comments.php
 * -------------------------------------------------------------
 * Halaman Moderasi Komentar (Admin Only).
 * POST handling MUST be done before any HTML output (admin_header).
 * -------------------------------------------------------------
 */

// ── Proses POST sebelum header dikirim ─────────────────────────
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action']     ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrfToken)) {
        $error = 'Token keamanan tidak valid. Silakan coba kembali.';
    } else {
        $commentId = (int)($_POST['id'] ?? 0);

        if ($action === 'delete') {
            $stmt = $pdo->prepare("SELECT 1 FROM comments WHERE id = :id");
            $stmt->execute(['id' => $commentId]);

            if ($stmt->fetch()) {
                $deleteStmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
                $deleteStmt->execute(['id' => $commentId]);

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Komentar berhasil dimoderasi (dihapus secara permanen).'
                ];
                header("Location: comments.php");
                exit;
            } else {
                $error = 'Komentar tidak ditemukan.';
            }
        }
    }
}

// ── Include header (HTML output mulai di sini) ─────────────────
$pageTitle = "Moderasi Komentar";
require_once __DIR__ . '/includes/admin_header.php';

// ── Ambil semua komentar ────────────────────────────────────────
$stmt = $pdo->query("
    SELECT c.*, u.username, u.full_name, a.title as asset_title, a.id as asset_id
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN assets a ON c.asset_id = a.id
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetchAll();
?>

<div class="glass-panel p-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--deep-violet);">Moderasi Diskusi / Komentar</h3>
            <p class="text-muted small mb-0">Total komentar terdata: <strong><?= count($comments); ?></strong> pesan masukan.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-4 py-2 px-3 mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="ri-error-warning-fill"></i>
            <div><?= e($error); ?></div>
        </div>
    <?php endif; ?>

    <?php if (count($comments) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Pengguna</th>
                        <th style="width: 25%;">Target Karya</th>
                        <th style="width: 30%;">Isi Komentar</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 10%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($comments as $comment): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <strong class="text-dark d-block"><?= e($comment['full_name'] ?? '-'); ?></strong>
                                <span class="text-muted small">@<?= e($comment['username']); ?></span>
                            </td>
                            <td>
                                <a href="<?= getBaseUrl(); ?>detail.php?id=<?= $comment['asset_id']; ?>" target="_blank" class="fw-semibold text-primary text-decoration-none">
                                    <?= e($comment['asset_title']); ?>
                                </a>
                            </td>
                            <td>
                                <div class="text-muted small" style="white-space: pre-line; max-height: 80px; overflow-y: auto;">
                                    <?= e($comment['comment']); ?>
                                </div>
                            </td>
                            <td>
                                <span class="small text-muted"><?= date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <form action="comments.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini dari sistem?');">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $comment['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Hapus Komentar">
                                            <i class="ri-delete-bin-fill"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted text-center py-4">Belum ada data komentar.</p>
    <?php endif; ?>
</div>

<?php 
require_once __DIR__ . '/includes/admin_footer.php';
?>

