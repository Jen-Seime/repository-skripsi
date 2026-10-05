<?php

namespace App\Http\Controllers\Api;

use App\Enums\SkripsiStatus;
use App\Http\Controllers\Controller;
use App\Models\Skripsi;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;

class EmbeddingController extends Controller
{
    public function generate(Request $request, int $id, EmbeddingService $embeddingService)
    {
        $skripsi = Skripsi::findOrFail($id);
        if ($skripsi->status === SkripsiStatus::Disetujui) {
            $embedding = $embeddingService->generete($skripsi);
            return response()->json([
                'message' => 'Embedding berhasil dibuat',
                'data' => $embedding,
            ]);
        }
        return response()->json([
            'message' => 'Skripsi belum disetujui',
        ], 400);
    }
}
