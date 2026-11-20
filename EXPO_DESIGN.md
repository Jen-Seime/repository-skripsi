# EXPO_DESIGN.md — Mobile App Expo: Repository Perpustakaan Skripsi & AI Semantic Search

Dokumen ini adalah **System Prompt & Knowledge Base (Context File)** untuk membangun aplikasi mobile **React Native (Expo)** yang terintegrasi penuh dengan backend Laravel REST API repositori perpustakaan skripsi.

---

## 1. Ringkasan Proyek (Mobile Overview)

**Nama Aplikasi:** Repositori Perpustakaan Skripsi Mobile (AI Semantic Search)  
**Target Platform:** Android & iOS (via Expo SDK 52+)  
**Backend:** Laravel 12 + Laravel Sanctum (Bearer Token) + PostgreSQL / pgvector (AI Embeddings)  

### Peran Pengguna & Ruang Lingkup Mobile
1. **`mahasiswa` (Pengguna Utama Mobile):**
   - Eksplorasi katalog skripsi disetujui (`disetujui`) dengan filter & paginasi.
   - **AI Semantic Search:** Pencarian berbasis makna kalimat dengan indikator persentase *similarity score* dan riwayat pencarian interaktif.
   - **Skripsi Saya:** Cek status skripsi (`menunggu`, `disetujui`, `ditolak`), unggah dokumen skripsi (PDF) & source code (ZIP) langsung dari penyimpanan ponsel, dan edit/hapus pengajuan.
   - **Bookmark / Simpanan:** Simpan skripsi favorit untuk dibaca cepat atau diunduh.
   - **Detail & Preview File:** Baca abstrak lengkap dan buka/unduh file PDF lampiran.
2. **`kepala_perpustakaan` & `super_admin`:**
   - Ringkasan statistik & KPI repositori secara *mobile-friendly*.
   - Antrean verifikasi skripsi mahasiswa: tinjau dokumen langsung di ponsel, lakukan **Approve** (memicu AI embedding) atau **Reject** disertai catatan.
   - Pemantauan status akun mahasiswa (lihat daftar & toggle aktif/nonaktif).

### Kredensial Uji Coba (Database Seeder)
| Role | Email | Password |
| :--- | :--- | :--- |
| **`mahasiswa`** | `ahmad.fauzi@mahasiswa.ac.id` | `password` |
| **`kepala_perpustakaan`** | `kepala.perpus@universitas.ac.id` | `password` |
| **`super_admin`** | `super.admin@universitas.ac.id` | `password` |

---

## 2. Tech Stack Rekomendasi Expo

- **Framework:** Expo SDK 52+ (Managed Workflow)
- **Routing:** Expo Router v4 (File-based navigation)
- **Styling:** NativeWind v4 (Tailwind CSS untuk React Native)
- **Icons:** `lucide-react-native`
- **UI & Sheet Primitives:**
  - `@gorhom/bottom-sheet` (Filter modal & konfirmasi aksi)
  - `react-native-reanimated` & `react-native-gesture-handler`
  - `react-native-safe-area-context` & `react-native-screens`
- **State & Data Fetching:**
  - `@tanstack/react-query` v5 (Caching, pull-to-refresh, infinite scroll)
  - `axios` (HTTP client dengan interceptor token)
- **Penyimpanan Kredensial:** `expo-secure-store` (Menyimpan Bearer Token secara aman)
- **Upload & Penanganan Berkas:**
  - `expo-document-picker` (Pilih PDF skripsi & ZIP source code)
  - `expo-file-system` & `expo-sharing` (Unduh & buka file PDF)
  - `expo-web-browser` (Alternatif in-app preview PDF)
- **Notifikasi Feedback:** `react-native-toast-message` / Haptic feedback via `expo-haptics`

---

## 3. Struktur Direktori Proyek Expo (`expo-app/`)

