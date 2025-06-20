<?php

use App\Http\Middleware\CekRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RTController;
use App\Http\Controllers\RWController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PendudukController;
use App\Http\Controllers\SettingsController;

// 🔑 Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::get('/cek', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();

        /** @var \App\Models\User $user */
        if ($user->hasRole('admin')) {
            return redirect('/dashboard-admin');
        } elseif ($user->hasRole('rw')) {
            return redirect('/dashboard-rw');
        } elseif ($user->hasRole('rt')) {
            return redirect('/dashboard-rt');
        } else {
            abort(403); // Tidak punya role yang dikenal
        }
    }

    return redirect()->route('login');
});

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard-admin', [AdminController::class, 'index'])
//         ->middleware(CekRole::class . ':admin')
//         ->name('admin.dashboard');

//     Route::get('/dashboard-rw', [RWController::class, 'index'])
//         ->middleware(CekRole::class . ':rw')
//         ->name('rw.dashboard');

//     Route::get('/dashboard-rt', [RTController::class, 'index'])
//         ->middleware(CekRole::class . ':rt')
//         ->name('rt.dashboard');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard-admin', [AdminController::class, 'index'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/dashboard-rw', [RWController::class, 'index'])
        ->middleware('role:rw')
        ->name('rw.dashboard');

    Route::get('/dashboard-rt', [RTController::class, 'index'])
        ->middleware('role:rt')
        ->name('rt.dashboard');
});

// =====================================================
// ROUTE TERBUKA UNTUK SEMUA ROLE (Admin, RW, RT)
// Index, Store, dan AJAX Filter (Semua tetap di PendudukController)
// =====================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/data-penduduk', [PendudukController::class, 'index'])->name('penduduk.index');
    Route::post('/data-penduduk', [PendudukController::class, 'store'])->name('penduduk.store');

    // AJAX untuk filter dropdown (Admin & RT bisa pakai)
    Route::get('/get-rw-by-daerah', [PendudukController::class, 'getRWByDaerah'])->name('get.rw.by.daerah');
    Route::get('/get-rt-by-daerah-rw', [PendudukController::class, 'getRTByDaerahRW'])->name('get.rt.by.daerah.rw');
});

// =====================================================
// ROUTE KHUSUS ADMIN (Edit, Update, Delete - bebas semua data)
// =====================================================
Route::middleware(['auth', 'role:admin|rw|rt'])->group(function () {
    Route::get('/data-penduduk/{id}/edit', [PendudukController::class, 'edit'])->name('penduduk.edit');
    Route::put('/data-penduduk/{id}', [PendudukController::class, 'update'])->name('penduduk.update');
    Route::delete('/data-penduduk/{id}', [PendudukController::class, 'destroy'])->name('penduduk.destroy');
});

// =====================================================
// ROUTE KHUSUS RW (Edit, Update, Delete - hanya RT dalam RW-nya)
// =====================================================
// Route::middleware(['auth', 'role:rw'])->group(function () {
//     Route::get('/rw/data-penduduk/{id}/edit', [PendudukController::class, 'edit'])->name('rw.penduduk.edit');
//     Route::put('/rw/data-penduduk/{id}', [PendudukController::class, 'update'])->name('rw.penduduk.update');
//     Route::delete('/rw/data-penduduk/{id}', [PendudukController::class, 'destroy'])->name('rw.penduduk.destroy');
// });

// // =====================================================
// // ROUTE KHUSUS RT (Edit, Update, Delete - hanya penduduk di RT sendiri)
// // =====================================================
// Route::middleware(['auth', 'role:rt'])->group(function () {
//     Route::get('/rt/data-penduduk/{id}/edit', [PendudukController::class, 'edit'])->name('rt.penduduk.edit');
//     Route::put('/rt/data-penduduk/{id}', [PendudukController::class, 'update'])->name('rt.penduduk.update');
//     Route::delete('/rt/data-penduduk/{id}', [PendudukController::class, 'destroy'])->name('rt.penduduk.destroy');
// });

Route::middleware('auth')->group(function () {
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan', [SettingsController::class, 'update'])->name('settings.update');
    Route::put('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
});

Route::resource('activity', ActivityController::class)->middleware(['auth']);