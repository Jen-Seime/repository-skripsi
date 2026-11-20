<?php

namespace App\Services;

use App\Enums\SkripsiFileType;
use App\Enums\SkripsiStatus;
use App\Models\Mahasiswa;
use App\Models\Skripsi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class MahasiswaSkripsiService
{
    public function getMySkripsi(Mahasiswa $mahasiswa)
    {
        return $mahasiswa->skripsi()
            ->with(['programStudi', 'kategori', 'files', 'latestVerifikasi'])
            ->latest()
            ->get();
    }

    public function showMySkripsi(Mahasiswa $mahasiswa, int $id)
    {
        return $mahasiswa->skripsi()
            ->with(['programStudi', 'kategori', 'files', 'latestVerifikasi'])
            ->findOrFail($id);
    }

    // cerate skripsi upload
    public function createSkripsi(Mahasiswa $mahasiswa, array $data, array $uploadFiles)
    {
        return DB::transaction(function () use ($mahasiswa, $data, $uploadFiles) {
            $skripsi = $mahasiswa->skripsi()->create([
                'program_studi_id' => $mahasiswa->program_studi_id,
                'kategori_id' => $data['kategori_id'],
                'judul' => $data['judul'],
                'abstrak' => $data['abstrak'],
                'kata_kunci' => $data['kata_kunci'],
                'tahun' => $data['tahun'],
                'status' => SkripsiStatus::Menunggu,
            ]);

            if (isset($uploadFiles['file_skripsi'])) {
                $this->seveFile($skripsi, $uploadFiles['file_skripsi'], SkripsiFileType::Skripsi);
            }

            if (isset($uploadFiles['file_source_code'])) {
                $this->seveFile($skripsi, $uploadFiles['file_source_code'], SkripsiFileType::SourceCode);
            }

            return $skripsi->load(['programStudi', 'kategori', 'files', 'latestVerifikasi']);
        });
    }

    // create skripsi update
    public function updateSkripsi(Mahasiswa $mahasiswa, array $data, array $uploadFiles, int $id)
    {
        return DB::transaction(function () use ($mahasiswa, $data, $uploadFiles, $id) {
            $skripsi = $mahasiswa->skripsi()->findOrFail($id);

            $skripsi->update([
                'program_studi_id' => $mahasiswa->program_studi_id,
                'kategori_id' => $data['kategori_id'],
                'judul' => $data['judul'],
                'abstrak' => $data['abstrak'],
                'kata_kunci' => $data['kata_kunci'],
                'tahun' => $data['tahun'],
                'status' => SkripsiStatus::Menunggu,
            ]);

            if (isset($uploadFiles['file_skripsi']) && $uploadFiles['file_skripsi']) {
                $this->updateFile($skripsi, $uploadFiles['file_skripsi'], SkripsiFileType::Skripsi);
            }

            if (isset($uploadFiles['file_source_code']) && $uploadFiles['file_source_code']) {
                $this->updateFile($skripsi, $uploadFiles['file_source_code'], SkripsiFileType::SourceCode);
            }

            return $skripsi->load(['programStudi', 'kategori', 'files', 'latestVerifikasi']);
        });
    }

    // delete skripsi
    public function deleteSkripsi(Mahasiswa $mahasiswa, int $id): void
    {
        DB::transaction(function () use ($mahasiswa, $id) {
            $skripsi = $mahasiswa->skripsi()->findOrFail($id);
            $skripsi->delete();
        });
    }

    protected function updateFile(Skripsi $skripsi, UploadedFile $file, SkripsiFileType $jenis)
    {
        $path = $file->store('skripsi_file', 'public');

        $existingFile = $skripsi->files()->where('jenis', $jenis)->first();

        if ($existingFile) {
            // Update via Eloquent Model agar me-trigger event static::updating di SkripsiFile
            $existingFile->update([
                'nama_file' => $file->getClientOriginalName(),
                'path' => $path,
                'ukuran' => $file->getSize(),
                'mime_type' => $file->getClientMimeType() ?? $file->getMimeType(),
            ]);
        } else {
            // Jika file jenis tersebut belum pernah ada, buat record baru
            $skripsi->files()->create([
                'jenis' => $jenis,
                'nama_file' => $file->getClientOriginalName(),
                'path' => $path,
                'ukuran' => $file->getSize(),
                'mime_type' => $file->getClientMimeType() ?? $file->getMimeType(),
            ]);
        }
    }

    protected function seveFile(Skripsi $skripsi, UploadedFile $file, SkripsiFileType $jenis)
    {
        $path = $file->store('skripsi_file', 'public');

        $skripsi->files()->create([
            'jenis' => $jenis,
            'nama_file' => $file->getClientOriginalName(),
            'path' => $path,
            'ukuran' => $file->getSize(),
            'mime_type' => $file->getClientMimeType() ?? $file->getMimeType(),
        ]);
    }
}
