<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\EmbeddingController;
use App\Http\Controllers\Api\KepalaMahasiswaController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Api\SavedController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SearchHistoryController;
use App\Http\Controllers\Api\StatistikController;
use App\Http\Controllers\Api\VerifikasiController;
use Illuminate\Support\Facades\Route;


// login
Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {

    // url logout
    Route::get('/logout', [AuthenticationController::class, 'logout']);

    //profil
    Route::get('/profil', [ProfilController::class, 'show']);

    Route::middleware('role:mahasiswa')->group(function () {

        //mahasiswa
        Route::get('/my-skripsi', [MahasiswaController::class, 'index']);
        Route::get('/my-skripsi/{id}', [MahasiswaController::class, 'show']);
        Route::put('/my-skripsi/{id}', [MahasiswaController::class,'update']);
        Route::delete('/my-skripsi/{id}', [MahasiswaController::class,'destroy']);

        // search
        Route::post('/search/semantic', [SearchController::class, 'semantic']);
        Route::get('/search-history', [SearchHistoryController::class, 'index']);
        Route::delete('/search-history/all', [SearchHistoryController::class, 'destroyAll']);
        Route::delete('/search-history/{id}', [SearchHistoryController::class, 'destroy']);

        //save
        Route::get('/saved-skripsi', [SavedController::class, 'index']);
        Route::post('/saved-skripsi/{skripsi}', [SavedController::class, 'store']);
        Route::delete('/saved-skripsi/{skripsi}', [SavedController::class, 'destroy']);
    });

    //repository
    Route::get('/repository/skripsi', [RepositoryController::class, 'index']);
    Route::get('/repository/skripsi/{id}', [RepositoryController::class, 'show']);

    // kepala perpustakaan
    Route::middleware('role:kepala_perpustakaan')->group(function () {
        
        Route::get('/verifikasi/skripsi', [VerifikasiController::class, 'index']);
        Route::get('/verifikasi/skripsi/{id}', [VerifikasiController::class, 'show']);
        Route::post('/verifikasi/skripsi/{id}/approve', [VerifikasiController::class, 'approve']);
        Route::post('/verifikasi/skripsi/{id}/reject', [VerifikasiController::class, 'reject']);
        Route::get('/mahasiswa', [KepalaMahasiswaController::class, 'index']);
        Route::get('/statistik', [StatistikController::class, 'index']);
    });

});

Route::post(
    '/embeddings/{id}',
    [EmbeddingController::class, 'generate']
);

