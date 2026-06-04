<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'idpesanan';

    protected $fillable = [
        'nama',
        'timestamp',
        'total',
        'metode_bayar',
        'status_bayar',
        'midtrans_order_id',
        'payment_reference',
        'payment_type',
        'payment_payload',
        'paid_at',
        'qr_code_path',
        'photo_path',
    ];

    /**
     * Relationship ke DetailPesanan
     */
    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'idpesanan', 'idpesanan');
    }
}
