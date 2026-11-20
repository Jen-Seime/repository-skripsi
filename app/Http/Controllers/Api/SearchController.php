<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkripsiResource;
use App\Services\searchHistoryService;
use App\Services\SemanticSearchService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function semantic(
        Request $request,
        SemanticSearchService $searchService,
        searchHistoryService $historyService
    ) {
        $request->validate([
            'q' => ['required', 'string', 'min:3'],
        ]);

        $results = $searchService->search(
            $request->q
        );

        $mahasiswa = $request->user()->mahasiswa;

        $skripsi = $results->map(function ($result) {
            $item = $result['skripsi'];

            $item->similarity = $result['similarity'];

            return $item;
        });

        if ($results->isEmpty()) {
            return response()->json([
                'query' => $request->q,
                'data' => SkripsiResource::collection($skripsi),
                'message' => 'idak ditemukan skripsi yang relevan dengan pencarian Anda.',
            ]);
        } else {
            // simpan storiy serach
            $historyService->searchHistory(['query' => $request->q], $mahasiswa);

            // ==========================================
            // 📄 LOGIKA PAGINATION MANUAL
            // ==========================================
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 10); // Default 10 data per halaman

            // Potong collection sesuai halaman aktif
            $currentPageItems = $skripsi->slice(($page - 1) * $perPage, $perPage)->values();

            $paginated = new LengthAwarePaginator(
                $currentPageItems,
                $skripsi->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // SkripsiResource::collection otomatis menyertakan meta & links pagination
            return SkripsiResource::collection($paginated)->additional([
                'query' => $request->q,
            ]);

        }

    }
}
