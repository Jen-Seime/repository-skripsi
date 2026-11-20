<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource;
use App\Services\KelolaMahasiswaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class KepalaMahasiswaController extends Controller
{
    public function __construct(protected KelolaMahasiswaService $kelolaMahasiswaService) {}

    /**
     * GET /mahasiswa
     * Daftar semua mahasiswa dengan filter dan paginasi.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $mahasiswa = $this->kelolaMahasiswaService->getMahasiswaList(
            $request->only(['q', 'search', 'program_studi_id', 'angkatan', 'is_active', 'per_page'])
        );

        return ProfilResource::collection($mahasiswa);
    }

    /**
     * GET /mahasiswa/{id}
     * Detail satu akun mahasiswa.
     */
    public function show(int $id): ProfilResource
    {
        $user = $this->kelolaMahasiswaService->getMahasiswaById($id);

        return new ProfilResource($user);
    }

    /**
     * POST /mahasiswa
     * Tambah akun mahasiswa baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'nim' => ['required', 'string', 'max:30', Rule::unique('mahasiswa', 'nim')],
            'program_studi_id' => ['required', 'integer', Rule::exists('program_studi', 'id')],
            'angkatan' => ['required', 'integer', 'digits:4'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = $this->kelolaMahasiswaService->createMahasiswa($validated);

        return response()->json([
            'message' => 'Akun mahasiswa berhasil ditambahkan',
            'data' => new ProfilResource($user),
        ], 201);
    }

    /**
     * PUT /mahasiswa/{id}
     * Edit akun mahasiswa.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->kelolaMahasiswaService->getMahasiswaById($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'nim' => ['required', 'string', 'max:30', Rule::unique('mahasiswa', 'nim')->ignore($user->mahasiswa?->id)],
            'program_studi_id' => ['required', 'integer', Rule::exists('program_studi', 'id')],
            'angkatan' => ['required', 'integer', 'digits:4'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updatedUser = $this->kelolaMahasiswaService->updateMahasiswa($id, $validated);

        return response()->json([
            'message' => 'Akun mahasiswa berhasil diperbarui',
            'data' => new ProfilResource($updatedUser),
        ]);
    }

    /**
     * PATCH /mahasiswa/{id}/toggle-status
     * Nonaktifkan atau aktifkan kembali akun mahasiswa.
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = $this->kelolaMahasiswaService->toggleStatusMahasiswa(
            $id,
            $validated['is_active'] ?? null
        );

        $statusMessage = $user->is_active
            ? 'Akun mahasiswa berhasil diaktifkan'
            : 'Akun mahasiswa berhasil dinonaktifkan';

        return response()->json([
            'message' => $statusMessage,
            'data' => new ProfilResource($user),
        ]);
    }

    /**
     * DELETE /mahasiswa/{id}
     * Hapus akun mahasiswa.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->kelolaMahasiswaService->deleteMahasiswa($id);

        return response()->json([
            'message' => 'Akun mahasiswa berhasil dihapus',
        ]);
    }
}
