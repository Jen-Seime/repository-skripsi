<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\EmbeddingController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\KepalaMahasiswaController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\ProgramStudiController;
use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Api\SavedController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SearchHistoryController;
use App\Http\Controllers\Api\StatistikController;
use App\Http\Controllers\Api\VerifikasiController;
use Illuminate\Support\Facades\Route;

// login
Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // repository
    Route::get('/repository/skripsi', [RepositoryController::class, 'index']);
    Route::get('/repository/skripsi/{id}', [RepositoryController::class, 'show']);

    // url logout
    Route::get('/logout', [AuthenticationController::class, 'logout']);

    // profil
    Route::get('/profil', [ProfilController::class, 'show']);

    Route::middleware('role:mahasiswa')->group(function () {

        // mahasiswa
        Route::get('/my-skripsi', [MahasiswaController::class, 'index']);
        Route::post('/my-skripsi', [MahasiswaController::class, 'store']);
        Route::get('/my-skripsi/{id}', [MahasiswaController::class, 'show']);
        Route::put('/my-skripsi/{id}', [MahasiswaController::class, 'update']);
        Route::delete('/my-skripsi/{id}', [MahasiswaController::class, 'destroy']);

        // search
        Route::post('/search/semantic', [SearchController::class, 'semantic']);
        Route::get('/search-history', [SearchHistoryController::class, 'index']);
        Route::delete('/search-history/all', [SearchHistoryController::class, 'destroyAll']);
        Route::delete('/search-history/{id}', [SearchHistoryController::class, 'destroy']);

        // save
        Route::get('/saved-skripsi', [SavedController::class, 'index']);
        Route::post('/saved-skripsi/{skripsi}', [SavedController::class, 'store']);
        Route::delete('/saved-skripsi/{skripsi}', [SavedController::class, 'destroy']);
    });

    // kepala perpustakaan & super admin
    Route::middleware('role:kepala_perpustakaan,super_admin')->group(function () {

        Route::get('/verifikasi/skripsi', [VerifikasiController::class, 'index']);
        Route::get('/verifikasi/skripsi/{id}', [VerifikasiController::class, 'show']);
        Route::post('/verifikasi/skripsi/{id}/approve', [VerifikasiController::class, 'approve']);
        Route::post('/verifikasi/skripsi/{id}/reject', [VerifikasiController::class, 'reject']);

        // kelola akun mahasiswa
        Route::get('/mahasiswa', [KepalaMahasiswaController::class, 'index']);
        Route::post('/mahasiswa', [KepalaMahasiswaController::class, 'store']);
        Route::get('/mahasiswa/{id}', [KepalaMahasiswaController::class, 'show']);
        Route::put('/mahasiswa/{id}', [KepalaMahasiswaController::class, 'update']);
        Route::patch('/mahasiswa/{id}/toggle-status', [KepalaMahasiswaController::class, 'toggleStatus']);
        Route::delete('/mahasiswa/{id}', [KepalaMahasiswaController::class, 'destroy']);

        Route::get('/statistik', [StatistikController::class, 'index']);

        Route::post('/kategori', [KategoriController::class, 'store']);
        Route::put('/kategori/{id}', [KategoriController::class, 'update']);
        Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

        Route::post('/program-studi', [ProgramStudiController::class, 'store']);
        Route::put('/program-studi/{id}', [ProgramStudiController::class, 'update']);
        Route::delete('/program-studi/{id}', [ProgramStudiController::class, 'destroy']);

    });

    // super admin (kelola akun admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index']);
        Route::post('/admin', [AdminController::class, 'store']);
        Route::get('/admin/{id}', [AdminController::class, 'show']);
        Route::put('/admin/{id}', [AdminController::class, 'update']);
        Route::patch('/admin/{id}/toggle-status', [AdminController::class, 'toggleStatus']);
        Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
    });

});

Route::post(
    '/embeddings/{id}',
    [EmbeddingController::class, 'generate']
);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/program-studi', [ProgramStudiController::class, 'index']);
