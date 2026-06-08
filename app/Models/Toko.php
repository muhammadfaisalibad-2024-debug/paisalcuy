<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $fillable = [
        'barcode',
        'nama_toko',
        'alamat',
        'latitude',
        'longitude',
        'accuracy'
    ];
}