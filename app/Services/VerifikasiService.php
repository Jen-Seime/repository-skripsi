<?php

namespace App\Services;

use App\Enums\SkripsiStatus;
use App\Enums\VerifikasiStatus;
use App\Models\Skripsi;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VerifikasiService
{
    /**
     * Get paginated list of skripsi pending verification with optional search and filters.
     *
     * @param  array<string, mixed>|string|null  $filters
     */
    public function getSkripsiMenunggu(array|string|null $filters = []): LengthAwarePaginator
    {
        if (is_string($filters)) {
            $filters = ['q' => $filters];
        }

        $filters = $filters ?? [];

        $query = Skripsi::with(['mahasiswa.user', 'programStudi', 'kategori', 'files', 'latestVerifikasi'])
            ->where('status', SkripsiStatus::Menunggu);

        $search = $filters['q'] ?? $filters['search'] ?? null;
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('abstrak', 'like', "%{$search}%")
                    ->orWhere('kata_kunci', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($mQuery) use ($search) {
                        $mQuery->where('nim', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uQuery) use ($search) {
                                $uQuery->where('name', 'like', "%{$search}%");
                            });

                    });
            });
        }

        if (! empty($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        if (! empty($filters['program_studi_id'])) {
            $query->where('program_studi_id', $filters['program_studi_id']);
        }

        if (! empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        $perPage = (int) ($filters['per_page'] ?? 10);

        return $query->latest('id')->paginate($perPage);
    }

    /**
     * Get detail of a skripsi with full verification history.
     */
    public function getDetail(int $id): Skripsi
    {
        return Skripsi::with([
            'mahasiswa.user',
            'programStudi',
            'kategori',
            'files',
            'verifikasiSkripsi.verifikator',
        ])->findOrFail($id);
    }

    /**
     * Approve a skripsi.
     */
    public function approve(int $skripsiId, User $kepala, ?string $catatan = null): Skripsi
    {
        return DB::transaction(function () use ($skripsiId, $kepala, $catatan) {
            $skripsi = Skripsi::findOrFail($skripsiId);

            $skripsi->update(['status' => SkripsiStatus::Disetujui]);

            $skripsi->verifikasiSkripsi()->create([
                'diverifikasi_oleh' => $kepala->id,
                'status' => VerifikasiStatus::Disetujui,
                'catatan' => $catatan,
                'verified_at' => now(),
            ]);

            return $skripsi;
        });
    }

    /**
     * Reject a skripsi.
     */
    public function reject(int $skripsiId, User $kepala, ?string $catatan = null): void
    {
        DB::transaction(function () use ($skripsiId, $kepala, $catatan) {
            $skripsi = Skripsi::findOrFail($skripsiId);

            $skripsi->update(['status' => SkripsiStatus::Ditolak]);

            $skripsi->verifikasiSkripsi()->create([
                'diverifikasi_oleh' => $kepala->id,
                'status' => VerifikasiStatus::Ditolak,
                'catatan' => $catatan,
                'verified_at' => now(),
            ]);
        });
    }
}
