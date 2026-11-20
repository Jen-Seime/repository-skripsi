<?php

use App\Enums\UserRole;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('kepala perpustakaan dapat menambah, mengedit, menonaktifkan, dan menghapus akun mahasiswa', function () {
    $admin = User::factory()->kepalaPerpustakaan()->create();
    $prodi = ProgramStudi::factory()->create();

    Sanctum::actingAs($admin);

    // 1. Tambah akun mahasiswa
    $createResponse = $this->postJson('/api/mahasiswa', [
        'name' => 'Budi Mahasiswa',
        'email' => 'budi@mahasiswa.ac.id',
        'password' => 'password123',
        'nim' => '220101001',
        'program_studi_id' => $prodi->id,
        'angkatan' => 2022,
    ]);

    $createResponse->assertCreated()
        ->assertJsonPath('data.email', 'budi@mahasiswa.ac.id')
        ->assertJsonPath('data.role', UserRole::Mahasiswa->value)
        ->assertJsonPath('data.is_active', true)
        ->assertJsonPath('data.mahasiswa.nim', '220101001');

    $mahasiswaUserId = $createResponse->json('data.id');
    $mahasiswaUser = User::findOrFail($mahasiswaUserId);
    $mahasiswaUser->createToken('testToken');
    expect($mahasiswaUser->tokens()->count())->toBe(1);

    // 2. Edit akun mahasiswa
    $updateResponse = $this->putJson("/api/mahasiswa/{$mahasiswaUserId}", [
        'name' => 'Budi Santoso Updated',
        'email' => 'budi@mahasiswa.ac.id',
        'nim' => '220101099',
        'program_studi_id' => $prodi->id,
        'angkatan' => 2023,
    ]);

    $updateResponse->assertOk()
        ->assertJsonPath('data.name', 'Budi Santoso Updated')
        ->assertJsonPath('data.mahasiswa.nim', '220101099')
        ->assertJsonPath('data.mahasiswa.angkatan', 2023);

    // 3. Nonaktifkan akun mahasiswa (cabut token otomatis)
    $deactivateResponse = $this->patchJson("/api/mahasiswa/{$mahasiswaUserId}/toggle-status", [
        'is_active' => false,
    ]);

    $deactivateResponse->assertOk()
        ->assertJsonPath('data.is_active', false);

    expect($mahasiswaUser->fresh()->is_active)->toBeFalse()
        ->and($mahasiswaUser->tokens()->count())->toBe(0);

    // 4. Hapus akun mahasiswa
    $deleteResponse = $this->deleteJson("/api/mahasiswa/{$mahasiswaUserId}");
    $deleteResponse->assertOk();

    $this->assertDatabaseMissing('users', ['id' => $mahasiswaUserId]);
    $this->assertDatabaseMissing('mahasiswa', ['user_id' => $mahasiswaUserId]);
});

test('admin biasa (kepala_perpustakaan) ditolak saat mengelola akun admin', function () {
    $admin = User::factory()->kepalaPerpustakaan()->create();
    $targetAdmin = User::factory()->kepalaPerpustakaan()->create();

    Sanctum::actingAs($admin);

    $this->getJson('/api/admin')->assertForbidden();

    $this->postJson('/api/admin', [
        'name' => 'Admin Baru',
        'email' => 'admin.baru@kampus.ac.id',
        'password' => 'password123',
    ])->assertForbidden();

    $this->putJson("/api/admin/{$targetAdmin->id}", [
        'name' => 'Admin Edit',
        'email' => $targetAdmin->email,
    ])->assertForbidden();

    $this->patchJson("/api/admin/{$targetAdmin->id}/toggle-status")->assertForbidden();
    $this->deleteJson("/api/admin/{$targetAdmin->id}")->assertForbidden();
});

test('super admin dapat menambah, mengedit, menonaktifkan, dan menghapus akun admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    Sanctum::actingAs($superAdmin);

    // 1. Tambah akun admin
    $createResponse = $this->postJson('/api/admin', [
        'name' => 'Admin Perpustakaan 1',
        'email' => 'admin1@kampus.ac.id',
        'password' => 'password123',
    ]);

    $createResponse->assertCreated()
        ->assertJsonPath('data.email', 'admin1@kampus.ac.id')
        ->assertJsonPath('data.role', UserRole::KepalaPerpustakaan->value)
        ->assertJsonPath('data.is_active', true);

    $adminId = $createResponse->json('data.id');
    $adminUser = User::findOrFail($adminId);
    $adminUser->createToken('adminToken');

    // 2. Edit akun admin
    $updateResponse = $this->putJson("/api/admin/{$adminId}", [
        'name' => 'Admin Perpustakaan Updated',
        'email' => 'admin1@kampus.ac.id',
    ]);

    $updateResponse->assertOk()
        ->assertJsonPath('data.name', 'Admin Perpustakaan Updated');

    // 3. Nonaktifkan akun admin
    $toggleResponse = $this->patchJson("/api/admin/{$adminId}/toggle-status");

    $toggleResponse->assertOk()
        ->assertJsonPath('data.is_active', false);

    expect($adminUser->fresh()->is_active)->toBeFalse()
        ->and($adminUser->tokens()->count())->toBe(0);

    // 4. Aktifkan kembali akun admin
    $reactivateResponse = $this->patchJson("/api/admin/{$adminId}/toggle-status");
    $reactivateResponse->assertOk()
        ->assertJsonPath('data.is_active', true);

    // 5. Hapus akun admin
    $this->deleteJson("/api/admin/{$adminId}")->assertOk();
    $this->assertDatabaseMissing('users', ['id' => $adminId]);
});

test('akun yang dinonaktifkan tidak dapat login', function () {
    $inactiveUser = User::factory()->mahasiswa()->inactive()->create([
        'email' => 'nonaktif@mahasiswa.ac.id',
        'password' => 'password123',
    ]);

    Mahasiswa::factory()->create(['user_id' => $inactiveUser->id]);

    $response = $this->postJson('/api/login', [
        'email' => 'nonaktif@mahasiswa.ac.id',
        'password' => 'password123',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});
