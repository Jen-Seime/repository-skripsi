<?php
namespace App\Services;

use App\Models\Mahasiswa;

class SavedService
{
    public function getSaved(Mahasiswa $mahasiswa)
    {
        return $mahasiswa->savedSkripsi()->with('skripsi')->latest()->get();
    }

    public function saveSkripsi(Mahasiswa $mahasiswa, int $skripsiId): void
    {
        $mahasiswa->savedSkripsi()->firstOrCreate([
            'skripsi_id' => $skripsiId,
        ]);
    }

    public function unsaveSkripsi(Mahasiswa $mahasiswa, int $skripsiId): void
    {
        $mahasiswa->savedSkripsi()
            ->where('skripsi_id', $skripsiId)
            ->firstOrFail()
            ->delete();
    }
}
