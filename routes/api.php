<?php

use App\Http\Controllers\Api\AuthenticationController;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// login
Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function() {

    // url logout
    Route::get('/logout', [AuthenticationController::class, 'logout']);

    // Route::middleware('role:mahasiswa')->group(function () {

    // };

});
