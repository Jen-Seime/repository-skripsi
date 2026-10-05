<?php

namespace App\Services;

use App\Enums\SkripsiStatus;
use App\Models\Skripsi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RepositoryService
{
    /**
     * Get paginated list of approved skripsi with optional filters.
     *
     * @param array<string, mixed> $filters
     */
    public function getSkripsi(array $filters = []): LengthAwarePaginator
    {
        $query = Skripsi::with(['programStudi', 'kategori', 'mahasiswa.user', 'files'])
            ->where('status', SkripsiStatus::Disetujui);

        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('abstrak', 'like', "%{$search}%")
                    ->orWhere('kata_kunci', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        if (!empty($filters['program_studi_id'])) {
            $query->where('program_studi_id', $filters['program_studi_id']);
        }

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        $perPage = (int) ($filters['per_page'] ?? 10);

        return $query->latest('id')->paginate($perPage);
    }

    /**
     * Find detail of an approved skripsi by id.
     */
    public function findSkripsi(int $id): Skripsi
    {
        return Skripsi::with(['programStudi', 'kategori', 'mahasiswa.user', 'files'])
            ->where('status', SkripsiStatus::Disetujui)
            ->findOrFail($id);
    }
}
