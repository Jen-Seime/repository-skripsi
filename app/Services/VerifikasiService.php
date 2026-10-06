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
     * Get paginated list of skripsi pending verification.
     */
    public function getSkripsiMenunggu(): LengthAwarePaginator
    {
        return Skripsi::with(['mahasiswa.user', 'programStudi', 'kategori', 'files', 'latestVerifikasi'])
            ->where('status', SkripsiStatus::Menunggu)
            ->latest('id')
            ->paginate(10);
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
