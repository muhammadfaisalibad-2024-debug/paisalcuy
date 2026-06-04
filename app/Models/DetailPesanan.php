<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'iddetail_pesanan';

    protected $fillable = [
        'idmenu',
        'idpesanan',
        'jumlah',
        'harga',
        'subtotal',
        'timestamp',
        'catatan',
    ];

    /**
     * Relationship ke MenuKantin
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(MenuKantin::class, 'idmenu', 'idmenu');
    }
}
