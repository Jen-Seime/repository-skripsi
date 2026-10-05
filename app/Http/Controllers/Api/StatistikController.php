<?php

namespace App\Http\Controllers\Api;

use App\Enums\SkripsiStatus;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Skripsi;
use Illuminate\Http\JsonResponse;

class StatistikController extends Controller
{
    /**
     * GET /statistik
     * Ringkasan data perpustakaan.
     */
    public function index(): JsonResponse
    {
        $totalSkripsi    = Skripsi::count();
        $totalMahasiswa  = Mahasiswa::count();
        $totalMenunggu   = Skripsi::where('status', SkripsiStatus::Menunggu)->count();
        $totalDisetujui  = Skripsi::where('status', SkripsiStatus::Disetujui)->count();
        $totalDitolak    = Skripsi::where('status', SkripsiStatus::Ditolak)->count();

        $perTahun = Skripsi::where('status', SkripsiStatus::Disetujui)
            ->selectRaw('tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->get();

        return response()->json([
            'data' => [
                'total_skripsi'   => $totalSkripsi,
                'total_mahasiswa' => $totalMahasiswa,
                'per_status'      => [
                    'menunggu'   => $totalMenunggu,
                    'disetujui'  => $totalDisetujui,
                    'ditolak'    => $totalDitolak,
                ],
                'per_tahun' => $perTahun,
            ],
        ]);
    }
}
