<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\AuthController;

// A. RUTE AUTENTIKASI (LOGIN/REGISTER)
Route::get('/', [AuthController::class, 'showRegister'])->name('register');
Route::post('/', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// B. RUTE APLIKASI (Membutuhkan Login)
Route::middleware(['auth'])->group(function () {
    
    // Rute Read (Akses untuk User & Admin)
    Route::get('/anime', [AnimeController::class, 'index'])->name('anime.index');
    Route::get('/detail-anime/{id}', [AnimeController::class, 'show'])->name('anime.show');

    // Rute Admin (CUD + Export)
    Route::middleware(['is_admin'])->group(function () {
        // CUD Rutes
        Route::get('/tambah-anime', [AnimeController::class, 'create'])->name('anime.create');
        Route::post('/simpan-anime', [AnimeController::class, 'store'])->name('anime.store');
        Route::get('/edit-anime/{id}', [AnimeController::class, 'edit'])->name('anime.edit');
        Route::put('/update-anime/{id}', [AnimeController::class, 'update'])->name('anime.update');
        Route::delete('/hapus-anime/{id}', [AnimeController::class, 'destroy'])->name('anime.destroy');

        // Export Rute
        Route::get('/anime/export', [AnimeController::class, 'export'])->name('anime.export');
    });
});