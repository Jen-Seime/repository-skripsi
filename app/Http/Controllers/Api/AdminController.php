<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfilResource;
use App\Services\KelolaAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct(protected KelolaAdminService $kelolaAdminService) {}

    /**
     * GET /admin
     * Daftar semua akun admin (kepala_perpustakaan).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $admins = $this->kelolaAdminService->getAdminList(
            $request->only(['q', 'search', 'is_active', 'per_page'])
        );

        return ProfilResource::collection($admins);
    }

    /**
     * GET /admin/{id}
     * Detail satu akun admin.
     */
    public function show(int $id): ProfilResource
    {
        $admin = $this->kelolaAdminService->getAdminById($id);

        return new ProfilResource($admin);
    }

    /**
     * POST /admin
     * Tambah akun admin baru (khusus super_admin).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $admin = $this->kelolaAdminService->createAdmin($validated);

        return response()->json([
            'message' => 'Akun admin berhasil ditambahkan',
            'data' => new ProfilResource($admin),
        ], 201);
    }

    /**
     * PUT /admin/{id}
     * Edit akun admin (khusus super_admin).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $admin = $this->kelolaAdminService->getAdminById($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updatedAdmin = $this->kelolaAdminService->updateAdmin($id, $validated);

        return response()->json([
            'message' => 'Akun admin berhasil diperbarui',
            'data' => new ProfilResource($updatedAdmin),
        ]);
    }

    /**
     * PATCH /admin/{id}/toggle-status
     * Nonaktifkan atau aktifkan kembali akun admin (khusus super_admin).
     */
    public function toggleStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
        ]);

        $admin = $this->kelolaAdminService->toggleStatusAdmin(
            $id,
            $validated['is_active'] ?? null
        );

        $statusMessage = $admin->is_active
            ? 'Akun admin berhasil diaktifkan'
            : 'Akun admin berhasil dinonaktifkan';

        return response()->json([
            'message' => $statusMessage,
            'data' => new ProfilResource($admin),
        ]);
    }

    /**
     * DELETE /admin/{id}
     * Hapus akun admin (khusus super_admin).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->kelolaAdminService->deleteAdmin($id);

        return response()->json([
            'message' => 'Akun admin berhasil dihapus',
        ]);
    }
}
