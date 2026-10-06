<?php
namespace App\Services;

use App\Models\Kategori;

class KategoriService {

    public function getKategori()
    {
        return Kategori::all();
    }

    public function createKategori(array $data) : Kategori
    {
        return Kategori::create([
            'nama' => $data['nama'],
            'slug' => $data['slug']
        ]);
    }

    public function updateKategori(array $data, int $id): Kategori
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->update([
            'nama' => $data['nama'],
            'slug' => $data['slug'],
        ]);

        return $kategori;
    }

    public function deleteKategori(int $id): void
    {
        Kategori::findOrFail($id)->delete();
    }
}