```text
expo-app/
├── app/
│   ├── _layout.tsx                   # Root Provider (React Query, AuthProvider, Portal, Toaster)
│   ├── (auth)/
│   │   ├── _layout.tsx               # Stack auth
│   │   └── login.tsx                 # Screen Login modern
│   ├── (tabs)/                       # Role Mahasiswa: Bottom Tab Navigator
│   │   ├── _layout.tsx               # Konfigurasi 4 Tab Bar
│   │   ├── index.tsx                 # Tab 1: Eksplorasi Katalog Skripsi
│   │   ├── search.tsx                # Tab 2: AI Semantic Search & Riwayat
│   │   ├── my-skripsi.tsx            # Tab 3: Skripsi Saya (Upload & Status)
│   │   ├── saved.tsx                 # Tab 4: Bookmark / Skripsi Tersimpan
│   │   └── profile.tsx               # Tab 5: Akun & Logout
│   ├── (admin)/                      # Role Kepala Perpustakaan & Super Admin
│   │   ├── _layout.tsx               # Bottom Tab atau Drawer admin
│   │   ├── index.tsx                 # Dashboard Statistik & KPI
│   │   ├── verifikasi/
│   │   │   ├── index.tsx             # Antrean Skripsi Menunggu
│   │   │   └── [id].tsx              # Review Dokumen & Approve / Reject
│   │   ├── mahasiswa.tsx             # Ringkasan & Toggle Akun Mahasiswa
│   │   └── profile.tsx               # Profil Admin
│   ├── skripsi/
│   │   ├── [id].tsx                  # Screen Detail Skripsi (Publik/Disetujui)
│   │   └── form.tsx                  # Screen Tambah/Edit Skripsi (Upload File)
│   └── viewer/
│       └── pdf.tsx                   # In-app PDF Viewer Screen
├── src/
│   ├── api/
│   │   ├── client.ts                 # Axios instance + Base URL dinamis + SecureStore interceptor
│   │   ├── auth.api.ts               # Login, Logout, Profil
│   │   ├── repository.api.ts         # Katalog publik, Semantic Search, History, Saved
│   │   ├── mahasiswa.api.ts          # CRUD My Skripsi (Multipart FormData)
│   │   └── admin.api.ts              # Statistik, Verifikasi Approve/Reject, Kelola Mahasiswa
│   ├── components/
│   │   ├── common/
│   │   │   ├── AppHeader.tsx         # Header dengan avatar & status network
│   │   │   ├── SearchInput.tsx       # Input pencarian dengan clear button
│   │   │   ├── EmptyState.tsx        # Ilustrasi data kosong
│   │   │   ├── SkeletonCard.tsx      # Shimmer loading skeleton
│   │   │   └── FilterBottomSheet.tsx # Sheet filter Kategori, Prodi, Tahun
│   │   ├── skripsi/
│   │   │   ├── SkripsiCard.tsx       # Kartu skripsi dengan chip prodi, tahun, badge
│   │   │   ├── SimilarityBadge.tsx   # Badge persentase skor AI Semantic
│   │   │   ├── StatusBadge.tsx       # Badge status: menunggu / disetujui / ditolak
│   │   │   └── FileUploadBox.tsx     # Komponen picker PDF & ZIP dengan info ukuran
│   ├── context/
│   │   └── AuthContext.tsx           # State user, token, role, login(), logout()
│   ├── hooks/
│   │   ├── useDebounce.ts
│   │   └── useDownloadFile.ts        # Download PDF via FileSystem & open preview
│   ├── types/
│   │   └── api.types.ts              # TypeScript interface sesuai response Laravel
│   └── utils/
│       ├── storage.ts                # Wrapper expo-secure-store
│       └── formatters.ts             # Format tanggal, byte to MB, persen similarity
├── .env.example
├── app.json
└── tailwind.config.js
```

---

## 4. UI/UX Design System Mobile (Academic & AI Clean)

### Palet Warna
- **Primary (Academic Indigo):** `#4F46E5` (`indigo-600`), Dark `#3730A3` (`indigo-800`), Light `#EEF2FF` (`indigo-50`).
- **AI Accent (Violet Aura):** `#7C3AED` (`violet-600`), Light `#F5F3FF` (`violet-50`). Digunakan untuk tombol Semantic Search, icon sparkle, dan persentase skor kemiripan.
- **Backgrounds:**
  - Screen Background: `#F8FAFC` (`slate-50`)
  - Card & Surfaces: `#FFFFFF` (White) dengan border `#E2E8F0` (`slate-200`)
  - Dark Surfaces (Tab bar/Header): `#0F172A` (`slate-900`) jika dark mode diaktifkan.
