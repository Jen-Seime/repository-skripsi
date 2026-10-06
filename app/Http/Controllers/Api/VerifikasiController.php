<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkripsiResource;
use App\Services\EmbeddingService;
use App\Services\VerifikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VerifikasiController extends Controller
{
    public function __construct(
        protected VerifikasiService $verifikasiService
    ) {}

    /**
     * GET /verifikasi/skripsi
     * Daftar skripsi yang menunggu verifikasi.
     */
    public function index(): AnonymousResourceCollection
    {
        $skripsi = $this->verifikasiService->getSkripsiMenunggu();

        return SkripsiResource::collection($skripsi);
    }

    /**
     * GET /verifikasi/skripsi/{id}
     * Detail satu skripsi beserta riwayat verifikasi.
     */
    public function show(string $id): SkripsiResource
    {
        $skripsi = $this->verifikasiService->getDetail((int) $id);

        return new SkripsiResource($skripsi);
    }

    /**
     * POST /verifikasi/skripsi/{id}/approve
     * Setujui skripsi.
     */
    public function approve(Request $request, string $id, EmbeddingService $embeddingService): JsonResponse
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $skripsi = $this->verifikasiService->approve(
            skripsiId: (int) $id,
            kepala: $request->user(),
            catatan: $request->catatan,
        );

        $embeddingService->generete($skripsi);

        return response()->json([
            'message' => 'Skripsi berhasil disetujui',
        ], 200);
    }

    /**
     * POST /verifikasi/skripsi/{id}/reject
     * Tolak skripsi.
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->verifikasiService->reject(
            skripsiId: (int) $id,
            kepala: $request->user(),
            catatan: $request->catatan,
        );

        return response()->json([
            'message' => 'Skripsi berhasil ditolak',
        ], 200);
    }
}
