# Smart Thesis Repository

Repositori Karya Ilmiah Digital Berbasis Full-Stack (React & Laravel) dengan Mesin Pencari Cerdas AI Semantic Search & Vector Embedding.

---

## 1. Tentang Proyek

Smart Thesis Repository adalah platform repositori akademik digital modern yang dirancang untuk mempermudah pengelolaan, verifikasi, dan temu kembali karya ilmiah (skripsi) mahasiswa di perguruan tinggi.

Platform ini memadukan arsitektur decoupled antarmuka pengguna **React (Single Page Application)** dengan backend **Laravel RESTful API**. Keunggulan utama sistem ini terletak pada penerapan **Google Gemini Vector Embedding** dan algoritma **Hybrid Semantic Search** (kombinasi *Dense Vector Retrieval* dan *Lexical Keyword Matching*). Mahasiswa dapat menelusuri katalog skripsi menggunakan bahasa alami deskriptif berdasarkan konteks permasalahan atau metode penelitian, melampaui batasan pencarian kata kunci konvensional (*exact keyword matching*).

---

## 2. Masalah & Solusi

### Masalah yang Dihadapi
- **Pencarian Katalog Kaku:** Sistem pencarian konvensional di perpustakaan hanya mencocokkan string teks persis. Jika mahasiswa mencari topik dengan sinonim atau istilah konseptual yang berbeda dari judul, sistem kerap gagal menampilkan referensi yang sebenarnya relevan.
- **Arsip & Verifikasi Terfragmentasi:** Pengumpulan naskah PDF dan kode sumber penelitian sering kali dilakukan terpisah (via email atau form manual), menyulitkan pelacakan status dan riwayat evaluasi.
- **Statistik Pasif:** Pengelola perpustakaan kesulitan melihat tren tahunan karya ilmiah mahasiswa dan proporsi status naskah secara terpusat.

### Solusi yang Dibangun
- **AI Hybrid Semantic Search:** Mengubah abstrak dan topik skripsi menjadi representasi vektor berdimensi tinggi untuk dihitung nilai kedekatan maknanya menggunakan *Cosine Similarity* dan *Lexical Boost*.
- **Alur Kerja Terpadu (Academic Workflow):** Mahasiswa mengunggah naskah PDF dan arsip source code ZIP/RAR dalam satu pintu. Status naskah (`menunggu`, `disetujui`, `ditolak`) terlacak secara transparan bersama catatan revisi.
- **Otomasi Vektor AI:** Begitu Kepala Perpustakaan menyetujui naskah, backend secara otomatis membuat representasi vektor dokumen ke database tanpa proses sinkronisasi manual.
- **Dashboard Analitik Interaktif:** Visualisasi data statistik naskah per tahun dan sebaran status verifikasi secara real-time.

---

## 3. Teknologi yang Digunakan

### Frontend (React SPA)
- **Framework & Tooling:** React 18/19, Vite, TypeScript
- **Styling & UI Components:** Tailwind CSS, shadcn/ui, Radix UI Primitives, Lucide Icons
- **State Management & Data Fetching:** TanStack Query (React Query) dan Axios dengan interceptor Bearer Token
- **Formulir & Validasi:** React Hook Form dan Zod untuk skema validasi berkas dan input formulir
- **Visualisasi Data:** Recharts untuk diagram analitik pada dashboard
- **Routing:** React Router DOM dengan komponen pelindung rute peran pengguna

### Backend (Laravel REST API)
- **Framework Utama:** Laravel 12/13, PHP 8.3+
- **Arsitektur:** RESTful API, Service Layer Pattern, Form Request Validation, Eloquent API Resources
- **Autentikasi & Keamanan:** Laravel Sanctum (Bearer Token) dengan Role-Based Access Control (RBAC)
- **Kecerdasan Buatan (AI Engine):** Google Gemini Embedding API via paket `laravel/ai`
- **Basis Data:** MySQL / PostgreSQL
- **Testing & Standarisasi Kode:** Pest PHP untuk pengujian otomatis dan Laravel Pint untuk penegakan standar PSR-12

---

## 4. Fitur Berdasarkan Peran Pengguna

### A. Mahasiswa (`role: mahasiswa`)
- **Pencarian Semantik AI:** Mencari skripsi menggunakan bahasa alami deskriptif, dilengkapi badge persentase skor kemiripan (*similarity score*).
- **Katalog & Filter Repositori:** Menjelajahi skripsi yang telah disetujui dengan filter Program Studi, Kategori, dan Tahun Terbit.
- **Riwayat Pencarian:** Menyimpan jejak kueri pencarian dalam bentuk tombol klik cepat yang dapat dihapus satuan atau dibersihkan sekaligus.
- **Simpan Skripsi (Bookmark):** Menandai skripsi referensi favorit ke daftar simpanan pribadi.
- **Manajemen Skripsi Saya:** Mengunggah naskah PDF (hingga 20 MB) dan source code ZIP/RAR (hingga 50 MB), memantau status verifikasi, serta membaca catatan evaluasi dari verifikator.

