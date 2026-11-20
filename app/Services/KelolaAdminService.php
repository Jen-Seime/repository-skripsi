<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class KelolaAdminService
{
    /**
     * Ambil daftar akun admin (kepala_perpustakaan) dengan filter dan paginasi.
     *
     * @param  array{q?: ?string, search?: ?string, is_active?: ?bool, per_page?: ?int}  $filters
     */
    public function getAdminList(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['q'] ?? $filters['search'] ?? null;

        return User::query()
            ->where('role', UserRole::KepalaPerpustakaan)
            ->when($search, function ($query, string $keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Ambil detail satu akun admin berdasarkan ID User.
     */
    public function getAdminById(int $id): User
    {
        return User::query()
            ->where('role', UserRole::KepalaPerpustakaan)
            ->findOrFail($id);
    }

    /**
     * Buat akun admin (kepala_perpustakaan) baru.
     *
     * @param  array{name: string, email: string, password: string, is_active?: bool}  $data
     */
    public function createAdmin(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::KepalaPerpustakaan,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * Perbarui data akun admin.
     *
     * @param  array{name: string, email: string, password?: ?string, is_active?: bool}  $data
     */
    public function updateAdmin(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data): User {
            $admin = $this->getAdminById($id);

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = $data['password'];
            }

            if (array_key_exists('is_active', $data) && $data['is_active'] !== null) {
                $payload['is_active'] = (bool) $data['is_active'];
            }

            $admin->update($payload);

            if (! $admin->is_active) {
                $admin->tokens()->delete();
            }

            return $admin;
        });
    }

    /**
     * Aktifkan atau nonaktifkan akun admin serta cabut token jika dinonaktifkan.
     */
    public function toggleStatusAdmin(int $id, ?bool $isActive = null): User
    {
        $admin = $this->getAdminById($id);
        $newStatus = $isActive ?? ! $admin->is_active;

        $admin->update([
            'is_active' => $newStatus,
        ]);

        if (! $newStatus) {
            $admin->tokens()->delete();
        }

        return $admin;
    }

    /**
     * Hapus akun admin beserta seluruh token login aktifnya.
     */
    public function deleteAdmin(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $admin = $this->getAdminById($id);
            $admin->tokens()->delete();
            $admin->delete();
        });
    }
}
