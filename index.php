<?php
/**
 * index.php
 * -------------------------------------------------------------
 * Halaman Utama / Galeri Publik.
 * -------------------------------------------------------------
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/functions.php';

// Handle Inline Like Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_like') {
    if (isLoggedIn()) {
        $assetId = (int)($_POST['asset_id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if (hasUserLiked($assetId, $userId)) {
            $stmt = $pdo->prepare("DELETE FROM likes WHERE asset_id = :asset_id AND user_id = :user_id");
            $stmt->execute(['asset_id' => $assetId, 'user_id' => $userId]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO likes (asset_id, user_id) VALUES (:asset_id, :user_id)");
            $stmt->execute(['asset_id' => $assetId, 'user_id' => $userId]);
        }
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}

$pageTitle = "Jelajahi Galeri";
require_once __DIR__ . '/includes/header.php';

// Ambil input filter dan pencarian
$categorySlug = trim($_GET['category'] ?? '');
$search = trim($_GET['search'] ?? '');

// Ambil semua kategori untuk navigasi filter
$categoriesStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesStmt->fetchAll();

// Bangun query filter asset
$query = "
    SELECT a.*, c.name as category_name, c.slug as category_slug, u.username, u.full_name
    FROM assets a
    JOIN categories c ON a.category_id = c.id
    JOIN users u ON a.user_id = u.id
    WHERE a.status = 'published'
";
$params = [];

if (!empty($categorySlug)) {
    $query .= " AND c.slug = :category_slug";
    $params['category_slug'] = $categorySlug;
}

if (!empty($search)) {
    $query .= " AND (a.title LIKE :search_title OR a.description LIKE :search_desc)";
    $params['search_title'] = "%" . $search . "%";
    $params['search_desc']  = "%" . $search . "%";
}

$query .= " ORDER BY a.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$assets = $stmt->fetchAll();
?>

<?php if (!isLoggedIn()): ?>
<!-- Hero Banner Section -->
<section class="hero-section text-center text-lg-start">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title animate__animated animate__fadeInLeft">
                    Pamerkan Karya Kreatif & <span>Desain Karakter</span> Anda
                </h1>
                <p class="hero-subtitle animate__animated animate__fadeInLeft animate__delay-1s">
                    Tempat berkumpulnya para desainer grafis, tipografer, pixel artist, dan pembuat skin karakter game untuk saling berbagi inspirasi, apresiasi, dan masukan.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 animate__animated animate__fadeInLeft animate__delay-2s">
                    <a href="<?= getBaseUrl(); ?>register.php" class="btn btn-primary-custom px-4 py-3 fs-5">
                        <i class="ri-rocket-fill me-1"></i> Mulai Gabung
                    </a>
                    <a href="#galeri" class="btn btn-outline-custom px-4 py-3 fs-5">
                        <i class="ri-eye-fill me-1"></i> Jelajahi Karya
                    </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <!-- Floating geometric graphics / visual decoration -->
                <div class="glass-panel p-3 shadow-lg text-center position-relative animate__animated animate__zoomIn" style="border-radius: 30px; background: rgba(255,255,255,0.7); overflow: visible !important;">
                    <img src="https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=500&q=80" alt="Creative Illustration" class="img-fluid rounded-4 shadow" style="border-radius: 20px;">
                    <div class="position-absolute badge bg-white text-dark shadow-sm py-2 px-3 d-flex align-items-center gap-2" style="bottom: 30px; right: -20px; border-radius: 50px;">
                        <i class="ri-star-fill text-warning fs-5"></i>
                        <span class="fw-bold">Komunitas Kreatif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Main Gallery Section -->
<div class="container px-0" id="galeri">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h2 class="h1-display text-start mb-2">Galeri Inspirasi Seni</h2>
            <p class="caption-text mb-0">Temukan, sukai, dan diskusikan karya visual terbaik dari para kreator lokal.</p>
        </div>
    </div>

    <!-- Category Filters Navigation -->
    <div class="category-filter-nav">
        <a href="index.php<?= !empty($search) ? '?search=' . urlencode($search) : ''; ?>" class="category-filter-btn <?= empty($categorySlug) ? 'active' : ''; ?>">
            Semua Karya
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="index.php?category=<?= e($cat['slug']); ?><?= !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="category-filter-btn <?= ($categorySlug === $cat['slug']) ? 'active' : ''; ?>">
                <?= e($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Masonry Board Grid List -->
    <?php if (count($assets) > 0): ?>
        <div class="pin-columns mb-5">
            <?php foreach ($assets as $asset): ?>
                <?php 
                $likesCount = getLikeCount($asset['id']);
                $commentsCount = getCommentCount($asset['id']);
                $liked = isLoggedIn() ? hasUserLiked($asset['id'], $_SESSION['user_id']) : false;
                ?>
                <div class="pin-item">
                    <div class="card-asset">
                        <div class="card-asset-img-wrapper">
                            <img src="<?= getImageUrl($asset['image_path']); ?>" alt="<?= e($asset['title']); ?>" class="card-asset-img">
                            <div class="card-asset-overlay">
                                <a href="detail.php?id=<?= $asset['id']; ?>" class="btn btn-accent-custom btn-sm w-100 fw-bold">
                                    <i class="ri-eye-fill me-1"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                        <div class="card-asset-body">
                            <div class="mb-2">
                                <span class="badge-category"><?= e($asset['category_name']); ?></span>
                            </div>
                            <h3 class="card-asset-title">
                                <a href="detail.php?id=<?= $asset['id']; ?>">
                                    <?= e($asset['title']); ?>
                                </a>
                            </h3>
                            <p class="caption-text mb-3" style="font-weight: 400;">
                                oleh <span class="fw-bold" style="color: var(--deep-violet);">@<?= e($asset['username']); ?></span>
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="d-flex gap-2">
                                    <form action="" method="POST" class="d-inline m-0">
                                        <input type="hidden" name="action" value="toggle_like">
                                        <input type="hidden" name="asset_id" value="<?= $asset['id']; ?>">
                                        <button type="submit" class="stat-item border-0 bg-transparent <?= $liked ? 'active' : ''; ?>">
                                            <i class="<?= $liked ? 'ri-heart-3-fill text-danger' : 'ri-heart-3-line'; ?>"></i> <?= $likesCount; ?>
                                        </button>
                                    </form>
                                    <a href="detail.php?id=<?= $asset['id']; ?>#komentar" class="stat-item text-decoration-none">
                                        <i class="ri-chat-3-line"></i> <?= $commentsCount; ?>
                                    </a>
                                </div>
                                <a href="detail.php?id=<?= $asset['id']; ?>" class="caption-text text-decoration-none fw-bold" style="color: var(--vivid-purple);">
                                    Buka <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-5 my-5 glass-panel">
            <i class="ri-palette-fill text-muted display-1 mb-3" style="opacity: 0.3;"></i>
            <h3 class="fw-bold" style="color: var(--deep-violet);">Belum Ada Karya</h3>
            <p class="caption-text px-3 mb-3">
                <?= (!empty($search) || !empty($categorySlug)) 
                    ? "Tidak dapat menemukan karya yang cocok dengan pencarian atau filter Anda." 
                    : "Belum ada karya yang diunggah ke galeri ini saat ini."; ?>
            </p>
            <?php if (!empty($search) || !empty($categorySlug)): ?>
                <a href="index.php" class="btn btn-primary-custom">
                    <i class="ri-refresh-line"></i> Reset Filter
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php 
require_once __DIR__ . '/includes/footer.php';
?>

