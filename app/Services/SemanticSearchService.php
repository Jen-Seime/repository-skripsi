<?php

namespace App\Services;

use App\Models\Embedding;
use App\Models\Skripsi;
use Illuminate\Support\Collection;
use Laravel\Ai\Embeddings;

class SemanticSearchService
{
    public function search(string $query): Collection
    {
        $cleanedQuery = $this->cleanQuery($query);
        $keywords = $this->extractKeywords($cleanedQuery);

        $response = Embeddings::for([$cleanedQuery])
            ->withProviderOptions([
                'taskType' => 'RETRIEVAL_QUERY',
            ])
            ->generate();

        $vector = $response->embeddings[0];

        $embeddings = Embedding::with([
            'skripsi.programStudi',
            'skripsi.kategori',
            'skripsi.mahasiswa.user',
            'skripsi.files',
        ])
            ->whereHas('skripsi', function ($q) {
                $q->where('status', 'disetujui');
            })
            ->get();

        // Hitung hybrid similarity (Semantic Cosine Similarity + Lexical Keyword Boost)
        $scoredResults = $embeddings->map(function (Embedding $embedding) use ($vector, $keywords) {
            $semanticSimilarity = $this->cosineSimilarity($vector, $embedding->embedding);

            [$lexicalBoost, $matchedKeywordCount] = $this->calculateLexicalBoost(
                $embedding->skripsi,
                $keywords
            );

            $finalSimilarity = min(1.0, $semanticSimilarity + $lexicalBoost);

            return [
                'skripsi' => $embedding->skripsi,
                'similarity' => $finalSimilarity,
                'semantic_similarity' => $semanticSimilarity,
                'matched_keywords' => $matchedKeywordCount,
            ];
        });

        $maxSimilarity = $scoredResults->max('similarity') ?? 0.0;

        // Filter berdasarkan minimum threshold (0.60) & jarak relatif terhadap hasil tertinggi
        $results = $scoredResults->filter(function (array $item) use ($maxSimilarity) {
            if ($item['similarity'] < 0.60) {
                return false;
            }

            if ($item['matched_keywords'] > 0) {
                return $item['similarity'] >= ($maxSimilarity - 0.12);
            }

            return $item['similarity'] >= ($maxSimilarity - 0.04);
        });

        // Ranking dari similarity tertinggi
        return $results
            ->sortByDesc('similarity')
            ->values();
    }

    /**
     * Bersihkan kata pengantar umum (seperti "cari skripsi tentang ...") agar tidak mengaburkan vektor topik.
     */
    protected function cleanQuery(string $query): string
    {
        $cleaned = preg_replace(
            '/^(?:tolong\s+)?(?:cari|mencari|carikan|tampilkan|lihat)?\s*(?:data\s+|daftar\s+|koleksi\s+|referensi\s+|dokumen\s+|jurnal\s+|skripsi\s+|penelitian\s+|tugas\s+akhir\s+)+(?:tentang|mengenai|terkait|yang\s+membahas|berhubungan\s+dengan|pada|untuk|di\s+bidang)?\s*/iu',
            '',
            trim($query)
        );

        $cleaned = trim((string) $cleaned);

        return $cleaned !== '' ? $cleaned : trim($query);
    }

    /**
     * Ekstrak kata kunci penting dari query untuk pencocokan leksikal pendukung.
     *
     * @return list<string>
     */
    protected function extractKeywords(string $cleanedQuery): array
    {
        $stopwords = [
            'dan', 'atau', 'yang', 'di', 'ke', 'dari', 'pada', 'untuk', 'dengan',
            'dalam', 'terhadap', 'sebagai', 'oleh', 'tentang', 'mengenai', 'berbasis',
            'menggunakan', 'pengaruh', 'analisis', 'pengembangan', 'sistem', 'informasi',
            'aplikasi', 'studi', 'kasus', 'skripsi', 'penelitian', 'metode', 'hasil',
        ];

        $words = preg_split('/[^a-zA-Z0-9]+/u', mb_strtolower($cleanedQuery), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter(
            $words,
            fn (string $word): bool => mb_strlen($word) >= 2 && ! in_array($word, $stopwords, true)
        ));
    }

    /**
     * Hitung tambahan skor (boost) jika kata kunci pada query ditemukan di judul, kategori, kata kunci, atau abstrak.
     *
     * @param  list<string>  $keywords
     * @return array{0: float, 1: int}
     */
    protected function calculateLexicalBoost(?Skripsi $skripsi, array $keywords): array
    {
        if (! $skripsi || $keywords === []) {
            return [0.0, 0];
        }

        $primaryText = mb_strtolower(implode(' ', array_filter([
            $skripsi->judul,
            $skripsi->kata_kunci,
            $skripsi->kategori?->nama,
            $skripsi->programStudi?->nama,
        ])));

        $secondaryText = mb_strtolower((string) $skripsi->abstrak);

        $boost = 0.0;
        $matchedKeywords = 0;

        foreach ($keywords as $keyword) {
            $pattern = '/\b'.preg_quote($keyword, '/').'/iu';

            if (preg_match($pattern, $primaryText)) {
                $boost += 0.08;
                $matchedKeywords++;
            } elseif (preg_match($pattern, $secondaryText)) {
                $boost += 0.06;
                $matchedKeywords++;
            }
        }

        return [min($boost, 0.12), $matchedKeywords];
    }

    /**
     * @param  array<int, float>  $vectorA
     * @param  array<int, float>  $vectorB
     */
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
