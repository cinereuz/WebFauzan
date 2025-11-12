<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

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
    
    // Rute untuk menampilkan halaman pembayaran
    Route::get('/pay/{animeId}', [PaymentController::class, 'showPaymentPage'])->name('payment.show');

    // Rute Admin (CUD + Export + Dashboard)
    Route::middleware(['is_admin'])->group(function () {
        Route::get('/admin/dashboard', [AnimeController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/tambah-anime', [AnimeController::class, 'create'])->name('anime.create');
        Route::post('/simpan-anime', [AnimeController::class, 'store'])->name('anime.store');
        Route::get('/edit-anime/{id}', [AnimeController::class, 'edit'])->name('anime.edit');
        Route::put('/update-anime/{id}', [AnimeController::class, 'update'])->name('anime.update');
        Route::delete('/hapus-anime/{id}', [AnimeController::class, 'destroy'])->name('anime.destroy');
        Route::get('/anime/export', [AnimeController::class, 'export'])->name('anime.export');
    });

    // Rute Pengguna
    Route::get('/my-library', [AnimeController::class, 'myLibrary'])->name('anime.library');
});

// RUTE RESET PASSWORD
Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('password.phone');
Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// RUTE LOGIN DENGAN GOOGLE
Route::get('/auth/google/redirect', [App\Http\Controllers\AuthController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [App\Http\Controllers\AuthController::class, 'handleGoogleCallback'])->name('google.callback');