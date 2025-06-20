<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RTController;
use App\Http\Controllers\RWController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\SettingsController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index']);
});
Route::middleware(['auth:sanctum', 'role:rw'])->get('/rw/dashboard', [RWController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:rt'])->get('/rt/dashboard', [RTController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    // CRUD Penduduk
    Route::get('/penduduk', [PendudukController::class, 'index']); // GET list semua penduduk
    Route::post('/penduduk', [PendudukController::class, 'store']); // POST data penduduk baru
    Route::get('/penduduk/{id}', [PendudukController::class, 'show']); // GET detail penduduk
    Route::put('/penduduk/{id}', [PendudukController::class, 'update']); // PUT update penduduk
    Route::delete('/penduduk/{id}', [PendudukController::class, 'destroy']); // DELETE penduduk

    // Dropdown filter (bisa dipakai di Flutter juga)
    Route::get('/get-rw-by-daerah', [PendudukController::class, 'getRWByDaerah']);
    Route::get('/get-rt-by-daerah-rw', [PendudukController::class, 'getRTByDaerahRW']);
    Route::get('/get-daerah-users', [PendudukController::class, 'getDaerahFromUsers']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::put('/settings', [SettingsController::class, 'update']);
    Route::put('/settings/password', [SettingsController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum')->get('/activity', [ActivityController::class, 'index']);