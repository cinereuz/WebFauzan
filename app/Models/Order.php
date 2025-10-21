<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'anime_id',
        'gross_amount',
        'payment_type',
        'bank_name',
        'va_number',
        'transaction_status',
        'midtrans_response',
    ];

    // Mengubah midtrans_response dari string JSON menjadi array saat diakses
    protected $casts = [
        'midtrans_response' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke AnimeModel
    public function anime()
    {
        return $this->belongsTo(AnimeModel::class, 'anime_id');
    }
}