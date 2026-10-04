<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkripsiResource;
use App\Services\MahasiswaSkripsiService;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function __construct(
        protected MahasiswaSkripsiService $skripsiService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mahasiswa = $request->user()->mahasiswa;

        $skripsi = $this->skripsiService->getMySkripsi($mahasiswa);

        return SkripsiResource::collection($skripsi);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id'      => 'required|exists:kategori,id',
            'judul'            => 'required|string|max:255',
            'abstrak'          => 'required|string',
            'kata_kunci'       => 'required|string|max:255',
            'tahun'            => 'required|integer|digits:4',
            'file_skripsi'     => 'required|file|mimes:pdf|max:20480', // Maks 20MB PDF
            'file_source_code' => 'nullable|file|mimes:zip,rar,tar,gz|max:51200', // Maks 50MB (Opsional)
        ]);

        $uploadFiles = [
            'file_skripsi' => $request->file('file_skripsi'),
            'file_source_code' => $request->file('file_source_code')
        ];

        $mahasiswa = $request->user()->mahasiswa;

        $skripsiCreate = $this->skripsiService->createSkripsi($mahasiswa,$validated,$uploadFiles );
        

        return new SkripsiResource($skripsiCreate );

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $mahasiswa = $request->user()->mahasiswa;

        $skripsi = $this->skripsiService->showMySkripsi($mahasiswa, (int) $id);

        return new SkripsiResource($skripsi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'kategori_id'      => 'required|exists:kategori,id',
            'judul'            => 'required|string|max:255',
            'abstrak'          => 'required|string',
            'kata_kunci'       => 'required|string|max:255',
            'tahun'            => 'required|integer|digits:4',
            'file_skripsi'     => 'nullable|file|mimes:pdf|max:20480', // Maks 20MB PDF (Opsional saat update)
            'file_source_code' => 'nullable|file|mimes:zip,rar,tar,gz|max:51200', // Maks 50MB (Opsional)
        ]);

        $uploadFiles = [
            'file_skripsi' => $request->file('file_skripsi'),
            'file_source_code' => $request->file('file_source_code')
        ];

        $mahasiswa = $request->user()->mahasiswa;

        $skripsiUpdate = $this->skripsiService->updateSkripsi($mahasiswa, $validated, $uploadFiles, (int) $id);

        return new SkripsiResource($skripsiUpdate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $mahasiswa = $request->user()->mahasiswa;

        $this->skripsiService->deleteSkripsi($mahasiswa, (int) $id);

        return response()->json([
            'message' => 'Skripsi berhasil dihapus',
        ], 200);
    }
}
