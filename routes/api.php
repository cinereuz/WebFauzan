<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Rute untuk webhook notifikasi dari Midtrans
Route::post('/midtrans/notification', [PaymentController::class, 'notificationHandler']);