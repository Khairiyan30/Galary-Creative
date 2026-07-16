<?php
/**
 * admin/assets.php
 * -------------------------------------------------------------
 * Halaman CRUD Karya/Aset Galeri (Admin Only).
 * POST handling MUST be done before any HTML output (admin_header).
 * -------------------------------------------------------------
 */

// ── Proses POST sebelum header dikirim ─────────────────────────
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$error  = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id     = isset($_GET['id'])     ? (int)$_GET['id'] : 0;

// Fetch all categories for form select drop-down
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories     = $categoriesStmt->fetchAll();

// -------------------------------------------------------------
// POST Form Handling
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!validateCSRFToken($csrfToken)) {
        $error = 'Token keamanan tidak valid. Silakan coba kembali.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'published');

        if ($postAction === 'add') {
            if (empty($title) || $categoryId <= 0) {
                $error = 'Judul dan Kategori wajib dipilih/diisi.';
            } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'File gambar wajib diunggah untuk karya baru.';
            } else {
                // Upload gambar
                $uploadedPath = uploadImage($_FILES['image'], __DIR__ . '/../assets/uploads');
                if ($uploadedPath !== false) {
                    // Simpan ke database
                    $insertStmt = $pdo->prepare("
                        INSERT INTO assets (user_id, category_id, title, description, image_path, status) 
                        VALUES (:user_id, :category_id, :title, :description, :image_path, :status)
                    ");
                    $insertStmt->execute([
                        'user_id' => $_SESSION['user_id'],
                        'category_id' => $categoryId,
                        'title' => $title,
                        'description' => $description,
                        'image_path' => $uploadedPath,
                        'status' => $status
                    ]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Karya baru <strong>' . e($title) . '</strong> berhasil dipublikasikan!'
                    ];
                    header("Location: assets.php");
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

        if ($postAction === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            
            // Ambil data lama dari DB
            $stmt = $pdo->prepare("SELECT * FROM assets WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $oldAsset = $stmt->fetch();

            if (!$oldAsset) {
                $error = 'Data karya tidak ditemukan.';
            } elseif (empty($title) || $categoryId <= 0) {
                $error = 'Judul dan Kategori wajib diisi.';
            } else {
                $imagePath = $oldAsset['image_path'];
                $uploadSuccess = true;

                // Cek jika admin mengganti gambar
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadedPath = uploadImage($_FILES['image'], __DIR__ . '/../assets/uploads');
                    if ($uploadedPath !== false) {
                        // Hapus file lama jika ada
                        $oldFileAbsolute = __DIR__ . '/../assets/' . $oldAsset['image_path'];
                        if (file_exists($oldFileAbsolute)) {
                            unlink($oldFileAbsolute);
                        }
                        $imagePath = $uploadedPath;
                    } else {
                        $uploadSuccess = false;
                        if (isset($_SESSION['flash_message'])) {
                            $error = $_SESSION['flash_message']['text'];
                            unset($_SESSION['flash_message']);
                        } else {
                            $error = 'Gagal mengunggah gambar baru.';
                        }
                    }
                }

                if ($uploadSuccess) {
                    // Update database
                    $updateStmt = $pdo->prepare("
                        UPDATE assets 
                        SET category_id = :category_id, title = :title, description = :description, image_path = :image_path, status = :status 
                        WHERE id = :id
                    ");
                    $updateStmt->execute([
                        'category_id' => $categoryId,
                        'title' => $title,
                        'description' => $description,
                        'image_path' => $imagePath,
                        'status' => $status,
                        'id' => $id
                    ]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Karya <strong>' . e($title) . '</strong> berhasil diperbarui!'
                    ];
                    header("Location: assets.php");
                    exit;
                }
            }
        }

        if ($postAction === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            
            // Ambil data untuk menghapus file di harddisk
            $stmt = $pdo->prepare("SELECT image_path FROM assets WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $imagePath = $stmt->fetchColumn();

            if ($imagePath) {
                $fileAbsolute = __DIR__ . '/../assets/' . $imagePath;
                if (file_exists($fileAbsolute)) {
                    unlink($fileAbsolute);
                }

                $deleteStmt = $pdo->prepare("DELETE FROM assets WHERE id = :id");
                $deleteStmt->execute(['id' => $id]);

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Karya berhasil dihapus secara permanen.'
                ];
                header("Location: assets.php");
                exit;
            } else {
                $error = 'Data karya tidak ditemukan.';
            }
        }
    }
}

// ── Include header (HTML output mulai di sini) ─────────────────
$pageTitle = "Kelola Karya/Aset";
require_once __DIR__ . '/includes/admin_header.php';

// ── GET Views (List / Add / Edit) ─────────────────────────────
if ($action === 'add'):
?>
    <!-- FORM TAMBAH ASSET -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="glass-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0" style="color: var(--deep-violet);"><i class="ri-add-circle-fill text-primary me-2"></i>Tambah Karya Baru</h3>
                    <a href="assets.php" class="btn btn-outline-secondary btn-sm"><i class="ri-arrow-left-line"></i> Kembali</a>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-4 py-2 px-3 mb-4 d-flex align-items-center gap-2" role="alert">
                        <i class="ri-error-warning-fill"></i>
                        <div><?= e($error); ?></div>
                    </div>
                <?php endif; ?>

                <form action="assets.php?action=add" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label for="title" class="form-label-custom">Judul Karya</label>
                        <input type="text" class="form-control form-control-custom" id="title" name="title" placeholder="Contoh: Skin Ksatria Pixel Art" value="<?= isset($_POST['title']) ? e($_POST['title']) : ''; ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label-custom">Kategori</label>
                            <select class="form-select form-control-custom" id="category_id" name="category_id" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?= e($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label-custom">Status Publikasi</label>
                            <select class="form-select form-control-custom" id="status" name="status" required>
                                <option value="published" <?= (isset($_POST['status']) && $_POST['status'] === 'published') ? 'selected' : ''; ?>>Published (Tampil)</option>
                                <option value="draft" <?= (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : ''; ?>>Draft (Sembunyikan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label-custom">Deskripsi Karya</label>
                        <textarea class="form-control form-control-custom" id="description" name="description" rows="5" placeholder="Tuliskan cerita di balik karya, tool yang digunakan, atau detail lainnya..." required><?= isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label-custom">Gambar Karya</label>
                        <input type="file" class="form-control form-control-custom" id="image" name="image" accept="image/*" required>
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Mendukung JPG, PNG, GIF, WEBP. Maksimal 2MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-3 mt-2">
                        <i class="ri-upload-cloud-2-fill me-1"></i> Publikasikan Karya
                    </button>
                </form>
            </div>
        </div>
    </div>

<?php 
elseif ($action === 'edit' && $id > 0):
    // Ambil detail asset lama
    $stmt = $pdo->prepare("SELECT * FROM assets WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $asset = $stmt->fetch();

    if (!$asset) {
        $_SESSION['flash_message'] = ['type' => 'danger', 'text' => 'Karya tidak ditemukan.'];
        header("Location: assets.php");
        exit;
    }
?>
    <!-- FORM EDIT ASSET -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="glass-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0" style="color: var(--deep-violet);"><i class="ri-edit-box-fill text-warning me-2"></i>Ubah Karya</h3>
                    <a href="assets.php" class="btn btn-outline-secondary btn-sm"><i class="ri-arrow-left-line"></i> Kembali</a>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-4 py-2 px-3 mb-4 d-flex align-items-center gap-2" role="alert">
                        <i class="ri-error-warning-fill"></i>
                        <div><?= e($error); ?></div>
                    </div>
                <?php endif; ?>

                <form action="assets.php?action=edit&id=<?= $id; ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <div class="mb-3">
                        <label for="title" class="form-label-custom">Judul Karya</label>
                        <input type="text" class="form-control form-control-custom" id="title" name="title" placeholder="Masukkan judul karya" value="<?= isset($_POST['title']) ? e($_POST['title']) : e($asset['title']); ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label-custom">Kategori</label>
                            <select class="form-select form-control-custom" id="category_id" name="category_id" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id']; ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) || ($asset['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?= e($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label-custom">Status Publikasi</label>
                            <select class="form-select form-control-custom" id="status" name="status" required>
                                <option value="published" <?= (isset($_POST['status']) && $_POST['status'] === 'published') || ($asset['status'] === 'published') ? 'selected' : ''; ?>>Published (Tampil)</option>
                                <option value="draft" <?= (isset($_POST['status']) && $_POST['status'] === 'draft') || ($asset['status'] === 'draft') ? 'selected' : ''; ?>>Draft (Sembunyikan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label-custom">Deskripsi Karya</label>
                        <textarea class="form-control form-control-custom" id="description" name="description" rows="5" required><?= isset($_POST['description']) ? e($_POST['description']) : e($asset['description']); ?></textarea>
                    </div>

                    <!-- Display Current Image -->
                    <div class="mb-3">
                        <label class="form-label-custom d-block">Gambar Karya Saat Ini</label>
                        <img src="<?= getImageUrl($asset['image_path']); ?>" alt="Current Image" class="img-thumbnail mb-2" style="max-height: 200px;">
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label-custom">Ganti Gambar <span class="text-muted fw-normal">(Biarkan kosong jika tidak ingin mengubah)</span></label>
                        <input type="file" class="form-control form-control-custom" id="image" name="image" accept="image/*">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Mendukung JPG, PNG, GIF, WEBP. Maksimal 2MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom w-100 py-3 mt-2">
                        <i class="ri-save-3-fill me-1"></i> Simpan Perubahan Karya
                    </button>
                </form>
            </div>
        </div>
    </div>

<?php 
else:
    // -------------------------------------------------------------
    // LIST VIEW
    // -------------------------------------------------------------
    // Query untuk mengambil list asset beserta kategori & uploader
    $assetsStmt = $pdo->query("
        SELECT a.*, c.name as category_name, u.username
        FROM assets a
        JOIN categories c ON a.category_id = c.id
        JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC
    ");
    $assets = $assetsStmt->fetchAll();
?>
    <div class="glass-panel p-4 mb-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="color: var(--deep-violet);">Kelola Galeri Karya</h3>
                <p class="text-muted small mb-0">Total karya yang terdata di sistem: <strong><?= count($assets); ?></strong> karya.</p>
            </div>
            <a href="assets.php?action=add" class="btn btn-primary-custom">
                <i class="ri-add-circle-fill me-1"></i> Tambah Karya Baru
            </a>
        </div>

        <?php if (count($assets) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 10%;">Gambar</th>
                            <th style="width: 25%;">Judul Karya</th>
                            <th style="width: 15%;">Kategori</th>
                            <th style="width: 15%;">Uploader</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 15%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($assets as $ast): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <img src="<?= getImageUrl($ast['image_path']); ?>" alt="Thumbnail" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong class="text-dark d-block"><?= e($ast['title']); ?></strong>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Dibuat: <?= date('d/m/Y H:i', strtotime($ast['created_at'])); ?></span>
                                </td>
                                <td>
                                    <span class="badge-category"><?= e($ast['category_name']); ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">@<?= e($ast['username']); ?></span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 bg-<?= $ast['status'] === 'published' ? 'success' : 'secondary'; ?>" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">
                                        <?= e($ast['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?= getBaseUrl(); ?>detail.php?id=<?= $ast['id']; ?>" target="_blank" class="btn btn-light btn-sm border rounded-3" title="Buka Detail Publik">
                                            <i class="ri-eye-line"></i>
                                        </a>
                                        <a href="assets.php?action=edit&id=<?= $ast['id']; ?>" class="btn btn-warning btn-sm rounded-3" title="Edit Karya">
                                            <i class="ri-edit-box-line"></i>
                                        </a>
                                        <form action="assets.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus karya ini secara permanen dari sistem?');">
                                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $ast['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Hapus Karya">
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
            <div class="text-center py-5 text-muted">
                <i class="ri-image-fill fs-1 d-block opacity-40 mb-2"></i>
                Belum ada karya yang diunggah.
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php 
require_once __DIR__ . '/includes/admin_footer.php';
?>


