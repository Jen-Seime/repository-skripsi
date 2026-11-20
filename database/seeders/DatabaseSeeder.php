<?php

namespace Database\Seeders;

use App\Enums\SkripsiFileType;
use App\Enums\SkripsiStatus;
use App\Enums\UserRole;
use App\Enums\VerifikasiStatus;
use App\Models\Embedding;
use App\Models\Kategori;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\SavedSkripsi;
use App\Models\SearchHistory;
use App\Models\Skripsi;
use App\Models\SkripsiFile;
use App\Models\User;
use App\Models\VerifikasiSkripsi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProgramStudiSeeder::class,
            KategoriSeeder::class,
            SuperAdminSeeder::class,
        ]);

        $prodiTi = ProgramStudi::where('kode', 'TI')->first();
        $prodiSi = ProgramStudi::where('kode', 'SI')->first();

        $katIot = Kategori::where('slug', 'internet-of-things')->first();
        $katAi = Kategori::where('slug', 'artificial-intelligence')->first();
        $katWeb = Kategori::where('slug', 'web-development')->first();

        // 1. Kepala Perpustakaan
        $kepala = User::updateOrCreate(
            ['email' => 'kepala.perpus@universitas.ac.id'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.Kom.',
                'password' => Hash::make('password'),
                'role' => UserRole::KepalaPerpustakaan,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Mahasiswa 1
        $userMhs1 = User::updateOrCreate(
            ['email' => 'ahmad.fauzi@mahasiswa.ac.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'role' => UserRole::Mahasiswa,
                'email_verified_at' => now(),
            ]
        );

        $mhs1 = Mahasiswa::updateOrCreate(
            ['user_id' => $userMhs1->id],
            [
                'nim' => '210101001',
                'program_studi_id' => $prodiTi->id,
                'angkatan' => 2021,
            ]
        );

        // 3. Mahasiswa 2
        $userMhs2 = User::updateOrCreate(
            ['email' => 'siti.nurhaliza@mahasiswa.ac.id'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role' => UserRole::Mahasiswa,
                'email_verified_at' => now(),
            ]
        );

        $mhs2 = Mahasiswa::updateOrCreate(
            ['user_id' => $userMhs2->id],
            [
                'nim' => '210201002',
                'program_studi_id' => $prodiSi->id,
                'angkatan' => 2021,
            ]
        );

        // 4. Skripsi 1 (Disetujui)
        $skripsi1 = Skripsi::create([
            'mahasiswa_id' => $mhs1->id,
            'program_studi_id' => $prodiTi->id,
            'kategori_id' => $katIot->id,
            'judul' => 'Sistem Monitoring Tanaman Berbasis IoT Menggunakan Sensor Kelembaban Tanah dan NodeMCU',
            'abstrak' => 'Penelitian ini mengembangkan sistem pemantauan kondisi kelembaban tanah dan suhu lingkungan tanaman secara real-time berbasis Internet of Things (IoT) untuk membantu efisiensi irigasi petani.',
            'kata_kunci' => 'IoT, NodeMCU, pertanian cerdas, sensor kelembaban, monitoring',
            'tahun' => 2024,
            'status' => SkripsiStatus::Disetujui,
        ]);

        SkripsiFile::create([
            'skripsi_id' => $skripsi1->id,
            'jenis' => SkripsiFileType::Skripsi,
            'nama_file' => 'Skripsi_210101001.pdf',
            'path' => 'skripsi/'.$skripsi1->id.'/Skripsi_210101001.pdf',
            'ukuran' => 2548000,
            'mime_type' => 'application/pdf',
        ]);

        SkripsiFile::create([
            'skripsi_id' => $skripsi1->id,
            'jenis' => SkripsiFileType::SourceCode,
            'nama_file' => 'source-code.zip',
            'path' => 'skripsi/'.$skripsi1->id.'/source-code.zip',
            'ukuran' => 10485760,
            'mime_type' => 'application/zip',
        ]);

        Embedding::create([
            'skripsi_id' => $skripsi1->id,
            'model' => 'text-embedding-3-small',
            'dimension' => 1536,
            'embedding' => array_map(fn () => fake()->randomFloat(6, -1, 1), range(1, 1536)),
        ]);

        VerifikasiSkripsi::create([
            'skripsi_id' => $skripsi1->id,
            'diverifikasi_oleh' => $kepala->id,
            'status' => VerifikasiStatus::Disetujui,
            'catatan' => 'Berkas lengkap dan telah memenuhi ketentuan publikasi repositori.',
            'verified_at' => now()->subDays(5),
        ]);

        // 5. Skripsi 2 (Disetujui)
        $skripsi2 = Skripsi::create([
            'mahasiswa_id' => $mhs1->id,
            'program_studi_id' => $prodiTi->id,
            'kategori_id' => $katAi->id,
            'judul' => 'Machine Learning untuk Prediksi Hasil Pertanian Menggunakan Algoritma Random Forest',
            'abstrak' => 'Pemanfaatan model machine learning dengan algoritma Random Forest untuk memprediksi kuantitas panen komoditas pangan berdasarkan parameter cuaca, pupuk, dan jenis tanah.',
            'kata_kunci' => 'Machine Learning, Random Forest, prediksi panen, data mining',
            'tahun' => 2024,
            'status' => SkripsiStatus::Disetujui,
        ]);

        Embedding::create([
            'skripsi_id' => $skripsi2->id,
            'model' => 'text-embedding-3-small',
            'dimension' => 1536,
            'embedding' => array_map(fn () => fake()->randomFloat(6, -1, 1), range(1, 1536)),
        ]);

        VerifikasiSkripsi::create([
            'skripsi_id' => $skripsi2->id,
            'diverifikasi_oleh' => $kepala->id,
            'status' => VerifikasiStatus::Disetujui,
            'catatan' => 'Disetujui tanpa catatan perbaikan.',
            'verified_at' => now()->subDays(2),
        ]);

        // 6. Skripsi 3 (Menunggu)
        Skripsi::create([
            'mahasiswa_id' => $mhs2->id,
            'program_studi_id' => $prodiSi->id,
            'kategori_id' => $katWeb->id,
            'judul' => 'Sistem Informasi Repositori Skripsi Berbasis Web Menggunakan AI Semantic Search',
            'abstrak' => 'Membangun aplikasi repositori karya ilmiah modern yang mendukung pencarian kontekstual berbasis vector embedding untuk meningkatkan temu balik informasi skripsi.',
            'kata_kunci' => 'Laravel, React, Semantic Search, Vector Embedding, Repositori',
            'tahun' => 2025,
            'status' => SkripsiStatus::Menunggu,
        ]);

        // 7. Saved Skripsi & Search History
        SavedSkripsi::firstOrCreate([
            'mahasiswa_id' => $mhs2->id,
            'skripsi_id' => $skripsi1->id,
        ]);

        SearchHistory::create([
            'mahasiswa_id' => $mhs2->id,
            'query' => 'aplikasi untuk membantu petani memantau tanaman',
            'created_at' => now()->subHours(3),
        ]);

        SearchHistory::create([
            'mahasiswa_id' => $mhs2->id,
            'query' => 'machine learning pertanian',
            'created_at' => now()->subHours(1),
        ]);
    }
}
