<?php
/**
 * admin/users.php
 * -------------------------------------------------------------
 * Halaman Kelola Data Pengguna (Admin Only).
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
        $targetUserId = (int)($_POST['id'] ?? 0);

        if ($targetUserId === (int)$_SESSION['user_id']) {
            $error = 'Tindakan ditolak! Anda tidak dapat menonaktifkan atau menghapus akun Anda sendiri.';
        } else {

            if ($action === 'toggle_status') {
                $stmt = $pdo->prepare("SELECT status, username FROM users WHERE id = :id");
                $stmt->execute(['id' => $targetUserId]);
                $user = $stmt->fetch();

                if ($user) {
                    $newStatus  = ($user['status'] === 'active') ? 'inactive' : 'active';
                    $updateStmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
                    $updateStmt->execute(['status' => $newStatus, 'id' => $targetUserId]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Status akun @<strong>' . e($user['username']) . '</strong> berhasil diubah menjadi <strong>' . $newStatus . '</strong>.'
                    ];
                    header("Location: users.php");
                    exit;
                } else {
                    $error = 'Pengguna tidak ditemukan.';
                }
            }

            if ($action === 'delete') {
                $stmt = $pdo->prepare("SELECT avatar, username FROM users WHERE id = :id");
                $stmt->execute(['id' => $targetUserId]);
                $user = $stmt->fetch();

                if ($user) {
                    if (!empty($user['avatar'])) {
                        $avatarFile = __DIR__ . '/../assets/' . $user['avatar'];
                        if (file_exists($avatarFile)) {
                            unlink($avatarFile);
                        }
                    }

                    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
                    $deleteStmt->execute(['id' => $targetUserId]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Akun @<strong>' . e($user['username']) . '</strong> berhasil dihapus secara permanen dari sistem.'
                    ];
                    header("Location: users.php");
                    exit;
                } else {
                    $error = 'Pengguna tidak ditemukan.';
                }
            }
        }
    }
}

// ── Include header (HTML output mulai di sini) ─────────────────
$pageTitle = "Kelola Pengguna";
require_once __DIR__ . '/includes/admin_header.php';

// ── Ambil data pengguna ────────────────────────────────────────
$stmt  = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="glass-panel p-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--deep-violet);">Kelola Data Pengguna</h2>
            <p class="text-muted small mb-0">Total terdaftar: <strong><?= count($users); ?></strong> akun pengguna.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger rounded-4 py-2 px-3 mb-4 d-flex align-items-center gap-2" role="alert">
            <i class="ri-error-warning-fill flex-shrink-0"></i>
            <div><?= e($error); ?></div>
        </div>
    <?php endif; ?>

    <?php if (count($users) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 10%;">Avatar</th>
                        <th style="width: 25%;">Nama Pengguna</th>
                        <th style="width: 25%;">E-mail</th>
                        <th style="width: 10%;">Peran</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 15%;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($users as $usr): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>
                                <?php if (!empty($usr['avatar'])): ?>
                                    <img src="<?= getImageUrl($usr['avatar']); ?>" alt="Avatar" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= getDefaultAvatar($usr['full_name'] ?? $usr['username']); ?>" alt="Avatar" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong class="text-dark d-block"><?= e($usr['full_name'] ?? '-'); ?></strong>
                                <span class="text-muted small">@<?= e($usr['username']); ?></span>
                            </td>
                            <td><?= e($usr['email']); ?></td>
                            <td>
                                <span class="badge <?= $usr['role'] === 'admin' ? 'bg-dark' : 'bg-primary'; ?> rounded-pill px-3 py-2" style="font-size: 11px; font-weight: 600;">
                                    <?= strtoupper(e($usr['role'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $usr['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?> rounded-pill px-3 py-2" style="font-size: 11px; font-weight: 600;">
                                    <?= strtoupper(e($usr['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Button Toggle Status -->
                                    <form action="users.php" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="id" value="<?= $usr['id']; ?>">
                                        <button type="submit"
                                            class="btn btn-sm <?= $usr['status'] === 'active' ? 'btn-outline-danger' : 'btn-outline-success'; ?> rounded-3"
                                            <?= ($usr['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>
                                            title="<?= $usr['status'] === 'active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun'; ?>">
                                            <i class="<?= $usr['status'] === 'active' ? 'ri-lock-fill' : 'ri-lock-unlock-fill'; ?>"></i>
                                        </button>
                                    </form>

                                    <!-- Button Hapus Akun -->
                                    <form action="users.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun @<?= e($usr['username']); ?> beserta seluruh komentar dan like karyanya secara permanen?');">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $usr['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm rounded-3"
                                            <?= ($usr['id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>
                                            title="Hapus Akun">
                                            <i class="ri-delete-bin-fill"></i>
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
        <p class="text-muted text-center py-4">Belum ada data pengguna.</p>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/admin_footer.php';
?>
