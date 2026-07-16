# Skrip Presentasi: Proyek Web "Galeri Kreatif"

Dokumen ini berisi draf narasi yang dapat Anda gunakan saat mempresentasikan proyek aplikasi web **Galeri Kreatif (Galeri Desain Grafis)**, mulai dari sesi pembuka hingga sesi penutup.

---

## 1. Sesi Pembuka (Opening)

**"Assalamualaikum Warahmatullahi Wabarakatuh / Selamat pagi / siang semuanya."**

"Yang terhormat Bapak/Ibu Dosen/Penguji, serta rekan-rekan sekalian. Pada kesempatan kali ini, saya sangat antusias untuk mempresentasikan hasil karya saya, yaitu sebuah proyek pengembangan aplikasi berbasis web yang saya beri nama **Galeri Kreatif** atau Platform Portofolio Desain Grafis.

Aplikasi ini saya rancang khusus sebagai sebuah ruang apresiasi dan interaksi bagi komunitas kreator visual, mulai dari desainer grafis, *pixel artist*, hingga tipografer. Terinspirasi oleh antarmuka modern bergaya Pinterest, tujuan utama dari proyek ini adalah memberikan wadah komersial di mana pengguna bisa mengunggah hasil karya terbaik mereka, saling memberikan *like*, dan meninggalkan komentar yang membangun."

---

## 2. Pengenalan Teknologi & Desain (Tech Stack & UI/UX)

"Dalam membangun Galeri Kreatif, saya sangat menekankan pada keseimbangan antara fungsi dan estetika *User Interface* (UI).

- **Teknologi Backend:** Saya menggunakan **PHP Native** berorientasi objek sederhana (PDO) yang dipadukan dengan *database* **MySQL**, sehingga performanya ringan dan kuerinya aman berkat implementasi perlindungan CSRF (*Cross-Site Request Forgery*) dan injeksi SQL.
- **Teknologi Frontend:** Untuk memastikan desainnya rapi dan responsif, saya mengandalkan **HTML5, CSS Vanilla,** dan *framework* **Bootstrap 5**.
- **Identitas Visual:** Untuk memberikan identitas (*branding*) yang kuat, saya menggunakan palet warna dominan **Merah (`#D72323`)** dan **Dark Gray (`#3E3636`)** yang dipadukan dengan tipografi modern (*Plus Jakarta Sans*). Selain itu, untuk ikonografinya saya sepenuhnya mengadopsi pustaka **RemixIcon** agar terlihat bersih dan profesional."

---

## 3. Demo Fitur-Fitur Utama (Core Features Showcase)

*(Sambil mendemonstrasikan layar aplikasi Anda / Share Screen)*

"Mari kita lihat langsung fitur-fitur yang telah saya bangun:

**a. Autentikasi yang Aman & Ramah Pengguna**
Ketika pengguna baru datang, mereka akan disambut dengan halaman Login dan Registrasi. Halaman ini dirancang sangat bersih tanpa gangguan. Saya juga menyematkan fitur kecil yang sangat krusial, yaitu kemampuan untuk menampilkan atau menyembunyikan (*hide/show*) *password* menggunakan ikon mata, agar pengguna tidak salah ketik. Selain itu, *password* mereka di-*hash* menggunakan bcrypt demi keamanan tingkat tinggi.

**b. Dasbor Profil Pengguna**
Setelah masuk, pengguna diarahkan ke halamannya sendiri. Di sini, sistem memiliki *fallback* foto profil. Artinya, jika pengguna belum memiliki foto profil, sistem otomatis membuatkan avatar berbasis SVG sesuai inisial nama mereka. Pengguna bisa sewaktu-waktu mengganti foto tersebut, bahkan mengganti nama lengkap dan *username* mereka melalui panel kontrol di sini.

