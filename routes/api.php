<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProfilController;
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

    });

});
