<?php
namespace App\Services;

use App\Models\Embedding;
use App\Models\Skripsi;
use Laravel\Ai\Embeddings;

class EmbeddingService {
    public function generete(Skripsi $skripsi)
    {
        $teks = implode("n/", [
            $skripsi->judul,
            $skripsi->abstrak,
            $skripsi->kata_kunci,
            $skripsi->tahun
        ]);

        $reponse = Embeddings::for([
            $teks
        ])->generate();

        $vector = $reponse->embeddings[0];

        return Embedding::updateOrCreate(
            [
                'skripsi_id' => $skripsi->id,
            ],
            [
                'model' => 'text-embedding-model',
                'dimension' => count($vector),
                'embedding' => $vector,
            ]
        );

    }
}