<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimeModel extends Model
{
    use HasFactory;

    protected $table = 'anime';
    protected $fillable = [
        'judul',
        'genre',
        'episode',
        'sinopsis',
        'gambar',
    ];
}