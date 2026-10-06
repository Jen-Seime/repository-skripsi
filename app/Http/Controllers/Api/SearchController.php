<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkripsiResource;
use App\Services\searchHistoryService;
use App\Services\SemanticSearchService;
use Illuminate\Http\Request;

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

        

        if($results->isEmpty()) {
            return response()->json([
                'query' => $request->q,
                'data' => SkripsiResource::collection($skripsi) ,
                'message' => "idak ditemukan skripsi yang relevan dengan pencarian Anda."
            ]);
        }else {
            //simpan storiy serach
            $historyService->searchHistory(['query' => $request->q], $mahasiswa);
            
            return response()->json([
                'query' => $request->q,
                'data' => SkripsiResource::collection($skripsi),
            ]);
        }

        
    }
}