- **Status Colors:**
  - `menunggu`: Background `#FEF3C7` (`amber-100`), Text `#B45309` (`amber-700`), Border `#FDE68A`
  - `disetujui`: Background `#D1FAE5` (`emerald-100`), Text `#047857` (`emerald-700`), Border `#A7F3D0`
  - `ditolak`: Background `#FFE4E6` (`rose-100`), Text `#BE123C` (`rose-700`), Border `#FECDD3`

### Karakteristik UI Mobile
1. **Bottom Tabs Navigation:** 
   - Ikon aktif berwarna Indigo dengan indikator titik atau glow violet.
   - Label rapi: *Katalog*, *AI Search*, *Skripsi Saya*, *Simpanan*, *Profil*.
2. **AI Semantic Search Experience:**
   - Search bar dihiasi ikon `Sparkles`.
   - Chip riwayat pencarian horizontal yang bisa langsung ditekan untuk mencari ulang.
   - Hasil pencarian menampilkan *Similarity Score Meter* (contoh: `89.4% Cocok` dengan gradient violet).
3. **Card Design:**
   - Sudut melengkung `rounded-2xl`, *subtle shadow* `shadow-sm`, border tipis `border-slate-100`.
   - Menampilkan judul (maks. 2 baris), nama mahasiswa, NIM (font monospace), badge prodi, dan tahun.
4. **Detail Screen:**
   - Sticky action bar di bagian bawah layar: tombol bookmark (icon heart) dan tombol unduh/baca PDF.
   - Teks abstrak disajikan dengan ukuran nyaman dibaca, `line-height` lega, serta chip kata kunci.
5. **Form Upload:**
   - Tombol picker file besar dengan indikator dokumen terpilih, nama file, ukuran dalam MB, dan tombol ganti/hapus file.

---

## 5. Konfigurasi Khusus React Native & Laravel API

### A. Alamat Base URL Sesuai Lingkungan Pengujian
Di aplikasi mobile (Expo), `localhost` merujuk ke ponsel itu sendiri, bukan komputer host Laravel. Konfigurasikan file `.env`:

```env
# 1. Jika pakai Android Emulator:
EXPO_PUBLIC_API_URL=http://10.0.2.2:8000/api

# 2. Jika pakai iOS Simulator:
# EXPO_PUBLIC_API_URL=http://localhost:8000/api

# 3. Jika pakai Perangkat Fisik (Expo Go melalui Wi-Fi yang sama):
# Ganti dengan IP lokal laptop/PC Anda:
# EXPO_PUBLIC_API_URL=http://192.168.1.10:8000/api
```

### B. Axios Client & Token Storage (`src/api/client.ts`)
Gunakan `expo-secure-store` untuk menyimpan token autentikasi:

```typescript
import axios from 'axios';
import * as SecureStore from 'expo-secure-store';

const API_BASE_URL = process.env.EXPO_PUBLIC_API_URL || 'http://10.0.2.2:8000/api';

export const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

// Interceptor: Tambahkan Bearer Token otomatis di setiap request
apiClient.interceptors.request.use(async (config) => {
  const token = await SecureStore.getItemAsync('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor: Handle error 401 (Token Expired / Akun Nonaktif)
apiClient.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      await SecureStore.deleteItemAsync('auth_token');
      await SecureStore.deleteItemAsync('auth_user');
      // Trigger navigasi ke (auth)/login jika diperlukan
    }
    return Promise.reject(error);
  }
);
```

### C. Penting: Penanganan Upload File Multipart di React Native
React Native tidak memiliki objek `File` browser standar. Format pengiriman berkas wajib menggunakan struktur `{ uri, name, type }` di dalam `FormData`:

