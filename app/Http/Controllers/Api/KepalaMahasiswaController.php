<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource;
use App\Models\Mahasiswa;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KepalaMahasiswaController extends Controller
{
    /**
     * GET /mahasiswa
     * Daftar semua mahasiswa.
     */
    public function index(): AnonymousResourceCollection
    {
        $mahasiswa = Mahasiswa::with(['user', 'programStudi'])
            ->latest('id')
            ->paginate(15);

        return ProfilResource::collection($mahasiswa->map(fn ($m) => $m->user));
    }
}
