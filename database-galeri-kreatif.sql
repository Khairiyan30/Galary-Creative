-- =====================================================================
-- Database: galeri_kreatif
-- Studi Kasus: Galeri Aset Kreatif & Desain Karakter
-- Deskripsi : Skema database untuk aplikasi CRUD Native PHP + MySQL
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `galeri_kreatif`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `galeri_kreatif`;

-- ---------------------------------------------------------------------
-- Tabel: users
-- Menyimpan akun admin dan pengguna (member komunitas kreatif)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`   VARCHAR(50)  NOT NULL UNIQUE,
  `email`      VARCHAR(100) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `full_name`  VARCHAR(100) DEFAULT NULL,
  `avatar`     VARCHAR(255) DEFAULT NULL,
  `role`       ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  `status`     ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabel: categories
-- Kategori karya, contoh: Vektor, Tipografi, Pixel Art, Ilustrasi Karakter
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(50) NOT NULL UNIQUE,
  `slug`        VARCHAR(60) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabel: assets
-- Karya/aset visual yang dipamerkan di galeri
-- ---------------------------------------------------------------------
CREATE TABLE `assets` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`       INT UNSIGNED NOT NULL COMMENT 'Admin/uploader pemilik karya',
  `category_id`   INT UNSIGNED NOT NULL,
  `title`         VARCHAR(150) NOT NULL,
  `description`   TEXT DEFAULT NULL,
  `image_path`    VARCHAR(255) NOT NULL,
  `status`        ENUM('published', 'draft') NOT NULL DEFAULT 'published',
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_assets_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_assets_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabel: likes
-- Apresiasi pengguna terhadap suatu karya (1 user hanya bisa like 1x per karya)
-- ---------------------------------------------------------------------
CREATE TABLE `likes` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `asset_id`   INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_likes_asset`
    FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_likes_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `uq_like_user_asset` (`asset_id`, `user_id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabel: comments
-- Diskusi / masukan konstruktif pengguna pada suatu karya
-- ---------------------------------------------------------------------
CREATE TABLE `comments` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `asset_id`   INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `comment`    TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_comments_asset`
    FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
-- Seed Data (Contoh Data Awal)
-- =====================================================================

-- Password contoh: "admin123" dan "user123" (hash bcrypt, ganti sesuai kebutuhan)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin',    'admin@galerikreatif.test', '$2y$10$abcdefghijklmnopqrstuvexampleHashAdmin123456789012', 'Admin Galeri', 'admin'),
('rasya_art','rasya@galerikreatif.test', '$2y$10$abcdefghijklmnopqrstuvexampleHashUser1234567890123',  'Rasya Pratama', 'user'),
('dinapixel','dina@galerikreatif.test',  '$2y$10$abcdefghijklmnopqrstuvexampleHashUser2234567890123',  'Dina Kusuma', 'user');

INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Vektor',              'vektor',              'Karya ilustrasi vektor, logo, dan grafis digital'),
('Tipografi',           'tipografi',           'Karya lettering, font custom, dan desain tipografi'),
('Pixel Art',           'pixel-art',           'Karya piksel, termasuk skin karakter game'),
('Ilustrasi Karakter',  'ilustrasi-karakter',  'Desain dan ilustrasi karakter original');

INSERT INTO `assets` (`user_id`, `category_id`, `title`, `description`, `image_path`) VALUES
(1, 3, 'Skin Ksatria Piksel',   'Desain skin karakter ksatria bergaya pixel art 32x32.', 'uploads/skin-ksatria.png'),
(2, 1, 'Logo Vektor Kopi Senja','Logo vektor minimalis untuk brand kedai kopi.',        'uploads/logo-kopi-senja.png'),
(3, 2, 'Lettering "Merdeka"',   'Karya tipografi bertema kemerdekaan dengan gaya brush.', 'uploads/lettering-merdeka.png');

INSERT INTO `likes` (`asset_id`, `user_id`) VALUES
(1, 2),
(1, 3),
(2, 1);

INSERT INTO `comments` (`asset_id`, `user_id`, `comment`) VALUES
(1, 2, 'Detail pikselnya rapi banget, warnanya juga enak dilihat!'),
(1, 3, 'Coba tambah shading di bagian armor biar lebih 3D.'),
(2, 3, 'Logonya clean, cocok buat brand kopi yang santai.');