```typescript
import * as DocumentPicker from 'expo-document-picker';

// Contoh fungsi upload skripsi baru:
export async function uploadSkripsi(data: {
  kategori_id: number;
  judul: string;
  abstrak: string;
  kata_kunci: string;
  tahun: number;
  file_skripsi: DocumentPicker.DocumentPickerAsset;
  file_source_code?: DocumentPicker.DocumentPickerAsset | null;
}) {
  const formData = new FormData();
  formData.append('kategori_id', String(data.kategori_id));
  formData.append('judul', data.judul);
  formData.append('abstrak', data.abstrak);
  formData.append('kata_kunci', data.kata_kunci);
  formData.append('tahun', String(data.tahun));

  // File PDF Wajib
  formData.append('file_skripsi', {
    uri: data.file_skripsi.uri,
    name: data.file_skripsi.name,
    type: data.file_skripsi.mimeType || 'application/pdf',
  } as any);

  // File ZIP/RAR Opsional
  if (data.file_source_code) {
    formData.append('file_source_code', {
      uri: data.file_source_code.uri,
      name: data.file_source_code.name,
      type: data.file_source_code.mimeType || 'application/zip',
    } as any);
  }

  // CATATAN: Jangan set manual header 'Content-Type': 'multipart/form-data'
  // agar Axios dan React Native menentukan boundary multipart secara otomatis.
  return apiClient.post('/my-skripsi', formData);
}

// KHUSUS UPDATE: Laravel butuh _method = PUT jika mengirim FormData via POST
export async function updateSkripsi(id: number, data: any) {
  const formData = new FormData();
  formData.append('_method', 'PUT');
  // ... append fields lainnya seperti di atas ...
  return apiClient.post(`/my-skripsi/${id}`, formData);
}
```

---

## 6. Spesifikasi Lengkap Endpoint API untuk Mobile

### A. Autentikasi
1. **Login:** `POST /api/login`
   - Request: `{ "email": "...", "password": "..." }`
   - Response Sukses (`200`):
     ```json
     {
       "message": "login berhasil",
       "data": {
         "user": {
           "id": 1,
           "name": "Ahmad Fauzi",
           "email": "ahmad.fauzi@mahasiswa.ac.id",
           "role": "mahasiswa",
           "is_active": true
         },
         "role": "mahasiswa",
         "token": "1|sanctum_token_plain_text..."
       }
     }
     ```
   - Catatan: Jika `is_active: false`, API mengembalikan `422` dengan pesan akun nonaktif.
2. **Logout:** `GET /api/logout` (Method GET)
   - Headers: `Authorization: Bearer <token>`
3. **Profil User:** `GET /api/profil`
   - Mengembalikan data user beserta relasi `mahasiswa` (NIM, Angkatan, Program Studi).

---

### B. Modul Mahasiswa

1. **Eksplorasi Katalog Disetujui:**
   - **Endpoint:** `GET /api/repository/skripsi`
   - **Query Params:** `q`, `kategori_id`, `program_studi_id`, `tahun`, `page`, `per_page`
   - Gunakan `useInfiniteQuery` pada TanStack Query untuk infinite scrolling list di mobile.
2. **Detail Skripsi:**
   - **Endpoint:** `GET /api/repository/skripsi/{id}`
   - Menampilkan detail abstrak, penulis, dan daftar `files` untuk diunduh.
3. **AI Semantic Search:**
   - **Endpoint:** `POST /api/search/semantic`
   - **Body:** `{ "q": "pencarian semantik topik atau masalah..." }` (min. 3 karakter).
   - **Response:** Array skripsi dengan atribut `similarity` (skor desimal `0.00` - `1.00`).
4. **Riwayat Pencarian:**
   - `GET /api/search-history`: Mengambil riwayat query terakhir.
   - `DELETE /api/search-history/{id}`: Hapus 1 riwayat.
   - `DELETE /api/search-history/all`: Bersihkan semua riwayat.
