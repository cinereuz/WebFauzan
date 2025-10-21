<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('anime_id')->constrained('anime')->onDelete('cascade');
            $table->decimal('gross_amount', 10, 2); // Total harga
            $table->string('payment_type')->nullable(); // Tipe pembayaran (gopay, bank_transfer)
            $table->string('bank_name')->nullable(); // Nama bank jika via transfer
            $table->string('va_number')->nullable(); // Nomor Virtual Account
            $table->string('transaction_status')->default('pending'); // Status transaksi
            $table->json('midtrans_response')->nullable(); // Untuk menyimpan seluruh response dari Midtrans
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};