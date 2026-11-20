01 · Tujuan & Hasil yang Diharapkan

Tahap ini dilakukan setelah judul proyek disetujui. Mahasiswa harus menunjukkan bahwa aplikasi yang akan dibangun mempunyai kebutuhan, alur kerja, database, dan tampilan yang telah direncanakan dengan jelas.
01
Analisis kebutuhan

Memetakan masalah, aktor, fungsi utama, dan batasan sistem.
02
Alur dan Use Case

Menjelaskan siapa melakukan apa dan bagaimana proses berjalan.
03
Perancangan database

Membuat ERD dengan tabel, kunci, dan relasi yang tepat.
04
Rancangan antarmuka

Menyusun wireframe halaman dan navigasi sesuai hak akses.
Luaran wajib: satu dokumen PDF berisi analisis dan perancangan, dilengkapi diagram yang terbaca. Pada tahap ini mahasiswa belum diwajibkan menyelesaikan seluruh aplikasi.
02 · Langkah Kerja Analisis & Perancangan
1
Identifikasi masalah dan objek

    Tuliskan judul yang sudah disetujui dan objek/lokasi.
    Jelaskan proses saat ini dan masalah yang ditemukan.
    Jelaskan solusi yang diusulkan serta batasan proyek.
    Nyatakan sumber informasi: observasi, wawancara, atau studi dokumen.

Luaran: uraian masalah, tujuan, dan ruang lingkup.
2
Analisis kebutuhan pengguna

    Identifikasi minimal 2 peran pengguna, misalnya Admin dan Operator.
    Daftarkan kebutuhan fungsional menggunakan kode KF-01, KF-02, dst.
    Daftarkan kebutuhan nonfungsional: responsif, validasi, keamanan dasar.
    Tentukan data masukan dan informasi keluaran.

Luaran: tabel aktor, hak akses, dan kebutuhan sistem.
3
Rancang alur sistem & Use Case

    Buat satu diagram alur proses utama (flowchart atau activity diagram).
    Buat Use Case Diagram dengan aktor dan fungsi aplikasi.
    Jelaskan minimal 3 use case utama secara singkat.
    Pastikan alur konsisten dengan kebutuhan yang ditulis.

Luaran: diagram alur, Use Case Diagram, dan deskripsi proses.
4
Rancang database & ERD

    Tentukan minimal 3 tabel utama di luar tabel bawaan Laravel.
    Tulis atribut, tipe data, primary key, dan foreign key.
    Gambarkan relasi serta kardinalitas antartabel.
    Jelaskan bagaimana data mendukung modul CRUD dan laporan.

Luaran: ERD dan kamus data ringkas.
5
Rancang antarmuka

    Buat wireframe login, dashboard, dan halaman modul inti.
    Gambarkan daftar data, form tambah/edit, dan detail jika diperlukan.
    Perlihatkan navigasi untuk tiap peran pengguna.
    Perhatikan tampilan desktop dan mobile.

Luaran: minimal 4 rancangan layar yang diberi keterangan.
6
Petakan implementasi Laravel 13

    Daftarkan model, controller, migration, dan route utama.
    Rencanakan minimal 2 modul CRUD.
    Jelaskan autentikasi, otorisasi, dan validasi form.
    Susun urutan pengerjaan fitur pada tahap implementasi.

Luaran: matriks fitur dan komponen Laravel.
Prinsip konsistensi: masalah → kebutuhan → use case → database → tampilan → fitur Laravel harus saling terhubung. Jangan membuat diagram hanya untuk memenuhi jumlah halaman.
03 · Contoh Kasus Sederhana

Contoh judul: Rancang Bangun Sistem Informasi Pengelolaan Sampah Berbasis Web pada Kelurahan XYZ.

Masalah: pencatatan laporan warga dan tindak lanjut pengangkutan masih tersebar sehingga status penyelesaian sulit dipantau.
Komponen	Contoh rancangan
Aktor	Admin, Petugas, Warga
Kebutuhan fungsional	Login, kelola kategori sampah, kirim laporan, verifikasi laporan, ubah status penanganan, lihat rekap
Use case utama	Warga mengirim laporan; Admin memverifikasi; Petugas memperbarui status penanganan
Tabel utama	kategori_sampah, laporan, penanganan (ditambah users)
Relasi	Satu kategori memiliki banyak laporan; satu laporan memiliki banyak catatan penanganan; satu pengguna dapat membuat banyak laporan
Rancangan layar	Login, dashboard, daftar laporan, form laporan, detail/status penanganan
Komponen Laravel	Laporan model, LaporanController, migration laporan, route CRUD, validasi dan middleware akses
Contoh ini hanya panduan struktur. Gunakan data, aktor, dan proses yang sesuai dengan judul proyek masing-masing.
04 · Sistematika Dokumen Pengumpulan