5. **Skripsi Saya:**
   - `GET /api/my-skripsi`: Daftar pengajuan skripsi user login.
   - `GET /api/my-skripsi/{id}`: Detail pengajuan beserta `catatan` review verifikator.
   - `POST /api/my-skripsi`: Tambah skripsi baru (Multipart form data).
   - `POST /api/my-skripsi/{id}` dengan field `_method: "PUT"`: Edit skripsi.
   - `DELETE /api/my-skripsi/{id}`: Hapus skripsi.
6. **Bookmark / Saved Skripsi:**
   - `GET /api/saved-skripsi`: Daftar skripsi tersimpan.
   - `POST /api/saved-skripsi/{id}`: Simpan skripsi.
   - `DELETE /api/saved-skripsi/{id}`: Hapus dari daftar simpanan.

---

### C. Modul Kepala Perpustakaan & Super Admin

1. **Dashboard Statistik Mobile:**
   - **Endpoint:** `GET /api/statistik`
   - Menampilkan total skripsi, mahasiswa, dan ringkasan status (`menunggu`, `disetujui`, `ditolak`).
2. **Antrean Verifikasi:**
   - **Endpoint:** `GET /api/verifikasi/skripsi` (status `menunggu`)
   - `GET /api/verifikasi/skripsi/{id}`: Detail skripsi yang perlu ditinjau.
   - `POST /api/verifikasi/skripsi/{id}/approve`:
     - Body: `{ "catatan": "Disetujui" }` (Opsional)
     - *Perhatian: Tampilkan loading spinner karena backend sekaligus mengenerate AI Vector Embedding.*
   - `POST /api/verifikasi/skripsi/{id}/reject`:
     - Body: `{ "catatan": "Alasan penolakan" }` (Wajib diisi)
3. **Kelola Mahasiswa (Quick Action):**
   - `GET /api/mahasiswa`: Daftar mahasiswa beserta status `is_active`.
   - `PATCH /api/mahasiswa/{id}/toggle-status`: Mengaktifkan / menonaktifkan akun mahasiswa.

---

### D. Data Master (Dropdown Filter)
- `GET /api/kategori`: List kategori skripsi untuk filter & form.
- `GET /api/program-studi`: List program studi untuk filter & form.

---

## 7. TypeScript Definitions (`src/types/api.types.ts`)

```typescript
export type UserRole = 'mahasiswa' | 'kepala_perpustakaan' | 'super_admin';
export type SkripsiStatus = 'menunggu' | 'disetujui' | 'ditolak';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  is_active: boolean;
  mahasiswa?: {
    id: number;
    nim: string;
    angkatan: number;
    program_studi_id: number;
    program_studi?: string;
  } | null;
  created_at: string;
  updated_at: string;
}

export interface Kategori {
  id: number;
  nama: string;
  slug: string;
}

export interface ProgramStudi {
  id: number;
  kode: string;
  nama: string;
}

export interface SkripsiFile {
  id: number;
  jenis: 'skripsi' | 'source_code';
  nama_file: string;
  path: string;
  ukuran: number;
}

export interface Skripsi {
  id: number;
  judul: string;
  abstrak: string;
  kata_kunci: string;
  tahun: number;
  status: SkripsiStatus;
  di_upload: string;
  similarity?: number | null;
  catatan?: string | null;
  program_studi?: {
    kode?: string;
    nama?: string;
  };
  kategori?: {
    nama?: string;
  };
  mahasiswa?: {
    id: number;
    nama: string;
    nim: string;
  };
  files?: SkripsiFile[];
}

export interface SearchHistoryItem {
  id: number;
  query: string;
  created_at: string;
}

export interface DashboardStatistik {
  total_skripsi: number;
  total_mahasiswa: number;
  per_status: {
    menunggu: number;
    disetujui: number;
    ditolak: number;
  };
  per_tahun: Array<{
    tahun: number;
    total: number;
  }>;
}

export interface PaginatedResponse<T> {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}
```

---

## 8. Panduan Vibe Coding untuk AI Assistant (Prompt Bertahap)

Salin prompt di bawah secara berurutan ke AI coding assistant Anda (Cursor / Windsurf / Claude Code) di dalam repositori Expo:

