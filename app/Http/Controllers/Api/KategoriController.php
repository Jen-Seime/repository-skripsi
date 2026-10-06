<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use App\Services\KategoriService;
use Illuminate\Http\Request;
use Laravel\Mcp\Response;

class KategoriController extends Controller
{
    public function __construct(  protected KategoriService $kategoriService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(KategoriService $kategori)
    {
        $hasil = $kategori->getKategori();

        return KategoriResource::collection($hasil);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, KategoriService $kategoriService)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'slug' => 'required'
        ]);

        $kategori = $kategoriService->createKategori($validated);

        return response()->json([
            'message' => 'kategori Berhasil dibuat',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id, KategoriService $kategoriService)
    {
        $validated = $request->validate([
            'nama' => 'required',
            'slug' => 'required'
        ]);

        $kategoriService->updateKategori($validated, $id);

        return response()->json([
            'message' => 'kategori Berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->kategoriService->deleteKategori($id);

        return response()->json([
            'message' => 'kategori Berhasil dihapus',
        ], 200);

    }
}
