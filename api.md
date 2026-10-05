/api
│
├── Authentication
│ ├── POST /login
│ └── POST /logout
│
├── Profil
│ ├── GET /profil
│ └── PUT /profil
│
├── MAHASISWA
│ │
│ ├── GET /my/skripsi
│ ├── POST /my/skripsi
│ ├── GET /my/skripsi/{id}
│ ├── PUT /my/skripsi/{id}
│ ├── DELETE /my/skripsi/{id}
│ │
│ └── Files
│ ├── GET /my/skripsi/{id}/files
│ ├── POST /my/skripsi/{id}/files
│ └── DELETE /my/skripsi/{id}/files/{file}
│
├── REPOSITORY
│ │
│ ├── GET /repository/skripsi
│ ├── GET /repository/skripsi/{id}
│ └── GET /repository/skripsi/{id}/files
│
├── SAVED
│ ├── GET /saved-skripsi
│ ├── POST /saved-skripsi/{skripsi}
│ └── DELETE /saved-skripsi/{skripsi}
│
├── SEARCH
│ ├── GET /search
│ ├── GET /search/semantic
│ ├── GET /search-history
│ └── DELETE /search-history/{id}
│
├── MASTER DATA
│ ├── GET /kategori
│ └── GET /program-studi
│
├── KEPALA PERPUSTAKAAN
│ │
│ ├── GET /verifikasi/skripsi
│ ├── GET /verifikasi/skripsi/{id}
│ ├── POST /verifikasi/skripsi/{id}approve
│ ├── POST /verifikasi/skripsi/{id}/reject
│ ├── GET /mahasiswa
│ └── GET /statistik
│
└── INTERNAL
└── POST /embeddings/{skripsi}
