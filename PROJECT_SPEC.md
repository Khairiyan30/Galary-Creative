# Galeri Aset Kreatif & Desain Karakter

Dokumen spesifikasi proyek untuk Ujian Tengah Semester — Praktikum Pemrograman Web
Studi kasus: platform galeri digital untuk komunitas kreatif (desainer grafis, pixel artist/pembuat skin karakter game) yang dibangun dengan **Native PHP CRUD**, **MySQL**, dan **Bootstrap/Tailwind CSS** (antarmuka responsif desktop & mobile).

---

## 1. Deskripsi Aplikasi

**Galeri Aset Kreatif & Desain Karakter** adalah aplikasi web yang memungkinkan komunitas kreatif memamerkan karya visual mereka secara digital — mulai dari karya vektor, tipografi, hingga pixel art (misalnya skin karakter game). Pengunjung dapat menjelajahi galeri, memberi apresiasi berupa *like*, dan berdiskusi lewat kolom komentar. Admin bertanggung jawab mengelola aset yang dipamerkan, kategori karya, dan data pengguna.

## 2. Aktor & Skenario

### 2.1 Admin
- Login ke dashboard admin.
- **Create**: menambahkan karya/aset baru (upload gambar, judul, deskripsi, kategori).
- **Read**: melihat daftar seluruh karya, kategori, pengguna, like, dan komentar.
- **Update**: mengedit data karya dan kategori.
- **Delete**: menghapus karya, kategori, komentar (moderasi), atau akun pengguna.
- Mengelola kategori karya (contoh: *Vektor*, *Tipografi*, *Pixel Art*, *Ilustrasi Karakter*).
- Mengelola data pengguna (lihat, nonaktifkan/hapus akun).

### 2.2 Pengguna (User)
- Registrasi & login.
- Menjelajahi/menikmati pameran karya secara digital (galeri publik, filter per kategori).
- Memberi apresiasi melalui tombol **Like** (satu like per pengguna per karya).
- Menulis **komentar/masukan konstruktif** pada suatu karya.
- Mengelola komentar miliknya sendiri (edit/hapus).

## 3. Fitur Minimum

| Modul | CRUD | Aktor |
|---|---|---|
| Karya/Aset | Create, Read, Update, Delete | Admin |
| Kategori | Create, Read, Update, Delete | Admin |
| Pengguna | Read, Update, Delete | Admin |
| Like | Create, Delete (toggle) | User |
| Komentar | Create, Read, Update, Delete | User |
| Autentikasi | Register, Login, Logout | Admin & User |

## 4. Teknologi

- **Backend**: Native PHP (PDO/MySQLi)
- **Database**: MySQL
- **Frontend**: Bootstrap **atau** Tailwind CSS (pilih salah satu), responsif desktop & mobile
- **Library PHP pendukung**: sesuai kebutuhan (contoh: PHPMailer, intervention/image untuk resize gambar — opsional)

## 5. Color Palette (UI/UX)

Tema visual bernuansa ungu kreatif, cocok untuk galeri karya desain.

| Warna | Hex | Peran yang disarankan |
|---|---|---|
| ![#3E3636](https://via.placeholder.com/15/3E3636/3E3636.png) Dark Gray | `#3E3636` | Primary — navbar, tombol utama, heading penting |
| ![#D72323](https://via.placeholder.com/15/D72323/D72323.png) Brand Red | `#D72323` | Secondary — hover state, aksen tombol, badge kategori |
| ![#000000](https://via.placeholder.com/15/000000/000000.png) Black | `#000000` | Accent — highlight, tombol sekunder |
| ![#F5EDED](https://via.placeholder.com/15/F5EDED/F5EDED.png) Ice White | `#F5EDED` | Background — latar halaman, card, area konten |

Saran penggunaan:
- Teks di atas `#3E3636`/`#D72323` gunakan warna putih (`#F5EDED` atau `#FFFFFF`) agar kontras terjaga.
- Teks utama di atas latar `#F5EDED` gunakan `#3E3636` atau `#000000` agar tetap terbaca.
- `#D72323` cocok untuk elemen interaktif (ikon like, tombol CTA).

## 6. Struktur Database (Ringkasan)

Tabel utama: `users`, `categories`, `assets`, `likes`, `comments`.
Relasi:
- `users` 1—N `assets` (opsional, jika admin adalah *uploader*)
- `categories` 1—N `assets`
- `users` N—N `assets` melalui `likes`
- `users` 1—N `comments`, `assets` 1—N `comments`

Detail struktur tabel dan skema lengkap tersedia pada file `database.sql`.

## 7. Rencana Halaman

**Publik / User**
- `index.php` — Landing/galeri publik (grid karya, filter kategori)
- `detail.php?id=` — Detail karya (gambar besar, deskripsi, like, komentar)
- `register.php`, `login.php`, `logout.php`
- `profile.php` — Karya yang di-like, komentar pengguna

**Admin**
- `admin/dashboard.php` — Ringkasan statistik (jumlah karya, user, komentar)
- `admin/assets.php` — CRUD karya
- `admin/categories.php` — CRUD kategori
- `admin/users.php` — Kelola pengguna
- `admin/comments.php` — Moderasi komentar

## 8. Struktur Folder (Contoh)

```
galeri-kreatif/
├── admin/
│   ├── dashboard.php
│   ├── assets.php
│   ├── categories.php
│   ├── users.php
│   └── comments.php
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/
├── includes/
│   ├── db.php
│   ├── auth.php
│   └── functions.php
├── database.sql
├── index.php
├── detail.php
├── login.php
├── register.php
└── logout.php
```

## 9. Catatan Pengumpulan Tugas

- Laporan PDF: deskripsi project, screenshot fitur, screenshot database, URL GitHub, URL video.
- Video presentasi (maks. 10 menit, wajah tampil, alur CRUD & daftar fitur dijelaskan), unggah ke YouTube *Unlisted*.
- Deskripsi video mencantumkan URL GitHub berisi seluruh kode + dump database (`.sql`).