### B. Kepala Perpustakaan (`role: kepala_perpustakaan`)
- **Dashboard Analitik:** Memantau metrik total skripsi, mahasiswa, naskah disetujui, antrean verifikasi, grafik tren publikasi per tahun, dan diagram proporsi status naskah.
- **Pusat Antrean Verifikasi:** Meninjau berkas naskah dan source code. Menyetujui naskah (*Approve*) yang langsung memicu pembuatan vektor AI, atau menolak (*Reject*) dengan catatan evaluasi.
- **Kelola Akun Mahasiswa:** Operasi tambah, ubah, dan hapus akun mahasiswa, serta tombol sakelar (*toggle*) status aktif yang otomatis mencabut token sesi login mahasiswa saat dinonaktifkan.
- **Kelola Data Master:** Manajemen data kategori topik skripsi dan program studi.

### C. Super Administrator (`role: super_admin`)
- **Hak Akses Seluruh Fitur Kepala Perpustakaan:** Mengelola repositori, verifikasi naskah, dan data mahasiswa.
- **Kelola Akun Administrator:** Hak eksklusif untuk menambah, mengubah, menonaktifkan, dan menghapus akun Kepala Perpustakaan.

---

## 5. Sorotan Rekayasa Teknis

### Mesin Pencarian Semantik Campuran (Hybrid Search Engine)
Sistem pencarian pada `SemanticSearchService` menerapkan pendekatan hibrida:
- **Pembersihan Bahasa Percakapan:** Menghapus kalimat pengantar bahasa Indonesia (seperti *"tolong carikan skripsi tentang"* atau *"lihat jurnal mengenai"*) menggunakan ekspresi reguler agar vektor kueri terfokus pada topik inti.
- **Stopwords Removal:** Mengekstrak kata kunci penting tanpa terdistraksi kata hubung umum.
- **Generasi Vektor Gemini:** Mengonversi teks kueri menjadi vektor float berdimensi tinggi menggunakan opsi `RETRIEVAL_QUERY`.
- **Kalkulasi Cosine Similarity:** Menghitung sudut kosinus kedekatan antara vektor kueri dengan vektor skripsi yang ada di database.
- **Lexical Keyword Boost:** Memberikan skor bonus ($+0.06$ hingga $+0.12$) apabila kata kunci kueri cocok secara harfiah pada judul, kategori, atau abstrak skripsi.
- **Penyaringan Dinamis:** Memangkas hasil di bawah ambang batas kemiripan ($0.60$) dan membatasi selisih jarak relatif terhadap skor tertinggi.

### Pipeline Otomasi Verifikasi
- Persetujuan skripsi dibungkus dalam transaksi database atomik (`DB::transaction`).
- Begitu naskah disetujui, sistem mengeksekusi `EmbeddingService::generate()` untuk menyusun metadata (judul, kategori, prodi, kata kunci, abstrak) dan menyimpannya ke tabel embeddings secara real-time.

### Keamanan & Revokasi Token Instan
- Menggunakan Laravel Sanctum dengan proteksi `RoleMiddleware`.
- Saat akun pengguna dinonaktifkan (`is_active = false`), sistem secara langsung mencabut seluruh personal access token pengguna tersebut.
- Interceptor Axios pada sisi klien mendeteksi status `401 Unauthorized` atau pesan akun nonaktif untuk segera membersihkan penyimpanan lokal dan mengarahkan kembali ke halaman login.

---

## 6. Daftar Endpoint RESTful API

### Autentikasi & Profil
- `POST /api/login` — Autentikasi pengguna & penerbitan Bearer Token (Publik)
- `GET /api/logout` — Pencabutan token sesi aktif (Terautentikasi)
- `GET /api/profil` — Mengambil data identitas pengguna aktif (Terautentikasi)

### Katalog Publik / Repositori
- `GET /api/repository/skripsi` — Daftar skripsi berstatus disetujui dengan filter dan pencarian (Terautentikasi)
- `GET /api/repository/skripsi/{id}` — Detail naskah skripsi beserta berkas lampiran (Terautentikasi)
- `GET /api/kategori` — Daftar master kategori skripsi (Publik)
- `GET /api/program-studi` — Daftar master program studi (Publik)

### Fitur Khusus Mahasiswa (`role: mahasiswa`)
- `POST /api/search/semantic` — Pencarian cerdas berbasis AI Semantic Search
- `GET /api/search-history` — Mengambil daftar riwayat pencarian
- `DELETE /api/search-history/{id}` — Menghapus satu item riwayat pencarian
- `DELETE /api/search-history/all` — Menghapus seluruh riwayat pencarian
- `GET /api/my-skripsi` — Mengambil daftar skripsi milik mahasiswa
- `POST /api/my-skripsi` — Mengunggah naskah skripsi baru beserta berkas PDF dan ZIP
- `GET /api/my-skripsi/{id}` — Melihat detail skripsi milik sendiri
- `PUT /api/my-skripsi/{id}` — Memperbarui data naskah atau mengganti berkas lampiran
- `DELETE /api/my-skripsi/{id}` — Menghapus naskah skripsi
- `GET /api/saved-skripsi` — Menampilkan daftar skripsi yang disimpan (bookmark)
- `POST /api/saved-skripsi/{id}` — Menyimpan skripsi ke daftar bookmark
- `DELETE /api/saved-skripsi/{id}` — Menghapus skripsi dari daftar bookmark

