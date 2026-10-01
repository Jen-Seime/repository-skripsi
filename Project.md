Buatkan desain dan implementasi database untuk sebuah sistem **Repository Skripsi Berbasis Web dengan AI Semantic Search**.

Sistem memiliki 2 role utama:

1. **Mahasiswa**
2. **Kepala Perpustakaan**

Gunakan teknologi:

* Backend: Laravel
* API: Laravel REST API
* Database: MySQL
* Frontend: React + TypeScript
* AI: Embedding Model untuk Semantic Search
* Penyimpanan file: Laravel Storage
* Authentication: Laravel Sanctum

## Struktur relasi utama

Gunakan struktur berikut:

```text
users
  │
  └── mahasiswa
          │
          └── skripsi
                │
                ├── kategori
                ├── program_studi
                ├── embeddings
                └── verifikasi

users (kepala perpustakaan)
  │
  └── verifikasi_skripsi

mahasiswa
  │
  ├── saved_skripsi
  └── search_histories
```

## 1. Users

Tabel `users` digunakan untuk authentication dan menyimpan semua akun pengguna.

Kolom minimal:

* id
* name
* email
* password
* role
* timestamps

Role:

```text
mahasiswa
kepala_perpustakaan
```

Satu user memiliki satu role.

---

## 2. Mahasiswa

Tabel `mahasiswa` menyimpan informasi akademik mahasiswa.

Kolom:

* id
* user_id
* nim
* program_studi_id
* angkatan
* timestamps

Relasi:

```text
users 1 : 1 mahasiswa
program_studi 1 : N mahasiswa
mahasiswa 1 : N skripsi
mahasiswa 1 : N saved_skripsi
mahasiswa 1 : N search_histories
```

---

## 3. Program Studi

Tabel `program_studi`.

Kolom:

* id
* kode
* nama
* timestamps

Contoh:

```text
TI  = Teknik Informatika
SI  = Sistem Informasi
MI  = Manajemen Informatika
```

Relasi:

```text
program_studi 1 : N mahasiswa
program_studi 1 : N skripsi
```

---

## 4. Kategori

Tabel `kategori`.

Digunakan untuk mengelompokkan topik skripsi.

Kolom:

* id
* nama
* slug
* timestamps

Contoh:

```text
Artificial Intelligence
Internet of Things
Sistem Informasi
Web Development
Mobile Development
Data Mining
UI/UX
Jaringan Komputer
```

Relasi:

```text
kategori 1 : N skripsi
```

---

## 5. Skripsi

Tabel `skripsi` adalah tabel utama repository.

Kolom:

* id
* mahasiswa_id
* program_studi_id
* kategori_id
* judul
* abstrak
* kata_kunci
* tahun
* status
* timestamps

Status:

```text
menunggu
disetujui
ditolak
```

Relasi:

```text
mahasiswa 1 : N skripsi
program_studi 1 : N skripsi
kategori 1 : N skripsi
skripsi 1 : N skripsi_files
skripsi 1 : N embeddings
skripsi 1 : N verifikasi_skripsi
skripsi 1 : N saved_skripsi
```

---

## 6. Skripsi Files

Buat tabel `skripsi_files` untuk menyimpan semua file yang berkaitan dengan skripsi.

Jangan menyimpan file binary langsung di MySQL.

File disimpan menggunakan Laravel Storage, sedangkan database hanya menyimpan metadata file.

Kolom:

* id
* skripsi_id
* jenis
* nama_file
* path
* ukuran
* mime_type
* timestamps

Jenis file:

```text
skripsi
source_code
dokumentasi
dataset
presentasi
lainnya
```

Contoh:

```text
Skripsi.pdf
source-code.zip
dataset.xlsx
dokumentasi.pdf
presentasi.pptx
```

Relasi:

```text
skripsi 1 : N skripsi_files
```

Contoh struktur storage:

```text
storage/app/public/
└── skripsi/
    └── {skripsi_id}/
        ├── Skripsi.pdf
        ├── source-code.zip
        ├── dataset.xlsx
        └── dokumentasi.pdf
```

---

## 7. Embeddings

Buat tabel `embeddings` untuk menyimpan vector hasil proses embedding AI.

Kolom:

* id
* skripsi_id
* model
* dimension
* embedding
* timestamps

Contoh:

```text
skripsi_id: 1
model: text-embedding-model
dimension: 1536

embedding:
[
    0.0123,
    -0.0456,
    0.7821,
    ...
]
```

Karena menggunakan MySQL, untuk tahap awal simpan embedding dalam format JSON.

Relasi:

```text
skripsi 1 : N embeddings
```

Embedding dibuat berdasarkan:

```text
judul
+
abstrak
+
kata_kunci
```

Kemudian:

```text
Text
 ↓
Embedding Model
 ↓
Vector
 ↓
MySQL
```

---

## 8. Verifikasi Skripsi

Buat tabel `verifikasi_skripsi`.

Digunakan untuk mencatat proses pemeriksaan oleh Kepala Perpustakaan.

Kolom:

* id
* skripsi_id
* diverifikasi_oleh
* status
* catatan
* verified_at
* timestamps

`diverifikasi_oleh` mengarah ke `users.id`.

Contoh status:

```text
disetujui
ditolak
```

Relasi:

```text
skripsi 1 : N verifikasi_skripsi
users 1 : N verifikasi_skripsi
```

Alur:

