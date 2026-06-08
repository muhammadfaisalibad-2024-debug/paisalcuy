<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $fillable = [
        'toko_id',
        'latitude',
        'longitude',
        'accuracy',
        'jarak',
        'status'
    ];

    public function toko()
    {
        return $this->belongsTo(
            Toko::class
        );
    }
}