### Fitur Kepala Perpustakaan & Super Admin
- `GET /api/statistik` — Mengambil data agregasi untuk grafik dan metrik dashboard
- `GET /api/verifikasi/skripsi` — Menampilkan daftar antrean skripsi berstatus menunggu
- `GET /api/verifikasi/skripsi/{id}` — Menampilkan detail naskah untuk ditinjau
- `POST /api/verifikasi/skripsi/{id}/approve` — Menyetujui naskah & memicu pembuatan vektor AI
- `POST /api/verifikasi/skripsi/{id}/reject` — Menolak naskah disertai catatan alasan
- `GET /api/mahasiswa` — Mengambil daftar seluruh akun mahasiswa
- `POST /api/mahasiswa` — Mendaftarkan akun mahasiswa baru
- `GET /api/mahasiswa/{id}` — Melihat profil akun mahasiswa
- `PUT /api/mahasiswa/{id}` — Memperbarui data akun mahasiswa
- `PATCH /api/mahasiswa/{id}/toggle-status` — Mengaktifkan/menonaktifkan akun mahasiswa
- `DELETE /api/mahasiswa/{id}` — Menghapus akun mahasiswa
- `POST /api/kategori` — Menambah kategori baru
- `PUT /api/kategori/{id}` — Memperbarui kategori
- `DELETE /api/kategori/{id}` — Menghapus kategori
- `POST /api/program-studi` — Menambah program studi baru
- `PUT /api/program-studi/{id}` — Memperbarui program studi
- `DELETE /api/program-studi/{id}` — Menghapus program studi

### Fitur Khusus Super Admin (`role: super_admin`)
- `GET /api/admin` — Menampilkan daftar seluruh akun admin/kepala perpustakaan
- `POST /api/admin` — Menambahkan akun admin baru
- `GET /api/admin/{id}` — Melihat detail akun admin
- `PUT /api/admin/{id}` — Memperbarui profil akun admin
- `PATCH /api/admin/{id}/toggle-status` — Mengaktifkan/menonaktifkan akun admin
- `DELETE /api/admin/{id}` — Menghapus akun admin

---

## 7. Akun Bawaan untuk Pengujian (Seeder)

Seluruh akun di bawah ini menggunakan kata sandi bawaan: `password`

- **Super Administrator:** `super.admin@universitas.ac.id` (Akses penuh termasuk kelola akun admin)
- **Kepala Perpustakaan:** `kepala.perpus@universitas.ac.id` (Verifikasi naskah, statistik, kelola mahasiswa & master data)
- **Mahasiswa 1 (Teknik Informatika):** `ahmad.fauzi@mahasiswa.ac.id` (NIM: `210101001`)
- **Mahasiswa 2 (Sistem Informasi):** `siti.nurhaliza@mahasiswa.ac.id` (NIM: `210201002`)

---

## 8. Panduan Instalasi & Menjalankan Proyek

### Prasyarat Sistem
- PHP versi 8.3 atau lebih tinggi
- Composer versi 2.x
- Basis Data MySQL atau PostgreSQL
- Kunci API Google Gemini (untuk fitur Semantic Search)

### Langkah Pemasangan Backend

1. Kloning repositori dan masuk ke direktori proyek:
```bash
git clone https://github.com/username/repository-perpustakaan.git
cd repository-perpustakaan
```

2. Pasang dependensi PHP:
```bash
composer install
```

3. Siapkan berkas konfigurasi lingkungan:
```bash
cp .env.example .env
php artisan key:generate
```

4. Konfigurasikan koneksi basis data dan kunci AI pada berkas `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=repository_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

GEMINI_API_KEY=kunci_api_gemini_anda
```

5. Jalankan migrasi basis data beserta data awal (seeder):
```bash
php artisan migrate --seed
```

6. Buat tautan simbolis ke folder penyimpanan berkas publik:
```bash
php artisan storage:link
```

7. Jalankan server pengembangan backend:
```bash
php artisan serve
```
Backend API akan berjalan pada `http://localhost:8000`.

---

## 9. Pengujian & Standarisasi Kode

- Menjalankan pengujian otomatis fitur dan otorisasi dengan Pest:
```bash
php artisan test --compact
```

- Memeriksa dan merapikan format kode dengan Laravel Pint:
```bash
vendor/bin/pint --format agent
```

---

## 10. Lisensi

Proyek ini dikembangkan untuk tujuan portofolio dan berlisensi di bawah [MIT License](LICENSE).
