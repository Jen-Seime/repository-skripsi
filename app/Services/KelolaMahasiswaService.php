<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class KelolaMahasiswaService
{
    /**
     * Ambil daftar akun mahasiswa dengan filter dan paginasi.
     *
     * @param  array{q?: ?string, search?: ?string, program_studi_id?: ?int, angkatan?: ?int, is_active?: ?bool, per_page?: ?int}  $filters
     */
    public function getMahasiswaList(array $filters = []): LengthAwarePaginator
    {
        $search = $filters['q'] ?? $filters['search'] ?? null;

        return User::query()
            ->where('role', UserRole::Mahasiswa)
            ->with('mahasiswa.programStudi')
            ->when($search, function ($query, string $keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($keyword) {
                            $mahasiswaQuery->where('nim', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($filters['program_studi_id'] ?? null, function ($query, $prodiId) {
                $query->whereHas('mahasiswa', function ($mahasiswaQuery) use ($prodiId) {
                    $mahasiswaQuery->where('program_studi_id', $prodiId);
                });
            })
            ->when($filters['angkatan'] ?? null, function ($query, $angkatan) {
                $query->whereHas('mahasiswa', function ($mahasiswaQuery) use ($angkatan) {
                    $mahasiswaQuery->where('angkatan', $angkatan);
                });
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== '', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->latest('id')
            ->paginate((int) ($filters['per_page'] ?? 15));
    }

    /**
     * Ambil detail satu akun mahasiswa berdasarkan ID User.
     */
    public function getMahasiswaById(int $id): User
    {
        return User::query()
            ->where('role', UserRole::Mahasiswa)
            ->with('mahasiswa.programStudi')
            ->findOrFail($id);
    }

    /**
     * Buat akun mahasiswa baru beserta data profil mahasiswanya.
     *
     * @param  array{name: string, email: string, password: string, nim: string, program_studi_id: int, angkatan: int, is_active?: bool}  $data
     */
    public function createMahasiswa(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => UserRole::Mahasiswa,
                'is_active' => $data['is_active'] ?? true,
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => $data['nim'],
                'program_studi_id' => $data['program_studi_id'],
                'angkatan' => $data['angkatan'],
            ]);

            return $user->load('mahasiswa.programStudi');
        });
    }

    /**
     * Perbarui data akun mahasiswa dan profil mahasiswanya.
     *
     * @param  array{name: string, email: string, password?: ?string, nim: string, program_studi_id: int, angkatan: int, is_active?: bool}  $data
     */
    public function updateMahasiswa(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data): User {
            $user = $this->getMahasiswaById($id);

            $userPayload = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $userPayload['password'] = $data['password'];
            }

            if (array_key_exists('is_active', $data) && $data['is_active'] !== null) {
                $userPayload['is_active'] = (bool) $data['is_active'];
            }

            $user->update($userPayload);

            $user->mahasiswa()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $data['nim'],
                    'program_studi_id' => $data['program_studi_id'],
                    'angkatan' => $data['angkatan'],
                ]
            );

            if (! $user->is_active) {
                $user->tokens()->delete();
            }

            return $user->load('mahasiswa.programStudi');
        });
    }

    /**
     * Aktifkan atau nonaktifkan akun mahasiswa serta cabut token jika dinonaktifkan.
     */
    public function toggleStatusMahasiswa(int $id, ?bool $isActive = null): User
    {
        $user = $this->getMahasiswaById($id);
        $newStatus = $isActive ?? ! $user->is_active;

        $user->update([
            'is_active' => $newStatus,
        ]);

        if (! $newStatus) {
            $user->tokens()->delete();
        }

        return $user->load('mahasiswa.programStudi');
    }

    /**
     * Hapus akun mahasiswa beserta sesi login aktifnya.
     */
    public function deleteMahasiswa(int $id): void
    {
        DB::transaction(function () use ($id): void {
            $user = $this->getMahasiswaById($id);
            $user->tokens()->delete();
            $user->delete();
        });
    }
}
