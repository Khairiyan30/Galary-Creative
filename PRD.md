# Product Requirements Document (PRD)
## Galeri Aset Kreatif & Desain Karakter

| | |
|---|---|
| **Jenis Dokumen** | Product Requirements Document |
| **Proyek** | Galeri Aset Kreatif & Desain Karakter |
| **Mata Kuliah** | Praktikum Pemrograman Web (UTS) |
| **Platform** | Native PHP + MySQL (XAMPP, lokal) |
| **Versi** | 1.0 |

---

## 1. Latar Belakang

Komunitas kreatif digital — desainer grafis, pembuat lettering/tipografi, hingga pixel artist yang membuat skin karakter game — sering kali tidak memiliki wadah terpusat untuk memamerkan karya sekaligus menerima apresiasi dan masukan dari sesama kreator. **Galeri Aset Kreatif & Desain Karakter** dibangun untuk menjawab kebutuhan tersebut: sebuah galeri digital sederhana tempat karya dipamerkan, diberi apresiasi (like), dan didiskusikan melalui komentar.

## 2. Tujuan Produk

1. Menyediakan platform CRUD sederhana bagi admin untuk mengelola aset visual, kategori karya, dan data pengguna.
2. Memberikan pengalaman menjelajah galeri yang responsif (desktop & mobile) bagi pengunjung/pengguna.
3. Mendorong interaksi komunitas lewat fitur like dan komentar konstruktif.
4. Memenuhi kebutuhan akademik: implementasi CRUD lengkap dengan Native PHP + MySQL.

## 3. Target Pengguna

| Peran | Deskripsi |
|---|---|
| **Admin** | Pengelola galeri: mengunggah/mengedit/menghapus karya, mengatur kategori, mengelola akun pengguna, moderasi komentar. |
| **User (Kreator/Pengunjung)** | Anggota komunitas yang mendaftar, menjelajahi galeri, memberi like, dan berkomentar pada karya. |

## 4. Ruang Lingkup (Scope)

### 4.1 Termasuk dalam Scope
- Autentikasi (register, login, logout) untuk role `admin` dan `user`.
- CRUD karya/aset (judul, deskripsi, gambar, kategori, status publish/draft).
- CRUD kategori karya (Vektor, Tipografi, Pixel Art, Ilustrasi Karakter, dst).
- Manajemen data pengguna oleh admin (lihat, nonaktifkan, hapus).
- Like/unlike karya (satu like per pengguna per karya).
- Komentar pada karya (tambah, edit milik sendiri, hapus milik sendiri; admin dapat moderasi/hapus semua).
- Antarmuka responsif menggunakan Bootstrap atau Tailwind CSS.
- Dashboard admin dengan ringkasan statistik dasar (jumlah karya, user, komentar).

### 4.2 Di Luar Scope
- Sistem pembayaran/monetisasi karya.
- Notifikasi real-time (push/email).
- Multi-bahasa.
- Deployment ke server produksi (proyek berjalan lokal via XAMPP).

## 5. User Stories

| ID | Sebagai | Saya ingin | Agar |
|---|---|---|---|
| US-01 | Admin | menambahkan karya baru beserta kategori | karya bisa dipamerkan di galeri |
| US-02 | Admin | mengedit/menghapus karya | data galeri tetap akurat dan relevan |
| US-03 | Admin | mengelola kategori karya | karya terklasifikasi dengan rapi |
| US-04 | Admin | melihat & mengelola data pengguna | komunitas tetap sehat dan tertib |
| US-05 | Admin | menghapus komentar yang tidak pantas | menjaga kualitas diskusi |
| User | mendaftar dan login | dapat memberi like dan berkomentar |
| US-06 | User | menjelajahi galeri dan memfilter per kategori | menemukan karya sesuai minat |
| US-07 | User | memberi like pada karya | mengapresiasi kreator |
| US-08 | User | menulis komentar konstruktif | memberi masukan kepada kreator |
| US-09 | User | mengedit/menghapus komentar sendiri | memperbaiki masukan yang sudah ditulis |

## 6. Kebutuhan Fungsional

| Kode | Kebutuhan |
|---|---|
| F-01 | Sistem dapat melakukan CRUD penuh terhadap tabel `assets` |
| F-02 | Sistem dapat melakukan CRUD penuh terhadap tabel `categories` |
| F-03 | Sistem dapat menampilkan, mengubah status, dan menghapus data `users` |
| F-04 | Sistem mencegah satu user memberi like lebih dari satu kali pada karya yang sama |
| F-05 | Sistem mencatat dan menampilkan komentar berurutan berdasarkan waktu |
| F-06 | Sistem membedakan akses admin dan user berdasarkan kolom `role` |
| F-07 | Sistem menampilkan galeri publik tanpa perlu login (read-only) |
| F-08 | Like dan komentar hanya dapat dilakukan oleh user yang sudah login |

## 7. Kebutuhan Non-Fungsional

| Kode | Kebutuhan |
|---|---|
| NF-01 | Antarmuka responsif pada resolusi desktop dan mobile (Bootstrap/Tailwind) |
| NF-02 | Password pengguna disimpan dalam bentuk hash (bcrypt) |
| NF-03 | Query database menggunakan prepared statement (PDO/MySQLi) untuk mencegah SQL Injection |
| NF-04 | Aplikasi berjalan pada lingkungan lokal XAMPP (Apache + MySQL + PHP) tanpa dependensi eksternal wajib |
| NF-05 | Struktur kode terorganisir (folder `admin/`, `includes/`, `assets/`) agar mudah dikembangkan |

## 8. Desain & Branding

**Color Palette:**

| Warna | Hex | Peran |
|---|---|---|
| Dark Gray | `#3E3636` | Primary |
| Brand Red | `#D72323` | Secondary |
| Black | `#000000` | Accent |
| Ice White | `#F5EDED` | Background |

Detail lengkap ada di `PROJECT_SPEC.md`.

## 9. Teknologi

- **Backend**: Native PHP (PDO)
- **Database**: MySQL (dijalankan via XAMPP)
- **Frontend**: Bootstrap atau Tailwind CSS
- **Lingkungan**: Lokal, folder `htdocs` XAMPP

## 10. Metrik Keberhasilan (untuk konteks tugas)

- Seluruh fitur CRUD berjalan tanpa error pada demo lokal.
- Antarmuka tetap rapi saat diakses dari perangkat desktop maupun mobile (resize browser/emulator).
- Video presentasi mampu menunjukkan alur CRUD end-to-end sesuai ketentuan tugas.

## 11. Risiko & Batasan

| Risiko | Mitigasi |
|---|---|
| Upload file gambar besar memperlambat aplikasi | Batasi ukuran & tipe file saat upload |
| Lupa mengatur koneksi database sesuai environment lokal | Sediakan `koneksi.php` terpusat & mudah dikonfigurasi |
| Duplikasi like akibat tidak ada constraint | Sudah ditangani via `UNIQUE KEY` pada tabel `likes` |

## 12. Referensi Dokumen Terkait

- `PROJECT_SPEC.md` — spesifikasi fitur & struktur detail
- `database.sql` — skema & seed database
- `koneksi.php` — konfigurasi koneksi database (XAMPP)
- `README.md` — panduan instalasi & menjalankan proyek
