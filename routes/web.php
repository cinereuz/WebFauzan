<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnimeController;

Route::get('/anime', [AnimeController::class, 'index'])->name('anime.index');
Route::get('/tambah-anime', [AnimeController::class, 'create'])->name('anime.create');
Route::post('/simpan-anime', [AnimeController::class, 'store'])->name('anime.store');
Route::get('/edit-anime/{id}', [AnimeController::class, 'edit'])->name('anime.edit');
Route::put('/update-anime/{id}', [AnimeController::class, 'update'])->name('anime.update');
Route::delete('/hapus-anime/{id}', [AnimeController::class, 'destroy'])->name('anime.destroy');
Route::get('/detail-anime/{id}', [AnimeController::class, 'show'])->name('anime.show');

Route::get('/', function () {
	return view('welcome');
});