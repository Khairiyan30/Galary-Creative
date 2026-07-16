<?php
/**
 * admin/categories.php
 * -------------------------------------------------------------
 * Halaman CRUD Kategori Karya (Admin Only).
 * POST handling MUST be done before any HTML output (admin_header).
 * -------------------------------------------------------------
 */

// ── Proses POST sebelum header dikirim ─────────────────────────
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$error = '';
$editCategory = null;

// Ambil ID Kategori untuk Mode Edit
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
if ($editId > 0) {
    $editStmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $editStmt->execute(['id' => $editId]);
    $editCategory = $editStmt->fetch();
}

// Proses POST Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validasi CSRF Token
    if (!validateCSRFToken($csrfToken)) {
        $error = 'Token keamanan tidak valid. Silakan coba kembali.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($action === 'add') {
            if (empty($name)) {
                $error = 'Nama kategori tidak boleh kosong.';
            } else {
                $slug = generateSlug($name);

                // Cek duplikasi slug/name
                $check = $pdo->prepare("SELECT 1 FROM categories WHERE slug = :slug OR name = :name");
                $check->execute(['slug' => $slug, 'name' => $name]);
                if ($check->fetch()) {
                    $error = 'Kategori dengan nama atau slug serupa sudah terdaftar.';
                } else {
                    $insertStmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)");
                    $insertStmt->execute([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description
                    ]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Kategori <strong>' . e($name) . '</strong> berhasil ditambahkan!'
                    ];
                    header("Location: categories.php");
                    exit;
                }
            }
        }

        if ($action === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $slugInput = trim($_POST['slug'] ?? '');
            
            if (empty($name)) {
                $error = 'Nama kategori tidak boleh kosong.';
            } else {
                $slug = !empty($slugInput) ? generateSlug($slugInput) : generateSlug($name);

                // Cek duplikasi untuk ID lain
                $check = $pdo->prepare("SELECT 1 FROM categories WHERE (slug = :slug OR name = :name) AND id != :id");
                $check->execute(['slug' => $slug, 'name' => $name, 'id' => $id]);
                if ($check->fetch()) {
                    $error = 'Kategori dengan nama atau slug serupa sudah terdaftar di kategori lain.';
                } else {
                    $updateStmt = $pdo->prepare("UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id");
                    $updateStmt->execute([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'id' => $id
                    ]);

                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => 'Kategori berhasil diperbarui!'
                    ];
                    header("Location: categories.php");
                    exit;
                }
            }
        }

        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);

            try {
                $deleteStmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
                $deleteStmt->execute(['id' => $id]);

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Kategori berhasil dihapus.'
                ];
                header("Location: categories.php");
                exit;
            } catch (PDOException $e) {
                // Tangani constraint asing (foreign key) RESTRICT secara elegan
                if ($e->getCode() == '23000') {
                    $error = 'Gagal menghapus! Kategori ini masih memiliki beberapa karya di dalamnya. Silakan hapus atau ubah kategori karya tersebut terlebih dahulu.';
                } else {
                    $error = 'Terjadi kesalahan sistem: ' . $e->getMessage();
                }
            }
        }
    }
}

// ── Include header (HTML output mulai di sini) ─────────────────
$pageTitle = "Kelola Kategori";
require_once __DIR__ . '/includes/admin_header.php';

// ── Ambil data seluruh kategori ───────────────────────────────
$stmt       = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="row g-4 mb-5">
    <!-- Form Tambah / Edit Kategori -->
    <div class="col-lg-4 order-lg-2">
        <div class="glass-panel p-4 sticky-top" style="top: 90px; z-index: 10;">
            <h4 class="fw-bold mb-3" style="color: var(--deep-violet);">
                <i class="<?= $editCategory ? 'ri-edit-box-fill' : 'ri-add-circle-fill'; ?> me-2"></i>
                <?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?>
            </h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-4 py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
                    <i class="ri-error-warning-fill"></i>
                    <div><?= e($error); ?></div>
                </div>
            <?php endif; ?>

            <form action="categories.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="<?= $editCategory ? 'edit' : 'add'; ?>">
                <?php if ($editCategory): ?>
                    <input type="hidden" name="id" value="<?= $editCategory['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="name" class="form-label-custom">Nama Kategori</label>
                    <input type="text" class="form-control form-control-custom" id="name" name="name" placeholder="Contoh: Pixel Art" value="<?= isset($_POST['name']) ? e($_POST['name']) : ($editCategory ? e($editCategory['name']) : ''); ?>" required>
                </div>

                <?php if ($editCategory): ?>
                    <div class="mb-3">
                        <label for="slug" class="form-label-custom">Slug (URL)</label>
                        <input type="text" class="form-control form-control-custom" id="slug" name="slug" placeholder="pixel-art" value="<?= isset($_POST['slug']) ? e($_POST['slug']) : e($editCategory['slug']); ?>">
                        <div class="form-text text-muted" style="font-size: 0.7rem;">Biarkan kosong untuk membuat slug otomatis dari nama.</div>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="description" class="form-label-custom">Deskripsi</label>
                    <textarea class="form-control form-control-custom" id="description" name="description" rows="3" placeholder="Deskripsi ringkas mengenai kategori ini..."><?= isset($_POST['description']) ? e($_POST['description']) : ($editCategory ? e($editCategory['description']) : ''); ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom w-100 py-2.5">
                        <i class="<?= $editCategory ? 'ri-checkbox-circle-fill' : 'ri-add-circle-fill'; ?> me-1"></i>
                        <?= $editCategory ? 'Simpan Perubahan' : 'Tambah Kategori'; ?>
                    </button>
                    <?php if ($editCategory): ?>
                        <a href="categories.php" class="btn btn-outline-secondary rounded-3 py-2.5 px-3">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Tabel Kategori -->
    <div class="col-lg-8 order-lg-1">
        <div class="glass-panel p-4">
            <h4 class="fw-bold mb-4" style="color: var(--deep-violet);">Daftar Kategori Karya</h4>

            <?php if (count($categories) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 30%;">Nama Kategori</th>
                                <th style="width: 25%;">Slug</th>
                                <th style="width: 25%;">Deskripsi</th>
                                <th style="width: 15%;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong class="text-dark"><?= e($cat['name']); ?></strong></td>
                                    <td><code>/<?= e($cat['slug']); ?></code></td>
                                    <td><span class="text-muted small"><?= e($cat['description'] ?? '-'); ?></span></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="categories.php?edit=<?= $cat['id']; ?>" class="btn btn-warning btn-sm rounded-3" title="Edit Kategori">
                                                <i class="ri-edit-box-fill"></i>
                                            </a>
                                            <form action="categories.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini secara permanen?');">
                                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $cat['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Hapus Kategori">
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
                <div class="text-center py-4 my-2 text-muted">
                    <i class="ri-price-tag-3-fill fs-1 d-block opacity-50 mb-2"></i>
                    Belum ada kategori yang ditambahkan.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/includes/admin_footer.php';
?>

