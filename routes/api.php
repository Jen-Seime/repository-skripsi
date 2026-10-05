<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\EmbeddingController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProfilController;
use App\Http\Controllers\Api\RepositoryController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SearchHistoryController;
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
        Route::post('/saved-skripsi', [MahasiswaController::class, 'store']);
        Route::put('/my-skripsi/{id}', [MahasiswaController::class,'update']);
        Route::delete('/my-skripsi/{id}', [MahasiswaController::class,'destroy']);

        // search
        Route::post('/search/semantic', [SearchController::class, 'semantic']);
        Route::get('/search-history', [SearchHistoryController::class, 'index']);
        Route::delete('/search-history/all', [SearchHistoryController::class, 'destroyAll']);
        Route::delete('/search-history/{id}', [SearchHistoryController::class, 'destroy']);
    });

    //repository
    Route::get('/repository/skripsi', [RepositoryController::class, 'index']);
    Route::get('/repository/skripsi/{id}', [RepositoryController::class, 'show']);

});

Route::post(
    '/embeddings/{id}',
    [EmbeddingController::class, 'generate']
);

