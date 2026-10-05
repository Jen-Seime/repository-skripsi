<?php
namespace App\Services;

use App\Models\Embedding;
use Laravel\Ai\Embeddings;

class SemanticSearchService {

    public function search(string $query)
    {
        $respone = Embeddings::for([
            $query
        ])->generate();

        $vector = $respone->embeddings[0];

        $embeddings = Embedding::with('skripsi')
        ->whereHas('skripsi', function($q) {

            $q->where('status', 'disetujui');
        })->get();

        // 3. Hitung similarity
        $results = $embeddings->map(function($embedding) use($vector) {

            $similarity = $this->cosineSimilarity($vector, $embedding->embedding);

            return [
                'skripsi' => $embedding->skripsi,
                'similarity' => $similarity,
            ];
        })->filter(fn ($item) => $item['similarity'] >= 0.5);

        
         // 4. Ranking
        return $results
            ->sortByDesc('similarity')
            ->values();
    }

    public function cosineSimilarity(
        array $vectorA,
        array $vectorB
    ): float {
        if (count($vectorA) !== count($vectorB)) {
            return 0;
        }

        $dotProduct = 0;
        $magnitudeA = 0;
        $magnitudeB = 0;

        foreach ($vectorA as $i => $valueA) {
            $valueB = $vectorB[$i];

            $dotProduct += $valueA * $valueB;

            $magnitudeA += $valueA ** 2;
            $magnitudeB += $valueB ** 2;
        }

        $magnitudeA = sqrt($magnitudeA);
        $magnitudeB = sqrt($magnitudeB);

        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0;
        }

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }
}