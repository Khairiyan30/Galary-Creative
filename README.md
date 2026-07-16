
# Galeri Aset Kreatif & Desain Karakter

Aplikasi web CRUD (Create, Read, Update, Delete) menggunakan **Native PHP** + **MySQL**, dengan antarmuka responsif (Bootstrap/Tailwind CSS). Studi kasus: galeri digital untuk komunitas kreatif (desainer grafis & pixel artist) — memamerkan karya, memberi like, dan berdiskusi lewat komentar.

Dijalankan secara **lokal menggunakan XAMPP**.

---

## 📁 Dokumen Terkait

| File | Isi |
|---|---|
| `PRD.md` | Product Requirements Document — latar belakang, tujuan, user stories, kebutuhan fungsional/non-fungsional |
| `PROJECT_SPEC.md` | Spesifikasi teknis — fitur, color palette, struktur database, rencana halaman |
| `database.sql` | Skema database MySQL + data awal (seed) |
| `koneksi.php` | Konfigurasi koneksi database untuk XAMPP |

## 🎨 Color Palette

| Warna | Hex |
|---|---|
| Dark Gray (Primary) | `#3E3636` |
| Brand Red (Secondary) | `#D72323` |
| Black (Accent) | `#000000` |
| Ice White (Background) | `#F5EDED` |

## 🛠️ Teknologi

- Native PHP (PDO)
- MySQL
- Bootstrap / Tailwind CSS
- XAMPP (Apache + MySQL + PHP) — lingkungan lokal

---

## 🚀 Instalasi & Menjalankan Proyek (Lokal via XAMPP)

### 1. Prasyarat
- [XAMPP](https://www.apachefriends.org/) sudah terinstal (berisi Apache, MySQL, PHP, phpMyAdmin).

### 2. Salin Proyek ke Folder `htdocs`

Letakkan seluruh folder proyek ini ke dalam direktori `htdocs` XAMPP:

- **Windows**: `C:\xampp\htdocs\galeri-kreatif\`
- **macOS**: `/Applications/XAMPP/htdocs/galeri-kreatif/`
- **Linux**: `/opt/lampp/htdocs/galeri-kreatif/`

Struktur folder yang disarankan:

```
htdocs/
└── galeri-kreatif/
    ├── admin/
    ├── assets/
    │   ├── css/
    │   ├── js/
    │   └── uploads/
    ├── includes/
    │   ├── koneksi.php
    │   ├── auth.php
    │   └── functions.php
    ├── database.sql
    ├── index.php
    ├── detail.php
    ├── login.php
    ├── register.php
    └── logout.php
```

### 3. Nyalakan Apache & MySQL

Buka **XAMPP Control Panel**, klik **Start** pada modul:
- `Apache`
- `MySQL`

### 4. Buat & Import Database

1. Buka browser, akses **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Klik menu **Import**.
3. Pilih file `database.sql` dari proyek ini.
4. Klik **Go/Import**.
   - Database `galeri_kreatif` beserta tabel (`users`, `categories`, `assets`, `likes`, `comments`) dan data contoh akan otomatis dibuat.

   > Alternatif via terminal:
   > ```bash
   > mysql -u root -p < database.sql
   > ```

### 5. Konfigurasi Koneksi Database

Buka file `koneksi.php` (letakkan di folder `includes/`), pastikan konfigurasi sesuai dengan MySQL di komputer kamu:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'galeri_kreatif');
define('DB_USER', 'root');
define('DB_PASS', ''); // default XAMPP kosong, sesuaikan jika kamu mengatur password
```

### 6. Jalankan Aplikasi

Buka browser dan akses:

```
http://localhost/galeri-kreatif/
```

Untuk dashboard admin:

```
http://localhost/galeri-kreatif/admin/dashboard.php
```

---

## 👤 Akun Contoh (Seed Data)

> Password pada `database.sql` masih berupa placeholder hash. Sebelum digunakan, buat hash asli menggunakan `password_hash()` di PHP, lalu update kolom `password` pada tabel `users`, contoh:
>
> ```php
> echo password_hash('admin123', PASSWORD_BCRYPT);
> ```

| Role | Username | Email |
|---|---|---|
| Admin | `admin` | admin@galerikreatif.test |
| User | `rasya_art` | rasya@galerikreatif.test |
| User | `dinapixel` | dina@galerikreatif.test |

## 🧩 Fitur Utama

**Admin**
- CRUD karya/aset (upload gambar, judul, deskripsi, kategori)
- CRUD kategori karya (Vektor, Tipografi, Pixel Art, dll)
- Kelola data pengguna
- Moderasi komentar

**User**
- Menjelajahi galeri & filter per kategori
- Memberi like pada karya
- Menulis, mengedit, menghapus komentar sendiri

## 🐛 Troubleshooting

| Masalah | Solusi |
|---|---|
| `Koneksi database gagal` | Pastikan Apache & MySQL sudah *Start* di XAMPP Control Panel |
| Halaman blank/error PHP | Cek `error_reporting` aktif di `koneksi.php`, lihat pesan error di browser |
| Gambar tidak tampil | Pastikan folder `assets/uploads/` ada dan memiliki permission tulis |
| Import `database.sql` gagal | Pastikan MySQL service aktif dan tidak ada database bernama sama yang konflik |

---

Dibuat untuk memenuhi tugas UTS Praktikum Pemrograman Web — implementasi CRUD Native PHP.

LINK DRIVE : https://drive.google.com/file/d/1AHwV7oYnc_CBUmwo-odKketOAAn-L9PU/view?usp=sharing
