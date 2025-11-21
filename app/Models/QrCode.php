<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $primaryKey = 'code_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_id',
        'user_id',
        'filename',
        'public_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}