<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'nfc_card_id',
        'serial',
        'status',
        'note',
        'scanned_at',
    ];

    protected $dates = ['scanned_at'];

    public function nfcCard(): BelongsTo
    {
        return $this->belongsTo(NfcCard::class, 'nfc_card_id');
    }
}
