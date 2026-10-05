<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SavedSkripsiResource;
use App\Services\SavedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SavedController extends Controller
{
    public function __construct(
        protected SavedService $savedService
    ) {}

    /**
     * Display a listing of saved skripsi.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $mahasiswa = $request->user()->mahasiswa;

        $savedSkripsi = $this->savedService->getSaved($mahasiswa);

        return SavedSkripsiResource::collection($savedSkripsi);
    }

    /**
     * Save a skripsi.
     */
    public function store(Request $request, string $skripsi): JsonResponse
    {
        $mahasiswa = $request->user()->mahasiswa;

        $this->savedService->saveSkripsi($mahasiswa, (int) $skripsi);

        return response()->json([
            'message' => 'Skripsi berhasil disimpan',
        ], 201);
    }

    /**
     * Unsave / remove a skripsi from saved list.
     */
    public function destroy(Request $request, string $skripsi): JsonResponse
    {
        $mahasiswa = $request->user()->mahasiswa;

        $this->savedService->unsaveSkripsi($mahasiswa, (int) $skripsi);

        return response()->json([
            'message' => 'Skripsi berhasil dihapus dari simpanan',
        ], 200);
    }
}
