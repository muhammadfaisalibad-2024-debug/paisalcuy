<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table      = 'tb_barang';
    protected $primaryKey = 'id_barang';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'id_barang',
        'nama_barang',
        'harga',
        'satuan',
        'kategori',
        'deskripsi',
        'stok',
    ];

    protected $casts = [
        'harga' => 'decimal:0',
        'stok'  => 'integer',
    ];

    /** Format harga ke Rupiah */
    public function getHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}
