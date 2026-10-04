<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required',
            'abstrak' => 'required',
            'kata_kunci' => 'required',
            'tahun' => 'required',
            'file_skripsi' => 'required|file|mimes:pdf|max:20480',
            'file_source_code' => 'nullable|file|mimes:zip,rar,tar,gz|max:51200',
        ]);

        $mahasiswa = $request->user()->mahasiswa;

        
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('mahasiswa.programStudi');

        return new ProfilResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