### Prompt 1: Setup Fondasi, Client API & Autentikasi
> *"Berdasarkan file `EXPO_DESIGN.md`, buatkan struktur dasar aplikasi Expo SDK 52 dengan Expo Router v4 dan NativeWind v4. Implementasikan `src/types/api.types.ts`, `src/api/client.ts` menggunakan Axios dan `expo-secure-store`, serta `AuthContext.tsx` untuk menyimpan data user dan token. Buat halaman login modern di `app/(auth)/login.tsx` yang menangani endpoint `POST /api/login`, menampilkan pesan validasi atau pesan akun nonaktif, dan me-redirect user sesuai rolenya (`mahasiswa` ke `(tabs)`, admin ke `(admin)`)."*

### Prompt 2: Bottom Tabs & Eksplorasi Katalog Skripsi
> *"Berdasarkan `EXPO_DESIGN.md`, buat konfigurasi Bottom Tab Navigator di `app/(tabs)/_layout.tsx` menggunakan `lucide-react-native`. Pada tab pertama `app/(tabs)/index.tsx`, buat halaman Eksplorasi Katalog Skripsi yang mengambil data dari `GET /api/repository/skripsi` dengan `@tanstack/react-query`. Tambahkan search bar di bagian atas, tombol untuk membuka Bottom Sheet filter (Kategori, Program Studi, Tahun), list kartu `SkripsiCard` yang rapi, fitur pull-to-refresh, dan infinite scroll."*

### Prompt 3: AI Semantic Search & Riwayat
> *"Berdasarkan `EXPO_DESIGN.md`, buat halaman AI Semantic Search pada `app/(tabs)/search.tsx`. Sediakan search bar bertema AI dengan icon sparkle dan debounce input yang memanggil `POST /api/search/semantic`. Tampilkan persentase similarity score (`SimilarityBadge`) pada setiap kartu hasil pencarian. Di bawah search bar, tampilkan chip riwayat pencarian dari `GET /api/search-history` yang dapat ditekan untuk langsung mengisi query, tombol hapus satu riwayat, dan tombol bersihkan semua riwayat."*

### Prompt 4: Skripsi Saya & File Upload Picker
> *"Berdasarkan `EXPO_DESIGN.md`, bangun halaman `app/(tabs)/my-skripsi.tsx` untuk menampilkan daftar skripsi yang diunggah mahasiswa dengan badge status (`menunggu`, `disetujui`, `ditolak`). Tambahkan tombol tambah skripsi yang mengarah ke `app/skripsi/form.tsx`. Pada form tersebut, implementasikan integrasi `expo-document-picker` untuk memilih file PDF skripsi (maks 20MB) dan file source code ZIP (maks 50MB). Kirim data menggunakan `FormData` sesuai panduan React Native di `EXPO_DESIGN.md` (gunakan `_method: 'PUT'` jika mode edit)."*

### Prompt 5: Detail Skripsi & Bookmark
> *"Berdasarkan `EXPO_DESIGN.md`, buat halaman detail skripsi di `app/skripsi/[id].tsx` yang menampilkan judul lengkap, nama mahasiswa, NIM, prodi, tahun, chip kata kunci, dan teks abstrak yang nyaman dibaca. Sediakan tombol simpan/bookmark yang memanggil `POST/DELETE /api/saved-skripsi/{id}`, serta tombol untuk mengunduh atau membuka file PDF lampiran menggunakan `expo-sharing` atau `expo-web-browser`."*

### Prompt 6: Dashboard & Verifikasi Skripsi (Role Admin)
> *"Berdasarkan `EXPO_DESIGN.md`, buat flow untuk role `kepala_perpustakaan` dan `super_admin` pada grup `app/(admin)/`. Halaman `index.tsx` menampilkan 4 kartu KPI statistik dari `GET /api/statistik`. Halaman `verifikasi/index.tsx` menampilkan antrean skripsi menunggu verifikasi, dan `verifikasi/[id].tsx` menampilkan detail skripsi serta Bottom Sheet konfirmasi untuk Approve (`POST /api/verifikasi/skripsi/{id}/approve`) dan Reject (`POST /api/verifikasi/skripsi/{id}/reject`) dengan input catatan penolakan."*
