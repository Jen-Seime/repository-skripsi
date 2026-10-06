<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramStudiResource;
use App\Services\ProgramStudiService;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    public function __construct(protected ProgramStudiService $programStudiService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(ProgramStudiService $programStudiService)
    {
        $hasil = $programStudiService->getProgramStudi();

        return ProgramStudiResource::collection($hasil);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ProgramStudiService $programStudiService)
    {
        $validated = $request->validate([
            'kode' => 'required',
            'nama' => 'required',
        ]);

        $programStudiService->createProgramStudi($validated);

        return response()->json([
            'message' => 'Program Studi berhasil dibuat',
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
    public function update(Request $request, int $id, ProgramStudiService $programStudiService)
    {
        $validated = $request->validate([
            'kode' => 'required',
            'nama' => 'required',
        ]);

        $programStudiService->updateProgramStudi($validated, $id);

        return response()->json([
            'message' => 'Program Studi berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->programStudiService->deleteProgramStudi($id);

        return response()->json([
            'message' => 'Program Studi berhasil dihapus',
        ], 200);
    }
}