**c. Tata Letak Galeri (Masonry Grid) & Interaksi**
Berpindah ke Beranda, inilah inti dari aplikasi ini. Karya-karya visual ditampilkan menggunakan tata letak *Masonry* layaknya Pinterest, di mana kartu-kartu karya akan menyesuaikan tinggi gambarnya tanpa merusak proporsi. 
Di sini, ada fitur sosial: pengguna bisa mengeklik ikon hati (Like) secara *real-time* dan membuka detail karya untuk memberikan komentar.

**d. Admin Panel (Manajemen Data)**
Sistem ini memisahkan hak akses antara *User* biasa dan *Admin*. Jika saya masuk sebagai admin, saya memiliki kendali penuh melalui Dasbor Admin. Admin dapat mengelola kategori desain (misalnya *Pixel Art*, *Vector*, *Typography*), mengelola pengguna (menghapus akun yang melanggar), hingga menghapus komentar *spam*. Dan yang paling menarik, setiap interaksi penghapusan data penting di panel admin ini sudah diamankan menggunakan jendela konfirmasi estetik dari *library* **SweetAlert2**, sehingga mencegah penghapusan yang tidak disengaja.

**e. Siklus CRUD (Create, Read, Update, Delete)**
Sebagai contoh penerapan operasi CRUD secara langsung, mari kita coba menambahkan sebuah karya baru (Create). Karya tersebut akan langsung tampil di galeri (Read). Kemudian, kita coba ubah judul atau kategorinya melalui panel ini (Update), dan jika dirasa tidak sesuai, karya tersebut dapat kita hapus beserta konfirmasinya (Delete). Semua alur ini bekerja dengan lancar dan aman.

**f. Tinjauan Database di XAMPP (phpMyAdmin)**
*(Buka tab browser phpMyAdmin di localhost/phpmyadmin)*
Untuk membuktikan bahwa seluruh data ini tersimpan secara nyata, mari kita lihat *database* di balik layar melalui phpMyAdmin bawaan XAMPP. Nama *database* kita adalah `galeri_desain_grafis`. Di sini terlihat struktur relasional antar-tabel. Misalnya, pada tabel `users`, kita bisa melihat data pengguna beserta *password* yang sudah diacak (di-*hash*). Begitu pula dengan tabel `assets` yang menyimpan path direktori gambar, tabel `categories`, `comments`, dan `likes`. Semuanya saling terkait menggunakan *Foreign Key* untuk menjamin integritas data."

---

## 4. Evaluasi & Tantangan (Challenges & Solutions)

"Tentu saja dalam proses pengembangannya saya menemui beberapa tantangan. Salah satunya adalah mengelola *layout Masonry* yang dinamis dengan menggunakan CSS murni (Kolom CSS) tanpa perlu *library* JavaScript yang berat. Selain itu, saya juga melakukan migrasi dari *FontAwesome* ke *RemixIcon* di puluhan file secara sistematis demi mencapai keseragaman desain. 

Tantangan lainnya adalah mendesain relasi _database_ (seperti relasi tabel `users`, `works/assets`, `likes`, dan `comments`) agar *query*-nya optimal saat menghitung jumlah *Like* yang sifatnya dinamis."

---

## 5. Sesi Penutup (Closing)

"Sebagai kesimpulan, **Galeri Kreatif** bukan hanya sekadar tugas aplikasi web biasa, melainkan sebuah purwarupa (_prototype_) platform komunitas yang mengutamakan keamanan dari sisi kode, kelengkapan fitur manajemen, dan juga estetika *user experience* kelas komersial.

Ke depannya, aplikasi ini sangat terbuka untuk dikembangkan lebih jauh, misalnya dengan menambahkan fitur *Follow User*, atau notifikasi langsung (*Real-time Notification*).

Demikian presentasi dari saya. Terima kasih banyak atas waktu dan perhatian Bapak/Ibu dan rekan-rekan sekalian. Apabila ada pertanyaan, saran, maupun masukan, saya persilakan. 

**Wassalamualaikum Warahmatullahi Wabarakatuh / Selamat pagi / siang.**"