Susun satu dokumen dengan urutan berikut. Gunakan judul subbab yang jelas dan diagram beresolusi cukup.
Bagian	Isi minimum
Halaman Judul	Judul proyek, nama, NIM, kelas, tema, mata kuliah
BAB I — Pendahuluan	Latar belakang masalah, rumusan masalah, tujuan, manfaat, batasan sistem
BAB II — Analisis Kebutuhan	Deskripsi objek, proses berjalan, aktor, kebutuhan fungsional/nonfungsional
BAB III — Perancangan Sistem	Flowchart/activity diagram, Use Case Diagram dan deskripsi, ERD, struktur tabel, wireframe, matriks implementasi Laravel
BAB IV — Rencana Implementasi	Prioritas fitur, urutan pengembangan, rencana pengujian awal
Lampiran (jika ada)	Bukti observasi/wawancara, referensi, tangkapan layar desain
Contoh tabel kebutuhan fungsional
Kode	Pengguna	Kebutuhan	Hasil yang diharapkan
KF-01	Admin	Login ke aplikasi	Masuk ke dashboard sesuai hak akses
KF-02	Warga	Mengirim laporan	Laporan tersimpan dan memiliki status
KF-03	Petugas	Memperbarui penanganan	Riwayat penanganan tercatat
Contoh matriks implementasi Laravel
Fitur	Model / Tabel	Controller	Halaman
Kelola laporan	Laporan / laporan	LaporanController	Index, Create, Edit, Show
Kelola kategori	KategoriSampah / kategori_sampah	KategoriSampahController	Index, Create, Edit
Penanganan	Penanganan / penanganan	PenangananController	Detail laporan, form status
05 · Ketentuan Teknis & Pemeriksaan

    Prasyarat: judul telah disetujui dosen pada Tahap 01.
    Individu: dokumen disusun sesuai judul masing-masing, meskipun objek proyek boleh sama.
    Minimal rancangan: 2 peran pengguna, 2 modul CRUD, 3 tabel utama berelasi, 1 diagram alur, 1 Use Case Diagram, 1 ERD, dan 4 rancangan layar.
    Alat bantu: diagrams.net/draw.io, Figma, Excalidraw, atau aplikasi sejenis; boleh memakai alat lain selama hasil dapat dibaca.
    Format: PDF dengan diagram terbaca, bukan kumpulan gambar tanpa penjelasan.
    Keaslian: AI boleh membantu penyusunan, tetapi mahasiswa wajib memahami dan dapat menjelaskan semua rancangan.
    Perubahan judul: wajib dikonsultasikan dan disetujui dosen.
    Pemeriksaan dosen: kelengkapan, konsistensi antarbagian, relevansi dengan masalah, dan kelayakan implementasi Laravel 13.

Kesalahan umum yang harus dihindari

Status pemeriksaan: ACC jika dokumen layak dilanjutkan ke implementasi; Revisi jika terdapat kekurangan yang perlu diperbaiki.
06 · Checklist Sebelum Pengumpulan

Centang setiap poin untuk memeriksa kesiapan dokumen. Checklist ini tersimpan hanya selama halaman terbuka.
Judul proyek sudah disetujui pada Tahap 01
Masalah, tujuan, manfaat, dan batasan sistem sudah jelas
Objek, sumber informasi, dan proses berjalan dijelaskan
Minimal 2 aktor beserta hak akses teridentifikasi
Kebutuhan fungsional dan nonfungsional disusun
Diagram alur dan Use Case Diagram sudah lengkap
Minimal 3 use case utama dijelaskan
ERD, relasi, dan minimal 3 tabel utama tersedia
Minimal 4 rancangan layar dan matriks Laravel tersedia
Dokumen PDF telah diperiksa dan siap dikumpulkan

0 dari 10 poin lengkap
07 · Pengumpulan Hasil Tahap 02

Setelah seluruh analisis dan rancangan selesai, gabungkan ke dalam satu dokumen PDF dan ajukan melalui tautan resmi berikut.

Nama file yang disarankan: WBF_Tahap02_NIM_Nama.pdf