<?php
namespace App\Services;

use App\Models\Mahasiswa;
use App\Models\SearchHistory;

class searchHistoryService {

    public function searchHistory(array $data, Mahasiswa $mahasiswa): SearchHistory
    {
        return SearchHistory::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'query'        => $data['query'],
            ]
        );
    }

    public function getHistory(Mahasiswa $mahasiswa)
    {
        return $mahasiswa->searchHistories()
            ->latest('id')
            ->get();
    }

    public function deleteHistory(Mahasiswa $mahasiswa, int $id): void
    {
        $mahasiswa->searchHistories()->findOrFail($id)->delete();
    }

    public function deleteHistoryAll(Mahasiswa $mahasiswa): void
    {
        $mahasiswa->searchHistories()->delete();
    }
}