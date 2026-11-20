<?php

namespace App\Services;

use App\Models\Embedding;
use App\Models\Skripsi;
use Laravel\Ai\Embeddings;

class EmbeddingService
{
    public function generete(Skripsi $skripsi): Embedding
    {
        return $this->generate($skripsi);
    }

    public function generate(Skripsi $skripsi): Embedding
    {
        $skripsi->loadMissing(['kategori', 'programStudi']);

        $teks = implode("\n", array_filter([
            "Judul: {$skripsi->judul}",
            $skripsi->kategori ? "Kategori: {$skripsi->kategori->nama}" : null,
            $skripsi->programStudi ? "Program Studi: {$skripsi->programStudi->nama}" : null,
            "Kata Kunci: {$skripsi->kata_kunci}",
            "Abstrak: {$skripsi->abstrak}",
            "Tahun: {$skripsi->tahun}",
        ]));

        $response = Embeddings::for([$teks])
            ->withProviderOptions([
                'taskType' => 'RETRIEVAL_DOCUMENT',
                'title' => $skripsi->judul,
            ])
            ->generate();

        $vector = $response->embeddings[0];

        return Embedding::updateOrCreate(
            [
                'skripsi_id' => $skripsi->id,
            ],
            [
                'model' => $response->meta->model ?? 'text-embedding-model',
                'dimension' => count($vector),
                'embedding' => $vector,
            ]
        );
    }
}
