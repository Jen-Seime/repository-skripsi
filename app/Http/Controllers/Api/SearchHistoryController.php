<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchHistoryResource;
use App\Services\searchHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchHistoryController extends Controller
{
    public function __construct(
        protected searchHistoryService $historyService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $mahasiswa = $request->user()->mahasiswa;

        $histories = $this->historyService->getHistory($mahasiswa);

        return SearchHistoryResource::collection($histories);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $mahasiswa = $request->user()->mahasiswa;

        $this->historyService->deleteHistory($mahasiswa, (int) $id);

        return response()->json([
            'message' => 'Riwayat pencarian berhasil dihapus',
        ], 200);
    }

    public function destroyAll(Request $request): JsonResponse
    {
        $mahasiswa = $request->user()->mahasiswa;

        $this->historyService->deleteHistoryAll($mahasiswa);


        return response()->json([
            'message' => 'Riwayat pencarian berhasil dihapus',
        ], 200);

        
    }
}