```text
Mahasiswa Upload
       ↓
status = menunggu
       ↓
Kepala Perpustakaan Review
       ↓
   ┌───┴────┐
   ↓        ↓
Setujui    Tolak
   ↓        ↓
disetujui  ditolak
```

Jika disetujui:

```text
Skripsi
 ↓
Generate Embedding
 ↓
Embedding Model
 ↓
Vector
 ↓
Simpan ke embeddings
 ↓
Masuk Repository
```

---

## 9. Saved Skripsi

Buat tabel `saved_skripsi`.

Digunakan ketika mahasiswa menyimpan skripsi favorit.

Kolom:

* id
* mahasiswa_id
* skripsi_id
* created_at

Relasi:

```text
mahasiswa 1 : N saved_skripsi
skripsi 1 : N saved_skripsi
```

Secara konsep:

```text
mahasiswa N : M skripsi
```

dengan `saved_skripsi` sebagai pivot table.

Contoh:

```text
Mahasiswa #1
 ├── Skripsi #2
 └── Skripsi #4

Mahasiswa #2
 └── Skripsi #1
```

Tambahkan unique constraint:

```text
unique(mahasiswa_id, skripsi_id)
```

agar mahasiswa tidak bisa menyimpan skripsi yang sama dua kali.

---

## 10. Search Histories

Buat tabel `search_histories`.

Digunakan untuk menyimpan riwayat pencarian mahasiswa.

Kolom:

* id
* mahasiswa_id
* query
* created_at

Contoh:

```text
"aplikasi pertanian IoT"
"sistem informasi desa"
"machine learning"
"Laravel"
```

Relasi:

```text
mahasiswa 1 : N search_histories
```

---

# Alur Semantic Search

Implementasikan alur berikut:

```text
Mahasiswa
    ↓
Input pencarian
    ↓
React
    ↓
Laravel REST API
    ↓
Embedding Model
    ↓
Query Vector
    ↓
Ambil embedding skripsi
dari MySQL
    ↓
Cosine Similarity
    ↓
Hitung similarity score
    ↓
Sort DESC
    ↓
Ambil Top 10
    ↓
JSON Response
    ↓
React
    ↓
Tampilkan hasil
```

Contoh:

Query:

```text
"aplikasi untuk membantu petani memantau tanaman"
```

Hasil:

```text
Sistem Monitoring Tanaman Berbasis IoT
similarity: 0.92

Machine Learning untuk Prediksi Hasil Pertanian
similarity: 0.76

Sistem Informasi Akademik Berbasis Web
similarity: 0.31
```

Jelaskan bahwa `similarity` adalah **skor kemiripan vector**, bukan persentase akurasi AI.

---

# Alur Upload Skripsi

```text
Mahasiswa
 ↓
Upload Skripsi
 ↓
Isi metadata
 ↓
Upload PDF
 ↓
Upload Source Code
 ↓
Upload Dataset
 ↓
Upload Dokumentasi
 ↓
Laravel Validasi
 ↓
Simpan file ke Storage
 ↓
Simpan metadata ke MySQL
 ↓
status = menunggu
 ↓
Kepala Perpustakaan melakukan review
```

---

# Alur Kepala Perpustakaan

Dashboard memiliki:

```text
Dashboard
Repository
 ├── Semua Skripsi
 ├── Menunggu Verifikasi
 └── Ditolak

Data Mahasiswa
AI Search
Statistik
Pengaturan
Logout
```

Kepala Perpustakaan dapat:

* melihat semua skripsi
* melihat skripsi yang menunggu verifikasi
* melihat detail skripsi
* membuka/download file
* menyetujui skripsi
* menolak skripsi
* memberikan alasan penolakan
* mencari skripsi menggunakan semantic search
* melihat statistik repository

---

# Alur Mahasiswa

Dashboard mahasiswa:

```text
Dashboard
Cari Skripsi
Repository
Tersimpan
Riwayat
Upload Skripsi
Profil
Logout
```

Mahasiswa dapat:

* mencari skripsi
* menggunakan semantic search
* melihat repository
* melihat detail skripsi
* download file yang diizinkan
* menyimpan skripsi
* melihat riwayat pencarian
* mengupload skripsi
* melihat status verifikasi
* melihat alasan penolakan jika ditolak

---

# Struktur Relasi Final

Gunakan struktur:

```text
users
│
├── mahasiswa
│     │
│     ├── skripsi
│     │     │
│     │     ├── skripsi_files
│     │     ├── embeddings
│     │     └── verifikasi_skripsi
│     │
│     ├── saved_skripsi
│     └── search_histories
│
└── verifikasi_skripsi


program_studi
│
├── mahasiswa
└── skripsi


kategori
│
└── skripsi
```

Buat desain database yang **normal, konsisten, menggunakan foreign key, index yang sesuai, unique constraint untuk data pivot, timestamps, soft delete jika diperlukan, serta mengikuti konvensi Laravel**.

Jangan membuat tabel atau role yang tidak diperlukan. Fokuskan sistem pada dua role utama: **Mahasiswa** dan **Kepala Perpustakaan**.

Hasil akhir yang diharapkan:

1. ERD
2. Daftar tabel
3. Kolom setiap tabel
4. Primary key
5. Foreign key
6. Cardinality/relasi
7. Contoh data
8. Laravel migration
9. Laravel Model dan relationship
10. Seeder data dummy
11. Alur upload file
12. Alur verifikasi
13. Alur embedding
14. Alur semantic search
15. Struktur API endpoint yang diperlukan
